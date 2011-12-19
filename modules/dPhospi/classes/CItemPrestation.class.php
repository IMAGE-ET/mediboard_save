<?php /* $Id: CItemPrestation.class.php $ */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CItemPrestation extends CMbMetaObject{
  // DB Table key
  var $item_prestation_id = null;
  
  // DB Fields
  var $nom                = null;
  var $rank               = null;
  
  // Ref field
  var $_ref_object        = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "item_prestation";
    $spec->key   = "item_prestation_id";
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["nom"]          = "str notNull";
    /*$specs["object_id"]    = "ref notNull class|CMbObject meta|object_class";*/
    $specs["object_class"] = "enum list|CPrestationPonctuelle|CPrestationJournaliere";
    $specs["rank"]         = "num pos default|1";
    return $specs;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["items_liaisons"]        = "CItemLiaison item_prestation_id";
    $backProps["items_liaisons_realises"] = "CItemLiaison item_prestation_realise_id";
    
    return $backProps;
  }
  
  function updateFormFields () {
    parent::updateFormFields();
    $this->_view = $this->nom;
  }
  
  function loadRefObject(){
    $this->_ref_object = new $this->object_class;
    $this->_ref_object = $this->_ref_object->getCached($this->object_id);
  }
}