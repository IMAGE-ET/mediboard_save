<?php /* $Id: do_affectation_split.php,v 1.3 2005/07/01 08:55:59 rhum1 Exp $ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision: 1.3 $
* @author Thomas Despoix
*/

global $AppUI, $m;

require_once($AppUI->getModuleClass($m, "affectation"));
require_once($AppUI->getSystemClass("doobjectaddedit"));

$entree = $_POST["entree"];
$sortie = $_POST["sortie"];

// Modifier la premi�re affectation
$do = new CDoObjectAddEdit("CAffectation", "affectation_id");
$do->createMsg = "Affectation cr��e";
$do->modifyMsg = "Affectation modifi�e";
$do->deleteMsg = "Affectation supprim�e";

$_POST["entree"] = $entree;
$_POST["sortie"] = $_POST["_date_split"];

$do->redirectStore = null;
$do->doIt();

// Cr�er le second
$do = new CDoObjectAddEdit("CAffectation", "affectation_id");
$do->createMsg = "Affectation cr��e";
$do->modifyMsg = "Affectation modifi�e";
$do->deleteMsg = "Affectation supprim�e";

$_POST["entree"] = $_POST["_date_split"];
$_POST["sortie"] = $sortie;
$_POST["lit_id"] = $_POST["_new_lit_id"] ;
$_POST["affectation_id"] = null;

$do->redirectStore = "m={$m}";
$do->doIt();

?>