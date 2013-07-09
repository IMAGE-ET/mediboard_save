<?php
/**
 * Look for a context to find a service
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

class CRegleSectorisation extends CMbObject {
  public $regle_id;

  public $service_id;
  public $function_id;
  public $praticien_id;
  public $duree_min;
  public $duree_max;
  public $date_min;
  public $date_max;
  public $type_admission;
  public $type_pec;
  public $group_id;

  //form field
  public $_ref_service;
  public $_ref_function;
  public $_ref_praticien;
  public $_ref_group;
  public $_inactive;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table  = "regle_sectorisation";
    $spec->key    = "regle_id";
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $sejour = new CSejour();
    $types_admission  = $sejour->_specs["type"]->_list;
    $types_pec        = $sejour->_specs["type_pec"]->_list;

    $props = parent::getProps();
    $props["service_id"]        = "ref class|CService seekable notNull";

    $props["function_id"]       = "ref class|CFunctions";
    $props["praticien_id"]      = "ref class|CMediusers";
    $props["duree_min"]         = "num";
    $props["duree_max"]         = "num moreEquals|duree_min";
    $props["date_min"]          = "dateTime";
    $props["date_max"]          = "dateTime moreEquals|date_min";
    $props["type_admission"]    = "enum list|".implode("|", $types_admission);
    $props["type_pec"]          = "enum list|".implode("|", $types_pec);
    $props["group_id"]          = "ref class|CGroups notNull";
    return $props;
  }

  /**
   * check if $this is an older rule
   *
   * @return bool
   */
  function checkOlder() {
    $now = CMbDT::dateTime();

    if ($this->date_min && $now < $this->date_min) {
      return $this->_inactive = true;
    }

    if ($this->date_max && $now > $this->date_max) {
      return $this->_inactive = true;
    }

    return $this->_inactive = false;

  }

  /**
   * Load the praticien by his _id
   *
   * @return CMbObject
   */
  function loadRefPraticien() {
    return $this->_ref_praticien = $this->loadFwdRef("praticien_id", true);
  }

  /**
   * load service by id
   *
   * @return CMbObject
   */
  function loadRefService() {
    return $this->_ref_service = $this->loadFwdRef("service_id", true);
  }

  /**
   * load function by id
   *
   * @return CMbObject
   */
  function loadRefFunction() {
    return $this->_ref_function = $this->loadFwdRef("function_id", true);
  }

  /**
   * load group
   *
   * @return CMbObject
   */
  function loadRefGroup() {
    return $this->_ref_group = $this->loadFwdRef("group_id", true);
  }
}