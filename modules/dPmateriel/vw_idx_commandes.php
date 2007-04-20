<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPmateriel
 *	@version $Revision: $
 *  @author Romain Ollivier
 */
 
global $AppUI, $can, $m;

$can->needsRead();

$commande_id = mbGetValueFromGetOrSession("commande_materiel_id");

// Chargement de la commande demand�
$commande = new CCommandeMateriel();
$commande->load($commande_id);

//Chargement de toutes les commandes
$where = array();
$listCommandes = $commande->loadList($where);
foreach($listCommandes as &$curr_comm) {
  $curr_comm->loadRefsFwd();
}

//Liste des r�f�rences
$reference = new CRefMateriel();
$listReferences = $reference->loadList();

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("commande"      , $commande     );
$smarty->assign("listCommandes" , $listCommandes);
$smarty->assign("listReferences", $listReferences);

$smarty->display("vw_idx_commandes.tpl");
?>
