<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage dPplanningOp
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

/**
 * Mode de sortie
 */
class CModeSortieSejour extends CMbObject {
  // DB Table key
  var $mode_sortie_sejour_id;

  // DB Table key
  var $code;
  var $mode;
  var $group_id;
  var $libelle;
  var $actif;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'mode_sortie_sejour';
    $spec->key   = 'mode_sortie_sejour_id';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["code"]     = "str notNull";

    $sejour = new CSejour();
    $props["mode"]     = $sejour->_props["mode_sortie"]." notNull";

    $props["group_id"] = "ref notNull class|CGroups";
    $props["libelle"]  = "str";
    $props["actif"]    = "bool default|0";

    return $props;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["sejours"] = "CSejour mode_sortie_id";
    return $backProps;
  }

  function updateFormFields() {
    parent::updateFormFields();

    $this->_view      = $this->libelle ? $this->libelle : $this->code;
    $this->_shortview = $this->code;
  }
}
