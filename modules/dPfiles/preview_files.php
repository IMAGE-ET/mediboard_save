<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass("dPfiles", "files"));
require_once($AppUI->getModuleClass("dPfiles", "filescategory"));

$file_id = mbGetValueFromGetOrSession("file_id", null);
$sfn = mbGetValueFromGet("sfn", 0);
$popup = mbGetValueFromGet("popup", 0);
$navig = mbGetValueFromGet("navig", null);

$largeur = null;
$hauteur = null;

$page_prev = null;
$page_next = null;

$file = new CFile;
$file->load($file_id);

// Cr�ation du template
require_once($AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("file_id", $file_id);
$smarty->assign("file"   , $file   );
if($popup==1){
  // Ouverture en Pop-up : si Image ou PDF : Preview
  if($navig || (strpos($file->file_type, "image") !== false || strpos($file->file_type, "pdf") !== false)) {
    //Popup avec seconde previsualisation
    
    //R�cup�ration de la liste des fichiers
    $listFiles = new CFile;
    $where = array();
    $where["file_class"] = "='$file->file_class'";
    $where["file_object_id"] = "= '$file->file_object_id'";
    $order = "nom,file_date";
    $leftjoin = array();
    $leftjoin["files_category"] = "files_mediboard.file_category_id=files_category.file_category_id";
    $listFiles = $listFiles->loadList($where, $order, null, null, $leftjoin);
    // R�cup�ration du fichier pr�c�dent et suivant
    $filePrev = 0;
    $fileNext = 0;
    $en_cours = 0;
    foreach($listFiles as $keyFile => $dataFile){
      if($keyFile == $file_id){
        $filePrev = $en_cours;
      }
      if($en_cours == $file_id){
        $fileNext = $keyFile;
      }  
      $en_cours = $keyFile;
    }
    
    //R�cup�ration de la cat�gorie du fichier en cours
    $listCat = new CFilesCategory;
    $listCat->load($file->file_category_id);
    
    //navigatino par pages (PDF)
    if($file->_nb_pages){
      if($sfn>$file->_nb_pages || $sfn<0){$sfn = 0;}
      if($sfn!=0){
        $page_prev = $sfn - 1; 
      }
      if($sfn<($file->_nb_pages-1)){
        $page_next = $sfn + 1;
      }
    }
    $smarty->assign("page_prev", $page_prev);
    $smarty->assign("page_next", $page_next);
    $smarty->assign("filePrev" , $filePrev);
    $smarty->assign("fileNext" , $fileNext);
    $smarty->assign("listCat"  , $listCat);
    $smarty->assign("sfn"      , $sfn);
    $smarty->display("inc_preview_file_popup.tpl");
  }else{
    header("Location: mbfileviewer.php?file_id=$file_id");
  }
}else{
  $smarty->display("inc_preview_file.tpl");
}
?>
