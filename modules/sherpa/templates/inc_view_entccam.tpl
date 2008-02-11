<form name="editEntCCCAM" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="dosql" value="do_entccam_aed" />
<input type="hidden" name="idinterv" value="{{$entccam->_id}}" />
<input type="hidden" name="del" value="0" />

<table class="form">
  <tr>
    {{if $entccam->_id != ''}}
    <th class="title modify" colspan="2">
 		  Informations de l'ent�te CCAM {{$entccam->_view}}
    </th>
    {{else}}
    <th class="title" colspan="2">
			Informations du l'ent�te CCAM
    </th>
    {{/if}}
  </tr>
  {{if $entccam->_id != ''}}
  
  <tr>
		<th>{{mb_label object=$entccam field="numdos"}}</th>
		<td>{{mb_value object=$entccam field="numdos"}}</td>
  </tr>  
  
  <tr>
		<th>{{mb_label object=$entccam field="malnum"}}</th>
		<td>{{mb_value object=$entccam field="malnum"}}</td>
  </tr>  
  
  <tr>
		<th>{{mb_label object=$entccam field="debint"}}</th>
		<td>{{mb_value object=$entccam field="debint"}}</td>
  </tr>  
  
  <tr>
		<th>{{mb_label object=$entccam field="finint"}}</th>
		<td>{{mb_value object=$entccam field="finint"}}</td>
  </tr>  
  
  <tr>
		<th>{{mb_label object=$entccam field="datope"}}</th>
		<td>{{mb_value object=$entccam field="datope"}}</td>
  </tr>  
  
  <tr>
		<th>{{mb_label object=$entccam field="finope"}}</th>
		<td>{{mb_value object=$entccam field="finope"}}</td>
  </tr>  

  <tr>
		<th>{{mb_label object=$entccam field="pracod"}}</th>
		<td>{{mb_value object=$entccam field="pracod"}}</td>
  </tr>  
  
  <tr>
		<th>{{mb_label object=$entccam field="codane"}}</th>
		<td>{{mb_value object=$entccam field="codane"}}</td>
  </tr>  
  
  <tr>
		<th>{{mb_label object=$entccam field="codsal"}}</th>
		<td>{{mb_value object=$entccam field="codsal"}}</td>
  </tr>  
  
  <tr>
		<th>{{mb_label object=$entccam field="aidop1"}}</th>
		<td>{{mb_value object=$entccam field="aidop1"}}</td>
  </tr>  
  
  <tr>
		<th>{{mb_label object=$entccam field="dhaid1"}}</th>
		<td>{{mb_value object=$entccam field="dhaid1"}}</td>
  </tr>  
  
  <tr>
		<th>{{mb_label object=$entccam field="fhaid1"}}</th>
		<td>{{mb_value object=$entccam field="fhaid1"}}</td>
  </tr>  
  
  <tr>
		<th>{{mb_label object=$entccam field="aidop2"}}</th>
		<td>{{mb_value object=$entccam field="aidop2"}}</td>
  </tr>  
  
  <tr>
		<th>{{mb_label object=$entccam field="dhaid2"}}</th>
		<td>{{mb_value object=$entccam field="dhaid2"}}</td>
  </tr>  
  
  <tr>
		<th>{{mb_label object=$entccam field="fhaid2"}}</th>
		<td>{{mb_value object=$entccam field="fhaid2"}}</td>
  </tr>  
  
  <tr>
		<th>{{mb_label object=$entccam field="aidop3"}}</th>
		<td>{{mb_value object=$entccam field="aidop3"}}</td>
  </tr>  
  
  <tr>
		<th>{{mb_label object=$entccam field="dhaid3"}}</th>
		<td>{{mb_value object=$entccam field="dhaid3"}}</td>
  </tr>  
  
  <tr>
		<th>{{mb_label object=$entccam field="fhaid3"}}</th>
		<td>{{mb_value object=$entccam field="fhaid3"}}</td>
  </tr>  
  
  <tr>
		<th>{{mb_label object=$entccam field="valigs"}}</th>
		<td>{{mb_value object=$entccam field="valigs"}}</td>
  </tr>  

  <tr>
		<th>{{mb_label object=$entccam field="datmaj"}}</th>
		<td>{{mb_value object=$entccam field="datmaj"}}</td>
  </tr>  
  
  {{if $can->edit}}
  <tr>
    <td class="button" colspan="2">
      <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'l\'ent�te CCAM',objName:'{{$entccam->_view|smarty:nodefaults|JSAttribute}}'})">
        {{tr}}Delete{{/tr}}</button>
    </td>
  </tr>
  {{/if}}     
</table>

</form>

{{assign var=id400 value=$entccam->_ref_id400}}
<table class="tbl">
  <tr>
    <th class="title" colspan="10">Correspondance pour l'�tablissement courant</th>
  </tr>
  
  <tr>
    <th>{{mb_label object=$id400 field=last_update}}</th>
    <th>{{mb_label object=$id400 field=object_id}}</th>
  </tr>

	{{assign var=codable value=$id400->_ref_object}}
  <tr>
	  {{if $id400->_id}}
    <td>{{mb_value object=$id400 field=last_update}}</td>
    <td>{{$codable->_view}}</td>
    {{else}}
    <td colspan="2"><em>Pas de correspondance</em></td>
    {{/if}}
  </tr>
</table>

{{/if}}
