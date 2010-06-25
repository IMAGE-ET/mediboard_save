<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPcabinet
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$dPconfig["dPcabinet"] = array(
  "keepchir" => 1,
  "CPlageconsult" => array (
    "hours_start"      => "8",
    "hours_stop"       => "20",
    "minutes_interval" => "15",
  ),
  "CConsultation" => array(
    "show_examen"           => 1,
    "show_histoire_maladie" => 0,
    "show_conclusion"       => 0,
    "attach_consult_sejour" => 0,
    "create_consult_sejour" => 0,
    "minutes_before_consult_sejour" => 1,
    "hours_after_changing_prat" => 0,
    "aide_autocomplete"     => 0
  ),
  "CConsultAnesth" => array(
    "feuille_anesthesie" => "print_fiche",
    /* Format des champs auto :
     * %N - Nom praticien interv
     * %P - Pr�nom praticien interv
     * %S - Initiales praticien interv
     * %L - Libell� intervention
     * %I - Jour intervention
     * %i - Heure intervention
     * %E - Jour d'entr�e
     * %e - Heure d'entr�e
     * %T - Type de s�jour (A, O, E...)
     */
    "format_auto_motif"  => "Pr�-an�sth. %I %L %S",
    "format_auto_rques"  => "%T %E %e",
    "view_premedication" => "0",
    "show_facteurs_risque" => "0",
    "show_mallampati" => "0"
  ),
  "CPrescription" => array (
    "view_prescription" => "0"
  )
);

?>