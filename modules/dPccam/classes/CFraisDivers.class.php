<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage ccam
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

/**
 * Frais divers
 */
class CFraisDivers extends CActe {
  public $frais_divers_id;

  // DB fields
  public $type_id;
  public $coefficient;
  public $quantite;

  public $_montant;

  public $_ref_type;

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["type_id"]     = "ref notNull class|CFraisDiversType autocomplete|code";
    $props["coefficient"] = "float notNull default|1";
    $props["quantite"]    = "num min|0";

    $props["_montant"]    = "currency";
    return $props;
  }

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "frais_divers";
    $spec->key   = "frais_divers_id";
    return $spec;
  }

  /**
   * Chargement du type de frais
   *
   * @return CFraisDiversType
   */
  function loadRefType(){
    return $this->_ref_type = $this->loadFwdRef("type_id", true);
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefType();

    $this->_montant = $this->montant_base;

    // Vue cod�e
    $this->_shortview  = $this->quantite > 1 ? "{$this->quantite}x " : "";
    $this->_shortview .= $this->_ref_type->_view;

    if ($this->coefficient != 1) {
      $this->_shortview .= $this->coefficient;      
    }

    $this->_view = "Frais divers $this->_shortview";
    if ($this->object_class && $this->object_id) {
      $this->_view .= " de $this->object_class-$this->object_id";
    }
  }

  /**
   * Cr�ation d'un item de facture pour un frais divers
   *
   * @param CFacture $facture la facture
   * @param string   $date    date � d�faut
   *
   * @return string|null
   */
  function creationItemsFacture($facture, $date) {
    $this->loadRefType();
    $ligne = new CFactureItem();
    $ligne->libelle       = $this->_ref_type->libelle;
    $ligne->code          = $this->_ref_type->code;
    $ligne->type          = $this->_class;
    $ligne->object_id     = $facture->_id;
    $ligne->object_class  = $facture->_class;
    $ligne->date          = $date;
    $ligne->montant_base  = $this->montant_base;
    $ligne->montant_depassement = $this->montant_depassement;
    $ligne->quantite      = $this->quantite;
    $ligne->coeff         = $this->coefficient;
    $msg = $ligne->store();
    return $msg;
  }
}
