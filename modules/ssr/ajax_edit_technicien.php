<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

// Plateau du contexte
$plateau = new CPlateauTechnique;
$plateau->load(CValue::get("plateau_id"));

// D�tails des techniciens
$date = CMbDT::date();
foreach ($plateau->loadRefsTechniciens(false) as $_technicien) {
  $_technicien->countSejoursDate($date);
};

// Technicien � editer
$technicien = new CTechnicien;
$technicien->load(CValue::get("technicien_id"));
$technicien->plateau_id = $plateau->_id;
$technicien->loadRefsNotes();
$technicien->loadRefPlateau(); 
$technicien->loadRefKine(); 
$technicien->countSejoursDate($date);

// Alter egos pour les transferts de s�jours
$where["kine_id"] = "= '$technicien->kine_id'";
/** @var CTechnicien[] $alteregos */
$alteregos = $technicien->loadList($where);
unset($alteregos[$technicien->_id]);
foreach ($alteregos as $_alterego) {
  $_alterego->loadRefPlateau(); 
  $_alterego->loadRefKine(); 
}

// Kin�s
$user = new CMediusers;
$kines = $user->loadKines();

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("technicien", $technicien);
$smarty->assign("alteregos", $alteregos);
$smarty->assign("plateau", $plateau);
$smarty->assign("kines", $kines);

$smarty->display("inc_edit_technicien.tpl");
