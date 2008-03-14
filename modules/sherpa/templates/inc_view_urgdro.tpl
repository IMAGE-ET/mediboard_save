<form name="editOuvertureDroit" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="dosql" value="do_SpUrgDro_aed" />
<input type="hidden" name="numdos" value="{{$droit->_id}}" />
<input type="hidden" name="del" value="0" />

<table class="form">
  <tr>
    {{if $droit->_id}}
    <th class="title modify" colspan="2">
 		  Affichage des informations de l'ouverture de droits {{$droit->numdos}}
    </th>
    {{else}}
    <th class="title" colspan="2">
			Affichage des informations d'une ouverture de droit
    </th>
    {{/if}}
  </tr>
  {{if $droit->_id}}
  
  <tr>
		<th>{{mb_label object=$droit field=urgfla}}</th>
		<td>{{mb_value object=$droit field=urgfla}}</td>
  </tr>

  <tr>
		<th>{{mb_label object=$droit field=numdos}}</th>
		<td>{{mb_value object=$droit field=numdos}}</td>
  </tr>
  
  <tr>
		<th>{{mb_label object=$droit field=malnum}}</th>
		<td>{{mb_value object=$droit field=malnum}}</td>
  </tr>
  
  <tr>
		<th>{{mb_label object=$droit field=datarr}}</th>
		<td>{{mb_value object=$droit field=datarr}}</td>
  </tr>

  <tr>
		<th>{{mb_label object=$droit field=datdep}}</th>
		<td>{{mb_value object=$droit field=datdep}}</td>
  </tr>

  <tr>
		<th>{{mb_label object=$droit field=urprov}}</th>
		<td>{{mb_value object=$droit field=urprov}}</td>
  </tr>

  <tr>
		<th>{{mb_label object=$droit field=urmtra}}</th>
		<td>{{mb_value object=$droit field=urmtra}}</td>
  </tr>
  
  <tr>
		<th>{{mb_label object=$droit field=urdest}}</th>
		<td>{{mb_value object=$droit field=urdest}}</td>
  </tr>

  <tr>
		<th>{{mb_label object=$droit field=urmuta}}</th>
		<td>{{mb_value object=$droit field=urmuta}}</td>
  </tr>

  <tr>
		<th>{{mb_label object=$droit field=urtype}}</th>
		<td>{{mb_value object=$droit field=urtype}}</td>
  </tr>

  <tr>
		<th>{{mb_label object=$droit field=urtrau}}</th>
		<td>{{mb_value object=$droit field=urtrau}}</td>
  </tr>

  <tr>
		<th>{{mb_label object=$droit field=urgems}}</th>
		<td>{{mb_value object=$droit field=urgems}}</td>
  </tr>

  <tr>
		<th>{{mb_label object=$droit field=urccmu}}</th>
		<td>{{mb_value object=$droit field=urccmu}}</td>
  </tr>

  {{if $can->edit}}
  <tr>
    <td class="button" colspan="2">
      <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le droit',objName:'{{$droit->_view|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
    </td>
  </tr>
  {{/if}}     
</table>

</form>

{{assign var=id400 value=$droit->_ref_id400}}
<table class="tbl">
  <tr>
    <th class="title" colspan="10">Correspondance pour l'�tablissement courant</th>
  </tr>
  <tr>
    <th>{{mb_label object=$id400 field=last_update}}</th>
    <th>{{mb_label object=$id400 field=object_id}}</th>
  </tr>

	{{assign var=droit value=$id400->_ref_object}}
  <tr>
	  {{if $id400->_id}}
    <td>{{mb_value object=$id400 field=last_update}}</td>
    <td>{{$droit->_view}}
    </td>
    {{else}}
    <td colspan="2"><em>Pas de correspondance</em></td>
    {{/if}}
  </tr>
  <tr>
</table>

{{/if}}
