<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author Fabien M�nager
*/

if (isset($_POST['date']) && $_POST['date'] == 'now') {
  $_POST['date'] = mbDateTime();
}

$do = new CDoObjectAddEdit('CReglement', 'reglement_id');

$do->doIt();
?>