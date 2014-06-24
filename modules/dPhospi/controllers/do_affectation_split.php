<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPhospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

global $m;

$entree = $_POST["entree"];
$sortie = $_POST["sortie"];

$tolerance          = CAppUI::conf("dPhospi CAffectation create_affectation_tolerance", CGroups::loadCurrent());
$modify_affectation = CMbDT::addDateTime("00:$tolerance:00", $entree) > $_POST["_date_split"];

// Modifier la premi�re affectation, affectation du lit si la tol�rance de cr�ation d'afectation n'est pas atteint
$do = new CDoObjectAddEdit("CAffectation", "affectation_id");

if ($modify_affectation) {
  $_POST["lit_id"] = $_POST["_new_lit_id"];
}
else {
  $_POST["entree"] = $entree;
  $_POST["sortie"] = $_POST["_date_split"];
}

$do->redirect = null;
$do->redirectStore = null;
$do->doIt();

$first_affectation = $do->_obj;

// Cr�er la seconde si la tol�rance est d�pass�
if (!$modify_affectation) {
  $do = new CDoObjectAddEdit("CAffectation", "affectation_id");

  $_POST["ajax"]   = 1;
  $_POST["entree"] = $_POST["_date_split"];
  $_POST["sortie"] = $sortie;
  $_POST["lit_id"] = $_POST["_new_lit_id"];
  $_POST["affectation_id"] = null;

  $do->doSingle(false);
}

// G�rer le d�placement du ou des b�b�s si n�cessaire
if (CModule::getActive("maternite")) {
  /** @var CAffectation[] $affectations_enfant */
  $affectations_enfant = $first_affectation->loadBackRefs("affectations_enfant");
  
  foreach ($affectations_enfant as $_affectation) {
    $save_sortie = $_affectation->sortie;

    $modify_affectation_enfant = CMbDT::addDateTime("00:$tolerance:00", $_affectation->entree) > $_POST["_date_split"];

    if ($modify_affectation_enfant) {
      $_affectation->lit_id = $_POST["_new_lit_id"];
    }
    else {
      $_affectation->sortie = $_POST["_date_split"];
    }

    if ($msg = $_affectation->store()) {
      CAppUI::setMsg($msg, UI_MSG_ERROR);
    }

    if (!$modify_affectation_enfant) {
      $affectation = new CAffectation;
      $affectation->lit_id                = $_POST["_new_lit_id"];
      $affectation->sejour_id             = $_affectation->sejour_id;
      $affectation->parent_affectation_id = $do->_obj->_id;
      $affectation->entree                = $_POST["_date_split"];
      $affectation->sortie                = $save_sortie;

      if ($msg = $affectation->store()) {
        CAppUI::setMsg($msg, UI_MSG_ERROR);
      }
    }
  }
}

$do->doRedirect();
