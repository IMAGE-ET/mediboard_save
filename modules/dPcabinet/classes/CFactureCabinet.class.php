<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPcabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * Facture li�e � une ou plusieurs consultations
 *
 */
class CFactureCabinet extends CFacture {
  // DB Table key
  public $facture_id;
  
  /**
   * getSpec
   * 
   * @return $spec
  **/
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'facture_cabinet';
    $spec->key   = 'facture_id';
    return $spec;
  }
    
  /**
   * getBackProps
   * 
   * @return $backProps
  **/
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["consultations"] = "CConsultation facture_id";
    $backProps["reglements"]    = "CReglement object_id";
    $backProps["relance_fact_cabinet"] = "CRelance object_id";
    return $backProps;
  }
   
  /**
   * updateFormFields
   * 
   * @return void
  **/
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = sprintf("FA%08d", $this->_id);
  }

  /**
   * Chargement des r�glements de la facture
   * 
   * @param bool $cache cache
   * 
   * @return $this->_ref_reglements
  **/
  function loadRefsReglements($cache = 1) {
    $this->_ref_reglements = $this->loadBackRefs("reglements", 'date');
    return parent::loadRefsReglements($cache);
  }
  
  /**
   * Red�finition du store
   * 
   * @return void
  **/
  function store() {
    $this->loadRefsConsultation();
    // A v�rifier pour le == 0 s'il faut faire un traitement
    if ($this->facture !== '0' && $this->_id) {
      foreach ($this->_ref_consults as $_consultation) {
        if ($this->facture == -1 && $_consultation->facture == 1) {
          $_consultation->facture = 0;
          $_consultation->store();
        }
        elseif ($this->facture == 1 && $_consultation->facture == 0) {
          $_consultation->facture = 0;
          $_consultation->store();
        }
      }
    }
  
    $this->loadRefsRelances();
    $this->loadRefsReglements();
    // Standard store
    if ($msg = parent::store()) {
      return $msg;
    }
  }
  
  /**
   * Red�finition du delete
   * 
   * @return void
  **/
  function delete() {
    $this->loadRefsReglements();
    $this->loadRefsRelances();
    // Standard delete
    if ($msg = parent::delete()) {
      return $msg;
    }
  }
  
  //Ne pas supprimer cette fonction!
  /**
   * loadRefPlageConsult
   * 
   * @return void
  **/
  function loadRefPlageConsult(){
    
  }
  
  /**
   * Fonction permettant � partir d'un num�ro de r�f�rence de retrouver la facture correspondante
   * 
   * @param string $num_reference le num�ro de r�f�rence 
   * 
   * @return $facture
  **/
  function findFacture($num_reference){
    $facture = new CFactureCabinet();
    $facture->num_reference = $num_reference;
    $facture->loadMatchingObject();
    return $facture;
  }
  
  /**
   * Chargement des relances de la facture
   * 
   * @return _ref_relances
  **/
  function loadRefsRelances(){
    $this->_ref_relances = $this->loadBackRefs("relance_fact_cabinet", 'date');
    $this->IsRelancable();
    return $this->_ref_relances;
  }
}
