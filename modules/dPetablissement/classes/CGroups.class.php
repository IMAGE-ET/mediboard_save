<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPetablissement
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Group class (Etablissement)
 */
class CGroups extends CMbObject {
  public $group_id;

  // DB Fields
  public $text;
  public $raison_sociale;
  public $adresse;
  public $cp;
  public $ville;
  public $tel;
  public $fax;
  public $mail;
  public $mail_apicrypt;
  public $web;
  public $directeur;
  public $domiciliation;
  public $siret;
  public $ape;
  public $tel_anesth;
  public $service_urgences_id;
  public $pharmacie_id;
  public $finess;
  public $chambre_particuliere;
  public $ean;
  public $rcc;

  // Form fields
  public $_cp_court;
  public $_is_ipp_supplier = false;
  public $_is_nda_supplier = false;

  /** @var CFunctions[] */
  public $_ref_functions;

  /** @var CBlocOperatoire[] */
  public $_ref_blocs;

  /** @var CPosteSSPI[] */
  public $_ref_postes;

  /** @var CDMICategory[] */
  public $_ref_dmi_categories;

  /** @var CService[] */
  public $_ref_services;

  /** @var CFunctions */
  public $_ref_pharmacie;

  /** @var CFunctions */
  public $_ref_service_urgences;

