<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPrepas
 *  @version $Revision: $
 *  @author S�bastien Fillonneau
 */

/**
 * The CValidationRepas class
 */
class CValidationRepas extends CMbObject {
  // DB Table key
  var $validationrepas_id     = null;
    
  // DB Fields
  var $service_id     = null;
  var $date           = null;
  var $typerepas_id   = null;
  var $modif          = null;
  
  function CValidationRepas() {
    $this->CMbObject("validationrepas", "validationrepas_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getSpecs() {
    return array (
      "service_id"   => "notNull ref class|CService",
      "date"         => "date",
      "typerepas_id" => "notNull ref class|CTypeRepas",
      "modif"        => "bool"
    );
  }
  
  function resetModifications(){
    
    $typeRepas = new CTypeRepas;
    $typeRepas->load($this->typerepas_id);
    $service = new CService;
    $service->load($this->service_id);
    $service->loadRefsBack();
    foreach ($service->_ref_chambres as $chambre_id => &$chambre) {
      $chambre->loadRefsBack();
      foreach ($chambre->_ref_lits as $lit_id => &$lit) {
        $lit->loadAffectations($this->date);
        foreach ($lit->_ref_affectations as $affectation_id => &$affectation) {
          $affectation->loadRefSejour();
          $affectation->loadMenu($this->date,array($this->typerepas_id => null));
          $sejour =& $affectation->_ref_sejour;
          
          $date_entree  = substr($affectation->entree, 0, 10);
          $date_sortie  = substr($affectation->sortie, 0, 10);
          $heure_entree = substr($affectation->entree, 11, 5);
          $heure_sortie = substr($affectation->sortie, 11, 5);
          
          if(!$sejour->sejour_id || $sejour->type == "ambu" || 
              ($this->date == $date_entree && $heure_entree > $typeRepas->fin) ||
              ($this->date == $date_sortie && $heure_sortie < $typeRepas->debut)){
          }else{
            $repas =& $affectation->_list_repas[$this->date][$this->typerepas_id];
            
            if($repas->modif){
              $repas->modif       = 0;
              $repas->_no_synchro = true;
              $repas->store();
            }
          }
        }
      }
    }
  }
}
?>