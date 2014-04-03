<?php

/**
 * $Id$
 *
 * @category search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */


CConfiguration::register(
  array("CGroups" => array(
      "search" => array(
        "active_handler" => array(
          "active_handler_search" => "bool default|0",
        ),
        "indexing" => array(
          "active_indexing"          => "bool default|0",
        )
      )
    )
  )
);