<?php
$document = new CMbXMLDocument();
$document->load("tmp/mouvementPatient.xml");
$msgEvt = utf8_encode($document->saveXML());
mbTrace($msgEvt);
/*
// R�cup�ration des informations du message XML
$domGetEvenement = CHPrimXMLEvenementsPatients::getHPrimXMLEvenementsPatients($msgEvt);
mbTrace($domGetEvenement);
$domGetEvenement->loadXML(utf8_decode($msgEvt));
$doc_errors = $domGetEvenement->schemaValidate(null, true);
mbTrace($doc_errors);
die;
$evtFusion->load("tmp/enregistrementPatient.xml");*/

$dest_hprim = new CDestinataireHprim();
$dest_hprim->type = "sip";
$dest_hprim->loadMatchingObject();

if (!$client = CMbSOAPClient::make($dest_hprim->url, $dest_hprim->username, $dest_hprim->password, "hprimxml")) {
  trigger_error("Impossible de joindre le destinataire : ".$dest_hprim->url);
} 
  
// R�cup�re le message d'acquittement apr�s l'execution la methode evenementPatient
if (null == $acquittement = $client->evenementPatient($msgEvt)) {
  trigger_error("Ev�nement patient impossible sur le SIP : ".$dest_hprim->url);
}
mbTrace($acquittement);
$domGetAcquittement = new CHPrimXMLAcquittementsPatients();
$domGetAcquittement->loadXML(utf8_decode($acquittement));  
$errors = $domGetAcquittement->schemaValidate(null, true);
?>