<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage Bloc
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

/**
 * Bloc opératoire
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
  public $cheklist_man;

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
    $props["cheklist_man"]= "bool default|0";
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
   * @param string $order   paramètre ORDER SQL
   * @param null   $limit   paramètre LIMIT SQL
   * @param null   $groupby paramètre GROUP BY SQL
   * @param array  $ljoin   Tableau de clauses LEFT JOIN SQL
   *
   * @return self[]
   */
  function loadGroupList($where = array(), $order = 'nom', $limit = null, $groupby = null, $ljoin = array()) {
    // Filtre sur l'établissement
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
   * Chargement de l'établissement correspondant
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
}
