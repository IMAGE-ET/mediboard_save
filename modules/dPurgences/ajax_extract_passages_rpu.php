<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 7212 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkAdmin();

ini_set("memory_limit", "512M");

$debut_selection = CValue::get("debut_selection");
$fin_selection   = CValue::get("fin_selection");

if (!$debut_selection || !$fin_selection) {
  $fin_selection   = CMbDT::date()." 00:00:00";
  $debut_selection = CMbDT::date("-7 DAY", $fin_selection)." 00:00:00";
}

$extractPassages = new CExtractPassages();
$extractPassages->date_extract    = CMbDT::dateTime();
$extractPassages->type            = "rpu";
$extractPassages->debut_selection = $debut_selection;
$extractPassages->fin_selection   = $fin_selection;
$extractPassages->group_id        = CGroups::loadCurrent()->_id;
$extractPassages->store();

$doc_valid = null;

$where = array();
$where['sejour.type'] = " = 'urg'";
$where['sejour.annule'] = " = '0'";
$where['sejour.entree'] = " BETWEEN '$debut_selection' AND '$fin_selection' "; 
$where['sejour.group_id'] = " = '".CGroups::loadCurrent()->_id."'";

$leftjoin = array();
$leftjoin['sejour'] = 'sejour.sejour_id = rpu.sejour_id';

$order = "entree ASC";

$rpu = new CRPU();
$rpus = $rpu->loadList($where, $order, null, null, $leftjoin);

if (count($rpus) == 0) {
  CAppUI::stepAjax("Aucun RPU � extraire.", UI_MSG_ERROR);
}

foreach ($rpus as $_rpu) {
  $sejour = $_rpu->_ref_sejour;
  $sejour->loadExtDiagnostics();
  $sejour->loadRefDossierMedical();
  $sejour->loadDiagnosticsAssocies(false);
  $sejour->loadRefsConsultations();
}

// Appel de la fonction d'extraction du RPUSender
$rpuSender = $extractPassages->getRPUSender();
if (!$rpuSender) {
	CAppUI::stepAjax("Aucun sender d�finit dans le module dPurgences.", UI_MSG_ERROR);
}
$extractPassages = $rpuSender->extractRPU($extractPassages, $rpus);

CAppUI::stepAjax("Extraction de ".count($rpus)." RPUs du ".CMbDT::dateToLocale($debut_selection)." au ".CMbDT::dateToLocale($fin_selection)." termin�e.", UI_MSG_OK);
if (!$extractPassages->message_valide)
  CAppUI::stepAjax("Le document produit n'est pas valide.", UI_MSG_WARNING);
else 
  CAppUI::stepAjax("Le document produit est valide.", UI_MSG_OK);

foreach ($rpus as $_rpu) {
  $rpu_passage = new CRPUPassage();
  $rpu_passage->rpu_id = $_rpu->_id;
  $rpu_passage->extract_passages_id = $extractPassages->_id;
  $rpu_passage->store();
}

echo "<script type='text/javascript'>extract_passages_id = $extractPassages->_id;</script>";
 
?>