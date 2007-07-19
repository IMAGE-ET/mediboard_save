<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Romain OLLIVIER
*/

global $AppUI, $can, $m;

$can->needsRead();
$ds = CSQLDataSource::get("std");
$sql = "SELECT * FROM import_praticiens";
$listImport = $ds->loadlist($sql);

$new = 0;
$link = 0;

foreach($listImport as $key => $value) {
  $sql = "SELECT * FROM users, users_mediboard" .
  		"\nWHERE users.user_id = users_mediboard.user_id" .
  		"\nAND users.user_first_name = '".trim($value["prenom"])."'" .
  		"\nAND users.user_last_name = '".trim($value["nom"])."'";
  $match = $ds->loadlist($sql);
  if(!count($match)) {
  	$user = new CMediusers;
  	// DB Table key
  	$user->user_id = '';
  	// DB Fields
    $user->remote = 0;
    // DB References
    // On prend la fonction ORL
	$user->function_id = 11;
    // dotProject user fields
    $user->_user_type = 3;
	$user->_user_username = trim($value["nom"]);
	$user->_user_password = "nevousconnectezpassvp";
	$user->_user_first_name = trim($value["prenom"]);
	$user->_user_last_name  = trim($value["nom"]);
	$user->store();
	$sql = "UPDATE import_praticiens" .
    		"\nSET mb_id = '".$user->user_id."'" .
    		"\nWHERE praticien_id = '".$value["praticien_id"]."'";
    $ds->exec($sql);
    $new++;
  } else {
    $sql = "UPDATE import_praticiens" .
    		"\nSET mb_id = '".$match[0]["user_id"]."'" .
    		"\nWHERE praticien_id = '".$value["praticien_id"]."'";
    $ds->exec($sql);
    $link++;
  }
}

echo '<p>Op�ration termin�e.</p>';
echo '<p>'.$new.' �l�ments cr��s, '.$link.' �l�ments li�s.</p><hr>';

?>