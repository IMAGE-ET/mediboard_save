<!-- $Id: $ -->
{{mb_include_script module="dPplanningOp" script="plage_selector"}}
{{mb_include_script module="dPpatients" script="pat_selector"}}


<form name="editOpEasy" action="?m={{$m}}" method="post" onsubmit="return checkFormOperation()">
<table class="form">
  {{if $op->annulee == 1}}
  <tr>
    <th class="category cancelled" colspan="3">
    {{tr}}COperation-annulee{{/tr}}
    </th>
  </tr>
  {{/if}}
  <!-- Selection du chirurgien -->
  <tr>
    <th>
      {{mb_label object=$op field="chir_id"}}
    </th>
    <td colspan="2">
      <select name="chir_id" class="{{$op->_props.chir_id}}" onchange="synchroPrat(); Value.synchronize(this); removePlageOp(true);">
        <option value="">&mdash; Choisir un chirurgien</option>
        {{foreach from=$listPraticiens item=curr_praticien}}
        <option class="mediuser" style="border-color: #{{$curr_praticien->_ref_function->color}};" value="{{$curr_praticien->user_id}}" {{if $chir->user_id == $curr_praticien->user_id}} selected="selected" {{/if}}>
        {{$curr_praticien->_view}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  
  <!-- Affichage du libelle -->
  <tr>
    <th>{{mb_label object=$op field="libelle"}}</th>
    <td  class="readonly" colspan="2">{{mb_field object=$op field="libelle" readonly="readonly"}}</td>
  </tr>
  
  
  <!-- Liste des codes ccam -->
  <tr>
    <th>Liste des codes CCAM
    {{mb_field object=$op field="codes_ccam" onchange="refreshListCCAM('easy');" hidden=1 prop=""}}
    </th>
    <td colspan="2" class="text" id="listCodesCcamEasy">
  </td>
  </tr>
  
  
    
  <!-- Selection du cot� --> 
  <tr>
    <th>{{mb_label object=$op field="cote"}}</th>
    <td colspan="2">
      {{mb_field object=$op field="cote" onchange="Value.synchronize(this); modifOp();"}}
    </td>
  </tr> 


  <!-- Selection de la date -->
  {{if $modurgence}}
  <tr>
    <th>
      {{mb_label object=$op field="date"}}
    </th>
    <td>
      <input type="hidden" name="plageop_id" value="" />
      <input type="hidden" name="_date" value="{{if $op->_datetime}}{{$op->_datetime}}{{else}}{{$today}}{{/if}}" />
     
      <select name="date" onchange="
        {{if !$op->operation_id}}updateEntreePrevue();{{/if}}
        Value.synchronize(this);
        document.editSejour._curr_op_date.value = this.value;
        modifSejour(); Form.Element.setValue(this.form._date, this.value);">
        {{if $op->operation_id}}
        <option value="{{$op->_datetime|date_format:"%Y-%m-%d"}}" selected="selected">
          {{$op->_datetime|date_format:"%d/%m/%Y"}} (inchang�e)
        </option>
        {{/if}}
        <option value="{{$today}}">
          {{$today|date_format:"%d/%m/%Y"}} (aujourd'hui)
        </option>
        <option value="{{$tomorow}}">
          {{$tomorow|date_format:"%d/%m/%Y"}} (demain)
        </option>
      </select>
    </td>
    <td>
      �
      <select name="_hour_urgence" onchange="Value.synchronize(this)">
      {{foreach from=$hours_urgence|smarty:nodefaults item=hour}}
        <option value="{{$hour}}" {{if $op->_hour_urgence == $hour || (!$op->operation_id && $hour == "8")}} selected="selected" {{/if}}>{{$hour}}</option>
      {{/foreach}}
      </select> h
      <select name="_min_urgence" onchange="Value.synchronize(this);">
      {{foreach from=$mins_duree|smarty:nodefaults item=min}}
        <option value="{{$min}}" {{if $op->_min_urgence == $min}}selected="selected"{{/if}}>{{$min}}</option>
      {{/foreach}}
      </select> mn
    </td>
  </tr>
  
  {{else}}
  <tr>
    <th>
      <input type="hidden" name="plageop_id" class="notNull {{$op->_props.plageop_id}}" onchange="Value.synchronize(this);" ondblclick="PlageOpSelector.init()" value="{{$plage->plageop_id}}" />
      {{mb_label object=$op field="plageop_id"}}
      <input type="hidden" name="date" value="" />
      <input type="hidden" name="_date" value="{{$plage->date}}" 
      onchange="Value.synchronize(this); 
                if(this.value){ 
                  this.form._locale_date.value = Date.fromDATE(this.value).toLocaleDate() 
                } else { 
                  this.form._locale_date.value = '' 
                }; 
                Sejour.preselectSejour(this.value);" />
    </th>
    <td class="readonly">
      <input type="text" name="_locale_date" readonly="readonly" size="10" ondblclick="PlageOpSelector.init()" value="{{$plage->date|date_format:"%d/%m/%Y"}}" />
    </td>
    <td class="button">
      <button type="button" class="search" onclick="PlageOpSelector.init()">Choisir une date</button>
    </td>
  </tr>
  {{/if}}

  <!-- Selection du patient -->
  <tr>
    <th>
      <input type="hidden" name="patient_id" class="notNull {{$sejour->_props.patient_id}}" ondblclick="PatSelector.init()" value="{{$patient->patient_id}}" onchange="changePat()" />
      {{mb_label object=$sejour field="patient_id"}}
    </th>
    <td class="readonly">
  	  <input type="text" name="_patient_view" size="30" value="{{$patient->_view}}" readonly="readonly"
  	    {{if $dPconfig.dPplanningOp.CSejour.patient_id || !$sejour->_id || $app->user_type == 1}}
  	      ondblclick="PatSelector.init()"
  	    {{/if}}
  	  />
    </td>
    <td colspan="2" class="button">
      {{if $dPconfig.dPplanningOp.CSejour.patient_id || !$sejour->_id || $app->user_type == 1}}
      <button type="button" class="search" onclick="PatSelector.init()">Choisir un patient</button>
      {{/if}}
    </td>
  </tr>
  
  <!-- Selection du type de chambre -->
  <tr>
    <th>{{mb_label object=$sejour field="chambre_seule"}}</th>
    <td colspan="2">
      {{mb_field object=$sejour field="chambre_seule" onchange="checkChambreSejourEasy()"}}
    </td>
  </tr>
  
  {{if !$modurgence && $dPconfig.dPplanningOp.COperation.horaire_voulu}}
  <tr>
    <th>Horaire souhait�</th>
    <td colspan="2">
      <select name="_hour_voulu" onchange="Value.synchronize(this);">
        <option value="">-</option>
      {{foreach from=$list_hours_voulu|smarty:nodefaults item=hour}}
        <option value="{{$hour}}" {{if $hour == $op->_hour_voulu}} selected="selected" {{/if}}>{{$hour}}</option>
      {{/foreach}}
      </select> h
      <select name="_min_voulu" onchange="Value.synchronize(this);">
      <option value="">-</option>
      {{foreach from=$list_minutes_voulu|smarty:nodefaults item=min}}
        <option value="{{$min}}" {{if $min == $op->_min_voulu}} selected="selected" {{/if}}>{{$min}}</option>
      {{/foreach}}
      </select> mn
    </td>
  </tr>
  {{/if}}
   
</table>
</form>