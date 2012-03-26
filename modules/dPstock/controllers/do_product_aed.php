<?php /* $Id: do_product_aed.php 7964 2010-02-01 17:08:23Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 7964 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$do = new CDoObjectAddEdit('CProduct');

if (CValue::post("_duplicate")) {
  $do->doBind();
  $product = $do->_objBefore;
  $product->code .= "-copie";
  $product->name .= " (Copie)";
  $product->_id = null;
  
  if ($msg = $product->store()) {
    CAppUI::setMsg($msg);
  }
  else {
    // Redirection vers le nouveau 
    $_GET["product_id"] = $product->_id;
  }
}
else {
  $do->doIt();
}
