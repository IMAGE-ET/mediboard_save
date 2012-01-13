<?php /* $Id:affectation.class.php 8146 2010-02-25 14:38:16Z rhum1 $ */

/**
 *	@package Mediboard
 *	@subpackage dPhospi
 *	@version $Revision:8146 $
 *  @author Thomas Despoix
 */

/**
 * Classe CAffectation.
 * @abstract G�re les affectation des s�jours dans des lits
 */
class CAffectation extends CMbObject {
  // DB Table key
  var $affectation_id = null;

  // DB References
  var $lit_id    = null;
  var $sejour_id = null;
  var $parent_affectation_id = null;
  
  // DB Fields
  var $entree   = null;
  var $sortie   = null;
  var $confirme = null;
  var $effectue = null;
  var $rques    = null;
  
  var $uf_hebergement_id = null; // UF de responsabilit� d'h�bergement
  var $uf_medicale_id    = null; // UF de responsabilit� m�dicale
  var $uf_soins_id       = null; // UF de responsabilit� de soins

  // Form Fields
  var $_entree_relative = null;
  var $_sortie_relative = null;
  var $_mode_sortie     = null;
  var $_duree           = null;
  
  // Order fields
  var $_patient   = null;
  var $_praticien = null;
  var $_chambre   = null;

  // Object references
  var $_ref_lit    = null;
  var $_ref_sejour = null;
  var $_ref_prev   = null;
  var $_ref_next   = null;
  var $_no_synchro = null;
  var $_list_repas = null;
  var $_ref_uf_hebergement = null; 
  var $_ref_uf_medicale    = null; 
  var $_ref_uf_soins       = null; 
  var $_ref_parent_affectation = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'affectation';
    $spec->key   = 'affectation_id';
    return $spec;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["echanges_hprim"]      = "CEchangeHprim object_id";
    $backProps["echanges_ihe"]        = "CExchangeIHE object_id";
    $backProps["repas"]               = "CRepas affectation_id";
    $backProps["items_liaisons"]      = "CItemLiaison affectation_id";
    $backProps["affectations_enfant"] = "CAffectation parent_affectation_id";
    $backProps["movements"]           = "CMovement affectation_id";
    
