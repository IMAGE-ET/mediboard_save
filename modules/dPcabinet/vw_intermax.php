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

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("intermaxFunctions", $intermaxFunctions);
$smarty->display("vw_intermax.tpl");

