<?php
/**
 * $Id: CModeleToPack.class.php $
 * 
 * @package    Mediboard
 * @subpackage dPcompteRendu
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: $
 */

/**
 * Lien vers un mod�le, composant d'un pack
 */
class CModeleToPack extends CMbObject {
  // DB Table key
  public $modele_to_pack_id;
  
  // DB References
  public $modele_id;
  public $pack_id;
  
  /** @var CCompteRendu */
  public $_ref_modele;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'modele_to_pack';
    $spec->key   = 'modele_to_pack_id';
    $spec->uniques['document'] = array('modele_id', 'pack_id');
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $specs = parent::getProps();
    $specs["modele_id"] = "ref class|CCompteRendu";
    $specs["pack_id"]   = "ref class|CPack cascade";
    return $specs;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields(){
    parent::updateFormFields();
    $this->_view = $this->loadRefModele()->nom;
  }
  
  /**
   * Chargement du mod�le r�f�renc�
   * 
   * @return CCompteRendu
   */
  function loadRefModele(){
    return $this->_ref_modele = $this->loadFwdRef("modele_id", true);
  }
  
  /**
   * Charge tous les liens vers les mod�les que composent un pack
   * 
   * @param object $pack_id identifiant du pack
   * 
   * @return array
   */
  function loadAllModelesFor($pack_id) {
    $where = array();
    $where["pack_id"] = " = '$pack_id'";

    return $this->loadList($where);
  }
}
