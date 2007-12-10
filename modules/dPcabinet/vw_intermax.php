<?php /* $Id: vw_compta.php 1738 2007-03-19 16:33:47Z maskas $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: 1738 $
* @author Thomas Despoix
*/

global $AppUI, $can, $m;

$can->edit &= $AppUI->user_prefs["GestionFSE"];
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
$mediuser = new CMediusers();
$mediuser->load($AppUI->user_id);
$mediuser->loadRefFunction();

// Liste des praticiens du cabinet -> on ne doit pas voir les autres...
global $utypes;
$praticiens = in_array($utypes[$mediuser->_user_type], array("Administrator", "Secr�taire")) ?
  $mediuser->loadPraticiens(PERM_READ) :
  array($mediuser->_id => $mediuser);
  
// Chargement des FSE trouv�es
$filter = @new CLmFSE();
$filter->S_FSE_MODE_SECURISATION = mbGetValueFromGet("S_FSE_MODE_SECURISATION");
$filter->_date_min = mbDate();
$filter->_date_max = mbDate("+ 1 day");

mbDump($filter->_spec);

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("intermaxFunctions", $intermaxFunctions);
$smarty->assign("praticiens", $praticiens);
$smarty->assign("filter", $filter);
$smarty->display("vw_intermax.tpl");

