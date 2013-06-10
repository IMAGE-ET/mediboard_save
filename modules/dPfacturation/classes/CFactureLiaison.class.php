<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * Liaison entre les �l�ments facturable et leur facture
 */
class CFactureLiaison extends CMbMetaObject {

  // DB Table key
  public $facture_liaison_id;
  
  // DB Fields
  public $facture_id;
  public $facture_class;
  public $object_id;
  public $object_class;
  
  // Object References
  /** @var  CFacture $_ref_facture*/
  public $_ref_facture;
  /** @var  CFacturable $_ref_facturable*/
  public $_ref_facturable;
  
  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'facture_liaison';
    $spec->key   = 'facture_liaison_id';
    return $spec;
  }
  
  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    return $backProps;
  }
  
  /**
   * @see parent::getProps()
   */
  function getProps() {
    $specs = parent::getProps();
    $specs["facture_id"]    = "ref notNull class|CFacture meta|facture_class";
    $specs["facture_class"] = "enum notNull list|CFactureCabinet|CFactureEtablissement show|0 default|CFactureCabinet";
    $specs["object_id"]     = "ref notNull class|CFacturable meta|object_class";
    return $specs;
  }
     
  /**
   * Chargement de la facture
   *
   * @return CFacture
   */
  function loadRefFacture() {
    return $this->_ref_facture = $this->loadFwdRef("facture_id", true);
  }
     
  /**
   * Chargement de l'objet facturable
   * 
   * @return CFacturable
   */
  function loadRefFacturable() {
    return $this->_ref_facturable =  $this->loadTargetObject();
  }
  
  /**
   * @see parent::store()
   */
  function store() {
    // Standard store
    if ($msg = parent::store()) {
      return $msg;
    }
    
    $this->loadRefFacture();
    return null;
  }

  /**
   * Clonage de la liaison de la facture
   *
   * @param object $item   l'item
   * @param int    $new_id l'identifant de la facture
   *
   * @return void
   */
  function cloneFrom($item, $new_id){
    $this->object_id     = $item->object_id;
    $this->object_class  = $item->object_class;
    $this->facture_id    = $new_id;
    $this->facture_class = $item->facture_class;
  }
}