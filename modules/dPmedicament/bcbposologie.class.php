<?php /* */

/**
* @package Mediboard
* @subpackage dPmedicament
* @version $Revision: $
* @author Alexis Granger
*/

require_once("bcbObject.class.php");

class CBcbPosologie extends CBcbObject {

  var $quantite1          = null;
  var $quantite2          = null;
  var $code_prise1        = null;
  var $code_prise2        = null;
  var $code_indication    = null;
  var $code_nature        = null;
  var $code_voie          = null;
  var $code_profil        = null;
  var $code_unite_prise   = null;
  var $code_duree1        = null;
  var $code_duree2        = null;
  var $code_duree3        = null;
  var $code_moment        = null;
  var $code_terrain       = null;
  var $code_age1          = null;
  var $code_age2          = null;    
	var $p_kg               = null;        
  var $adequation_up1_up2 = null;
	var $combien1           = null;           
	var $combien2           = null;
  var $tous_les           = null; 
 	var $pendant1           = null;
	var $pendant2           = null;          
	var $maximum            = null;          
	var $maximum_pds        = null;       
	var $nombre_unites      = null;     
	var $code_posologie     = null;   
	var $code_cip           = null;        
	var $code_par           = null;        
	var $commentaire        = null; 
	
  // Others fields
  var $_code_prise1       = null;
  var $_code_prise2       = null;
  var $_code_indication   = null;
  var $_code_terrain      = null;
  var $_code_nature       = null;
  var $_code_voie         = null;
  var $_code_profil       = null;
  var $_code_unite_prise  = null;
  var $_code_duree1       = null;
  var $_code_duree2       = null;
  var $_code_duree3       = null;
  var $_code_moment       = null;
  var $_code_age1         = null;
  var $_code_age2         = null;
  
  var $_view              = null;
  
  // Constructeur
  function CBcbPosologie(){
    $this->distClass = "BCBPosologie";
    parent::__construct();
  }
 
