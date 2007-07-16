<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage sherpa
* @version $Revision: $
* @author Sherpa
*/

global $can;
$can->needsRead();

// Chargement de l'�tablissement courant
$etablissement = new CSpEtablissement;
$etablissement->load(mbGetAbsValueFromGetOrSession("sp_etab_id"));

// Chargement de tous les �tablissements
$etablissements = $etablissement->loadList();
foreach ($etablissements as &$_etablissement) {
  $_etablissement->loadRefs();
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("etablissement", $etablissement);
$smarty->assign("etablissements", $etablissements);

$smarty->display("view_etablissements.tpl");

?>