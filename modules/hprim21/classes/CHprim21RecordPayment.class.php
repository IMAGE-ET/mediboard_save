<?php
/**
 * $Id: CHL7v2RecordObservationResultSet.class.php 16357 2012-08-10 08:18:37Z lryo $
 * 
 * @package    Mediboard
 * @subpackage hprim21
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision: 16357 $
 */

/**
 * Class CHprim21RecordPayment 
 * Record payment, message XML
 */
class CHprim21RecordPayment extends CHPrim21MessageXML {  
  function getContentNodes() {
    $data = array();
    
    $exchange_hpr = $this->_ref_exchange_hpr;
    $sender       = $exchange_hpr->_ref_sender;
    $sender->loadConfigValues();    
    
    //$reg_patient = $this->queryNode("REG.PATIENT", null, $varnull, true);
    $this->queryNodes("REG", null, $data, true);

    return $data;
  }
 
  function handle($ack, CMbObject $object, $data) {
    // Traitement du message des erreurs
    $comment = "";
    
    $exchange_hpr = $this->_ref_exchange_hpr;
    $exchange_hpr->_ref_sender->loadConfigValues();
    $sender       = $exchange_hpr->_ref_sender;
    
    $this->_ref_sender = $sender;
    
    CMbObject::$useObjectCache = false;
    
    // Rejets partiels du message
    $errors = array();
    
    // R�cup�ration des r�glements
    foreach ($data["REG"] as $_REG) {
      $sejour = new CSejour();
      
      $NDA         = $this->getNDA($_REG);
      $user_reg    = $this->getUser($_REG);
      $segment_row = $this->getREGSegmentRow($_REG);
      
      // Recherche si on retrouve le s�jour
      if (!$this->admitFound($NDA, $sejour)) {
        $errors[] = $this->addError(
                      "P", 
                      null, 
                      array(
                        "REG", 
                        $segment_row, 
                        array(
                          $NDA,
                          $user_reg
                        )
                      ), 
                      null, 
                      $NDA, 
                      "I", 
                      CAppUI::tr("CHL7EventADT-P-01", $NDA)
                    );
        continue;
      }
      
      $consults      = array();
      $consultations = $sejour->loadRefsConsultations();
            
      // S�lection des consultations �ligibles
      foreach ($consultations as $_consult) {
        $user     = $_consult->loadRefPraticien();
        
        if ($user_reg) {
          if ($user->adeli == $user_reg) {
            $consults[$_consult->_id] = $_consult;
          }
          
          continue;
        }
      }
      
      // Si une seule consultation donn�e
      if (!count($consults) && count($consultations) == 1) {
        $consults = $consultations;
      } 
      
      $consultation  = new CConsultation();
      // On essaie d'en trouver une qui ne soit pas acquitt�e
      foreach ($consults as $_consult) {
        $facture = $_consult->loadRefFacture();
        
        if (!$facture->patient_date_reglement) {
          $consultation = $_consult;
          break;
        }
      }

      // Aucune consultation trouv�e
      if (!$consultation->_id && count($consults) > 0) {
        $consultation = end($consults);
      }
      
      if (!$consultation || !$consultation->_id) {
        $errors[] = $this->addError(
                      "P", 
                      null, 
                      array(
                        "REG", 
                        $segment_row, 
                        array(
                          $NDA,
                          $user_reg
                        )
                      ), 
                      null, 
                      $NDA, 
                      "I", 
                      CAppUI::tr("CHL7EventADT-P-02")
                    );
        continue;
      }
      
      $facture = $consultation->loadRefFacture();
      if (!$facture->_id) {
        /* @TODO avant de transposer la cr�ation de la facture dans le store */
        $facture = $consultation->createFactureConsult();
        if (!$facture->_id) {
          $errors[] = $this->addError(
                        "P", 
                        null, 
                        array(
                          "REG", 
                          $segment_row, 
                          array(
                            $NDA,
                            $user_reg
                          )
                        ), 
                        null, 
                        $NDA, 
                        "I", 
                        CAppUI::tr("CHL7EventADT-P-03")
                      );
          continue;
        }
      }
      
      $filename = substr($exchange_hpr->nom_fichier, 0, strpos($exchange_hpr->nom_fichier, ".")); 
      
      // Recherche d'un regl�ment par tag + idex (nom fichier - id reg)
      $id400 = $filename."_".$segment_row;
      $tag   = CAppUI::conf("hprim21 tag");
      $idex  = CIdSante400::getMatch("CReglement", $tag, $id400);
      
      // Mapping des r�glements
      $return_payment = $this->mapAndStorePayment($_REG, $facture, $idex);
      if (is_string($return_payment)) {
        $errors[] = $this->addError(
                      "P", 
                      null, 
                      array(
                        "REG", 
                        $segment_row, 
                        array(
                          $NDA,
                          $user_reg
                        )
                      ), 
                      null, 
                      $NDA, 
                      "I", 
                      CAppUI::tr("CHL7EventADT-P-04", $return_payment)
                    );
        continue;
      }      
    }

    if (count($errors) > 0) {
      return $exchange_hpr->setAckP($ack, $errors, $object);
    }
    
    return $exchange_hpr->setAckI($ack, null, $object);
  } 

