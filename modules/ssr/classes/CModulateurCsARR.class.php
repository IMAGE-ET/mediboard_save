<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

/**
 * Modulateur d'activit�s CsARR
 */
class CModulateurCsARR extends CCsARRObject {

  // DB Fields
  public $code       = null;
  public $modulateur = null;

  // Derived Fields
  public $_libelle;
    
  public $_ref_code = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'modulateur';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();

    // DB Fields
    $props["code"       ] = "str notNull length|7";
    $props["modulateur" ] = "str notNull length|2";

    // Plain Fields
    $props["_libelle"] = "str";

    return $props;
  }

  /**
   * Override
   *
   * @see parent::updateFormFields()
   * @return void
   */
  function updateFormFields() {
    static $libelles = array(
      "ZV" => "R�alisation de l'acte au lit du patient",
      "ME" => "R�alisation de l'acte en salle de soins",
      "QM" => "R�alisation de l'acte en piscine ou en baln�otherapie",
      "TF" => "R�alisation de l'acte en �tablissement, en ext�rieur sans �quipement",
      "RW" => "R�alisation de l'acte en �tablissement, en ext�rieur avec �quipement",
      "HW" => "R�alisation de l'acte hors �tablissement en milieu urbain",
      "LJ" => "R�alisation de l'acte hors �tablissement en milieu naturel",
      "XH" => "R�alisation de l'acte sur le lieu de vie du patient",
      "BN" => "N�cessit� de recours � un interpr�te",
      "EZ" => "R�alisation fractionn�e de l'acte",
    );
    
    parent::updateFormFields();
    $this->_libelle = $libelles[$this->modulateur];
    $this->_view = "$this->modulateur: $this->_libelle";
  }

  /**
   * Chargement du d�tail d'activit� CsARR
   *
   * @return CActiviteCsARR
   */
  function loadRefCode() {
    return $this->_ref_code = CActiviteCsARR::get($this->code);
  }
	
	function loadView(){
    parent::loadView();
    $this->loadRefCode();
  }
}
