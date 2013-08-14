<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHPrimXMLAcquittementsPatients extends CHPrimXMLAcquittements {
  public $_identifiant_acquitte;
  public $_sous_type_evt;
  
  var $_codes_erreurs        = array(
    "ok"  => "OK",
    "avt" => "avertissement",
    "err" => "erreur"
  );
  
  static function getVersionAcquittementsPatients() {    
    return "msgAcquittementsPatients".str_replace(".", "", CAppUI::conf('hprimxml evt_patients version'));
  } 
  
  function __construct() {
    $this->evenement = "evt_patients";
     
    parent::__construct("patients", self::getVersionAcquittementsPatients());
  }

  function generateEnteteMessageAcquittement($statut, $codes = null, $commentaires = null) {
    $echg_hprim      = $this->_ref_echange_hprim;
    $identifiant     = isset($echg_hprim->_id) ? str_pad($echg_hprim->_id, 6, '0', STR_PAD_LEFT) : "ES{$this->now}";
    
    $acquittementsPatients = $this->addElement($this, "acquittementsPatients", null, "http://www.hprim.org/hprimXML");

    $enteteMessageAcquittement = $this->addElement($acquittementsPatients, "enteteMessageAcquittement");
    $this->addAttribute($enteteMessageAcquittement, "statut", $statut);

    $this->addElement($enteteMessageAcquittement, "identifiantMessage", $identifiant);
    $this->addDateTimeElement($enteteMessageAcquittement, "dateHeureProduction");

    $emetteur = $this->addElement($enteteMessageAcquittement, "emetteur");
    $agents = $this->addElement($emetteur, "agents");
    $this->addAgent($agents, "application", "MediBoard", "Gestion des Etablissements de Sant�");
    $group = CGroups::loadCurrent();
    $group->loadLastId400();
    $this->addAgent($agents, $this->getAttSysteme(), CAppUI::conf('mb_id'), $group->text);

    $echg_hprim->loadRefsInteropActor();
    // Pour un acquittement l'emetteur du message devient destinataire
    $destinataire = $this->addElement($enteteMessageAcquittement, "destinataire");
    $agents = $this->addElement($destinataire, "agents");
    $this->addAgent($agents, "application", $echg_hprim->_ref_sender->nom, $echg_hprim->_ref_sender->libelle);
    /* @todo Doit-on g�rer le syst�me du destinataire ? */
    //$this->addAgent($agents, "syst�me", $group->_id, $group->text);

    $this->addElement($enteteMessageAcquittement, "identifiantMessageAcquitte", $this->_identifiant_acquitte);
    
    if ($statut == "OK") {
      if (is_array($codes)) {
        $_codes = $_libelle_codes = "";
        foreach ($codes as $code) {
          $_codes .= $code;
          $_libelle_codes .= CAppUI::tr("hprimxml-error-$code");
        }
        $this->addObservation($enteteMessageAcquittement, $_codes, $_libelle_codes, $commentaires);
      } else {
        $this->addObservation($enteteMessageAcquittement, $codes, CAppUI::tr("hprimxml-error-$codes"), $commentaires);
      }
    }
  }

  function addErreursAvertissements($statut, $codes, $commentaires = null, $mbObject = null) {
    $acquittementsPatients = $this->documentElement;
     
    $erreursAvertissements = $this->addElement($acquittementsPatients, "erreursAvertissements");
     
    if (is_array($codes)) {
      foreach ($codes as $code) {
        $this->addErreurAvertissement($erreursAvertissements, $statut, $code, CAppUI::tr("hprimxml-error-$code"), $commentaires, $mbObject);
      }
    }
    else {
      $this->addErreurAvertissement($erreursAvertissements, $statut, $codes, CAppUI::tr("hprimxml-error-$codes"), $commentaires, $mbObject);
    }   
  }

  /**
   * @see parent::generateAcquittements
   */
  function generateAcquittements($statut, $codes, $commentaires = null, $mbObject = null) {
    if ($statut != "OK") {
      $this->generateEnteteMessageAcquittement($statut);
      $this->addErreursAvertissements($statut, $codes, $commentaires, $mbObject);
    }
    else {
      $this->generateEnteteMessageAcquittement($statut, $codes, $commentaires);
    }

    return utf8_encode($this->saveXML());
  }

  /**
   * @see parent::generateAcquittementsError
   */
  function generateAcquittementsError($code, $commentaire = null, CMbObject $mbObject = null) {
    return $this->_ref_echange_hprim->setAckError($this, $code, $commentaire, $mbObject);
  }

  /**
   * @see parent::getStatutAcquittement
   */
  function getStatutAcquittement() {
    return $this->getStatutAcquittementPatient();
  }

  /**
   * R�cup�ration du statut de l'acquittement patient
   *
   * @return string
   */
  function getStatutAcquittementPatient() {
    $xpath = new CHPrimXPath($this);
        
    return $xpath->queryAttributNode("/hprim:acquittementsPatients/hprim:enteteMessageAcquittement", null, "statut"); 
  }

  /**
   * R�cup�ration de l'acquittement patient
   *
   * @return array
   */
  function getAcquittementsPatients() {
    $xpath = new CHPrimXPath($this);
    
    $xpath->queryAttributNode("/hprim:acquittementsPatients/hprim:enteteMessageAcquittement", null, "statut");
    
    $query = "/hprim:acquittementsPatients/hprim:enteteMessageAcquittement";
    $enteteMessageAcquittement = $xpath->queryUniqueNode($query);  
    
    $data['identifiantMessage'] = $xpath->queryTextNode("hprim:identifiantMessage", $enteteMessageAcquittement);
    $agents = $xpath->queryUniqueNode("hprim:emetteur/hprim:agents", $enteteMessageAcquittement);
    $systeme = $xpath->queryUniqueNode("hprim:agent[@categorie='".$this->getAttSysteme()."']", $agents);
    $data['idClient'] = $xpath->queryTextNode("hprim:code", $systeme);
    $data['libelleClient'] = $xpath->queryTextNode("hprim:libelle", $systeme);
    
    $data['identifiantMessageAcquitte'] = $xpath->queryTextNode("hprim:identifiantMessageAcquitte", $enteteMessageAcquittement);
    
    return $data;
  }

  /**
   * R�cup�ration des observations de l'acquittement patient
   *
   * @return array
   */
  function getAcquittementObservationPatients() {
    $xpath = new CHPrimXPath($this);
    
    $statut = $xpath->queryAttributNode("/hprim:acquittementsPatients/hprim:enteteMessageAcquittement", null, "statut"); 
    
    $query = "/hprim:acquittementsPatients/hprim:enteteMessageAcquittement";
    $enteteMessageAcquittement = $xpath->queryUniqueNode($query);  
    
    $observations = array();
    if ($statut == "OK") {
      $d = array();
      $observations[] = &$d;
        
      $observation = $xpath->queryUniqueNode("hprim:observation", $enteteMessageAcquittement);
      $d['code'] = chunk_split($xpath->queryTextNode("hprim:code", $observation, "", false), 4, ' ');
      $d['libelle'] = $xpath->queryTextNode("hprim:libelle", $observation, "", false);
      $d['commentaire'] = $xpath->queryTextNode("hprim:commentaire", $observation, "", false);
    }
    else {
      $query = "/hprim:acquittementsPatients/hprim:erreursAvertissements/*";
      $erreursAvertissements = $xpath->query($query);   

      foreach ($erreursAvertissements as $erreurAvertissement) {
        $d = array();

        $observation = $xpath->queryUniqueNode("hprim:observations/hprim:observation", $erreurAvertissement);
        $d['code'] = chunk_split($xpath->queryTextNode("hprim:code", $observation, "", false), 4, ' ');
        $d['libelle'] = $xpath->queryTextNode("hprim:libelle", $observation, "", false);
        $d['commentaire'] = $xpath->queryTextNode("hprim:commentaire", $observation, "", false);
        $observations[] = $d;
      }
    }  
    
    return $observations;
  } 
}

