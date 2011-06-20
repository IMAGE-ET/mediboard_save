<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();

$echange_hprim_id         = CValue::get("echange_xml_id");
$echange_hprim_classname  = CValue::get("echange_xml_classname");

$where = '';
if (!$echange_hprim_id) {
  if (!($limit = CAppUI::conf('sip batch_count'))) {
    return;
  }
  $echange_hprim = new CEchangeHprim();
  $where['statut_acquittement']     = "IS NULL";
  $where['emetteur_id']             = "IS NULL";
  $where['destinataire_id']         = "IS NOT NULL";
  $where['message_valide']          = "= '1'";
  $where['acquittement_valide']     = "IS NULL"; 
  $where['acquittement_content_id'] = "IS NULL"; 
  $where['date_echange']            = "IS NULL"; 
  
  $notifications = $echange_hprim->loadList($where, null, $limit);
  // Effectue le traitement d'enregistrement des notifications sur lequel le cron vient de passer
  // ce qui permet la gestion des doublons
  foreach ($notifications as $notification) {
    $notification->date_echange = mbDateTime();
    $notification->store();
  }
  
  foreach ($notifications as $notification) {      
    $dest_hprim = new CDestinataireHprim();
    $dest_hprim->load($notification->destinataire_id);
        
    if ($dest_hprim->actif) {
      $source = CExchangeSource::get("$dest_hprim->_guid-evenementPatient");
      $source->setData($notification->_message);
      try {
        $source->send();
      } catch(Exception $e) {
        $notification->date_echange = "";
        $notification->store();
        continue;
      }
      
      $acquittement = $source->getACQ();
      
      if ($acquittement) {
        $domGetAcquittement = new CHPrimXMLAcquittementsPatients();
        $domGetAcquittement->loadXML($acquittement);
        $doc_valid = $domGetAcquittement->schemaValidate();
        if ($doc_valid) {
          $notification->statut_acquittement = $domGetAcquittement->getStatutAcquittementPatient();
        }
        $notification->acquittement_valide = $doc_valid ? 1 : 0;
        
        $notification->date_echange = mbDateTime();
        $notification->_acquittement = $acquittement;
        $notification->store();
      } 
      else {
        $notification->date_echange = "";
        $notification->store();
      }   
    } else {
      $notification->date_echange = "";
      $notification->store();
    }
  }
} else {
  // Chargement de l'objet
  $echange_hprim = new $echange_hprim_classname;
  $echange_hprim->load($echange_hprim_id);
  
  $dest_hprim = new CDestinataireHprim();
  $dest_hprim->load($echange_hprim->destinataire_id);
  
  $source = CExchangeSource::get("$dest_hprim->_guid-evenementPatient");
  $source->setData($echange_hprim->_message);
  $source->send();
  $acquittement = $source->getACQ();
  
  if ($acquittement) {
    $domGetAcquittement = new CHPrimXMLAcquittementsPatients();
    $domGetAcquittement->loadXML($acquittement);
    $doc_valid = $domGetAcquittement->schemaValidate();
    if ($doc_valid) {
      $echange_hprim->statut_acquittement = $domGetAcquittement->getStatutAcquittementPatient();
    }
    $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
      
    $echange_hprim->date_echange = mbDateTime();
    $echange_hprim->_acquittement = $acquittement;
  
    $echange_hprim->store();
    
    CAppUI::setMsg("Message HPRIM envoy�", UI_MSG_OK);
    
    echo CAppUI::getMsg();
  }
}

?>