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

CCanDo::checkEdit();

// RHS concern�s
$rhs = new CRHS();
$rhs->load(CValue::get("rhs_id"));

$rhs->loadRefDependances();
$rhs->loadRefSejour();

if (!$rhs->_ref_dependances->_id) {
  $rhs->_ref_dependances->rhs_id = $rhs->_id;
  $rhs->_ref_dependances->store();
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("rhs", $rhs);

$smarty->display("inc_edit_dependances_rhs.tpl");
