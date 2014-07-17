{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 7494 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<tr>
  <th>{{mb_label object=$source field="libelle"}}</th>
  <td>{{mb_field object=$source field="libelle" size="50"}}</td>
</tr>
<tr>
  <th>{{mb_label object=$source field="active"}}</th>
  <td>{{mb_field object=$source field="active"}}</td>
</tr>
<tr {{if !$can->admin}}style="display:none;"{{/if}}>
  <th>{{mb_label object=$source field="name"}}</th>
  <td><input type="text" readonly="readonly" name="name" value="{{$source->name}}" size="50"/></td>
</tr>
<tr {{if !$can->admin}}style="display:none;"{{/if}}>
  <th>{{mb_label object=$source field="role"}}</th>
  <td>{{mb_field object=$source field="role" typeEnum="radio"}}</td>
</tr>
<tr {{if !$can->admin}}style="display:none;"{{/if}}>
  <th>{{mb_label object=$source field="loggable"}}</th>
  <td>{{mb_field object=$source field="loggable"}}</td>
</tr>
<tr>
  <th>{{mb_label object=$source field="host"}}</th>
  <td>{{mb_field object=$source field="host"}}</td>
</tr>

