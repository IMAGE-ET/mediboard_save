<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author S�bastien Fillonneau
*/

$do = new CDoObjectAddEdit("CExamPossum", "exampossum_id");
$do->redirect = null;
$do->doIt();
?>