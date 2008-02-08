<?php /* */

/**
* @package Mediboard
* @subpackage dPmedicament
* @version $Revision: $
* @author Alexis Granger
*/

require_once("bcbObject.class.php");

class CBcbProduitLivretTherapeutique extends CBcbObject {

  var $group_id = null;
  var $code_cip = null;
  var $prix_hopital = null;
  var $prix_ville = null;
  var $date_prix_ville = null;
  var $date_prix_hopital = null;
  var $code_interne = null;
  var $commentaire = null;
  
  var $_ref_produit = null;
  
  
  // Constructeur
  function CBcbProduitLivretTherapeutique(){
    $this->distClass = "BCBLivretTherapeutique";
    parent::__construct();
    
    // Connexion a la base de Gestion
    $result = $this->distObj->InitConnexionGestion(CBcbObject::$objDatabaseGestion->LinkDB, CBcbObject::$TypeDatabaseGestion);
  }
  
  
  function load($code_cip){
    global $g;
    $ds = CSQLDataSource::get("bcb");
    $query = "SELECT * FROM `livrettherapeutique` WHERE `CODEETABLISSEMENT` = '$g' AND `CODECIP` = '$code_cip';";
    $result = reset($ds->loadList($query));
    if($result){
      $this->group_id          = $result["CODEETABLISSEMENT"];
      $this->code_cip          = $result["CODECIP"];
      $this->prix_hopital      = $result["PRIXHOPITAL"];
      $this->prix_ville        = $result["PRIXVILLE"];
      $this->date_prix_hopital = $result["DATEPRIXHOPITAL"];
      $this->date_prix_ville   = $result["DATEPRIXVILLE"];
      $this->code_interne      = $result["CODEINTERNE"];
      $this->commentaire       = $result["COMMENTAIRE"];
      // return true si le produit existe => chargement
      return true;
    }  
    // return false si le produit n'existe pas
    return false;
  }
  
  
  function updateFormFields(){
    $this->date_prix_hopital = preg_replace("/(\d{2})\/(\d{2})\/(\d{2,4})/", "$1-$2-$3", $this->date_prix_hopital);
    $this->date_prix_ville = preg_replace("/(\d{2})\/(\d{2})\/(\d{2,4})/", "$1-$2-$3", $this->date_prix_ville);
  }
  
  
  function updateDBFields(){
    $this->distObj->DatePrixHopital = preg_replace("/(\d{4})-(\d{2})-(\d{2})/", "$3/$2/$1", $this->distObj->DatePrixHopital);
	  $this->distObj->DatePrixVille = preg_replace("/(\d{4})-(\d{2})-(\d{2})/", "$3/$2/$1", $this->distObj->DatePrixVille);
  }
    
  function loadRefProduit(){
    $this->_ref_produit = new CBcbProduit();
    if($this->code_cip){
      $this->_ref_produit->load($this->code_cip);
    }
  }
} 

?>