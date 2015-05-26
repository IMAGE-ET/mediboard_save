<?php
/**
 * $Id:$
 *  
 * @category SalleOp
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision:$
 * @link     http://www.mediboard.org
 */

CConfiguration::register(
  array(
    "CGroups" => array(
      "dPsalleOp" => array(
        "COperation" => array(
          "use_sortie_reveil_reel" => "bool default|0",
          "use_tto"                => "bool default|0",
          "password_sortie"        => "bool default|0",
          "use_time_vpa"           => "bool default|0"
        ),
        "hors_plage" => array(
          "type_anesth"         => "bool default|0",
          "heure_entree_sejour" => "bool default|0",
        ),
        "CDailyCheckList" => array(
          "choose_moment_edit"  => "bool default|0",
        )
      )
    )
  )
);