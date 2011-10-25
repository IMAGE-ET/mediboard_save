<?php /* $Id: httpreq_do_add_insee.php 2342 2007-07-19 14:24:59Z mytto $ */

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision$
* @author SARL OpenXtrem
*/

$dPconfig["dPsalleOp"] = array(
  "mode_anesth"     => "0",
  "max_add_minutes" => "10",
  "max_sub_minutes" => "30",
  "COperation"      => array(
    "mode"        => "0",
    "modif_salle" => "0",
    "modif_actes" => "oneday",
  ),
  "CActeCCAM"       => array(
    "contraste"               => "0",
    "alerte_asso"             => "1",
    "tarif"                   => "0",
    "codage_strict"           => "0",
    "signature"               => "0",
    "openline"                => "0",
    "modifs_compacts"         => "0",
    "commentaire"             => "1",
    "envoi_actes_salle"       => "0",
    "envoi_motif_depassement" => "1",
  ),
  "CDossierMedical" => array (
    "DAS" => "0",
  ),
  "CReveil"         => array (
    "multi_tabs_reveil" => "1",
  ),
  "CDailyCheckList" => array(
    "active"              => "0",
    "active_salle_reveil" => "0",
    "default_good_answer_COperation"      => "0",
    "default_good_answer_CBlocOperatoire" => "0",
    "default_good_answer_CSalle"          => "0",
  ),
);