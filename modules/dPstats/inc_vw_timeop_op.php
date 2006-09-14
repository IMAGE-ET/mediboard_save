<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision: $
* @author Romain Ollivier
*/

$codeCCAM   = strtoupper(mbGetValueFromGetOrSession("codeCCAM", ""));
$prat_id    = mbGetValueFromGetOrSession("prat_id", 0);

$total["nbInterventions"] = 0;
$total["estim_moy"] = 0;
$total["estim_somme"] = 0;
$total["occup_moy"] = 0;
$total["occup_somme"] = 0;
$total["duree_moy"] = 0;
$total["duree_somme"] = 0;


$listTemps = new CTempsOp;

$where = array();
if($prat_id) {
  $where["chir_id"] = "= '$prat_id'";
} elseif(count($listPrats)) {
  $where["chir_id"] = "IN (".implode(",", array_keys($listPrats)).")";
} else {
  $where[] = "0 = 1";
}

if($codeCCAM) {
  $codeCCAM = trim($codeCCAM);
  $listCodeCCAM=explode(" ",$codeCCAM);
  $listCodeCCAM=array_filter($listCodeCCAM);
  foreach($listCodeCCAM as $keyccam => $code){
    $where[] = "ccam LIKE '%$code%'";
  }
}

$ljoin = array();
$ljoin["users"] = "users.user_id = temps_op.chir_id";

$order = "users.user_last_name ASC, users.user_first_name ASC, ccam";

$listTemps = $listTemps->loadList($where, $order, null, null, $ljoin);


if($codeCCAM) {
  // Groupement des donn�es par chirurgien
  $old_chir = 0;
  $TempsOperatoire = array();
  
  foreach($listTemps as $keyTemps => $temps) {    
    if($old_chir != $temps->chir_id) {
      // Si on change de chirurgien, alors on initialise la variable
      $old_temps_id = $temps->temps_op_id;
      $TempsOperatoire[$temps->chir_id] = new CTempsOp();
      $TempsOperatoire[$temps->chir_id]->chir_id = $temps->chir_id;
      $TempsOperatoire[$temps->chir_id]->nb_intervention = 0;
      $TempsOperatoire[$temps->chir_id]->occup_moy = 0;
      $TempsOperatoire[$temps->chir_id]->duree_moy = 0;
      $TempsOperatoire[$temps->chir_id]->estimation = 0;
      $TempsOperatoire[$temps->chir_id]->duree_ecart = "-";
      $TempsOperatoire[$temps->chir_id]->occup_ecart = "-";
      $TempsOperatoire[$temps->chir_id]->ccam= $codeCCAM;
    }
    
    $TempsOperatoire[$temps->chir_id]->nb_intervention += $temps->nb_intervention;
    $TempsOperatoire[$temps->chir_id]->occup_moy += $temps->nb_intervention * strtotime($temps->occup_moy);
    $TempsOperatoire[$temps->chir_id]->duree_moy += $temps->nb_intervention * strtotime($temps->duree_moy);
    $TempsOperatoire[$temps->chir_id]->estimation += $temps->nb_intervention * strtotime($temps->estimation);

    $old_chir = $temps->chir_id;    
  }
  $listTemps=$TempsOperatoire;
}


foreach($listTemps as $keyTemps => $temps) {
  if($codeCCAM) {
    $temps->occup_moy = strftime("%H:%M:%S",$temps->occup_moy / $temps->nb_intervention);
    $temps->estimation = strftime("%H:%M:%S",$temps->estimation / $temps->nb_intervention);
    $temps->duree_moy = strftime("%H:%M:%S",$temps->duree_moy / $temps->nb_intervention); 
  }
  $listTemps[$keyTemps]->loadRefsFwd();
  $total["nbInterventions"] += $temps->nb_intervention;
  $total["occup_somme"] += $temps->nb_intervention * strtotime($temps->occup_moy);
  $total["duree_somme"] += $temps->nb_intervention * strtotime($temps->duree_moy);
  $total["estim_somme"] += $temps->nb_intervention * strtotime($temps->estimation);
}
if($total["nbInterventions"]!=0){
  $total["occup_moy"] = $total["occup_somme"] / $total["nbInterventions"];
  $total["duree_moy"] = $total["duree_somme"] / $total["nbInterventions"];
  $total["estim_moy"] = $total["estim_somme"] / $total["nbInterventions"];
}
?>
