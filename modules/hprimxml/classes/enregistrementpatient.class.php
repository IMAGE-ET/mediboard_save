<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("hprimxml", "evenementspatients");

class CHPrimXMLEnregistrementPatient extends CHPrimXMLEvenementsPatients { 
  var $actions = array(
    'cr�ation' => "cr�ation",
    'remplacement' => "remplacement",
    'modification' => "modification",
  );
  
  function __construct() {   
    $this->sous_type = "enregistrementPatient";
             
    parent::__construct();
  }
  
  function generateFromOperation($mbPatient, $referent) {  
    $evenementsPatients = $this->documentElement;
    $evenementPatient   = $this->addElement($evenementsPatients, "evenementPatient");

    $enregistrementPatient = $this->addElement($evenementPatient, "enregistrementPatient");
    $actionConversion = array (
      "create" => "cr�ation",
      "store"  => "modification",
      "delete" => "suppression"
    );
    $action = (!$mbPatient->_ref_last_log) ? "modification" : $actionConversion[$mbPatient->_ref_last_log->type];

    $this->addAttribute($enregistrementPatient, "action", $action);
    
    $patient = $this->addElement($enregistrementPatient, "patient");
    // Ajout du patient   
    $this->addPatient($patient, $mbPatient, $referent);
        
    // Traitement final
    $this->purgeEmptyElements();
  }
  
  function getContentsXML() {
    $xpath = new CHPrimXPath($this);

    $query = "/hprim:evenementsPatients/hprim:evenementPatient";

    $evenementPatient = $xpath->queryUniqueNode($query);
    $enregistrementPatient = $xpath->queryUniqueNode("hprim:enregistrementPatient", $evenementPatient);

    $data['action'] = $this->getActionEvenement("hprim:enregistrementPatient", $evenementPatient);

    $data['patient'] = $xpath->queryUniqueNode("hprim:patient", $enregistrementPatient);

    $data['idSourcePatient'] = $this->getIdSource($data['patient']);
    $data['idCiblePatient']  = $this->getIdCible($data['patient']);
    
    return $data;
  }
  
