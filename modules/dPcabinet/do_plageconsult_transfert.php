<?php /* $Id: do_plageconsult_aed.php 2083 2007-06-18 15:27:36Z MyttO $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: 2083 $
* @author Thomas Despoix
*/

// Filtre des plages
$chir_id = $_POST['_old_chir_id'];
$where["chir_id"] = "= '$chir_id'";

if ($date_min = mbGetValueFromPost("_date_min")) {
  $where[] = "date >= '$date_min'";
}

if ($date_max = mbGetValueFromPost("_date_max")) {
  $where[] = "date <= '$date_max'";
}

// Chargement des plages
$plage = new CPlageconsult();
$plages = $plage->loadList($where);

foreach ($plages as $_plage) {
  $_POST["plageconsult_id"] = $_plage->_id;
  
  // Calcul de collisions
  $_POST["date"] = $_plage->date;

  // Do it !
  $do = new CDoObjectAddEdit("CPlageconsult", "plageconsult_id");
	$do->redirect = null;
  $do->doIt();
}

// Redirection finale
$do->redirect = "m=$m&a=transfert_plageconsult&dialog=1";
$do->doRedirect();
?>