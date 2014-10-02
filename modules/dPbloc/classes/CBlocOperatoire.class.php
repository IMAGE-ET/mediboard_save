<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Bloc
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Bloc op�ratoire
 * Class CBlocOperatoire
 */
class CBlocOperatoire extends CMbObject {
  public $bloc_operatoire_id;

  // DB references
  public $group_id;

  // DB Fields
  public $nom;
  public $type;
  public $days_locked;
  public $tel;
  public $fax;

  /** @var CSalle[] */
  public $_ref_salles;

  /** @var CGroups */
  public $_ref_group;

  /** @var  CAlert[] */
  public $_alertes_intervs;

  // Form field
  public $_date_min;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'bloc_operatoire';
    $spec->key   = 'bloc_operatoire_id';
    return $spec;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["salles"]                  = "CSalle bloc_id";
    $backProps["check_lists"]             = "CDailyCheckList object_id";
    $backProps["destination_brancardage"] = "CDestinationBrancardage object_id";
    $backProps["stock_locations"]         = "CProductStockLocation object_id";
    $backProps["postes"]                  = "CPosteSSPI bloc_id";
    $backProps["check_list_categories"]   = "CDailyCheckItemCategory target_id";
    $backProps["check_list_type_links"]   = "CDailyCheckListTypeLink object_id";
    $backProps["product_address_orders"]  = "CProductOrder address_id";
    $backProps["monitoring_concentrator"] = "CMonitoringConcentrator bloc_operatoire_id";
    return $backProps;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["group_id"]    = "ref notNull class|CGroups";
    $props["nom"]         = "str notNull seekable";
    $props["type"]        = "enum notNull list|chir|obst default|chir";
    $props["days_locked"] = "num min|0 default|0";
    $props["tel"]         = "phone";
    $props["fax"]         = "phone";
    $props["_date_min"]   = "date";
    return $props;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->nom;
  }

  /**
   * Load list overlay for current group
   *
   * @param array  $where   Tableau de clauses WHERE MYSQL
   * @param string $order   param�tre ORDER SQL
   * @param null   $limit   param�tre LIMIT SQL
   * @param null   $groupby param�tre GROUP BY SQL
   * @param array  $ljoin   Tableau de clauses LEFT JOIN SQL
   *
   * @return self[]
   */
  function loadGroupList($where = array(), $order = 'nom', $limit = null, $groupby = null, $ljoin = array()) {
    // Filtre sur l'�tablissement
    $g = CGroups::loadCurrent();
    $where["group_id"] = "= '$g->_id'";
    /** @var CBlocOperatoire[] $list */
    $list = $this->loadListWithPerms(PERM_READ, $where, $order, $limit, $groupby, $ljoin);
    foreach ($list as &$bloc) {
      $bloc->loadRefsSalles();
    }
    return $list;
  }

  /**
   * Chargement de l'�tablissement correspondant
   *
   * @return CGroups
   */
  function loadRefGroup(){
    $group = new CGroups();
    return $this->_ref_group = $group->getCached($this->group_id);
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd(){
    return $this->loadRefGroup();
  }

  /**
   * Chargement des salles du bloc
   *
   * @return CSalle[]
   */
  function loadRefsSalles() {
    return $this->_ref_salles = $this->loadBackRefs('salles', 'nom');
  }

  /**
   * Chargement des salles du bloc
   *
   * @return CSalle[]
   * @deprecated use loadRefsSalles instead
   */
  function loadRefsBack() {
    return $this->loadRefsSalles();
  }

  /**
   * Chargement des alertes sur le bloc
   *
   * @return CAlert[]
   */
  function loadRefsAlertesIntervs() {
    $this->loadRefsSalles();
    $inSalles = CSQLDataSource::prepareIn(array_keys($this->_ref_salles));
    $alerte = new CAlert();
    $ljoin = array();
    $ljoin["operations"] = "operations.operation_id = alert.object_id";
    $ljoin["plagesop"]   = "plagesop.plageop_id = operations.plageop_id";
    $where = array();
    $where["alert.object_class"] = "= 'COperation'";
    $where["alert.tag"] = "= 'mouvement_intervention'";
    $where["alert.handled"]   = "= '0'";
    $where[] = "operations.salle_id ".$inSalles.
      " OR plagesop.salle_id ".$inSalles.
      " OR (plagesop.salle_id IS NULL AND operations.salle_id IS NULL)";
    $order = "operations.date, operations.chir_id";
    return $this->_alertes_intervs = $alerte->loadList($where, $order, null, null, $ljoin);
  }

  /**
   * count the number of alerts for this bloc
   *
   * @param array $key_ids list of salle keys
   *
   * @return int
   */
  static function countAlertesIntervsForSalles($key_ids) {
    if (!count($key_ids)) {
      return 0;
    }
    $inSalles = CSQLDataSource::prepareIn($key_ids);
    $alerte = new CAlert();
    $ljoin = array();
    $ljoin["operations"] = "operations.operation_id = alert.object_id";
    $ljoin["plagesop"]   = "plagesop.plageop_id = operations.plageop_id";
    $where = array();
    $where["alert.object_class"] = "= 'COperation'";
    $where["alert.tag"] = "= 'mouvement_intervention'";
    $where["alert.handled"]   = "= '0'";
    $where[] = "operations.salle_id ".$inSalles.
      " OR plagesop.salle_id ".$inSalles.
      " OR (plagesop.salle_id IS NULL AND operations.salle_id IS NULL)";
    return $alerte->countList($where, null, $ljoin);
  }
}