  /**
   * Recording a patient with an IPP in the system
   * @param CHPrimXMLAcquittementsPatients $domAcquittement
   * @param CEchangeHprim $echange_hprim
   * @param CPatient $newPatient
   * @param array $data
   * @return CHPrimXMLAcquittementsPatients $messageAcquittement 
   **/
  function enregistrementPatient($domAcquittement, &$newPatient, $data) {
    $echange_hprim = $this->_ref_echange_hprim;        
    
    // Traitement du message des erreurs
    $avertissement = $msgID400 = $msgIPP = "";
    $_IPP_create = $_modif_patient = false;
    $mutex = new CMbSemaphore("sip-ipp");
    
    $dest_hprim = $echange_hprim->_ref_emetteur;
    
    // Acquittement d'erreur : identifiants source et cible non fournis
    if (!$data['idCiblePatient'] && !$data['idSourcePatient']) {
      $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E005");
      $doc_valid = $domAcquittement->schemaValidate();
      
      $echange_hprim->setAckError($doc_valid, $messageAcquittement, "erreur");
      return $messageAcquittement;
    }
      
    // Si SIP
    if (CAppUI::conf('sip server')) {      
      // Cas 1 : Identifiant source (IC) non fourni et identifiant cible fourni (IPP)
      if (!$data['idSourcePatient'] && $data['idCiblePatient']) {
        $IPP = new CIdSante400();
        //Param�trage de l'id 400
        $IPP->object_class = "CPatient";
        $IPP->tag = CAppUI::conf("sip tag_ipp");

        $IPP->id400 = str_pad($data['idCiblePatient'], 6, '0', STR_PAD_LEFT);

        // Cas 1.1 : idCible connu
        if ($IPP->loadMatchingObject()) {
          $newPatient->load($IPP->object_id);
          
          if (!$this->checkSimilarPatient($newPatient, $data['patient'])) {
            $commentaire = "Le nom et/ou le pr�nom sont tr�s diff�rents."; 
            $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E016", $commentaire);
            $doc_valid = $domAcquittement->schemaValidate();
            
            $echange_hprim->setAckError($doc_valid, $messageAcquittement, "erreur");
            return $messageAcquittement;
          }
          
          // Mapping du patient
          $newPatient = $this->mappingPatient($data['patient'], $newPatient);
          $newPatient->_IPP = $IPP->id400;
          $msgPatient = $newPatient->store();
          $newPatient->loadLogs();

          $modified_fields = "";
          if (is_array($newPatient->_ref_last_log->_fields)) {
            foreach ($newPatient->_ref_last_log->_fields as $field) {
              $modified_fields .= "$field \n";
            }
          } 
          
          $codes = array ($msgPatient ? "A003" : "I002", "I003");
          if ($msgPatient) {
            $avertissement = $msgPatient." ";
          } else {
            $commentaire .= "Patient modifi�e : $newPatient->_id. Les champs mis � jour sont les suivants : $modified_fields. IPP associ� : $IPP->id400.";
          }
        }
        // Cas 1.2 : idCible non connu
        else {          
          if (is_numeric($IPP->id400) && (strlen($IPP->id400) <= 6)) {
            // Mapping du patient
            $newPatient = $this->mappingPatient($data['patient'], $newPatient);
             // Si serveur et pas d'IPP sur le patient
            $newPatient->_no_ipp = 1;
            $msgPatient = $newPatient->store();
            
            $IPP->object_id = $newPatient->_id;
            $IPP->last_update = mbDateTime();
            $msgIPP = $IPP->store();
            
            $newPatient->_IPP = $IPP->id400;
            // Si serveur et on a un IPP sur le patient
            $newPatient->_no_ipp = 0;
            $msgPatient = $newPatient->store();
                      
            $codes = array ($msgPatient ? "A002" : "I001", $msgIPP ? "A005" : "A001");
            if ($msgPatient) {
              $avertissement = $msgPatient." ";
            } else {
              $commentaire = "Patient cr�� : $newPatient->_id. IPP cr�� : $IPP->id400.";
            }
          }
        }
        
        $messageAcquittement = $domAcquittement->generateAcquittementsPatients($avertissement ? "avertissement" : "OK", $codes, $avertissement ? $avertissement : $commentaire);
        $doc_valid = $domAcquittement->schemaValidate();
        $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
          
        $echange_hprim->_acquittement = $messageAcquittement;
        $echange_hprim->statut_acquittement = $avertissement ? "avertissement" : "OK";
        $echange_hprim->setObjectIdClass("CPatient", $data['idSourcePatient'] ? $data['idSourcePatient'] : $newPatient->_id);
        $echange_hprim->store();
        
        return $messageAcquittement;
      }
        
      $id400 = new CIdSante400();
      //Param�trage de l'id 400
      $id400->object_class = "CPatient";
      $id400->tag = $dest_hprim->_tag_patient;
      $id400->id400 = $data['idSourcePatient'];
   
      // Cas 2 : Patient existe sur le SIP
      if($id400->loadMatchingObject()) {
        // Identifiant du patient sur le SIP
        $idPatientSIP = $id400->object_id;
        // Cas 2.1 : Pas d'idCible
        if(!$data['idCiblePatient']) {
          if ($newPatient->load($idPatientSIP)) {
            // Mapping du patient
            $newPatient = $this->mappingPatient($data['patient'], $newPatient);

            // Cr�ation de l'IPP
            $IPP = new CIdSante400();
            //Param�trage de l'id 400
            $IPP->object_class = "CPatient";
            $IPP->tag = CAppUI::conf("sip tag_ipp");
            $IPP->object_id = $idPatientSIP;

            $mutex->acquire();
            // Chargement du dernier IPP s'il existe
            if (!$IPP->loadMatchingObject("id400 DESC")) {
              // Incrementation de l'id400
              $IPP->id400++;
              $IPP->id400 = str_pad($IPP->id400, 6, '0', STR_PAD_LEFT);

              $IPP->_id = null;
              $IPP->last_update = mbDateTime();
              $msgIPP = $IPP->store();
                
              $_IPP_create = true;
            }
            $mutex->release();

            $newPatient->_IPP = $IPP->_id;
            $msgPatient = $newPatient->store();
            $newPatient->loadLogs();

            $modified_fields = "";
            if (is_array($newPatient->_ref_last_log->_fields)) {
              foreach ($newPatient->_ref_last_log->_fields as $field) {
                $modified_fields .= "$field \n";
              }
            }
             
            $codes = array ($msgPatient ? "A003" : "I002", $msgIPP ? "A005" : $_IPP_create ? "I006" : "I008");
            if ($msgPatient || $msgIPP) {
              $avertissement = $msgPatient." ".$msgIPP;
            } else {
              $commentaire = "Patient modifi� : $newPatient->_id. Les champs mis � jour sont les suivants : $modified_fields. IPP cr�� : $IPP->id400.";
            }
            $messageAcquittement = $domAcquittement->generateAcquittementsPatients($avertissement ? "avertissement" : "OK", $codes, $avertissement ? $avertissement : $commentaire);
            $doc_valid = $domAcquittement->schemaValidate();
            $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
              
            $echange_hprim->statut_acquittement = $avertissement ? "avertissement" : "OK";
          }
        }
        // Cas 2.2 : idCible envoy�
        else {
          $IPP = new CIdSante400();
          //Param�trage de l'id 400
          $IPP->object_class = "CPatient";
          $IPP->tag = CAppUI::conf("sip tag_ipp");

          $IPP->id400 = $data['idCiblePatient'];
          
          // Cas 2.2.1 : idCible connu
          if ($IPP->loadMatchingObject()) {
            // Acquittement d'erreur idSource et idCible incoh�rent
            if ($idPatientSIP != $IPP->object_id) {
              $commentaire = "L'identifiant source fait r�f�rence au patient : $idPatientSIP et l'identifiant cible au patient : $IPP->object_id.";
              $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E004", $commentaire);
              $doc_valid = $domAcquittement->schemaValidate();
              $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
        
              $echange_hprim->_acquittement = $messageAcquittement;
              $echange_hprim->statut_acquittement = "erreur";
              $echange_hprim->date_echange = mbDateTime();
              $echange_hprim->setObjectIdClass("CPatient", $data['idSourcePatient'] ? $data['idSourcePatient'] : $newPatient->_id);
              $echange_hprim->store();
              
              return $messageAcquittement;
            } else {
              $newPatient->load($IPP->object_id);

              // Mapping du patient
              $newPatient = $this->mappingPatient($data['patient'], $newPatient);
              $newPatient->_IPP = $IPP->id400;
              $msgPatient = $newPatient->store();
              $newPatient->loadLogs();
               
              $modified_fields = "";
              if (is_array($newPatient->_ref_last_log->_fields)) {
                foreach ($newPatient->_ref_last_log->_fields as $field) {
                  $modified_fields .= "$field \n";
                }
              }
               
              if ($msgPatient) {
                $avertissement = $msgPatient." ";
              } else {
                $commentaire = "Patient modifi� : $newPatient->_id. Les champs mis � jour sont les suivants : $modified_fields.";
              }
              $messageAcquittement = $domAcquittement->generateAcquittementsPatients($avertissement ? "avertissement" : "OK", $msgPatient ? "A003" : "I002", $avertissement ? $avertissement : $commentaire);
              
              $echange_hprim->statut_acquittement = $avertissement ? "avertissement" : "OK";
            }
          } 
          // Cas 2.2.2 : idCible non connu
          else {
            $commentaire = "L'identifiant source fait r�f�rence au patient : $idPatientSIP et l'identifiant cible n'est pas connu.";
            $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E003", $commentaire);
            $doc_valid = $domAcquittement->schemaValidate();
            $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
        
            $echange_hprim->statut_acquittement = "erreur";
            $echange_hprim->_acquittement = $messageAcquittement;
            $echange_hprim->date_echange = mbDateTime();
            $echange_hprim->store();
    
            return $messageAcquittement; 
          }
        }
      }
      // Cas 3 : Patient n'existe pas sur le SIP
      else {
        // Mapping du patient
        $newPatient = $this->mappingPatient($data['patient'], $newPatient);
        // Cas 3.1 : Patient retrouv�  (nom, pr�nom et date de naissance)      
        if ($newPatient->loadMatchingPatient()) {
          // Mapping du patient
          $newPatient = $this->mappingPatient($data['patient'], $newPatient);
          // Si serveur et pas d'IPP sur le patient
          $newPatient->_no_ipp = 1;
          $msgPatient = $newPatient->store();
      
          $newPatient->loadLogs();
          $modified_fields = "";
          if (is_array($newPatient->_ref_last_log->_fields)) {
            foreach ($newPatient->_ref_last_log->_fields as $field) {
              $modified_fields .= "$field \n";
            }
          }
          $_modif_patient = true; 
          $commentaire = "Patient modifi� : $newPatient->_id.  Les champs mis � jour sont les suivants : $modified_fields.";           
        } 
        // Cas 3.2 : Patient non retrouv�
        else {
          // Si serveur et pas d'IPP sur le patient
          $newPatient->_no_ipp = 1;
          $msgPatient = $newPatient->store();
        
          $commentaire = "Patient cr�� : $newPatient->_id. ";
        }
        
        // Cr�ation de l'identifiant externe TAG CIP + idSource
        $id400Patient = new CIdSante400();
        //Param�trage de l'id 400
        $id400Patient->object_class = "CPatient";
        $id400Patient->tag = $dest_hprim->_tag_patient;
        $id400Patient->id400 = $data['idSourcePatient'];
        $id400Patient->object_id = $newPatient->_id;
        $id400Patient->_id = null;
        $id400Patient->last_update = mbDateTime();
        $msgID400 = $id400Patient->store();
        
        // Cr�ation de l'IPP
        $IPP = new CIdSante400();
        //Param�trage de l'id 400
        $IPP->object_class = "CPatient";
        $IPP->tag = CAppUI::conf("sip tag_ipp");
        
        $mutex->acquire();
        // Cas IPP fourni
        if ($data['idCiblePatient']) {
          $IPP->id400 = str_pad($data['idCiblePatient'], 6, '0', STR_PAD_LEFT);

          // IPP fourni non connu
          if (!$IPP->loadMatchingObject() && is_numeric($IPP->id400) && (strlen($IPP->id400) <= 6)) {
            $_code_IPP = "A001";
          }
          // IPP fourni connu
          else {  
            // Si IPP est identique au patient retrouv�
            if ($IPP->object_id == $newPatient->_id) {
              $_code_IPP = "I025";
            } else {
              // Annule l'IPP envoy�          
              $IPP->id400 = null;
              $IPP->loadMatchingObject("id400 DESC");
  
              // Incrementation de l'id400
              $IPP->id400++;
              $IPP->id400 = str_pad($IPP->id400, 6, '0', STR_PAD_LEFT);
              $IPP->_id = null;
               
              $_code_IPP = "I009";
            }
          }
        } else { 
          // Si le patient a �t� retrouv� on a d�j� l'IPP
          if ($_modif_patient) {
            $IPP->object_id = $newPatient->_id;
            $IPP->loadMatchingObject();
            $_code_IPP = "I026";
          } else {
            $IPP->loadMatchingObject("id400 DESC");
  
            // Incrementation de l'id400
            $IPP->id400++;
            $IPP->id400 = str_pad($IPP->id400, 6, '0', STR_PAD_LEFT);
  
            $IPP->_id = null;
  
            $_code_IPP = "I006";
          }          
        }

        $IPP->object_id = $newPatient->_id;

        $IPP->last_update = mbDateTime();
        $msgIPP = $IPP->store();

        $mutex->release();
        
        $newPatient->_IPP = $IPP->id400;
        // Si serveur et on a un IPP sur le patient
        $newPatient->_no_ipp = 0;
        $msgPatient = $newPatient->store();
        
        $codes = array ($msgPatient ? ($_modif_patient ? "A003" : "A002") : "I001", $msgID400 ? "A004" : "I004", $msgIPP ? "A005" : $_code_IPP);
        if ($msgPatient || $msgID400 || $msgIPP) {
          $avertissement = $msgPatient." ".$msgID400." ".$msgIPP;
        } else {
          $commentaire = "Patient cr�� : $newPatient->_id. Identifiant externe cr�� : $id400Patient->id400. IPP cr�� : $IPP->id400.";
        }
        $messageAcquittement = $domAcquittement->generateAcquittementsPatients($avertissement ? "avertissement" : "OK", $codes, $avertissement ? $avertissement : $commentaire);
        $doc_valid = $domAcquittement->schemaValidate();
        $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
        
        $echange_hprim->statut_acquittement = $avertissement ? "avertissement" : "OK";
      }
    } 
    // Si CIP
    else {      
      $IPP = new CIdSante400();
      //Param�trage de l'id 400
      $IPP->object_class = "CPatient";
      $IPP->tag = $dest_hprim->_tag_patient;
      $IPP->id400 = $data['idSourcePatient'];
      
      // idSource non connu
      if(!$IPP->loadMatchingObject()) {
        // idCible fourni
        if ($data['idCiblePatient']) {
          if ($newPatient->load($data['idCiblePatient'])) {
            // Mapping du patient
            $newPatient = $this->mappingPatient($data['patient'], $newPatient);
        
            // Notifier les autres destinataires
            $newPatient->_hprim_initiateur_group_id = $dest_hprim->group_id;
            $msgPatient = $newPatient->store();
        
            $newPatient->loadLogs();
            $modified_fields = "";
            if (is_array($newPatient->_ref_last_log->_fields)) {
              foreach ($newPatient->_ref_last_log->_fields as $field) {
                $modified_fields .= "$field \n";
              }
            }
            $_code_IPP = "I021";
            $_modif_patient = true; 
            $commentaire = "Patient modifi�e : $newPatient->_id. Les champs mis � jour sont les suivants : $modified_fields.";
          } else {
            $_code_IPP = "I020";
          }
        } else {
          $_code_IPP = "I022";  
        }
        // Mapping du patient
        $newPatient = $this->mappingPatient($data['patient'], $newPatient);
        // Notifier les autres destinataires
        $newPatient->_hprim_initiateur_group_id = $dest_hprim->group_id;
        
        // Patient retrouv�      
        if (!$newPatient->_id) {
          if ($newPatient->loadMatchingPatient()) {
            $msgPatient = $newPatient->store();
        
            $newPatient->loadLogs();
            $modified_fields = "";
            if (is_array($newPatient->_ref_last_log->_fields)) {
              foreach ($newPatient->_ref_last_log->_fields as $field) {
                $modified_fields .= "$field \n";
              }
            }
            $_code_IPP = "A021";
            $_modif_patient = true; 
            $commentaire = "Patient modifi�e : $newPatient->_id.  Les champs mis � jour sont les suivants : $modified_fields.";           
          } else {
            $msgPatient = $newPatient->store();
          
            $commentaire = "Patient cr�� : $newPatient->_id. ";
          }
        }
          
        $IPP->object_id = $newPatient->_id;
        $IPP->last_update = mbDateTime();
        $msgIPP = $IPP->store();
        
        $codes = array ($msgPatient ? ($_modif_patient ? "A003" : "A002") : ($_modif_patient ? "I002" : "I001"), $msgIPP ? "A005" : $_code_IPP);
        
        if ($msgPatient || $msgIPP) {
          $avertissement = $msgPatient." ".$msgIPP;
        } else {
          $commentaire .= "IPP cr�� : $IPP->id400.";
        }
      } 
      // idSource connu
      else {
        $newPatient->load($IPP->object_id);
        // Mapping du patient
        $newPatient = $this->mappingPatient($data['patient'], $newPatient);
                        
        // idCible non fourni
        if (!$data['idCiblePatient']) {
          $_code_IPP = "I023"; 
        } else {
          $tmpPatient = new CPatient();
          // idCible connu
          if ($tmpPatient->load($data['idCiblePatient'])) {
            if ($tmpPatient->_id != $IPP->object_id) {
              $commentaire = "L'identifiant source fait r�f�rence au patient : $IPP->object_id et l'identifiant cible au patient : $tmpPatient->_id.";
              $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E004", $commentaire);
              $doc_valid = $domAcquittement->schemaValidate();
              
              $echange_hprim->setAckError($doc_valid, $messageAcquittement, "erreur");
              return $messageAcquittement;
            }
            $_code_IPP = "I024"; 
          }
          // idCible non connu
          else {
            $_code_IPP = "A020";
          }
        }
        // Notifier les autres destinataires
        $newPatient->_hprim_initiateur_group_id = $dest_hprim->group_id;
        $msgPatient = $newPatient->store();
        
        $newPatient->loadLogs();
        $modified_fields = "";
        if (is_array($newPatient->_ref_last_log->_fields)) {
          foreach ($newPatient->_ref_last_log->_fields as $field) {
            $modified_fields .= "$field \n";
          }
        }
        $codes = array ($msgPatient ? "A003" : "I002", $_code_IPP);
        
        if ($msgPatient) {
          $avertissement = $msgPatient." ";
        } else {
          $commentaire = "Patient modifi�e : $newPatient->_id. Les champs mis � jour sont les suivants : $modified_fields. IPP associ� : $IPP->id400.";
        }
      }
      $messageAcquittement = $domAcquittement->generateAcquittementsPatients($avertissement ? "avertissement" : "OK", $codes, $avertissement ? $avertissement : $commentaire); 
      $doc_valid = $domAcquittement->schemaValidate();
      $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
        
      $echange_hprim->statut_acquittement = $avertissement ? "avertissement" : "OK";
    }
    
    $echange_hprim->_acquittement = $messageAcquittement;
    $echange_hprim->date_echange = mbDateTime();
    $echange_hprim->setObjectIdClass("CPatient", $data['idCiblePatient'] ? $data['idCiblePatient'] : $newPatient->_id);
    $echange_hprim->store();
    
    return $messageAcquittement;
  }
}
?>