<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$context = CValue::get("context");

echo <<<HTML
<div class="small-info">
Vous n'�tes pas autoris� � acc�der <label title="$context">� cette information</label> !
</div>
HTML;


