<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Stock
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$reception_id = CValue::get('reception_id');
$lot_id = CValue::get('lot_id');
$force_print  = CValue::get('force_print');

$reception = new CProductReception();
if ($reception_id) {
  $reception->load($reception_id);
  $reception->loadRefsFwd();
  $reception->loadRefsBack();
}

$pdf = new CMbPdf(); 

$pdf->setFont("vera", '', "10");

// D�finition des marges de la pages
//$pdf->SetMargins(15, 15);

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Creation d'une nouvelle page
$pdf->AddPage();

if ($reception->_id) {
  $lots = $reception->_ref_reception_items;
}
else {
  $lot = new CProductOrderItemReception;
  $lot->load($lot_id);
  $lots = array($lot);
}

$data = array();
$j = 0;

foreach ($lots as &$item) {
  $item->loadRefsBack();
  $item->loadRefsFwd();
  $item->_ref_order_item->loadReference();
  
  $reference = $item->_ref_order_item->_ref_reference;
  $reference->loadRefsFwd();
  $reference->_ref_product->loadRefsFwd();
  
  if (!$item->barcode_printed || $force_print) {
    for ($i = 0; $i < $item->quantity / $reference->quantity; $i++) {
      $data[$j] = array();
      $d = &$data[$j];
      
      $lines = explode("\n", wordwrap($reference->_ref_product->name, 30, "\n", true));
      $d[] = $lines[0];
      $d[] = isset($lines[1]) ? $lines[1] : "";
      $d[] = $reference->_ref_product->code;
      $d[] = "LOT $item->code  PER $item->lapsing_date";
      
      $d[] = array(
        'barcode' => "MB".str_pad($item->_id, 8, "0", STR_PAD_LEFT),
        'type'    => 'C128B'
      );
      $j++;
    }
  }
}

$pdf->WriteBarcodeGrid(8, 8, 210-16, 297-16, 3, 10, $data);

// Nom du fichier: prescription-xxxxxxxx.pdf   / I : sortie standard
$pdf->Output("barcodes.pdf", "I");
