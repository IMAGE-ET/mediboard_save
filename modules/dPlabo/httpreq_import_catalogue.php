<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPlabo
 *	@version $Revision: $
 *  @author Thomas Despoix
 */
 
global $can, $m, $AppUI, $dPconfig, $remote_name;

$can->needsAdmin();

/**
 * Catalogue import
 */
function importCatalogue($cat, $parent_id = null) {  
  global $AppUI, $remote_name;
 
  //$ana = new CExamenLabo();
  //mbTrace($ana);
  //die();
  
  set_time_limit(180);
  
  $compteur["analyses"] = 0;
  $compteur["chapitres"] = 0;
  $compteur["sousChapitre"] = 0; 
  
 
  $catalogues = array();
  // Creation du catalogue global LABO
  $catal = new CCatalogueLabo();
  $catalogue = new CCatalogueLabo();
  $catal->identifiant = substr(hash('md5',$remote_name), 0, 4);  // libelle modifi� par hash
  
  $catal->libelle = $remote_name;
  $catal->pere_id = $parent_id;
  
  // creation de son id400
  $idCat = new CIdSante400();
  $idCat->tag = $remote_name;
  $idCat->id400 = $remote_name;
  
  $idCat->bindObject($catal);
  //$AppUI->stepAjax("Catalogue '$catal->libelle' import�", UI_MSG_OK);
  
  $path = $remote_name;
  // on met a jour $catalogues
  $catalogues[$path] = $catal;
          
  //Parcours des analyses
  foreach($cat->analyse as $_analyse){ 
    $chapitre = (string) $_analyse->chapitre;
  	$path = "$remote_name/$chapitre/";
    if(!$chapitre){
    	$path = $remote_name;
    }
  	//$pathssChap = "$remote_name/$analyse_->sschapitre";
    $catChapitre = new CCatalogueLabo();
    
    // si le catalogue n'existe pas deja
    if(!array_key_exists($path,$catalogues)){
      // creation du catalogue
      $catChapitre->identifiant = substr(hash('md5',$chapitre), 0, 4);  // libelle modifi� par hash;
      $catChapitre->libelle = $chapitre;
      $catChapitre->pere_id = $catal->_id;
      $catChapitre->decodeUtfStrings();
      
      //creation de l'id400 
      $idCatChapitre = new CIdSante400();
      $idCatChapitre->tag = $remote_name;
      $idCatChapitre->id400 = substr(hash('md5',$chapitre), 0, 4);
      
      $idCatChapitre->bindObject($catChapitre);

      //$AppUI->stepAjax("Catalogue '$catChapitre->libelle' import�", UI_MSG_OK);
      $compteur["chapitres"]++;
      // on met a jour $catalogues
      $catalogues[$path] = $catChapitre;
      	    	
    }
    
    $catChapitre = $catalogues[$path];
    $catalogue = $catChapitre;
    // si il y a un sous chapitre a creer==> le pere du sous chapitre est $catalogue->_id;
    $sschapitre = (string) $_analyse->sschapitre;
    
    if($sschapitre){
      // modification du path
      $path .= $sschapitre;
      
      
      $catssChapitre = new CCatalogueLabo();
      
      if(!array_key_exists($path,$catalogues)){
        // creation du catalogue
        $catssChapitre->identifiant = substr(hash('md5',$sschapitre), 0, 4);  // libelle modifi� par hash;
        $catssChapitre->libelle = $sschapitre;
        $catssChapitre->pere_id = $catChapitre->_id;
        $catssChapitre->decodeUtfStrings();
        //creation de l'id400
        $idCatssChapitre = new CIdSante400();
        $idCatssChapitre->tag = $remote_name;
        $idCatssChapitre->id400 = substr(hash('md5', $sschapitre), 0, 4);
        
        $idCatssChapitre->bindObject($catssChapitre);
        //$AppUI->stepAjax("Sous Catalogue '$catssChapitre->libelle' import�", UI_MSG_OK);
        $compteur["sousChapitre"]++; 
        //on met � jour les catalogues
        $catalogues[$path] = $catssChapitre;
      }
      $catssChapitre = $catalogues[$path];
      $catalogue = $catssChapitre;
    }
    // Code de l'analyse
    $catAtt = $_analyse->attributes();
  	$code = $catAtt["code"];
  	
  	$idAnalyse = new CIdSante400();
  	$idAnalyse->tag = $remote_name;
  	$idAnalyse->id400 = (string) $code;
  	
  	
    $analyse = new CExamenLabo();
    $analyse->identifiant = (string) $code;
    $analyse->libelle = (string) $_analyse->libelle;
    $analyse->decodeUtfStrings();
    $analyse->technique = (string) $_analyse->technique;
    
    switch((string) $_analyse->materiel){	
      case "SANG VEINEUX":
    	$analyse->type_prelevement = "sang";
    	break;
      case "URINE":
    	$analyse->type_prelevement = "urine";
    	break;
      case "BIOPSIE":
    	$analyse->type_prelevement = "biopsie";
    	break;
    }
    
    //$analyse->applicabilite = (string) $_analyse->applicablesexe;
    $analyse->execution_lun = (string) $_analyse->joursrealisation->lundi;
    $analyse->execution_mar = (string) $_analyse->joursrealisation->mardi;
    $analyse->execution_mer = (string) $_analyse->joursrealisation->mercredi;
    $analyse->execution_jeu = (string) $_analyse->joursrealisation->jeudi;
    $analyse->execution_ven = (string) $_analyse->joursrealisation->vendredi;
    $analyse->execution_sam = (string) $_analyse->joursrealisation->samedi;
    $analyse->execution_dim = (string) $_analyse->joursrealisation->dimanche;
    
    
    
    $analyse->catalogue_labo_id = $catalogue->_id;
    $analyse->type = "num";
  	
    $idAnalyse->bindObject($analyse);
    //$AppUI->stepAjax("Analyse '$analyse->identifiant' import�e", UI_MSG_OK);
    $compteur["analyses"]++;
  }// fin du foreach
  $AppUI->stepAjax("Analyses Import�es: ".$compteur["analyses"].", Chapitres Import�s: ".$compteur["chapitres"].", Sous chapitres Import�s: ".$compteur["sousChapitre"], UI_MSG_OK);
}



