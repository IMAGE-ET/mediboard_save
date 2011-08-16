<!-- $Id$ -->
{{mb_script module="dPplanningOp" script="plage_selector"}}
{{mb_script module="dPpatients" script="pat_selector"}}


<form name="editOpEasy" action="?m={{$m}}" method="post" onsubmit="return checkFormOperation()">
{{if $op->_id && $op->_ref_sejour->sortie_reelle && !$modules.dPbloc->_can->edit}}
<!-- <input type="hidden" name="_locked" value="1" /> -->
{{/if}}
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
  
	{{if $conf.dPplanningOp.CSejour.easy_service}}
  <!-- Selection du service -->
  <tr>
	  <th>
	    {{mb_label object=$sejour field="service_id"}}
	  </th>
	  <td colspan="3">
	    <select name="service_id" class="{{$sejour->_props.service_id}}" onchange="synchroService(this);" style="max-width: 150px;">
	      <option value="">&mdash; Choisir un service</option>
	      {{foreach from=$listServices item=_service}}
	      <option value="{{$_service->_id}}" {{if $sejour->service_id == $_service->_id}} selected="selected" {{/if}}>
	        {{$_service->_view}}
	      </option>
	      {{/foreach}}
	    </select>
	  </td>
	</tr>
  {{/if}}
	
	
  <!-- Affichage du libelle -->
  <tr>
    <th>{{mb_label object=$op field="libelle"}}</th>
    <td colspan="2">{{mb_field object=$op field="libelle" readonly="readonly"}}</td>
  </tr>
  
  
  <!-- Liste des codes ccam -->
  <tr>
    <th>Liste des codes CCAM
    {{mb_field object=$op field="codes_ccam" onchange="refreshListCCAM('easy');" hidden=1}}
    </th>
    <td colspan="2" class="text" id="listCodesCcamEasy">
  </td>
  </tr>
  
  
    
  <!-- Selection du cot� --> 
  <tr>
    <th>{{mb_label object=$op field="cote"}}</th>
    <td colspan="2">
      {{mb_field object=$op field="cote" defaultOption="&mdash; Choisir" onchange="Value.synchronize(this); modifOp();"}}
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
      <input type="hidden" name="_date" value="{{if $op->_datetime}}{{$op->_datetime|iso_date}}{{else}}{{$today}}{{/if}}" />
      {{if $can->admin}}
        {{assign var="operation_id" value=$op->operation_id}}
        {{mb_ternary var=update_entree_prevue test=$op->operation_id value="" other="updateEntreePrevue();"}}
        {{mb_field object=$op field="date" name="date" prop="date" form="editOpEasy" register=true onchange="
          $update_entree_prevue
          Value.synchronize(this.form.date_da);
          Value.synchronize(this);
          document.editSejour._curr_op_date.value = this.value;
          modifSejour();  \$V(this.form._date, this.value);"}}
      {{else}}
        <select name="date" onchange="
          {{if !$op->operation_id}}updateEntreePrevue();{{/if}}
          Value.synchronize(this);
          document.editSejour._curr_op_date.value = this.value;
          modifSejour(); $V(this.form._date, this.value);">
          {{if $op->operation_id}}
          <option value="{{$op->_datetime|iso_date}}" selected="selected">
            {{$op->_datetime|date_format:$conf.date}} (inchang�e)
          </option>
          {{/if}}
          <option value="{{$today}}">
            {{$today|date_format:$conf.date}} (aujourd'hui)
          </option>
          <option value="{{$tomorow}}">
            {{$tomorow|date_format:$conf.date}} (demain)
          </option>
        </select>
      {{/if}}
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
    <td>
      <input type="text" name="_locale_date" readonly="readonly" size="10" ondblclick="PlageOpSelector.init()" value="{{$plage->date|date_format:"%d/%m/%Y"}}" />
    </td>
    <td class="button">
      <button type="button" class="search" onclick="PlageOpSelector.init()">Choisir une date</button>
    </td>
  </tr>
  {{/if}}
  
  <tr>
    <th>Taux d'occupation</th>
    <td colspan="2" id="occupationeasy">
    </td>
  </tr>

  <!-- Selection du patient -->
  <tr>
    <th>
      <input type="hidden" name="patient_id" class="notNull {{$sejour->_props.patient_id}}" ondblclick="PatSelector.init()" value="{{$patient->patient_id}}" onchange="changePat()" />
      {{mb_label object=$sejour field="patient_id"}}
    </th>
    <td>
  	  <input type="text" name="_patient_view" size="30" value="{{$patient->_view}}" readonly="readonly"
  	    {{if $conf.dPplanningOp.CSejour.patient_id || !$sejour->_id || $app->user_type == 1}}
  	      ondblclick="PatSelector.init()"
  	    {{/if}}
  	  />
    </td>
    <td colspan="2" class="button">
      {{if $conf.dPplanningOp.CSejour.patient_id || !$sejour->_id || $app->user_type == 1}}
      <button type="button" class="search" onclick="PatSelector.init()">Choisir un patient</button>
      {{/if}}
    </td>
  </tr>
  
  
	{{if $conf.dPplanningOp.CSejour.easy_chambre_simple || $conf.dPplanningOp.COperation.easy_regime}}
  <tr>
  	{{if $conf.dPplanningOp.CSejour.easy_chambre_simple}}
      <!-- Selection du type de chambre -->
	    <th>{{mb_label object=$sejour field="chambre_seule"}}</th>
	    <td>
	      {{mb_field object=$sejour field="chambre_seule" onchange="checkChambreSejourEasy()"}}
	    </td>
			{{else}}
			<td colspan="2" />
		{{/if}}
   
	  {{if $conf.dPplanningOp.COperation.easy_regime}}
			<td class="button">
	      <button type="button" class="new" onclick="popRegimes()">R�gime alimentaire</button>
	    </td>
			{{else}}
			<td />
		{{/if}}
	</tr>
	{{/if}}

  {{if $conf.dPplanningOp.CSejour.consult_accomp}}
  <tr>
    <th>{{mb_label object=$sejour field=consult_accomp}}</th>
    <td colspan="3">{{mb_field object=$sejour field=consult_accomp typeEnum=radio onchange="checkConsultAccompSejourEasy()"}}</td>
  </tr>
  {{/if}}
	
  {{if !$modurgence && $conf.dPplanningOp.COperation.horaire_voulu && $conf.dPplanningOp.COperation.easy_horaire_voulu}}
  <tr>
    <th>Horaire souhait�</th>
    <td colspan="2">
      <select name="_hour_voulu" onchange="setMinVoulu(this.form); Value.synchronize(this);">
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
  
	{{if $conf.dPplanningOp.COperation.easy_materiel || $conf.dPplanningOp.COperation.easy_remarques}}
	<tr>
		<td />
		{{if $conf.dPplanningOp.COperation.easy_materiel}}
    <td class="text" {{if !$conf.dPplanningOp.COperation.easy_remarques}}colspan="2"{{/if}}>{{mb_label object=$op field="materiel"}}</td>
		{{/if}}
		{{if $conf.dPplanningOp.COperation.easy_remarques}}
    <td class="text" {{if !$conf.dPplanningOp.COperation.easy_materiel}}colspan="2"{{/if}}>{{mb_label object=$op field="rques"}}</td>
		{{/if}}
  </tr>
  <tr>
  <td />
  	{{if $conf.dPplanningOp.COperation.easy_materiel}}
    <td style="width: 33%;" {{if !$conf.dPplanningOp.COperation.easy_remarques}}colspan="2"{{/if}}>
      <script type="text/javascript">
        Main.add(function() {
          new AideSaisie.AutoComplete(getForm("editOpEasy").elements.materiel, {
            objectClass: "{{$op->_class}}",
            timestamp: "{{$conf.dPcompteRendu.CCompteRendu.timestamp}}",
            validateOnBlur: 0
          });
        });
      </script>
      {{mb_field object=$op field="materiel" onchange="Value.synchronize(this);"}}
    </td>
		{{/if}}
		{{if $conf.dPplanningOp.COperation.easy_remarques}}
    <td style="width: 33%;" {{if !$conf.dPplanningOp.COperation.easy_materiel}}colspan="2"{{/if}}>
      <script type="text/javascript">
        Main.add(function() {
          new AideSaisie.AutoComplete(getForm("editOpEasy").elements.rques, {
            objectClass: "{{$op->_class}}",
            timestamp: "{{$conf.dPcompteRendu.CCompteRendu.timestamp}}",
            validateOnBlur: 0
          });
        });
      </script>
      {{mb_field object=$op field="rques" onchange="Value.synchronize(this);"}}
    </td>
		{{/if}}
  </tr>
	{{/if}}
</table>
</form>