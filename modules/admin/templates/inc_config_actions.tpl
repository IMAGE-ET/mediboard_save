{{* $Id: configure.tpl 10842 2010-12-08 21:57:35Z MyttO $ *}}

{{*
 * @package Mediboard
 * @subpackage admin
 * @version $Revision: 10842 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}


<table class="tbl">
  <tr>
    <th>{{tr}}Classname{{/tr}}</th>
    <th>{{tr}}Action{{/tr}}</th>
  </tr>
  
  <tr>
    <td>
      {{tr}}CUser{{/tr}}
    </td>
    <td>
      <script type="text/javascript">
        CUser = {
          checkSiblings: function () {
            new Url('admin', 'check_siblings').requestModal();
          }
        }
      </script>
      <button class="tick" onclick="CUser.checkSiblings()">{{tr}}mod-admin-action-check_siblings{{/tr}}</button>
    </td>
  </tr>

</table>