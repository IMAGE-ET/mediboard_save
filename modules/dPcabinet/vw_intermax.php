<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Thomas Despoix
*/

global $can;

$can->edit &= CAppUI::pref("GestionFSE");
$can->needsEdit();

if ($can->admin) {
	$intermaxFunctions = array(
	  "Fonctions Int�gr�es" => array(
	    "Lire CPS",
	    "Lire Vitale",
	    "Consulter Vitale",
	    "Formater FSE",
	  ),
	  "G�n�rales" => array(
	    "Professionnels de sant�",
	    "Feuilles de soins",
	    "Visites",
	    "T�l�transmission",
	    "Liaison comptable",
	  ),
	  "Pr�f�rences" => array(
	    "Configuration",
	    "Coordonn�es",
	    "Pr�f�rences Liaison comptable",
	    "Fichiers RSP",
	    "Tiers-payant",
	    "Type Emetteur",
	  ),
	  "Fichiers de base" => array(
	    "Titres",
	    "Jours f�ri�s",
	    "Communes",
	    "Prescripteurs",
	    "Lettres cl�s",
	    "Organismes AMO",
	    "Organismes AMC",
	    "Centre de service",
	    "Th�saurus",
	  ),
	  "Utilitaires" => array(
	    "Modification r�f�rentiel CCAM",
	    "V�rification des donn�es",
	    "Recalcul des soldes patients",
	    "Emettre vers le CNDA",
	    "D�blocage CPS",
	    "Mode de Trace",
	    "Initialisation des compteurs",
	  ),
	);
}
else {
	$intermaxFunctions = array(
	  "Fonctions Int�gr�es" => array(
	    "Lire CPS",
	    "Lire Vitale",
	  ),
	  "G�n�rales" => array(
	    "Feuilles de soins",
	    "T�l�transmission",
	  ),
	  "Pr�f�rences" => array(
	    "Configuration",
	    "Coordonn�es",
	    "Fichiers RSP",
	    "Tiers-payant",
	  ),
	);
}

// Praticiens autoris�s
$mediuser = CMediusers::get();
$mediuser->loadRefFunction();

// Liste des praticiens du cabinet -> on ne doit pas voir les autres...
if(in_array(CUser::$types[$mediuser->_user_type], array("Administrator", "Secr�taire"))) {
  if(CAppUI::pref("pratOnlyForConsult", 1)) {
    $prats = $mediuser->loadPraticiens(PERM_READ);
  } else {
    $prats = $mediuser->loadProfessionnelDeSante(PERM_READ);
  }
} else {
  $prats = array($mediuser->_id => $mediuser);
}
foreach($prats as $_prat) {
  $_prat->loadIdCPS();
}
  
// Chargement des FSE trouv�es
$fse = @new CLmFSE();
$fse->S_FSE_MODE_SECURISATION = CValue::get("S_FSE_MODE_SECURISATION");
$fse->_date_min = mbDate();
$fse->_date_max = mbDate("+ 1 day");

$lot = @new CLmLot();
$lot->_date_min = mbDate();
$lot->_date_max = mbDate("+ 1 day");

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("intermaxFunctions", $intermaxFunctions);
$smarty->assign("prats", $prats);
$smarty->assign("fse", $fse);
$smarty->assign("lot", $lot);
$smarty->display("vw_intermax.tpl");

