<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision: 7816 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

global $can;

$can->needsAdmin();

$do_optimize= CValue::get("do_optimize");

// Filtre sur les enregistrements
$itemEchangeHprim = new CEchangeHprim;

// Requ�tes
$where = array();
$where["compressed"] = "= '0'";
$where["acquittement"] = "IS NOT NULL ";

if (!$do_optimize) {
  $count = $itemEchangeHprim->countList($where);
  
  CAppUI::stepAjax($count." �changes HPRIM � optimiser");
} else {
  /*$order = "date_production DESC";
  
  // R�cup�ration de la liste des echanges HPRIM
  $listEchangeHprim = $itemEchangeHprim->loadList($where, $order, "0, 1000");
  $count  = 0;
  foreach($listEchangeHprim as $_echange_hprim) {
    $errors = 0;
    
    // Affectation de l'object_id et object_class
    $_echange_hprim->getObjectIdClass();
    if (!$errors) {
      if ($msg = $_echange_hprim->store()) {
        CAppUI::stepAjax("#$_echange_hprim->_id : Impossible � sauvegarder l'�change HPRIM", UI_MSG_WARNING);
        $msg = $_echange_hprim->delete();
        CAppUI::stepAjax("#$_echange_hprim->_id : Suppression de l'�change HPRIM", UI_MSG_WARNING);
        CAppUI::stepAjax($msg, UI_MSG_WARNING);
        
        continue;
      } 
    }

    // D�compression
    if (!$_echange_hprim->message = @gzuncompress($_echange_hprim->message)) {
      $errors++;
      CAppUI::stepAjax("#$_echange_hprim->_id : D�compression du message impossible", UI_MSG_WARNING);
    }
    if (!$_echange_hprim->acquittement = @gzuncompress($_echange_hprim->acquittement)) {
      $errors++;
      CAppUI::stepAjax("#$_echange_hprim->_id : D�compression de l'acquittement impossible", UI_MSG_WARNING);
    }

    if (!$errors) {
      $_echange_hprim->compressed = 1;
      if ($msg = $_echange_hprim->store()) {
        $errors++;
        CAppUI::stepAjax("#$_echange_hprim->_id : Impossible � d�compresser l'�change HPRIM", UI_MSG_WARNING);
        CAppUI::stepAjax($msg, UI_MSG_WARNING);
        
        continue;
      } else {
        $count++;
      }
    } else {
      $_echange_hprim->delete();
      CAppUI::stepAjax("#$_echange_hprim->_id : Suppression de l'�change HPRIM", UI_MSG_WARNING);
    }
  }
  if ($count == 0) {
    echo "<script type='text/javascript'>stop=true;</script>";
  }
  CAppUI::stepAjax($count. " �changes HPRIM d�compress�s et sauvegard�s");*/
}
?>