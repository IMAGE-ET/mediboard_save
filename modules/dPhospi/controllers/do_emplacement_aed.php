<?php /* $Id: do_emplacement_aed.php $ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision: 8216 $
* @author Aur�lie Hebras
*/

$do = new CDoObjectAddEdit("CEmplacement", "emplacement_id");
$do->doIt();
?>