<?php

/**
 * The HPRIM 2.1 parent class declaration
 *
 * @category Hprim21
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id$
 * @link     http://www.mediboard.org
 */

/**
 * The HPRIM 2.1 parent class
 */
class CHprim21Object extends CMbObject {
  
  // DB Fields
  public $emetteur_id;
  public $external_id;
  public $echange_hprim21_id;

  // Back reference
  /** @var  CEchangeHprim21 */
  public $_ref_echange_hprim21;

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $specs = parent::getProps();
    
    $specs["emetteur_id"]        = "str notNull";
    $specs["external_id"]        = "str";
    $specs["echange_hprim21_id"] = "ref class|CEchangeHprim21";
    
    return $specs;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    
    $this->_view = $this->emetteur_id." : ".$this->external_id;
  }

  /**
   * Load exchange
   *
   * @return CEchangeHprim21
   */
  function loadRefExchange() {
    return $this->_ref_echange_hprim21 = $this->loadFwdRef("echange_hprim21_id", true);
  }

  function massLoadExchanges($objects) {
    return CStoredObject::massLoadFwdRef($objects, "echange_hprim21_id");
  }
  
  function setHprim21ReaderVars(CHPrim21Reader $hprim21_reader) {
    $this->emetteur_id        = $hprim21_reader->id_emetteur;
    $this->echange_hprim21_id = $hprim21_reader->_echange_hprim21->_id;
  }
  
  function bindToLine($line, CHPrim21Reader &$reader) {
    return "Bind de $this->_class non pris en charge";
  }
  
  function getDateFromHprim($date) {
    if (strlen($date) >= 8) {
      $annee = substr($date, 0, 4);
      $mois = substr($date, 4, 2);
      if ($mois == "00") {
        $mois = "01";
      }
      $jour = substr($date, 6, 2);
      if ($jour == "00") {
        $jour = "01";
      }
      return "$annee-$mois-$jour";
    }
    else {
      return "";
    }
  }
  
  function getDateTimeFromHprim($date) {
    if (strlen($date) >= 12) {
      $annee = substr($date, 0, 4);
      $mois = substr($date, 4, 2);
      if ($mois == "00") {
        $mois = "01";
      }
      $jour = substr($date, 6, 2);
      if ($jour == "00") {
        $jour = "01";
      }
      $heure   = substr($date, 8, 2);
      $minutes = substr($date, 10, 2);
      return "$annee-$mois-$jour $heure:$minutes:00";
    }
    else {
      return "";
    }
  }
}
