<?php

//$catalogue_item = new CFacturecatalogueitem;
//$catalogue_list = $catalogue_item->loadList();

// Cr�ation du template
$smarty = new CSmartyDP();

//$smarty->assign("catalogue_list", $catalogue_list);

$smarty->display("vw_idx_facturecatalogueitem.tpl");

?>