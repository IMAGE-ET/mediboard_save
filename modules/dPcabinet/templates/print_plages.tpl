<!-- $Id$ -->

<table class="tbl">
  <tr class="clear">
    <th colspan="6">
      <a href="#" onclick="window.print()">
        Rapport du {{$filter->_date_min|date_format:"%d/%m/%Y"}}
        {{if $filter->_date_min != $filter->_date_max}}
        au {{$filter->_date_max|date_format:"%d/%m/%Y"}}
        {{/if}}
      </a>
    </th>
  </tr>
  {{foreach from=$listPlage item=curr_plage}}
  <tr class="clear">
    <td colspan="6">
      <b>{{$curr_plage->date|date_format:"%d/%m/%Y"}} - Dr {{$curr_plage->_ref_chir->_view}}</b>
    </td>
  </tr>
  <tr>
    <th rowspan="2"><b>Heure</b></th>
    <th {{if $coordonnees}}colspan="4"{{else}}colspan="2"{{/if}}><b>Patient</b></th>
    <th colspan="3"><b>Consultation</b></th>
  </tr>
  <tr>
    <th>Nom / Pr�nom</th>
    {{if $coordonnees}}
    <th>Adresse</th>
    <th>Tel</th>
    {{/if}}
    <th>Age</th>
    <th>Motif</th>
    <th>Remarques</th>
    <th>Dur�e</th>
  </tr>
  {{foreach from=$curr_plage->_ref_consultations item=curr_consult}}
  <tr>
    {{if $curr_consult->premiere}}
    <td style="background-color:#eaa">
    {{else}}
    <td>
    {{/if}}
      <div style="display: inline; float: left;">        
        {{$curr_consult->heure|date_format:"%Hh%M"}}
      </div>
      <div style="display: inline; float: right;">  
        <img src="./modules/dPcabinet/images/categories/{{$curr_consult->_ref_categorie->nom_icone}}" alt="{{$curr_consult->_ref_categorie->nom_categorie}}" title="{{$curr_consult->_ref_categorie->nom_categorie}}" />
      </div>
    </td>
    {{if $curr_consult->patient_id}}
    <td>{{$curr_consult->_ref_patient->_view}}</td>
    {{if $coordonnees}}
    <td>{{$curr_consult->_ref_patient->adresse}}<br />{{$curr_consult->_ref_patient->cp}} {{$curr_consult->_ref_patient->ville}}</td>
    <td>
      {{mb_value object=$curr_consult->_ref_patient field=tel}}
      <br />
      {{mb_value object=$curr_consult->_ref_patient field=tel2}}
    </td>
    {{/if}}
    <td>
      {{$curr_consult->_ref_patient->_age}} ans
      {{if $curr_consult->_ref_patient->_age != "??"}}
        ({{mb_value object=$curr_consult->_ref_patient field="naissance"}})
      {{/if}}
    </td>
    {{else}}
    <td colspan="{{if $coordonnees}}4{{else}}2{{/if}}">
      [PAUSE]
    </td>
    {{/if}}
    <td class="text">
      {{$curr_consult->motif|nl2br}}
      {{assign var=consult_anesth value=$curr_consult->_ref_consult_anesth}}
      {{if $consult_anesth->_id && $consult_anesth->operation_id}}
        <div style="border-left: 4px solid #aaa; padding-left: 5px;">
        {{assign var=operation value=$consult_anesth->_ref_operation}}

        Intervention le {{$operation->_datetime|date_format:$dPconfig.date}}
        - Dr {{$operation->_ref_praticien->_view}}<br />
        {{if $operation->libelle}}
          <em>[{{$operation->libelle}}]</em>
          <br />
        {{/if}}
        {{foreach from=$operation->_ext_codes_ccam item=curr_code}}
          {{if !$curr_code->_code7}}<strong>{{/if}}
          {{$curr_code->code}} : {{$curr_code->libelleLong|truncate}}
          {{if !$curr_code->_code7}}</strong>{{/if}}
          <br/>
        {{/foreach}}
        </div>
      {{/if}}
    </td>
    <td class="text">{{$curr_consult->rques|nl2br}}</td>
    <td class="text">{{$curr_consult->duree}} x {{$curr_plage->freq|date_format:"%M"}} min</td>
  </tr>
  {{/foreach}}
  {{/foreach}}
</table>