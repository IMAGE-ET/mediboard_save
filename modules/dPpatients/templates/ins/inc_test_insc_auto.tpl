{{*
 * $Id$
 *  
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}


<table class="tbl">
  <tr>
    <th colspan="2" class="title">
      {{tr}}Total{{/tr}} : {{$result.total}}
    </th>
  </tr>
  <tr>
    <th>
      {{tr}}OK{{/tr}}
    </th>
    <th>
      {{tr}}Error{{/tr}}
    </th>
  </tr>
  <tr>
    <td>
      {{$result.correct}}
    </td>
    <td>
      {{$result.incorrect}}
    </td>
  </tr>
</table>
