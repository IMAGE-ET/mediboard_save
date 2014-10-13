{{*
 * $Id$
 *  
 * @category Admin
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}


<h2>Liste des acc�s � ce s�jour</h2>
<table class="tbl">
  <tr>
    <td colspan="4">
      {{mb_include module=system template=inc_pagination total=$total current=$page step=$step change_page="refreshListMA"}}
    </td>
  </tr>
  <tr>
    <th>{{mb_title class=CLogAccessMedicalData field=user_id}}</th>
    <th>{{mb_title class=CMediusers field=function_id}}</th>
    <th colspan="2">Date et heure d'acces</th>
  </tr>
  <tbody>
    {{foreach from=$list item=_access}}
      <tr>
        <td>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_access->_ref_user}} ({{$_access->_ref_user->_ref_function}})</td>
        <td>{{mb_include module=mediusers template=inc_vw_function function=$_access->_ref_user->_ref_function}}</td>
        <td class="narrow">{{mb_value object=$_access field=datetime}}</td>
        <td class="narrow">({{mb_value object=$_access field=datetime format=relative}})</td>
      </tr>
    {{foreachelse}}
      <tr>
        <td class="empty" colspan="3">{{tr}}CLogAccessMedicalData.none{{/tr}}</td>
      </tr>
    {{/foreach}}
    <tr>
      <td colspan="4">
        {{mb_include module=system template=inc_pagination total=$total current=$page step=$step change_page="refreshListMA"}}
      </td>
    </tr>
  </tbody>
</table>