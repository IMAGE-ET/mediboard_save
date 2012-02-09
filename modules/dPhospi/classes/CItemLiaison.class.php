<?php /* $Id: CItemPrestation.class.php $ */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CItemLiaison extends CMbObject{
  // DB Table key
  var $item_liaison_id  = null;
  
  // DB Fields
  var $sejour_id         =  null;
  var $item_prestation_id = null;
  var $item_prestation_realise_id = null;
  var $date              = null;
  var $quantite          = null;
  
  // Ref Fields
  var $_ref_affectation  = null;
  var $_ref_item         = null;
  var $_ref_item_realise = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "item_liaison";
    $spec->key   = "item_liaison_id";
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["sejour_id"]          = "ref notNull class|CSejour cascade";
    $specs["item_prestation_id"] = "ref class|CItemPrestation cascade";
    $specs["item_prestation_realise_id"] = "ref class|CItemPrestation cascade";
    $specs["date"]               = "date";
    $specs["quantite"]           = "num default|0";
    
    return $specs;
  }
  
  function loadRefItem() {
    return $this->_ref_item = $this->loadFwdRef("item_prestation_id", true);
  }
  
  function loadRefItemRealise() {
    return $this->_ref_item_realise = $this->loadFwdRef("item_prestation_realise_id", true);
  }
  
  function loadRefSejour() {
    return $this->_ref_sejour = $this->loadFwdRef("sejour_id", true);
  }
}
?>