<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPqualite
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI;


$do = new CDoObjectAddEdit("CChapitreDoc", "doc_chapitre_id");
$do->createMsg = "Chapitre cr��";
$do->modifyMsg = "Chapitre modifi�";
$do->deleteMsg = "Chapitre supprim�";
$do->doIt();

?>