<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage mediusers
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * The CFunction Class
 */
class CFunctions extends CMbObject {
  // DB Table key
  public $function_id;

  // DB References
  public $group_id;

  // DB Fields
  public $type;
  public $text;
  public $soustitre;
  public $color;
  public $adresse;
  public $cp;
  public $ville;
  public $tel;
  public $fax;
  public $actif;
  public $compta_partagee;
  public $admission_auto;
  public $consults_partagees;
  public $quotas;
  public $facturable;

  /** @var CGroups */
  public $_ref_group;

  /** @var CMediusers[] */
  public $_ref_users;

  // Form fields
  public $_ref_protocoles = array();
  public $_count_protocoles;

  // Filter fields
  public $_skipped;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'functions_mediboard';
    $spec->key   = 'function_id';
    return $spec;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["users"]                          = "CMediusers function_id";
    $backProps["secondary_functions"]            = "CSecondaryFunction function_id";
    $backProps["aides"]                          = "CAideSaisie function_id";
    $backProps["catalogues_labo"]                = "CCatalogueLabo function_id";
    $backProps["compte_rendu"]                   = "CCompteRendu function_id";
    $backProps["packs"]                          = "CPack function_id";
    $backProps["consultation_cats"]              = "CConsultationCategorie function_id";
    $backProps["employes"]                       = "CEmployeCab function_id";
    $backProps["executants_prescription"]        = "CFunctionCategoryPrescription function_id";
    $backProps["fiches_compta"]                  = "CGestionCab function_id";
    $backProps["services_urgence_pour"]          = "CGroups service_urgences_id";
    $backProps["pharmacie_pour"]                 = "CGroups pharmacie_id";
    $backProps["listes_choix"]                    = "CListeChoix function_id";
    $backProps["paiements"]                      = "CModePaiement function_id";
    $backProps["pack_examens"]                   = "CPackExamensLabo function_id";
    $backProps["plages_op"]                      = "CPlageOp spec_id";
    $backProps["plages_op_repl"]                 = "CPlageOp spec_repl_id";
    $backProps["prescriptions"]                  = "CPrescription function_id";
    $backProps["packs_prescription_protocole"]   = "CPrescriptionProtocolePack function_id";
    $backProps["rubriques"]                      = "CRubrique function_id";
    $backProps["tarifs"]                         = "CTarif function_id";
    $backProps["sigems_skipped"]                 = "CSigemsSkippedFunction function_id";
    $backProps["printers"]                       = "CPrinter function_id";
    $backProps["protocoles"]                     = "CProtocole function_id";
    $backProps["ufs"]                            = "CAffectationUniteFonctionnelle object_id";
    $backProps["destination_brancardage"]        = "CDestinationBrancardage object_id";
    $backProps["affectations"]                   = "CAffectation function_id";
    $backProps["caisses_maladies"]               = "CCaisseMaladie function_id";
    $backProps["regle_sectorisation_function"]   = "CRegleSectorisation function_id";
    $backProps["product_address_orders"]         = "CProductOrder address_id";
    return $backProps;
  }

  function getProps() {
    $props = parent::getProps();

    $props["group_id"]           = "ref notNull class|CGroups";
    $props["type"]               = "enum notNull list|administratif|cabinet";
    $props["text"]               = "str notNull confidential seekable";
    $props["color"]              = "str notNull length|6 default|ffffff";
    $props["adresse"]            = "text";
    $props["cp"]                 = "numchar length|5";
    $props["ville"]              = "str maxLength|50";
    $props["tel"]                = "phone";
    $props["fax"]                = "phone";
    $props["soustitre"]          = "text";
    $props["compta_partagee"]    = "bool default|0 notNull";
    $props["consults_partagees"] = "bool default|1 notNull";
    $props["admission_auto"]     = "bool";
    $props["actif"]              = "bool default|1";
    $props["quotas"]             = "num pos";
    $props["facturable"]         = "bool default|1";

    return $props;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->text;
    $this->_shortview = CMbString::truncate($this->text);
  }

  function loadView() {
    parent::loadView();
    $this->loadRefsFwd();
  }

  // Forward references
  function loadRefsFwd() {
    $this->loadRefGroup();
  }

  /**
   * @return CGroups
   */
  function loadRefGroup() {
    return $this->_ref_group = $this->loadFwdRef("group_id", true);
  }

  // Backward references
  function loadRefsBack() {
    $this->loadRefsUsers();
  }

  /**
   * @param string $type
   *
   * @return CMediusers[]
   */
  function loadRefsUsers($type = null) {
    $user = new CMediusers();

    if (!$type) {
      $where = array(
        "function_id" => "= '$this->function_id'",
        "actif"       => "= '1'"
      );
      $ljoin = array(
        "users" => "`users`.`user_id` = `users_mediboard`.`user_id`"
      );
      $order = "`users`.`user_last_name`, `users`.`user_first_name`";
      return $this->_ref_users = $user->loadList($where, $order, null, null, $ljoin);
    }

    return $this->_ref_users = $user->loadListFromType($type, PERM_READ, $this->function_id);
  }

  /**
   * @param string $type
   *
   * @return CProtocole[]
   */
  function loadProtocoles($type = null) {
    $where = array(
      "function_id" => "= '$this->_id'"
    );

    if ($type) {
      $where["type"] = "= '$type'";
    }

    $protocole = new CProtocole();
    return $this->_ref_protocoles = $protocole->loadList($where, "libelle_sejour, libelle, codes_ccam");
  }

  /**
   * @param string $type
   *
   * @return int
   */
  function countProtocoles($type = null) {
    $where = array(
      "function_id" => "= '$this->_id'"
    );

    if ($type) {
      $where["type"] = "= '$type'";
    }

    $protocole = new CProtocole();
    return $this->_count_protocoles = $protocole->countList($where);
  }

  // @todo : ameliorer le choix des sp�cialit�s
  // (loadfunction($groupe, $permtype) par exemple)
  function loadSpecialites($perm_type = null, $include_empty = 0) {
    $group_id = CGroups::loadCurrent()->_id;
    $where = array(
      "functions_mediboard.type" => "= 'cabinet'",
      "functions_mediboard.group_id" => "= '$group_id'"
    );
    $ljoin = array();
    if (!$include_empty) {
      // Fonctions secondaires actives
      $sec_function = new CSecondaryFunction();
      $where_secondary = array();
      $ljoin_secondary = array();
      $ljoin_secondary["functions_mediboard"] = "functions_mediboard.type = 'cabinet'
                                                 AND functions_mediboard.group_id = '$group_id'
                                                 AND functions_mediboard.function_id = secondary_functions.secondary_function_id";
      $ljoin_secondary["users_mediboard"] = "users_mediboard.actif = '1'
                                             AND users_mediboard.function_id = secondary_functions.secondary_function_id";
      $group = "secondary_function.function_id";
      $sec_functions = $sec_function->loadListWithPerms($perm_type, $where_secondary, null, null, $group, $ljoin);
      $in_functions = CSQLDataSource::prepareIn(CMbArray::pluck($sec_functions, "function_id"));

      $ljoin["users_mediboard"] = "users_mediboard.actif = '1' AND users_mediboard.function_id = functions_mediboard.function_id";
      $where[] = "users_mediboard.user_id IS NOT NULL OR functions_mediboard.function_id $in_functions";
    }

    return $this->loadListWithPerms($perm_type, $where, "text", null, null, $ljoin);
  }

  function fillTemplate(&$template) {
    $this->loadRefsFwd();
    $this->_ref_group->fillTemplate($template);
    $template->addProperty("Cabinet - nom"         , $this->text      );
    $template->addProperty("Cabinet - sous-titre"  , $this->soustitre );
    $template->addProperty("Cabinet - adresse"     , $this->adresse   );
    $template->addProperty("Cabinet - cp ville"    , "$this->cp $this->ville");
    $template->addProperty("Cabinet - t�l�phone"   , $this->getFormattedValue("tel"));
    $template->addProperty("Cabinet - fax"         , $this->getFormattedValue("fax"));
  }
}
