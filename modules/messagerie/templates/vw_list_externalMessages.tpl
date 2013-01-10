{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage messagerie
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=messagerie script=UserEmail}}


{{if count($mails)}}
<script type="text/javascript">
  Main.add(function () {
    var tabs = Control.Tabs.create("tab-mail", true);
    tabs.activeLink.onmousedown();
    //messagerie.getLastMessages({{$user->_id}}, null);
  });
</script>
{{/if}}

<div>
<button class="button change" onclick="messagerie.getLastMessages({{$user->_id}});">{{tr}}CUserMAil-button-getNewMails{{/tr}}</button>
<button class="button mail" onclick="messagerie.modalwriteMessage();">{{tr}}CUserMAil-button-writeMail{{/tr}}</button>

  <select style="width: 50px;" name="action">
    <option va>{{tr}}CUserMail-option-More{{/tr}}</option>
    <option value="">{{tr}}CUserMail-option-allmarkasread{{/tr}}</option>
  </select>
</div>
<table class="main" id="list_external_mail">
  <tr>
    <td style="width:200px;">
      <ul id="tab-mail" class="control_tabs_vertical">
      {{foreach from=$mails key=k item=_mail}}
        <li>
          <a href="#{{$k}}" style="white-space: nowrap;" onmousedown="messagerie.refreshList('{{$k}}')"
            {{if count($_mail)==0}}class="empty"{{/if}}>{{$_mail}}
          </a>
        </li>
      {{foreachelse}}
        <li>{{tr}}CUserMail-noAccount{{/tr}}</li>
      {{/foreach}}
      </ul>
    </td>
    <td>
      {{foreach from=$mails key=k item=_list}}
        <table id="{{$k}}" class="tbl" style="display: none;"></table>
      {{/foreach}}

    </td>
  </tr>
</table>


