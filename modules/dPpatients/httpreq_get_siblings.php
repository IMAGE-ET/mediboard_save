<?php /* $Id:  $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision:  $
* @author S�bastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$nom       = mbGetValueFromGetOrSession("nom"      , null);
$prenom    = mbGetValueFromGetOrSession("prenom"   , null);
$naissance = mbGetValueFromGetOrSession("naissance", "0000-00-00");

$patientSib = new CPatient();
$patientSib->nom       = $nom;
$patientSib->prenom    = $prenom;
$patientSib->naissance = $naissance;

$siblings = $patientSib->getSiblings();

if(count($siblings) == 0) {
  $textSiblings = null;
}else{
	$textSiblings = "Risque de doublons :";
  foreach($siblings as $key => $value) {
    $textSiblings .= "\n>> ".$value->nom." ".$value->prenom.
                     " n�(e) le ".$value->naissance.
                     " habitant ".$value->adresse." ".$value->cp." ".$value->ville;
  }
  $textSiblings .= "\nVoulez-vous tout de m�me le cr�er ?";
}


// Cr�ation du template
$smarty = new CSmartyDP(1);
$smarty->assign("textSiblings"    , $textSiblings);

$smarty->display("httpreq_get_siblings.tpl");
?>