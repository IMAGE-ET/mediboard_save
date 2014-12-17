<?php
/**
 * $Id$
 *  
 * @category Cabinet
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CConfiguration::register(
  array(
    "CGroups" => array(
      "dPcabinet" => array(
        "CPrescription" => array(
          "view_prescription"         => "bool default|0",
          "view_prescription_externe"  => "bool default|0"
        ),
        "CAntecedent" => array(
          "show_form_add_atcd" => "bool default|0"
        )
      )
    )
  )
);