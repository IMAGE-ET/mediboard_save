<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$chir_id     = CValue::get("chir_id");
$function_id = CValue::get("function_id");

$function = new CFunctions();
$function->load($function_id);

$chir = new CMediusers();
$chir->load($chir_id);

$protocole = new CProtocole();
$protocole->chir_id = $chir_id ? $chir_id : null;
$protocole->function_id = $function_id ? $function_id : null;

/** @var CProtocole[] $protocoles */
$protocoles = $protocole->loadMatchingList("libelle");

if (!$function->_id) {
  $function = $chir->loadRefFunction();
}

$csv = new CCSVFile();

$line = array(
  "Nom de la fonction",
  "Nom du praticien",
  "Pr�nom du praticien",
  "Motif d'hospitalisation",
  "Libell� du s�jour",
  "Dur�e d'intervention",
  "Actes CCAM",
  "Diagnostic",
  "Type d'hospitalisation",
  "Dur�e d'hospitalisation",
  "Dur�e USCPO",
  "Dur�e pr�op",
  "Pr�sence pr�op",
  "Pr�sence postop",
  "UF d'h�bergement",
  "UF de soins",
  "UF m�dicale",
  "Facturable",
  "M�dical",
  "Exam. extempo. pr�vu",
  "C�t�",
  "Bilan pr�op",
  "Mat�riel � pr�voir",
  "Examens per-op",
  "D�passement d'honoraires",
  "Forfait clinique",
  "Fournitures",
  "Remarques sur l'intervention",
  "Convalescence",
  "Remarques sur le s�jour",
  "Septique",
  "Dur�e en heure d'hospitalisation",
  "Pathologie",
  "Type de prise en charge"
);
$csv->writeLine($line);

CMbObject::massLoadFwdRef($protocoles, "chir_id");
CMbObject::massLoadFwdRef($protocoles, "function_id");

foreach ($protocoles as $_protocole) {
  $_protocole->loadRefUfHebergement();
  $_protocole->loadRefUfMedicale();
  $_protocole->loadRefUfSoins();
  $_protocole->loadRefChir();
  $_protocole->loadRefFunction();

  $_line = array(
    $_protocole->_ref_function->text,
    $_protocole->_ref_chir->_user_last_name,
    $_protocole->_ref_chir->_user_first_name,
    $_protocole->libelle,
    $_protocole->libelle_sejour,
    CMbDT::transform($_protocole->temp_operation, null, "%H:%M"),
    $_protocole->codes_ccam,
    $_protocole->DP,
    $_protocole->type,
    $_protocole->duree_hospi,
    $_protocole->duree_uscpo,
    $_protocole->duree_preop ? CMbDT::transform($_protocole->duree_preop, null, "%H:%M") : "",
    $_protocole->presence_preop ? CMbDT::transform($_protocole->presence_preop, null, "%H:%M") : "",
    $_protocole->presence_postop ? CMbDT::transform($_protocole->presence_postop, null, "%H:%M") : "",
    $_protocole->_ref_uf_hebergement->code,
    $_protocole->_ref_uf_medicale->code,
    $_protocole->_ref_uf_soins->code,
    $_protocole->facturable,
    $_protocole->for_sejour,
    $_protocole->exam_extempo,
    $_protocole->cote,
    $_protocole->examen,
    $_protocole->materiel,
    $_protocole->exam_per_op,
    $_protocole->depassement,
    $_protocole->forfait,
    $_protocole->fournitures,
    $_protocole->rques_operation,
    $_protocole->convalescence,
    $_protocole->rques_sejour,
    $_protocole->septique,
    $_protocole->duree_heure_hospi,
    $_protocole->pathologie,
    $_protocole->type_pec
  );
  $csv->writeLine($_line);
}

$csv->stream("export-protocoles-".($chir_id ? $chir->_view : $function->text));
