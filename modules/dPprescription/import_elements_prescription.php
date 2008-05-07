<?php

/**
* @package Mediboard
* @subpackage dPmedicament
* @version $Revision: $
* @author Thomas Despoix
*/

global $m, $can;
$can->needsAdmin();

$schemaPath = "modules/$m/xml/elementsPrescription.xsd";

// Affichage du template
$smarty = new CSmartyDP;
$smarty->assign("schemaPath", $schemaPath);
$smarty->display("import_elements_prescription.tpl");

$doc = new CMbXMLDocument();
$doc->setSchema($schemaPath);
$docPath = @$_FILES["docPath"]["tmp_name"];


// No file, do nothing
if (!$docPath) {
  return;
}

// Valid file ?
if ($msg = $doc->loadAndValidate($docPath)) {
  CAppUI::stepAjax($msg, UI_MSG_ERROR);
}
  
CAppUI::stepAjax("Catalogue d'�lements de prescriptions valide", UI_MSG_OK);

// Import catalogue
$domCatalogue = new SimpleXMLElement(file_get_contents($docPath));
foreach ($domCatalogue->chapitre as $domChapitre) {
  $chapitre = (string) $domChapitre["type"];
  foreach ($domChapitre->categorie as $domCategorie) {
    $categorie = new CCategoryPrescription();
    $categorie->chapitre = $chapitre;
    $categorie->nom = utf8_decode((string) $domCategorie->nom[0]);
    
    $categorie->nom = addslashes($categorie->nom);
    $categorie->loadMatchingObject();
    $categorie->nom = stripslashes($categorie->nom);
    
    $categorie_id = $categorie->_id;
    $categorie->description = utf8_decode((string) $domCategorie->description[0]);

    if ($msg = $categorie->store()) {
      CAppUI::stepAjax("Erreur import categorie: $msg", UI_MSG_WARNING);
      continue;
    }
    
    $msg = !$categorie_id ? "Ajout cat�gorie" : 
       ($categorie->_ref_last_log ? "Mise � jour cat�gorie" : "Cat�gorie inchang�e");
    CAppUI::stepAjax($msg, UI_MSG_OK);
	       
    foreach ($domCategorie->element as $domElement) {
      $element = new CElementPrescription();
      $element->category_prescription_id = $categorie->_id;
      $element->libelle = utf8_decode((string) $domElement->libelle);
      
      $element->libelle = addslashes($element->libelle);
      $element->loadMatchingObject();
      $element->libelle = stripslashes($element->libelle);
      
      $element_id = $element->_id;
      $element->description = utf8_decode((string) $domElement->description);
      
	    if ($msg = $element->store()) {
	      CAppUI::stepAjax("Erreur import �lement: $msg", UI_MSG_WARNING);
	      continue;
	    }
	    
	    $msg = !$element_id ? "Ajout �l�ment" : 
	       ($element->_ref_last_log ? "Mise � jour �l�ment" : "El�ment inchang�");
	    CAppUI::stepAjax($msg, UI_MSG_OK);
    } 
  }
}


?>