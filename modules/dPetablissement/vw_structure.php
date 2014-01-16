<?php
/**
 * $Id: $
 *
 * @package    Mediboard
 * @subpackage dPetablissement
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 18985 $
 */

CCanDo::checkRead();

// R�cup�ration du groupe selectionn�
$group_id = CValue::getOrSession("group_id");

// Fiche �tablissement
$etab = new CGroups();
$etab->load($group_id);

// Services d'hospitalisation
$service            = new CService();
$service->group_id  = $etab->_id;
$service->cancelled = 0;
$service->externe   = 0;

/** @var CService[] $services */
$services = $service->loadMatchingList("nom");

foreach ($services as $_service) {
  $_service->loadRefsChambres(false);
  foreach ($_service->_ref_chambres as $_chambre) {
    $_chambre->loadRefsLits(false);
  }
}

// Blocs op�ratoires
$bloc           = new CBlocOperatoire();
$bloc->group_id = $etab->_id;

/** @var CBlocOperatoire[] $blocs */
$blocs = $bloc->loadMatchingList("nom");

foreach($blocs as $_bloc) {
  $_bloc->loadRefsSalles();
}

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("etab"    , $etab);
$smarty->assign("services", $services);
$smarty->assign("blocs"   , $blocs);

$smarty->display("vw_structure.tpl");