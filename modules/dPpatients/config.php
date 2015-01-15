<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$dPconfig["dPpatients"] = array (
  "CPatient"    => array (
    "tag_ipp"           => "",
    "tag_ipp_group_idex"=> "",
    "tag_ipp_trash"     => "trash_",
    "tag_conflict_ipp"  => "conflict_",
    "multi_group"       => "limited",
    "function_distinct" => "0",
    "extended_print"    => "0",
    "adult_age"         => "15",
    "limit_char_search" => "0",
    "check_code_insee"  => "1",
  ),

  "CConstantesMedicales" => array(
    "unite_ta"        => "cmHg",
    "unite_glycemie"  => "g/l",
    "unite_cetonemie" => "g/l",
  ),

  "CAntecedent" => array (
    "types"     => "med|alle|trans|obst|chir|fam|anesth|gyn",
    "mandatory_types" => "",
    "appareils" => "cardiovasculaire|digestif|endocrinien|neuro_psychiatrique|pulmonaire|uro_nephrologique",
  ),

  "CTraitement" => array (
    "enabled" => "1",
  ),

  "CDossierMedical" => array (
  ),

  "CMedecin" => array(
   "medecin_strict" => "0",
  ),

  "imports" => array(
    "pat_csv_path" => "",
    "pat_start" => 0,
    "pat_count" => 20,
  ),

  "INSEE" => array(
   "france" => "1",
   "suisse" => "0",
  ),
);
