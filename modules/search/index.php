<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage search
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab('vw_search'                   , TAB_READ);
$module->registerTab('vw_cartographie_mapping'     , TAB_ADMIN);
$module->registerTab('vw_search_log'               , TAB_ADMIN);
$module->registerTab('vw_search_thesaurus'         , TAB_READ);