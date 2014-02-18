<?php

/**
 * dPbloc
 *
 * @category Bloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id$
 * @link     http://www.mediboard.org
 */

/**
 * Salle de bloc op�ratoire
 * Class CSalle
 */
class CSalle extends CMbObject {
  public $salle_id;
  
  // DB references
  public $bloc_id;
  
  // DB Fields
  public $nom;
  public $stats;
  public $dh;
  public $cheklist_man;

  /** @var CBlocOperatoire */
  public $_ref_bloc;

  /** @var CPlageOp[] */
  public $_ref_plages;

  /** @var COperation[] */
  public $_ref_urgences;

  /** @var COperation[] */
  public $_ref_deplacees;

  /** @var  CAlert[] */
  public $_alertes_intervs;
  
  // Form fields
  public $_blocage = array();

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'sallesbloc';
    $spec->key   = 'salle_id';
    $spec->measureable = true;
    return $spec;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["operations"]   = "COperation salle_id";
    $backProps["plages_op"]    = "CPlageOp salle_id";
    $backProps["check_lists"]  = "CDailyCheckList object_id";
    $backProps["blocages"]     = "CBlocage salle_id";
    $backProps["commentaires"] = "CCommentairePlanning salle_id";
    $backProps["check_list_categories"] = "CDailyCheckItemCategory target_id";
    $backProps["check_list_type_links"] = "CDailyCheckListTypeLink object_id";
    return $backProps;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["bloc_id"] = "ref notNull class|CBlocOperatoire";
    $props["nom"]     = "str notNull seekable";
    $props["stats"]   = "bool notNull";
    $props["dh"]      = "bool notNull default|0";
    $props["cheklist_man"]= "bool default|0";
    return $props;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();

    $bloc = $this->loadRefBloc();

