<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPstock
* @version $Revision: $
* @author Fabien M�nager
*/

global $AppUI;

$do = new CDoObjectAddEdit('CProduct', 'product_id');
$do->createMsg = 'Produit cr��';
$do->modifyMsg = 'Produit modifi�';
$do->deleteMsg = 'Produit supprim�';
$do->doIt();

?>