{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl">
  <tr>
    <th>{{mb_title class=CUser field=user_username}}</th>
    <th>{{tr}}CUser{{/tr}}</th>
  </tr>
  
  {{foreach from=$siblings key=user_name item=users}}
  <tr>
    <td>{{$user_name}}</td>
    <td>
      {{foreach from=$users item=_user}}
      	<a href="?m=admin&amp;tab=vw_edit_users&amp;user_id={{$_user->_id}}">{{$_user}}</a>
			{{/foreach}}
    </td>
  </tr>
  {{foreachelse}}
  <tr>
    <td class="empty" colspan="2">{{tr}}CUser-message-nosiblings{{/tr}}</td>
  </tr>
	{{/foreach}}

</table>
