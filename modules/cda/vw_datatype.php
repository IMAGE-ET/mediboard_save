<?php 

/**
 * $Id$
 *
 * Vue des datatypes pr�sent dans les CDA
 *  
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */

$listtypes = CCdaTools::returnType("modules/cda/resources/datatypes-base_original.xsd");

//template
$smarty = new CSmartyDP();

$smarty->assign("listTypes", $listtypes);

$smarty->display("vw_datatype.tpl");