  // Chargement d'une posologie a partir d'un code CIP
  function load($cip, $numPoso = null){
    /* Test des fonctions int�gr�es BCB
    $this->distObj->chargementDetail($cip, $numPoso);
    //mbTrace($this->distObj, "chargementDetail(CIP : $cip, NUMPOSO : $numPoso)"); die;
    $result = $this->distObj->ChargementGetData(1,25);
    mbTrace($result); die;
    $args = "";
    $arrayPoso = array_values(get_object_vars($this->distObj->gPosoDetail));
    foreach($arrayPoso as $arg) {
      $args .=", $arg";
    }
    mbTrace($args); die;
    $result = $this->distObj->DecodageDetail(1, 2, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 6, 0, 40, 2004, 0, 0, 1, 3419544, 1, "P", "Boire imm�diatement apr�s dissolution compl�te dans un grand verre d'eau, de pr�f�rence au cours des repas.
*1 comprim� effervescent (200 mg), � renouveler si besoin au bout de 6 heures. En cas de douleurs ou de fi�vre plus intense, 2 comprim�s effervescents � 200 mg, � renouveler si besoin au bout de 6 heures. Dans tous les cas ne pas d�passer 6 comprim�s effervescents par jour (1200 mg par jour).
- Les prises syst�matiques permettent d'�viter les oscillations de fi�vre ou de douleur. Elles doivent �tre espac�es d'au moins 6 heures.
- Boire imm�diatement apr�s dissolution compl�te du comprim� effervescent dans un grand verre d'eau, de pr�f�rence au cours des repas.
- Au cours des traitements prolong�s, contr�ler la formule sanguin, les fonctions h�patiques et r�nales.");
    mbTrace($result); die;
    */
    // Chargement des posologies du produit
    $ds = CSQLDataSource::get("bcb");
    if($numPoso){
      $query = "SELECT * FROM `POSO_PRODUITS` WHERE `CODE_CIP` = '$cip' AND `NO_POSO` = '$numPoso';";
    } else {
      $query = "SELECT * FROM `POSO_PRODUITS` WHERE `CODE_CIP` = '$cip' ORDER BY `NO_POSO` ASC;";
    }
    
    $posologies = $ds->loadList($query);
    $posologie = reset($posologies);
    
    if (!$posologie){
      return;
    }
    
    if ($posologie){
      $this->quantite1 = $posologie["QUANTITE1"];
      $this->quantite2 = $posologie["QUANTITE2"];
     
      $this->code_prise1 = $posologie["CODE_PRISE1"];
      $this->getValeur($this->code_prise1, "_code_prise1", "LIBELLE_SPECIF", "CODE_SPECIF",  "POSO_SPECIF_PRISE");
  
      $this->code_prise2 = $posologie["CODE_PRISE2"];
      $this->getValeur($this->code_prise2, "_code_prise2", "LIBELLE_SPECIF", "CODE_SPECIF", "POSO_SPECIF_PRISE");
 
      $this->code_indication = $posologie["CODE_INDICATION"];
      $this->getValeur($this->code_indication, "_code_indication", "LIBELLE_INDICATION", "CODE_INDICATION", "POSO_INDICATIONS");

      $this->code_indication = $posologie["CODE_NATURE"];
      $this->getValeur($this->code_nature, "_code_nature", "LIBELLE_NATURE", "CODE_NATURE", "POSO_NATURES");
      
      $this->code_voie = $posologie["CODE_VOIE"];
      $this->getValeur($this->code_voie, "_code_voie", "LIBELLE_VOIE", "CODE_VOIE", "POSO_VOIES");
      
      $this->code_profil = $posologie["CODE_PROFIL"];
      $this->getValeur($this->code_profil, "_code_profil", "LIBELLE_PROFIL", "CODE_PROFIL", "POSO_PROFILS");
      
      $this->code_unite_prise = $posologie["CODE_UNITE_DE_PRISE"];
      $this->getValeur($this->code_unite_prise, "_code_unite_prise", "LIBELLE_UNITE_DE_PRISE,LIBELLE_UNITE_DE_PRISE_PLURIEL", "CODE_UNITE_DE_PRISE", "POSO_UNITES_PRISE");
      
      $this->code_duree1 = $posologie["CODE_DUREE1"];
      $this->getValeur($this->code_duree1, "_code_duree1", "LIBELLE_DUREE", "CODE_DUREE", "POSO_DUREES");
      
      $this->code_duree2 = $posologie["CODE_DUREE2"];
      $this->getValeur($this->code_duree2, "_code_duree2", "LIBELLE_DUREE", "CODE_DUREE", "POSO_DUREES");
      
      $this->code_duree3 = $posologie["CODE_DUREE3"];
      $this->getValeur($this->code_duree3, "_code_duree3", "LIBELLE_DUREE", "CODE_DUREE", "POSO_DUREES");
      
      $this->code_moment = $posologie["CODE_MOMENT"];
		  $this->getValeur($this->code_moment, "_code_moment", "LIBELLE_MOMENT", "CODE_MOMENT", "POSO_MOMENTS");
     
		  $this->code_terrain = $posologie["CODE_TERRAIN"];
		  $this->getValeur($this->code_terrain, "_code_terrain", "TERRAIN,AGE1,AGE2,CAGE1,CAGE2,POIDS1,POIDS2", "CODE_TERRAIN", "POSO_PRODUITS_TERRAIN");
		  
		  $this->code_age1 = $this->_code_terrain["CAGE1"].$this->_code_terrain["AGE1"];
		  $this->getValeur($this->code_age1, "_code_age1", "POIDS_G,POIDS_F,TAILLE_G,TAILLE_F,SURFACE_G,SURFACE_F", "CODE_AGE", "POSO_TABLEAU");
      
		  $this->code_age2 = $this->_code_terrain["CAGE2"].$this->_code_terrain["AGE2"];
		  $this->getValeur($this->code_age2, "_code_age2", "POIDS_G,POIDS_F,TAILLE_G,TAILLE_F,SURFACE_G,SURFACE_F", "CODE_AGE", "POSO_TABLEAU");
      
      $this->p_kg               = $posologie["P_KG"];
		  $this->adequation_up1_up2 = $posologie["ADEQUATION_UP1_UP2"];
		  $this->combien1           = $posologie["COMBIEN1"];
		  $this->combien2           = $posologie["COMBIEN2"];
		  $this->tous_les           = $posologie["TOUS_LES"];
		  
		  $this->pendant1           = $posologie["PENDANT1"];
		  $this->pendant2           = $posologie["PENDANT2"];
		  $this->maximum            = $posologie["MAXIMUM"];
		  $this->maximum_pds        = $posologie["MAXIMUM_PDS"];
		  
		  $this->nombre_unites      = $posologie["NOMBRE_UNITES"];
		  $this->code_posologie     = $posologie["NO_POSO"];
		  $this->code_cip           = $posologie["CODE_CIP"];
		  
		  $this->code_par           = $posologie["CODE_PAR"];
		  $this->commentaire        = $posologie["COMMENTAIRE"];
    }
    $this->updateFormFields();
  }
  
 
  // Recupere la valeur d'un code
  function getValeur($champ, $_champ, $champs, $nom_champ_base2, $table){
    $ds = CSQLDataSource::get("bcb");
    $query = "SELECT * FROM `$table` WHERE `$nom_champ_base2` = '$champ';";
    $object = null;
 
    $ds->loadObject($query, $object);
    // Si un objet a ete trouv�
    if($object){  
      $listChamps = explode(",", $champs);
      // Si un seul champ est pr�sent
      if(count($listChamps) == 1){
        $this->$_champ = $object->$listChamps[0];
      }
      // Si plusieus champs
      if(count($listChamps) > 1){
        $tab = array();
        foreach($listChamps as $key => $_champ_temp){
          $tab[$_champ_temp] = $object->$listChamps[$key];
        }
        $this->$_champ = $tab;
      }
    }  
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = "";
    $this->_view .= $this->quantite1;
    if($this->quantite2) {
      $this->_view .= " � $this->quantite2";
    }
    $this->_view .= " ".$this->_code_unite_prise["LIBELLE_UNITE_DE_PRISE"];
    if($this->p_kg) {
      $this->_view .= "/kg";
    }
    if($this->code_moment) {
      $this->_view .= " $this->_code_moment";
    }
    if($this->code_duree1) {
      if($this->combien1) {
        $this->_view .= " $this->combien1";
        if($this->combien2) {
          $this->_view .= " � $this->combien2";
        }
        $this->_view .= " fois";
      }
      if($this->tous_les <= 1) {
        $this->_view .= " par";
      } elseif($this->tous_les > 1) {
        $this->_view .= " tous les $this->tous_les";
      }
      $this->_view .= " $this->_code_duree1";
    }
    if($this->code_prise1) {
      $this->_view .= " $this->_code_prise1";
    }
    if($this->code_duree2) {
      if($this->pendant1) {
        $this->_view .= " pendant $this->pendant1";
      }
      if($this->pendant2) {
        $this->_view .= " � $this->pendant2";
      }
      $this->_view .= " $this->_code_duree2";
    }
  }
  
  static function checkTerrain($patient) {
    
  }

}

?>