  /** @var self */
  static $_ref_current = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'groups_mediboard';
    $spec->key   = 'group_id';
    return $spec;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["aides_saisie"]              = "CAideSaisie group_id";
    $backProps["categories_prescription"]   = "CCategoryPrescription group_id";
    $backProps["category_DM"]               = "CCategoryDM group_id";
    $backProps["config_moment"]             = "CConfigMomentUnitaire group_id";
    $backProps["config_service"]            = "CConfigService group_id";
    $backProps["check_item_types"]          = "CDailyCheckItemType group_id";
    $backProps["dmi_categories"]            = "CDMICategory group_id";
    $backProps["documents_ged"]             = "CDocGed group_id";
    $backProps["etablissements_sherpa"]     = "CSpEtablissement group_id";
    $backProps["functions"]                 = "CFunctions group_id";
    $backProps["listes_choix"]              = "CListeChoix group_id";
    $backProps["modeles"]                   = "CCompteRendu group_id";
    $backProps["menus"]                     = "CMenu group_id";
    $backProps["messages"]                  = "CMessage group_id";
    $backProps["packs"]                     = "CPack group_id";
    $backProps["packs_categorie_prescription"] = "CPrescriptionCategoryGroup group_id";
    $backProps["plats"]                     = "CPlat group_id";
    $backProps["blocs"]                     = "CBlocOperatoire group_id";
    $backProps["sejours"]                   = "CSejour group_id";
    $backProps["services"]                  = "CService group_id";
    $backProps["types_repas"]               = "CTypeRepas group_id";
    $backProps["chapitres_qualite"]         = "CChapitreDoc group_id";
    $backProps["themes_qualite"]            = "CThemeDoc group_id";
    $backProps["plateaux_techniques"]       = "CPlateauTechnique group_id";
    $backProps["prestations"]               = "CPrestation group_id";
    $backProps["product_orders"]            = "CProductOrder group_id";
    $backProps["product_address_orders"]    = "CProductOrder address_id";
    $backProps["product_receptions"]        = "CProductReception group_id";
    $backProps["product_stock_locations"]   = "CProductStockLocation group_id";
    $backProps["product_stocks"]            = "CProductStockGroup group_id";
    $backProps["protocoles_prescription"]   = "CPrescription group_id";
    $backProps["packs_prescription"]        = "CPrescriptionProtocolePack group_id";
    $backProps["reception_bills"]           = "CProductReceptionBill group_id";
    $backProps['object_configs']            = "CGroupsConfig object_id";
    $backProps["stock_locations"]           = "CProductStockLocation object_id";
    $backProps["stock_first_config"]        = "CCegiStockFirstConfig object_id";
    $backProps["destinataires_hprim"]       = "CDestinataireHprim group_id";
    $backProps["destinataires_sigems"]      = "CDestinataireSigems group_id";
    $backProps["destinataires_hprim21"]     = "CDestinataireHprim21 group_id";
    $backProps["destinataires_ihe"]         = "CReceiverIHE group_id";
    $backProps["echanges_generique"]        = "CExchangeAny group_id";
    $backProps["echanges_hprim"]            = "CEchangeHprim group_id";
    $backProps["echanges_hprim21"]          = "CEchangeHprim21 group_id";
    $backProps["echanges_ihe"]              = "CExchangeIHE group_id";
    $backProps["echanges_phast"]            = "CExchangePhast group_id";
    $backProps["echanges_dicom"]            = "CExchangeDicom group_id";
    $backProps["extract_passages"]          = "CExtractPassages group_id";
    $backProps["destinataires_phast"]       = "CPhastDestinataire group_id";
    $backProps["senders_ftp"]               = "CSenderFTP group_id";
    $backProps["senders_soap"]              = "CSenderSOAP group_id";
    $backProps["senders_mllp"]              = "CSenderMLLP group_id";
    $backProps["senders_fs"]                = "CSenderFileSystem group_id";
    $backProps["view_sender_sources"]       = "CViewSenderSource group_id";
    $backProps["modeles_etiquette"]         = "CModeleEtiquette group_id";
    $backProps["unites_fonctionnelles"]     = "CUniteFonctionnelle group_id";
    $backProps["ex_classes"]                = "CExClass group_id";
    $backProps["prestations_journalieres"]  = "CPrestationJournaliere group_id";
    $backProps["prestations_ponctuelles"]   = "CPrestationPonctuelle group_id";
    $backProps["supervision_graphs"]        = "CSupervisionGraph owner_id";
    $backProps["ressources_materielles"]    = "CRessourceMaterielle group_id";
    $backProps["type_ressources"]           = "CTypeRessource group_id";
    $backProps["secteurs"]                  = "CSecteur group_id";
    $backProps["protocoles"]                = "CProtocole group_id";
    $backProps["charges"]                   = "CChargePriceIndicator group_id";
    $backProps["postes"]                    = "CPosteSSPI group_id";
    $backProps["group_domains"]             = "CGroupDomain group_id";
    $backProps["modes_entree_sejour"]       = "CModeEntreeSejour group_id";
    $backProps["modes_sortie_sejour"]       = "CModeSortieSejour group_id";
    $backProps["dicom_sender"]              = "CDicomSender group_id";
    $backProps["dicom_session"]             = "CDicomSession group_id";
    $backProps["pyxvital_receiver"]         = "CReceiverPyxVital group_id";
    $backProps["diet"]                      = "CDiet group_id";
    $backProps["diet_property"]             = "CDietProperty group_id";
    $backProps["dish"]                      = "CDish group_id";
    $backProps["meal_menu"]                 = "CMealMenu group_id";
    $backProps["meal_type"]                 = "CMealType group_id";
    $backProps["regle_sectorisation_group"] = "CRegleSectorisation group_id";
    $backProps["tarif_group"]               = "CTarif group_id";
    return $backProps;
  }

  function getProps() {
    $props = parent::getProps();

    $props["text"]                = "str notNull confidential seekable";
    $props["raison_sociale"]      = "str maxLength|50";
    $props["adresse"]             = "text confidential";
    $props["cp"]                  = "str minLength|4 maxLength|10";
    $props["ville"]               = "str maxLength|50 confidential";
    $props["tel"]                 = "phone";
    $props["fax"]                 = "phone";
    $props["tel_anesth"]          = "phone";
    $props["service_urgences_id"] = "ref class|CFunctions";
    $props["pharmacie_id"]        = "ref class|CFunctions";
    $props["directeur"]           = "str maxLength|50";
    $props["domiciliation"]       = "str maxLength|9";
    $props["siret"]               = "str length|14";
    $props["ape"]                 = "str maxLength|6 confidential";
    $props["mail"]                = "email";
    $props["mail_apicrypt"]       = "email";
    $props["web"]                 = "str";
    $props["finess"]              = "numchar length|9 confidential mask|9xS9S99999S9 control|luhn";
    $props["chambre_particuliere"]= "bool notNull default|0";
    $props["ean"]                 = "str";
    $props["rcc"]                 = "str";

    $props["_cp_court"]           = "numchar length|2";

    return $props;
  }

  function updateFormFields () {
    parent::updateFormFields();
    $this->_view = $this->text;
    $this->_shortview = CMbString::truncate($this->text);
    $this->_cp_court = substr($this->cp, 0, 2);
  }

  function store(){
    $is_new = !$this->_id;

    if ($msg = parent::store()) {
      return $msg;
    }

    if ($is_new && CModule::getActive("dPprescription")) {
      CConfigService::emptySHM();
      CConfigMomentUnitaire::emptySHM();
    }

    return null;
  }

  /**
   * Load functions with given permission
   *
   * @param int $permType Permission level
   *
   * @return CFunctions[]
   */
  function loadFunctions($permType = PERM_READ) {
    return $this->_ref_functions = CMediusers::loadFonctions($permType, $this->_id);
  }

  /**
   * Load blocs operatoires with given permission
   *
   * @param int  $permType    Permission level
   * @param bool $load_salles Load salles
   *
   * @return CBlocOperatoire[]
   */
  function loadBlocs($permType = PERM_READ, $load_salles = true) {
    $bloc = new CBlocOperatoire();
    $where = array(
      'group_id' => "= '$this->_id'"
    );

    /** @var CBlocOperatoire[] $blocs */
    $blocs = $bloc->loadListWithPerms($permType, $where, "nom");

    if ($load_salles) {
      foreach ($blocs as $_bloc) {
        $_bloc->loadRefsSalles();
      }
    }

    return $this->_ref_blocs = $blocs;
  }

  /**
   * Load postes SSPI
   *
   * @param int  $permType  Permission level
   * @param bool $load_bloc Load blocs
   *
   * @return CPosteSSPI[]
   */
  function loadPostes($permType = PERM_READ, $load_bloc = true) {
    $poste = new CPosteSSPI();
    $where = array(
      "group_id" => "= '$this->_id'"
    );

    /** @var CPosteSSPI[] $postes */
    $postes = $poste->loadListWithPerms($permType, $where, "nom");

    if ($load_bloc) {
      foreach ($postes as $_poste) {
        $_poste->loadRefBloc();
      }
    }

    return $this->_ref_postes = $postes;
  }

  function loadRefsBack() {
    $this->loadFunctions();
  }

  /**
   * Get group's services
   *
   * @return CService[]
   */
  function loadRefsServices(){
    return $this->_ref_services = $this->loadBackRefs("services", "nom");
  }

  /**
   * Get pharmacy function
   *
   * @return CFunctions
   */
  function loadRefPharmacie(){
    return $this->_ref_pharmacie = $this->loadFwdRef("pharmacie_id");
  }

  /**
   * Get emergency function
   *
   * @return CFunctions
   */
  function loadRefServiceUrgences(){
    return $this->_ref_service_urgences = $this->loadFwdRef("service_urgences_id");
  }

  /**
   * Load groups with given permission
   *
   * @param int $permType Permission level
   *
   * @return self[]
   */
  static function loadGroups($permType = PERM_READ) {
    $group = new self();
    $groups = $group->loadList(null, "text");
    self::filterByPerm($groups, $permType);
    return $groups;
  }

  function fillLimitedTemplate(&$template) {
    $this->notify("BeforeFillLimitedTemplate", $template);

    $template->addProperty("Etablissement - Nom"             , $this->text);
    $template->addProperty("Etablissement - Adresse"         , "$this->adresse \n $this->cp $this->ville");
    $template->addProperty("Etablissement - Ville"           , $this->ville);
    $template->addProperty("Etablissement - T�l�phone"       , $this->getFormattedValue("tel"));
    $template->addProperty("Etablissement - Fax"             , $this->getFormattedValue("fax"));
    $template->addProperty("Etablissement - E-mail"          , $this->getFormattedValue("mail"));
    $template->addProperty("Etablissement - E-mail Apicrypt" , $this->getFormattedValue("mail_apicrypt"));
    $template->addProperty("Etablissement - Domiciliation"   , $this->domiciliation);
    $template->addProperty("Etablissement - Siret"           , $this->siret);
    $template->addProperty("Etablissement - Finess"          , $this->finess);
    $template->addProperty("Etablissement - Ape"             , $this->ape);

    $barcode = array("barcode" => array(
      "title" => CAppUI::tr("{$this->_class}-finess")
    ));
    $template->addBarCode("Etablissement - Code Barre FINESS", $this->finess, $barcode);

    $this->notify("AfterFillLimitedTemplate", $template);
  }

  function fillTemplate(&$template) {
    $this->fillLimitedTemplate($template);
  }

  /**
   * Load the current group
   *
   * @return CGroups
   */
  static function loadCurrent() {
    if (!self::$_ref_current) {
      global $g;
      self::$_ref_current = new CGroups();
      self::$_ref_current->load($g);
    }

    return self::$_ref_current;
  }

  /**
   * Get DMI categories
   *
   * @return CDMICategory[]
   */
  function loadRefsDMICategories() {
    return $this->_ref_dmi_categories = $this->loadBackRefs("dmi_categories", "nom");
  }

  /**
   * Construit le tag de l'�tablissement en fonction des variables de configuration
   *
   * @return string|null
   */
  function getTagGroup() {
    // Pas de tag sur l'�tabliessement
    if (null == $tag_group = CAppUI::conf("dPetablissement tag_group")) {
      return null;
    }

    return str_replace('$g', $this->_id, $tag_group);
  }


  /**
   * get holidays for a postal code
   *
   * @param int    $pays the country (config mediboard)
   * @param string $date date (Y-m-d)
   *
   * @return array
   */
  function getCpHolidays($pays, $date) {
    $subdivisionHoliday = array();
    if (!$this->cp) {
      return $subdivisionHoliday;
    }

    $year = CMbDT::transform("+0 DAY", $date, "%Y");
    $paques = CMbDT::getEasterDate($date);

    switch ($pays) {
      case '2':
        $firstSundaySeptember = CMbDT::transform("next sunday", $year."-09-00", "%Y-%m-%d");
        $thirdSundaySeptember = CMbDT::transform("+2 WEEK", $firstSundaySeptember, "%Y-%m-%d");

        $canton = substr($this->cp, 0, 2);
        switch ($canton) {
          case '10':  // Vaud
            $subdivisionHoliday[] = "$year-01-02"; // Saint-Berchtold
            $subdivisionHoliday[] = CMbDT::transform("last friday", $paques, "%Y-%m-%d");  //vendredi saint
            $subdivisionHoliday[] = CMbDT::transform("+1 DAY", $paques, "%Y-%m-%d");  //lundi de paques
            $subdivisionHoliday[] = CMbDT::transform("+39 DAY", $paques, "%Y-%m-%d");  //Ascension (40 jours - dimanche de paques)
            $subdivisionHoliday[] = CMbDT::transform("+50 DAY", $paques, "%Y-%m-%d");  //lundi de pantecote
            $subdivisionHoliday[] = CMbDT::transform("+1 DAY", $thirdSundaySeptember, "%Y-%m-%d");  //Lundi du Je�ne f�d�ral
            break;

          case '12':  // Gen�ve
            //jeudi suivant le 1er dimanche de septembre
            $subdivisionHoliday[] = CMbDT::transform("next thursday", $firstSundaySeptember, "%Y-%m-%d");
            $subdivisionHoliday[] = CMbDT::transform("last friday", $paques, "%Y-%m-%d");  //vendredi saint
            $subdivisionHoliday[] = CMbDT::transform("+1 DAY", $paques, "%Y-%m-%d");  //lundi de paques
            $subdivisionHoliday[] = CMbDT::transform("+39 DAY", $paques, "%Y-%m-%d");  //Ascension
            $subdivisionHoliday[] = CMbDT::transform("+50 DAY", $paques, "%Y-%m-%d");  //lundi de pantecote
            $subdivisionHoliday[] = "$year-12-31"; //fete du travail
            break;
        }
        break;
    }
    return $subdivisionHoliday;
  }


  /**
   * R�cup�re les cong�s pour un pays
   *
   * @param string $date          the date to check
   * @param bool   $includeRegion are the territory holidays included ?
   *
   * @return array
   */
  function getHolidays($date = null, $includeRegion = true){
    $holidays = array();

    // No Group, error
    if (!$this->_id) {
      return false;
    }

    //no date => today
    if (!$date) {
      $date = CMbDT::date();
    }

    $year = CMbDT::transform("+0 DAY", $date, "%Y");
    $code_pays = CAppUI::conf("ref_pays");

    switch ($code_pays) {
      case '2': // Switzerland
        $holidays[] = "$year-01-01";                // Jour de l'an
        $holidays[] = "$year-08-01";                // fete nationnale suisse
        $holidays[] = "$year-12-25";                // No�l
        break;

      default:  // France
        $paques = CMbDT::getEasterDate($date);
        $holidays[] = "$year-01-01";                   // Jour de l'an
        $holidays[] = CMbDT::date("+1 DAY", $paques);  // Lundi de paques
        $holidays[] = "$year-05-01";                   // F�te du travail
        $holidays[] = "$year-05-08";                   // Victoire de 1945
        $holidays[] = CMbDT::date("+39 DAYS", $paques);// Jeudi de l'ascension
        $holidays[] = CMbDT::date("+50 DAYS", $paques);// Lundi de pentec�te
        $holidays[] = "$year-07-14";                   // F�te nationnale
        $holidays[] = "$year-08-15";                   // Assomption
        $holidays[] = "$year-11-01";                   // Toussaint
        $holidays[] = "$year-11-11";                   // Armistice 1918
        $holidays[] = "$year-12-25";                   // No�l
        break;
    }

    if ($includeRegion) {
      $holidaysSub = $this->getCpHolidays($code_pays, $date); //r�cup�ration des r�gions
      $holidays = array_merge($holidays, $holidaysSub);
    }

    return $holidays;
  }

  /**
   * Charge l'idex de l'�tablissement
   *
   * @return string|null
   */
  function loadIdex() {
    $tag_group = $this->getTagGroup();

    if (!$this->_id || !$tag_group) {
      return null;
    }

    // R�cup�ration du premier idex cr��
    $order = "id400 ASC";

    // Recuperation de la valeur de l'id400
    $idex = new CIdSante400();
    $idex->setObject($this);
    $idex->tag = $tag_group;
    $idex->loadMatchingObject($order);

    return $idex->id400;
  }

  /**
   * Is the group a domain supplier ?
   *
   * @param string $domain_type Domain type (CSejour, CPatient, etc)
   *
   * @return bool
   */
  function isNumberSupplier($domain_type) {
    if (!$this->_id) {
      return false;
    }

    $group_domain = new CGroupDomain();
    $group_domain->object_class = $domain_type;
    $group_domain->group_id     = $this->_id;
    $group_domain->master       = true;
    $group_domain->loadMatchingObject();

    if (!$group_domain->_id) {
      return false;
    }

    $domain = $group_domain->loadRefDomain();

    return $domain->loadRefIncrementer()->_id ? 1 : 0;
  }

  /**
   * Is the group an IPP supplier ?
   *
   * @return bool
   */
  function isIPPSupplier() {
    return $this->_is_ipp_supplier = $this->isNumberSupplier("CPatient");
  }

  /**
   * Is the group an NDA supplier ?
   *
   * @return bool
   */
  function isNDASupplier() {
    return $this->_is_nda_supplier = $this->isNumberSupplier("CSejour");
  }
}