    return $backProps;
  }

  function getProps() {
  	$specs = parent::getProps();
    $specs["lit_id"]       = "ref notNull class|CLit";
    $specs["sejour_id"]    = "ref class|CSejour cascade";
    $specs["parent_affectation_id"] = "ref class|CAffectation";
    $specs["entree"]       = "dateTime notNull";
    $specs["sortie"]       = "dateTime notNull";
    $specs["confirme"]     = "bool";
    $specs["effectue"]     = "bool";
    $specs["rques"]        = "text";
    
    $specs["uf_hebergement_id"] = "ref class|CUniteFonctionnelle";
    $specs["uf_medicale_id"]    = "ref class|CUniteFonctionnelle";
    $specs["uf_soins_id"]       = "ref class|CUniteFonctionnelle";

    $specs["_duree"]       = "num";
    $specs["_mode_sortie"] = "enum list|normal|mutation|transfert|deces default|normal";
    
    $specs["_patient"]     = "str";
    $specs["_praticien"]   = "str";
    $specs["_chambre"]     = "str";
    
    return $specs;
  }

  function loadView() {
    $sejour = $this->loadRefSejour();
    $sejour->loadRefPraticien();
    $sejour->loadRefPatient()->loadRefPhotoIdentite();
    $sejour->loadRefsAffectations();
    $affectations = $sejour->_ref_affectations;
    
    if (is_array($affectations) && count($affectations)) {
      foreach ($affectations as $_affectation) {
        $_affectation->loadRefLit()->loadCompleteView();
        $_affectation->_view = $_affectation->_ref_lit->_view;
        $_affectation->loadRefParentAffectation();
      }
    }
    
    $this->loadRefLit()->loadCompleteView();
    $this->_view = $this->_ref_lit->_view;
    $this->loadRefParentAffectation();
    
    foreach ($sejour->loadRefsOperations() as $_operation) {
      $_operation->loadRefChir();
      $_operation->loadRefPlageOp();
    }
    
    $sejour->getDroitsCMU();
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_duree = mbDaysRelative($this->entree, $this->sortie);
  }

  function check() {
    if($msg = parent::check()) {
      return $msg;
    }
    
    //TODO: utiliser moreThan
    $this->completeField("entree");
    $this->completeField("sortie");
    if ($this->sortie < $this->entree) {
      return "La date de sortie doit �tre sup�rieure ou �gale � la date d'entr�e";
    }
    
    if ($msg = $this->checkCollisions()) {
      return $msg;
    }
    
    return null;
  }
  
  /**
   * @return string Store-like message
   */
  function checkCollisions(){
    $this->completeField("sejour_id");
    if (!$this->sejour_id) return;
    
    $affectation = new CAffectation;
    $affectation->sejour_id = $this->sejour_id;
    $affectations = $affectation->loadMatchingList();
    unset($affectations[$this->_id]);
    
    foreach($affectations as $_aff) {
      if($this->collide($_aff)) {
        return "Placement d�j� effectu�";
      }
    }
  }

  function deleteOne() {
    return parent::delete();
  }

  function delete() {
    $this->completeField("sejour_id");
    if ($this->sejour_id){
      $this->loadRefSejour();
      if ($this->_ref_sejour->type != "seances") {
        return $this->_ref_sejour->delAffectations();
      }
    }
    return $this->deleteOne();
  }

  function store() {
  	// Conserver l'ancien objet avant d'enregistrer
    $old = new CAffectation();
    if ($this->_id) {
      $old->load($this->_id);
      $old->loadRefsAffectations();
    }
    
    // Gestion des UFs
    $this->makeUF();
    
    // Enregistrement standard
    if ($msg = parent::store()) {
      return $msg;
    }

    // Pas de probl�me de synchro pour les blocages de lits
    if (!$this->sejour_id || $this->_no_synchro) {
      return $msg;
    }
    
    // Modification de la date d'admission et de la dur�e de l'hospi
    $this->load($this->affectation_id);

    if ($old->_id) {
      $this->_ref_prev = $old->_ref_prev;
      $this->_ref_next = $old->_ref_next;
    } 
    else {
      $this->loadRefsAffectations();
    }
     
    $changeSejour = 0;
    $changePrev   = 0;
    $changeNext   = 0;
    
    $prev = $this->_ref_prev;
    $next = $this->_ref_next;
    $sejour = $this->loadRefSejour();
    
    // Mise � jour vs l'entr�e
    if (!$prev->_id) {
      if ($this->entree != $sejour->_entree) {
      	$field = $sejour->entree_reelle ? "entree_reelle" : "entree_prevue";
      	$sejour->$field = $this->entree;
        $changeSejour = 1;
      }
    } 
    elseif ($this->entree != $prev->sortie) {
      $prev->sortie = $this->entree;
      $changePrev = 1;
    }
    
    // Mise � jour vs la sortie
    if (!$next->_id) {
      if ($this->sortie != $sejour->_sortie) {
      	$field = $sejour->sortie_reelle ? "sortie_reelle" : "sortie_prevue";
      	$sejour->$field = $this->sortie;
        $changeSejour = 1;
      }
    }
    elseif ($this->sortie != $next->entree) {
      $next->entree = $this->sortie;
      $changeNext = 1;
    }

    if ($changePrev) {
      $prev->store();
    }
    
    if ($changeNext) {
      $next->store();
    }
    
    if ($changeSejour) {
      $sejour->_no_synchro = 1;
      $sejour->updateFormFields();
      $sejour->store();
    }
    
    return $msg;
  }

  function loadRefLit($cache = 1) {
    return $this->_ref_lit = $this->loadFwdRef("lit_id", $cache);
  }

  function loadRefSejour($cache = 1) {
    return $this->_ref_sejour =  $this->loadFwdRef("sejour_id", $cache);
  }

  function loadRefsFwd($cache = 1) {
    $this->loadRefLit($cache);
    $this->loadView();
    $this->loadRefSejour($cache);
    $this->loadRefsAffectations();
  }

  function loadRefsAffectations() {
    $where = array (
      "affectation_id" => "!= '$this->affectation_id'",
      "sejour_id" => "= '$this->sejour_id'",
      "sortie" => "= '$this->entree'"
    );

    $this->_ref_prev = new CAffectation;
    $this->_ref_prev->loadObject($where);

    $where = array (
      "affectation_id" => "!= '$this->affectation_id'",
      "sejour_id" => "= '$this->sejour_id'",
      "entree" => "= '$this->sortie'"
    );

    $this->_ref_next = new CAffectation;
    $this->_ref_next->loadObject($where);
  }
  
  function loadRefsAffectationsEnfant() {
    return $this->_ref_affectations_enfant = $this->loadBackRefs("affectations_enfant");
  }
  
  function getPerm($permType) {
    if(!$this->_ref_lit) {
      $this->loadRefLit();
    }
    if(!$this->_ref_sejour) {
      $this->loadRefSejour();
    }
    return ($this->_ref_lit->getPerm($permType) && $this->_ref_sejour->getPerm($permType));
  }

  function checkDaysRelative($date) {
    if ($this->entree and $this->sortie) {
      $this->_entree_relative = mbDaysRelative("$date 10:00:00", $this->entree);
      $this->_sortie_relative = mbDaysRelative("$date 10:00:00", $this->sortie);
    }
  }

  function collide($aff) {
    return CMbRange::collides($this->entree, $this->sortie, $aff->entree, $aff->sortie);
  }

  function loadMenu($date, $listTypeRepas = null){
    $this->_list_repas[$date] = array();
    $repas =& $this->_list_repas[$date];
    if(!$listTypeRepas){
      $listTypeRepas = new CTypeRepas;
      $order = "debut, fin, nom";
      $listTypeRepas = $listTypeRepas->loadList(null,$order);
    }

    $where                   = array();
    $where["date"]           = $this->_spec->ds->prepare(" = %", $date);
    $where["affectation_id"] = $this->_spec->ds->prepare(" = %", $this->affectation_id);
    foreach($listTypeRepas as $keyType=>$typeRepas){
      $where["typerepas_id"] = $this->_spec->ds->prepare("= %",$keyType);
      $repasDuJour = new CRepas;
      $repasDuJour->loadObject($where);
      $repas[$keyType] = $repasDuJour;
    }
  }
  
  function loadRefParentAffectation() {
    return $this->_ref_parent_affectation = $this->loadFwdRef("parent_affectation_id", true);
  }
  
  static function getDefaultAffectation(CSejour $sejour) {
    $service = new CService();
    $service->load(CAppUI::conf("dPhospi default_service_types_sejour $sejour->type"));
    if (!$service->_id) {
      // envoi par d�faut le premier de la liste si pas d�fini
      $service->loadObject();  
    }

    $affectation = new CAffectation();
    $affectation->entree = $sejour->entree;
    $affectation->sortie = $sejour->sortie;
    $affectation->loadRefLit();
    $affectation->_ref_lit->loadRefChambre();
    $affectation->_ref_lit->_ref_chambre->_ref_service = $service;
    $affectation->sejour_id = $sejour->_id;
    $affectation->loadRefSejour();
    $affectation->_ref_sejour->loadNDA();
    $affectation->_ref_sejour->loadRefPatient();
    $affectation->_ref_sejour->loadRefPraticien();
    
    return $affectation;
  }
  
  function makeUF() {
    $this->completeField("uf_hebergement_id", "uf_soins_id", "uf_medicale_id");
    
    $this->loadRefLit()->loadRefChambre()->loadRefService();
    $lit       = $this->_ref_lit;
    $chambre   = $lit->_ref_chambre;
    $service   = $chambre->_ref_service;
    
    $modified = false;
    
    $affectation_uf = new CAffectationUniteFonctionnelle();
    if (!$this->uf_hebergement_id) {
      $where["object_id"]     = "= '{$lit->_id}'";
      $where["object_class"]  = "= 'CLit'";
      $affectation_uf->loadObject($where);

      if (!$affectation_uf->_id) {
        $where["object_id"]     = "= '{$chambre->_id}'";
        $where["object_class"]  = "= 'CChambre'";
        $affectation_uf->loadObject($where);
        
        if (!$affectation_uf->_id) {
          $where["object_id"]     = "= '{$service->_id}'";
          $where["object_class"]  = "= 'CService'";
          $affectation_uf->loadObject($where);
        }
      }
      
      if ($affectation_uf->uf_id) {
        $modified = true;
      }
      
      $this->uf_hebergement_id = $affectation_uf->uf_id;      
    }
    
    $affectation_uf = new CAffectationUniteFonctionnelle();
    if (!$this->uf_soins_id) {
      $where["object_id"]     = "= '{$service->_id}'";
      $where["object_class"]  = "= 'CService'";
      $affectation_uf->loadObject($where);
      
      if ($affectation_uf->uf_id) {
        $modified = true;
      }
      
      $this->uf_soins_id = $affectation_uf->uf_id;
    }
    
    $affectation_uf = new CAffectationUniteFonctionnelle();
    if (!$this->uf_medicale_id) {
      $praticien = $this->loadRefSejour()->loadRefPraticien();
      $where["object_id"]     = "= '{$praticien->_id}'";
      $where["object_class"]  = "= 'CMediusers'";
      $affectation_uf->loadObject($where);

      if (!$affectation_uf->_id) {
        $where["object_id"]     = "= '{$praticien->_ref_function->_id}'";
        $where["object_class"]  = "= 'CFunctions'";
        $affectation_uf->loadObject($where);
      }
      
      if ($affectation_uf->uf_id) {
        $modified = true;
      }
      
      $this->uf_medicale_id = $affectation_uf->uf_id;
    }
  }
  
  function loadRefUfs($cache = 1) {     
    $this->loadRefUFHebergement($cache);
    $this->loadRefUFMedicale($cache);
    $this->loadRefUFSoins($cache);
  }
  
  function loadRefUFHebergement($cache = true) {
    return $this->_ref_uf_hebergement = $this->loadFwdRef("uf_hebergement_id", $cache);
  }
  
  function loadRefUFMedicale($cache = true) {
    return $this->_ref_uf_medicale = $this->loadFwdRef("uf_medicale_id", $cache);
  }
  
  function loadRefUFSoins($cache = true) {
    return $this->_ref_uf_soins = $this->loadFwdRef("uf_soins_id", $cache);
  }
  
  function getUFs(){
    $this->loadRefUfs();
    return array(
      "hebergement" => $this->_ref_uf_hebergement,
      "medicale"    => $this->_ref_uf_medicale,
      "soins"       => $this->_ref_uf_soins,
    );
  }
  
  function getMovementType() {
    $sejour = $this->loadRefSejour();
    
    return $sejour->getMovementType();
  }
}
?>
