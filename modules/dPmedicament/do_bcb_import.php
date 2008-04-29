<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPmedicament
* @version $Revision: $
* @author Alexis Granger
*/


global $AppUI, $g;

// Recuperation du fichier
$file = $_FILES['datafile'];

if (strtolower(pathinfo($file['name'] , PATHINFO_EXTENSION) == 'csv')) {
  $csv = fopen($file['tmp_name'], 'r');
  
  $line = 0;
  while (($data = fgetcsv($csv, 2000, ',')) !== false) {
    $num = count($data);
    for ($c = 0; $c < $num; $c++) {
      $data[$c] = trim($data[$c]);
    }

    $cip = str_replace(array('.', '-'), '', $data[0]);
    
    // if there is more than 1 column and the first isn't empty
    if ($num > 0 && ($cip != '') && is_numeric($cip) && (strlen($cip) == 7)) {
      $livret = new CBcbProduitLivretTherapeutique();
      $livretLoad = new CBcbProduitLivretTherapeutique();
      
      $i = 0;
      $livret->distObj->Code            = $g;
      $livret->distObj->CIP             = $cip; $i++;
      $livret->distObj->PrixHopital     = $data[$i]?$data[$i]:null; $i++;
      $livret->distObj->PrixVille       = $data[$i]?$data[$i]:null; $i++;
      $livret->distObj->DatePrixHopital = $data[$i]?$data[$i]:null; $i++;
      $livret->distObj->DatePrixVille   = $data[$i]?$data[$i]:null; $i++;
      $livret->distObj->Commentaire     = $data[$i]?$data[$i]:null; $i++;
      $livret->distObj->CodeInterne     = $data[$i]?$data[$i]:null; $i++;
        
      // If the product exists
      if($livretLoad->load($cip)) {
    	  $livret->updateDBFields();
    
    	  if($livret->distObj->Update() >= 0){
          $AppUI->setMsg("Element mis � jour");
        }
      } 
      
      else {
        if($livret->distObj->Insert() >= 0){
          $AppUI->setMsg("Element ins�r�");
        }
      }
    }
    
    else {
      $AppUI->setMsg("Code CIP [$data[0]] invalide � la ligne $line", UI_MSG_WARNING);
    }
    $line++;
  }
  
  fclose($csv);
}

$AppUI->redirect('m=dPmedicament&a=vw_bcb_import&dialog=1');
?>