<form name="find" action="?" method="get">

<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="tab" value="{{$tab}}" />
<input type="hidden" name="new" value="1" />

<table class="form">
  <tr>
    <th class="title" colspan="3">Recherche d'un {{tr}}{{$filter->_class_name}}{{/tr}}</th>
  </tr>
  <tr>
    <th>{{mb_label object=$filter field="idacte"}}</th>
		<td>{{mb_field object=$filter field="idacte"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$filter field="idinterv"}}</th>
		<td>{{mb_field object=$filter field="idinterv"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$filter field="numdos"}}</th>
		<td>{{mb_field object=$filter field="numdos"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$filter field="malnum"}}</th>
		<td>{{mb_field object=$filter field="malnum"}}</td>
  </tr>
  <tr>
    <th>
      <label for="Day" title="Date de l'intervention">
        Date
      </label>
    </th>
    <td >
      {{html_select_date
           prefix=""
           time=$date
           start_year=2005
           field_order=DMY
           day_empty="Jour"
           month_empty="Mois"
           year_empty="Ann�e"
           all_extra="style='display:inline;'"}}
    </td>
  </tr>
  <tr>
    <td class="button" colspan="3">
      <button class="search" type="submit">Rechercher</button>
    </td>
  </tr>
</table>
</form>

<table class="tbl">
  <tr>
    <th>{{mb_title object=$filter field="idacte"}}</th>
    <th>{{mb_title object=$filter field="idinterv"}}</th>
    <th>{{mb_title object=$filter field="numdos"}}</th>
    <th>{{mb_title object=$filter field="malnum"}}</th>
    <th>{{mb_title object=$filter field="codpra"}}</th>
    <th>{{mb_title object=$filter field="codact"}}</th>
    <th>{{mb_title object=$filter field="activ"}}</th>
    <th>{{mb_title object=$filter field="phase"}}</th>
  </tr>

  {{assign var="href" value="?m=sherpa&tab=view_detccam&sel_idacte="}}
  
  {{foreach from=$detsccam item=_detccam}}
  <tr {{if $detccam->_id == $_detccam->_id && $detccam->numdos == $_detccam->numdos}}class="selected"{{/if}}>
    <td class="text">
      <a href="{{$href}}{{$_detccam->_id}}">
        {{mb_value object=$_detccam field="idacte"}}
      </a>
    <td>{{mb_value object=$_detccam field="idinterv"}}</td>
    <td>{{mb_value object=$_detccam field="numdos"}}</td>
    <td>{{mb_value object=$_detccam field="malnum"}}</td>
    <td>{{mb_value object=$_detccam field="codpra"}}</td>
    <td>{{mb_value object=$_detccam field="codact"}}</td>
    <td>{{mb_value object=$_detccam field="activ"}}</td>
    <td>{{mb_value object=$_detccam field="phase"}}</td>
  </tr>
  {{/foreach}}
</table>