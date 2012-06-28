{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module="system" script="object_selector"}}

<form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="dosql" value="do_personnel_aed" />
<input type="hidden" name="personnel_id" value="{{$personnel->_id}}" />
<input type="hidden" name="del" value="0" />

<table class="form">
  <tr>
  {{if $personnel->_id}}
    <th class="title modify" colspan="2">
      {{mb_include module=system template=inc_object_idsante400 object=$personnel}}
      {{mb_include module=system template=inc_object_history object=$personnel}}
      {{tr}}CPersonnel-title-modify{{/tr}} '{{$personnel}}'
    </th>
    {{else}}
    <th class="title" colspan="2">
      {{tr}}CPersonnel-title-create{{/tr}}
    </th>
    {{/if}}
  </tr>
  
  <tr>
    <th>{{mb_label object=$personnel field="user_id"}}</th>
		<td>
      <input type="hidden" name="user_id" class="notNull" value="{{$personnel->user_id}}"/>
      <input type="hidden" name="object_class" value="CMediusers" />
    	<input size="40" readonly="readonly" name="object_view" value="{{$personnel->_ref_user}}" onclick="ObjectSelector.initEdit()" />
      <button class="search notext" type="button" onclick="ObjectSelector.initEdit()">{{tr}}Search{{/tr}}</button>
      <script type="text/javascript">
      ObjectSelector.initEdit = function(){
        this.sForm     = "editFrm";
        this.sId       = "user_id";
        this.sClass    = "object_class";  
        this.sView     = "object_view";  
        this.onlyclass = "true";
        this.pop();
      }
      </script>
    </td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$personnel field="emplacement"}}</th>
    <td>{{mb_field object=$personnel field="emplacement"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$personnel field="actif"}}</th>
    <td>{{mb_field object=$personnel field="actif"}}</td>
  </tr>
         
  <tr>
    <td class="button" colspan="2">
      {{if $personnel->_id}}
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le personnel ',objName:'{{$personnel->_view|smarty:nodefaults|JSAttribute}}'})">
        {{tr}}Delete{{/tr}}
      </button>
      {{else}}
      <button class="submit" name="btnFuseAction" type="submit">{{tr}}Create{{/tr}}</button>
      {{/if}}
    </td>
  </tr>
</table>
   
</form>

{{if $personnel->_id}}
  
<table class="tbl">
  <tr>
    <th clas="category" colspan="3">
      {{$personnel->_back.affectations|@count}} derni�res affectations
      {{if $personnel->_count.affectations != $personnel->_back.affectations|@count}}
      sur {{$personnel->_count.affectations}} trouv�es
      {{/if}} 
    </th>
  </tr>
  
  {{foreach from=$personnel->_back.affectations item=_affectation}}
  <tr>
    <td>
      <a href="?m={{$m}}&amp;tab=vw_affectations_pers&amp;user_id={{$personnel->user_id}}&amp;list[{{$personnel->emplacement}}]={{$personnel->_id}}&amp;affect_id={{$_affectation->_id}}" 
      	onmouseover="ObjectTooltip.createEx(this, '{{$_affectation->_guid}}')">
      	{{$_affectation->_ref_object}}
      </a>
    </td>
    <td>{{$_affectation->debut}}</td>
    <td>{{$_affectation->fin}}</td>
  </tr>
  {{/foreach}}

</table>

{{/if}}
