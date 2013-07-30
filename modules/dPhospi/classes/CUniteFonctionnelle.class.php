<?php /* $Id: CTransmissionMedicale.class.php 12900 2011-08-22 14:07:55Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: 12900 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @abstract Liste les unit�s fonctionnelles des �tablissements 
 */

class CUniteFonctionnelle extends CMbObject {
  // DB Table key
  var $uf_id = null;	
  
  // DB Fields
  var $group_id    = null;
  var $code        = null;
  var $libelle     = null;
  var $description = null;
  var $type        = null;
  
  // References
  var $_ref_group           = null;
  var $_ref_affectations_uf = null;
  
  // Distant references
  var $_ref_praticiens = null;
  var $_ref_lits       = null;
  var $_ref_chambre    = null;
  var $_ref_service    = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'uf';
    $spec->key   = 'uf_id';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["group_id"]    = "ref class|CGroups notNull";
    $props["code"]        = "str notNull seekable";
    $props["libelle"]     = "str notNull seekable";
    $props["description"] = "text";
    $props["type"]        = "enum list|hebergement|soins|medicale default|hebergement";
    
    return $props;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["affectations_uf"         ] = "CAffectationUniteFonctionnelle uf_id";

    $backProps["affectations_hebergement"] = "CAffectation uf_hebergement_id";
    $backProps["affectations_medical"    ] = "CAffectation uf_medicale_id";
    $backProps["affectations_soin"       ] = "CAffectation uf_soins_id";
    
    $backProps["sejours_hebergement"     ] = "CSejour uf_hebergement_id";
    $backProps["sejours_medical"         ] = "CSejour uf_medicale_id";
    $backProps["sejours_soin"            ] = "CSejour uf_soins_id";
    
    $backProps["protocoles_hebergement"  ] = "CProtocole uf_hebergement_id";
    $backProps["protocoles_medical"      ] = "CProtocole uf_medicale_id";
    $backProps["protocoles_soin"         ] = "CProtocole uf_soins_id";
    
    return $backProps;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->libelle;
  }
  
  static function getUF($code_uf, $type = null, $group_id = null) {
    $uf       = new self;
    $uf->code = $code_uf;
    $uf->type = $type;
    $uf->group_id = $group_id ? $group_id : CGroups::loadCurrent()->_id;
    $uf->loadMatchingObject();

    return $uf;
  }
  
  static function getUFs() {
    $uf = new self;
    $group_id = CGroups::loadCurrent()->_id;
    
    return array("hebergement" =>
                   $uf->loadList(array("type" => "= 'hebergement'", "group_id" => "= '$group_id'"), "libelle"),
                 "medicale"    =>
                   $uf->loadList(array("type" => "= 'medicale'", "group_id" => "= '$group_id'"), "libelle"),
                 "soins"       =>
                   $uf->loadList(array("type" => "= 'soins'", "group_id" => "= '$group_id'"), "libelle"));
  }
}

?>