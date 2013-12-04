<?php

/**
 * Configurations du module
 *  
 * @category Maternite
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

$dPconfig["maternite"] = array(
  "days_terme" => "5",
  "duree_sejour" => "3",
  "CGrossesse" =>array(
    "date_regles_obligatoire" => "0",
    "manage_provisoire" => "1"
  ),
  "CNaissance" => array(
    "num_naissance" => "1",
  )
);