    $this->_view      = $bloc->nom.' - '.$this->nom;
    $this->_shortview = $this->nom;
  }

  /**
   * Load list overlay for current group
   *
   * @see parent::loadGroupList()
   *
   */
  function loadGroupList($where = array(), $order = 'bloc_id, nom', $limit = null, $groupby = null, $ljoin = array()) {
    $list_blocs = CGroups::loadCurrent()->loadBlocs(PERM_READ, false);
    
    // Filtre sur l'�tablissement
    $where[] = "bloc_id ".CSQLDataSource::prepareIn(array_keys($list_blocs));
    
    return $this->loadList($where, $order, $limit, $groupby, $ljoin);
  }

  /**
   * @see parent::getPerm()
   */
  function getPerm($permType) {
    $this->loadRefBloc();
    return $this->_ref_bloc->getPerm($permType) && parent::getPerm($permType);
  }

  /**
   * Chargement du bloc op�ratoire
   *
   * @return CBlocOperatoire
   */
  function loadRefBloc() {
    return $this->_ref_bloc = $this->loadFwdRef("bloc_id", true);
  }

  /**
   * @see parent::loadRefsFwd()
   * @deprecated
   */
  function loadRefsFwd(){
    $this->loadRefBloc();
  }
  
  /**
   * Charge la liste de plages et op�rations pour un jour donn�
   * Analogue � CMediusers::loadRefsForDay
   *
   * @param string $date Date to look for
   *
   * @return void
   */
  function loadRefsForDay($date) {
    // Liste des utilisateurs
    $user      = new CMediusers();
    $listPrats = $user->loadPraticiens(PERM_READ);
    // Liste des fonctions
    $function      = new CFunctions();
    $listFunctions = $function->loadListWithPerms(PERM_READ);
    // Plages d'op�rations
    $plage = new CPlageOp();
    $conf_chambre_operation = $plage->conf("chambre_operation");
    $where = array();
    $where["plagesop.date"]     = "= '$date'";
    $where["plagesop.salle_id"] = "= '$this->_id'";
    $where[]                    = "`plagesop`.`chir_id` ".CSQLDataSource::prepareIn(array_keys($listPrats)).
      " OR `plagesop`.`spec_id` ".CSQLDataSource::prepareIn(array_keys($listFunctions));
    $order = "debut";
    $this->_ref_plages = $plage->loadList($where, $order);

    // Chargement d'optimisation

    CMbObject::massLoadFwdRef($this->_ref_plages, "chir_id");
    CMbObject::massLoadFwdRef($this->_ref_plages, "anesth_id");
    CMbObject::massLoadFwdRef($this->_ref_plages, "spec_id");
    CMbObject::massLoadFwdRef($this->_ref_plages, "salle_id");

    CMbObject::massCountBackRefs($this->_ref_plages, "notes");
    CMbObject::massCountBackRefs($this->_ref_plages, "affectations_personnel");

    foreach ($this->_ref_plages as $_plage) {
      /** @var CPlageOp $_plage */
      $_plage->loadRefChir();
      $_plage->loadRefAnesth();
      $_plage->loadRefSpec();
      $_plage->loadRefSalle();
      $_plage->makeView();
      $_plage->loadRefsOperations();
      $_plage->loadRefsNotes();
      $_plage->loadAffectationsPersonnel();
      $_plage->_unordered_operations = array();

      // Chargement d'optimisation

      CMbObject::massLoadFwdRef($_plage->_ref_operations, "chir_id");
      $sejours = CMbObject::massLoadFwdRef($_plage->_ref_operations, "sejour_id");
      CMbObject::massLoadFwdRef($sejours, "patient_id");

      foreach ($_plage->_ref_operations as $operation) {
        $operation->loadRefAnesth();
        $operation->loadRefChir();
        $operation->loadRefPatient();
        $operation->loadExtCodesCCAM();
        $operation->loadRefPlageOp();

        if ($conf_chambre_operation) {
          $operation->loadRefAffectation();
        }
        
        // Extraire les interventions non plac�es
        if ($operation->rank == 0) {
          $_plage->_unordered_operations[$operation->_id] = $operation;
          unset($_plage->_ref_operations[$operation->_id]);
        }
      }
    }
    
    // Interventions d�plac�s
    $deplacee = new COperation();
    $ljoin = array();
    $ljoin["plagesop"] = "operations.plageop_id = plagesop.plageop_id";
    $where = array();
    $where["operations.plageop_id"] = "IS NOT NULL";
    $where["plagesop.salle_id"]     = "!= operations.salle_id";
    $where["plagesop.date"]         = "= '$date'";
    $where["operations.salle_id"]   = "= '$this->_id'";
    $where[]                        = "`plagesop`.`chir_id` ".CSQLDataSource::prepareIn(array_keys($listPrats)).
      " OR `plagesop`.`spec_id` ".CSQLDataSource::prepareIn(array_keys($listFunctions));
    $order = "operations.time_operation";
    $this->_ref_deplacees = $deplacee->loadList($where, $order, null, null, $ljoin);

    // Chargement d'optimisation
    CMbObject::massLoadFwdRef($this->_ref_deplacees, "chir_id");
    $sejours_deplacees = CMbObject::massLoadFwdRef($this->_ref_deplacees, "sejour_id");
    CMbObject::massLoadFwdRef($sejours_deplacees, "patient_id");

    foreach ($this->_ref_deplacees as $_deplacee) {
      /** @var COperation $_deplacee */
      $_deplacee->loadRefChir();
      $_deplacee->loadRefPatient();
      $_deplacee->loadExtCodesCCAM();
      $_deplacee->loadRefPlageOp();
    }

    // Hors plage
    $urgence = new COperation();
    $ljoin = array();
    $ljoin["plagesop"] = "operations.plageop_id = plagesop.plageop_id";
    $where = array();
    $where["operations.date"]     = "= '$date'";
    $where["operations.salle_id"] = "= '$this->_id'";
    $where["operations.chir_id"]  = CSQLDataSource::prepareIn(array_keys($listPrats));
    $order = "time_operation, chir_id";
    $this->_ref_urgences = $urgence->loadList($where, $order);

    // Chargement d'optimisation
    CMbObject::massLoadFwdRef($this->_ref_urgences, "chir_id");
    $sejours_urgences = CMbObject::massLoadFwdRef($this->_ref_urgences, "sejour_id");
    CMbObject::massLoadFwdRef($sejours_urgences, "patient_id");

    foreach ($this->_ref_urgences as $_urgence) {
      /** @var COperation $_urgence */
      $_urgence->loadRefChir();
      $_urgence->loadRefPatient();
      $_urgence->loadExtCodesCCAM();
      $_urgence->loadRefPlageOp();

      if ($conf_chambre_operation) {
        $_urgence->loadRefAffectation();
      }
    }
  }

  /**
   * R�cup�ration des alertes sur les interventions de la salle
   *
   * @return CAlert[]
   */
  function loadRefsAlertesIntervs() {
    $alerte = new CAlert();
    $ljoin = array();
    $ljoin["operations"] = "operations.operation_id = alert.object_id";
    $ljoin["plagesop"]   = "plagesop.plageop_id = operations.plageop_id";
    $where = array();
    $where["alert.object_class"] = "= 'COperation'";
    $where["alert.tag"] = "= 'mouvement_intervention'";
    $where["alert.handled"]   = "= '0'";
    $where[] = "operations.salle_id = '$this->salle_id'
      OR plagesop.salle_id = '$this->salle_id'
      OR (plagesop.salle_id IS NULL AND operations.salle_id IS NULL)";
    $order = "operations.date, operations.chir_id";
    return $this->_alertes_intervs = $alerte->loadList($where, $order, null, null, $ljoin);
  }

  /**
   * R�cup�ration des blocages de la salle
   *
   * @param string $date Date de v�rification des blocages
   *
   * @return CBlocage[]
   */
  function loadRefsBlocages($date = "now") {
    $blocage = new CBlocage();
    
    if ($date == "now") {
      $date = CMbDT::date();
    }
    
    $where = array();
    $where["salle_id"] = "= '$this->_id'";
    $where[] = "'$date' BETWEEN deb AND fin";
    
    return $blocage->loadList($where);
  }

  /**
   * R�cup�ration des salles activers pour les stats
   *
   * @param int $salle_id Limitation du retour � une seule salle
   * @param int $bloc_id  Limitation du retour � un seul bloc
   *
   * @return self[]
   */
  static function getSallesStats($salle_id = null, $bloc_id = null) {
    $group_id = CGroups::loadCurrent()->_id;

    $where = array();
    $where['stats'] = " = '1'";

    $ljoin = array();

    if ($salle_id) {
      $where['salle_id'] = " = '$salle_id'";
    }
    elseif ($bloc_id) {
      $where['bloc_id'] = "= '$bloc_id'";
    }
    else {
      $where['bloc_operatoire.group_id'] = "= '$group_id'";
      $ljoin['bloc_operatoire'] = 'bloc_operatoire.bloc_operatoire_id = sallesbloc.bloc_id';
    }

    $salle = new self;
    return $salle->loadList($where, null, null, null, $ljoin);
  }
}