  function addError($gravite, $ligne, $adr_segment, $donnee, $valeur, $type, $error_code) {
    return array(
      $gravite,
      $ligne,
      $adr_segment,
      $donnee,
      $valeur,
      $type,
      $error_code
    );
  }
  
  function admitFound($NDA, CSejour $sejour) {
    $sender = $this->_ref_sender;

    // NDA    
    $idexNDA = CIdSante400::getMatch("CSejour", $sender->_tag_sejour, $NDA);
    
    if ($idexNDA->_id) {
      $sejour->load($idexNDA->object_id);
      
      return true;
    }
    
    return false;
  }
  
  function getREGSegmentRow(DOMNode $node) {
    return $this->queryTextNode("REG.1", $node);  
  }
  
  function getNDA(DOMNode $node) {
    return $this->queryTextNode("REG.2", $node);  
  }
  
  function getUser(DOMNode $node) {
    return $this->queryTextNode("REG.7", $node);
  } 
  
  function getAmountPaid(DOMNode $node) {
    return $this->queryTextNode("REG.3/AM.1", $node);
  }
  
  function getDirection(DOMNode $node) {
    return $this->queryTextNode("REG.4", $node);
  }
  
  function getDatePayment(DOMNode $node) {
    return mbDate($this->queryTextNode("REG.5/TS.1", $node));
  }
  
  function mapAndStorePayment(DOMNode $node, CFactureConsult $facture, CIdSante400 $idex) {
    $reglement = new CReglement();
    $reglement->load($idex->object_id);     
    
    // Recherche du r�glement si pas retrouv� par son idex
    $reglement->setObject($facture);    
    $reglement->date     = $this->getDatePayment($node)." 00:00:00";
    
    $amount_paid = $this->getAmountPaid($node);
    $reglement->montant  = $amount_paid;
    
    $direction = $this->getDirection($node);
    if ($direction == "-") {
      $reglement->montant = $reglement->montant * -1;
    }
    $reglement->emetteur = "tiers";
    $reglement->mode     = "autre";
    
    $reglement->loadOldObject();
    
    if ($reglement->_old && round($reglement->montant, 3) == round($reglement->_old->montant, 3)) {
      return $reglement;
    }
         
    // Mise � jour du montant (du_tiers) de la facture         
    $value = ($reglement->_old) ? ($reglement->montant - $reglement->_old->montant) : $reglement->montant; 
    
    // Acquittement de la facture associ�e ?
    if ($msg = $reglement->store()) {
      return $msg;
    }

    // Gestion de l'idex
    if (!$idex->object_id) {
      $idex->object_id = $reglement->_id;
    }
    $idex->last_update = mbDateTime();
    if ($msg = $idex->store()) {
      return $msg;
    }

    if ($direction != "+") {
      return $reglement;
    }

    $facture->du_tiers += $value;
    if ($msg = $facture->store()) {
      return $msg;
    }
    
    return $reglement;
  } 
}