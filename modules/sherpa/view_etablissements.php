<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage sherpa
* @version $Revision$
* @author Sherpa
*/

global $can;
$can->needsRead();

// Chargement de l'�tablissement courant
$etablissement = new CSpEtablissement;
$etablissement->load(mbGetAbsValueFromGetOrSession("sp_etab_id"));
$etablissement->loadRefs();

// Chargement de tous les �tablissements
$etablissements = $etablissement->loadList();
foreach ($etablissements as &$_etablissement) {
  $_etablissement->loadRefs();
}

// R�cup�ration des groupes
$listGroups = new CGroups;
$listGroups = $listGroups->loadList();

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("etablissement"	, $etablissement);
$smarty->assign("etablissements", $etablissements);
$smarty->assign("listGroups"		, $listGroups);

$smarty->display("view_etablissements.tpl");

?>