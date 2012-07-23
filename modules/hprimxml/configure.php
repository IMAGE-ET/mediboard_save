<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkAdmin();

$status = 0;
$evenements = array(
  'evt_serveuractes'        => array("1.01", "1.04", "1.05", "1.06", "1.07"),
  'evt_pmsi'                => array("1.01", "1.04", "1.05", "1.06", "1.07"),
  'evt_serveuretatspatient' => array("1.04", "1.05", "1.06", "1.07"),
  'evt_frais_divers'        => array("1.05", "1.06", "1.07"),
  'evt_serveurintervention' => array("1.072"),
  'evt_patients'            => array("1.05", "1.052", "1.053", "1.06", "1.07"),
  'evt_mvtStock'            => array("1.01", "1.02"),
);

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("status"    , $status);
$smarty->assign("evenements", $evenements);

$smarty->display("configure.tpl");
?>