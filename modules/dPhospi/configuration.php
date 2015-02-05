<?php 

/**
 * $Id$
 *  
 * @category dPhospi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CConfiguration::register(
  array(
    "CGroups" => array(
      "dPhospi" => array(
        "CAffectation" => array(
          "create_affectation_tolerance" => "num min|2 max|120 default|2",
        ),
        "vue_tableau" =>  array(
          "show_labo_results" => "bool default|1",
        ),
        "vue_tempo" =>  array(
          "show_imc_patient" => "bool default|0",
        )
      )
    ),

    "CService CGroups.group_id" => array(
      "dPhospi" => array(
        "vue_temporelle" => array(
          "hour_debut_day" => "num min|0 max|23 default|0",
          "hour_fin_day"   => "num min|0 max|23 default|23",
        ),
      )
    ),
  )
);