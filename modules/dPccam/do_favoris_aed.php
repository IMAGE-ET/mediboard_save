<?php /* $Id: do_favoris_aed.php,v 1.5 2005/10/04 10:56:49 rhum1 Exp $ */

/**
* @package Mediboard
* @subpackage dPccam
* @version $Revision: 1.5 $
* @author Romain Ollivier
*/

require_once($AppUI->getModuleClass("dPccam", "dPccam"));
require_once($AppUI->getSystemClass('doobjectaddedit'));

$do = new CDoObjectAddEdit("CFavoriCCAM", "favoris_id");
$do->createMsg = "Favori cr��";
$do->modifyMsg = "Favori modifi�";
$do->deleteMsg = "Favori supprim�";
$do->doIt();

?>