// Check import configuration
$config = $dPconfig[$m]["CCatalogueLabo"];

if (null == $remote_name = $config["remote_name"]) {
  $AppUI->stepAjax("Remote name not configured", UI_MSG_ERROR);
}

if (null == $remote_url = $config["remote_url"]) {
  $AppUI->stepAjax("Remote URL not configured", UI_MSG_ERROR);
}

if (false === $content = file_get_contents($remote_url)) {
  $AppUI->stepAjax("Couldn't connect to remote url", UI_MSG_ERROR);
}


// Check imported catalogue document
$doc = new CMbXMLDocument;

if (!$doc->loadXML($content)) {
  $AppUI->stepAjax("Document is not well formed", UI_MSG_ERROR);
}

$tmpPath = "tmp/dPlabo/import_catalogue.xml";
CMbPath::forceDir(dirname($tmpPath));
$doc->save($tmpPath);
$doc->load($tmpPath);

if (!$doc->schemaValidate("modules/$m/remote/catalogue.xsd")) {
  $AppUI->stepAjax("Document is not valid", UI_MSG_ERROR);
}

$AppUI->stepAjax("Document is valid", UI_MSG_OK);

// Check access to idSante400
$canSante400 = CModule::getCanDo("dPsante400");
if (!$canSante400->edit) {
  $AppUI->stepAjax("No permission for module 'dPsante400' or module not installed", UI_MSG_ERROR);
}

// Import catalogue
$cat = new SimpleXMLElement($content);
try {
  importCatalogue($cat);
} 
catch (Exception $e) {
  mbTrace($e);
  $AppUI->stepAjax("Couldn't import catalogue for the  reason stated above", UI_MSG_ERROR);
}

?>