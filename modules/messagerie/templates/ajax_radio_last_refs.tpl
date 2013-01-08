{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage messagerie
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script>
  checkrelation = function() {
    console.log(this);
    $("do_link_attachments").show();
  };
</script>

<form>
  <ul style="text-align: left;">
    <li><strong>Sejours</strong></li>
  {{foreach from=$patient->_ref_sejours item=_sejour}}
    <li><input type="radio" name="object"/> {{$_sejour}}</li>
    {{foreach from=$_sejour->_ref_operations item=_op}}
      <li style="margin-left:15px; padding-left: 15px; border-left: solid 1px grey;">
        <input type="radio" name="object" onclick="checkrelation()"/>
        <span onmouseover="ObjectTooltip.createEx(this, '{{$_op->_guid}}')">
                  Intervention le {{mb_value object=$_op field=_datetime}}
                </span>
        avec le Dr {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_op->_ref_chir}}
        {{if $_op->annulee}}<span style="color: red;">[ANNULE]</span>{{/if}}
      </li>
    {{foreachelse}}
      <li class="empty">{{tr}}COperation.none{{/tr}}</li>
    {{/foreach}}

    {{foreach from=$_sejour->_ref_consultations item=_consult}}
      <li style="margin-left:15px; padding-left: 15px; border-left: solid 1px grey;">
        <input type="radio" name="object" onclick="checkrelation()"/>
              <span onmouseover="ObjectTooltip.createEx(this, '{{$_consult->_guid}}')">
              Consultation le  {{mb_value object=$_consult field=_datetime}}
              </span>
        avec le Dr {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_consult->_ref_chir}}
        {{if $_consult->annule}}<span style="color: red;">[ANNULE]</span>{{/if}}
      </li>
    {{/foreach}}

  {{foreachelse}}
    <li class="empty">{{tr}}CSejour.none{{/tr}}</li>
  {{/foreach}}

  <li><strong>Consultations</strong></li>
  {{foreach from=$patient->_ref_consultations item=_consult}}
    <li>
      <input type="radio" name="object" onclick="checkrelation()"/>
      <span onmouseover="ObjectTooltip.createEx(this, '{{$_consult->_guid}}')">
              Consultation le  {{mb_value object=$_consult field=_datetime}}
            </span>
      avec le Dr {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_consult->_ref_chir}}
      {{if $_consult->annule}}<span style="color: red;">[ANNULE]</span>{{/if}}
    </li>
    {{foreachelse}}
    <li class="empty">{{tr}}CConsultation.none{{/tr}}</li>
  {{/foreach}}
  </ul>
</form>

