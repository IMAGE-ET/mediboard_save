<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Qualite
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkEdit();
$group = CGroups::loadCurrent();

$doc_ged_id = CValue::getOrSession("doc_ged_id", 0);
$fileSel = new CFile;

$docGed = new CDocGed();
if (!$docGed->load($doc_ged_id) || $docGed->etat!=0) {
  // Ce document n'est pas valide ou n'est pas un mod�le
  $doc_ged_id = null;
  CValue::setSession("doc_ged_id");
  $docGed = new CDocGed();
}
else {
  $docGed->loadLastEntry();
  if (!$docGed->_lastentry->doc_ged_suivi_id) {
    // Ce document n'a pas de mod�le
    $doc_ged_id = null;
    CValue::setSession("doc_ged_id");
    $docGed = new CDocGed;  
  }
  else {
    $docGed->_lastentry->loadFile();
  }
}

if (!$docGed->_lastentry) {
  $docGed->loadLastEntry();
}

// Mod�les de proc�dure
$modele = new CDocGed();
$where = array();
$where["doc_ged.etat"]   = "= '0'";
$where["group_id"] = "= '$group->_id'";
$order = "titre ASC";
$ljoin = array();
$ljoin["doc_ged_suivi"] = "doc_ged.doc_ged_id = doc_ged_suivi.doc_ged_id";

/** @var CDocGed[] $modeles */
$modeles = $modele->loadList($where, $order, null, null, $ljoin);
foreach ($modeles as $_proc) {
  $_proc->loadLastEntry();
}

// Liste des Etablissements selon Permissions
$user = new CMediusers();
$etablissements = $user->loadEtablissements(PERM_READ);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("etablissements" , $etablissements);
$smarty->assign("modeles"        , $modeles);
$smarty->assign("docGed"         , $docGed);
$smarty->assign("fileSel"        , $fileSel);

$smarty->display("vw_modeles.tpl");

