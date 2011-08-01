<!-- $Id$ -->

{{mb_script module="dPplanningOp" script="ccam_selector"}}
{{mb_script module="dPplanningOp" script="plage_selector"}}

<script type="text/javascript">
Main.add(function(){
   Document.refreshList('{{$op->_id}}');
});

PlageOpSelector.init = function(){
  if(!(checkChir() && checkDuree())) return;

  var oOpForm     = document.editOp;
  var oSejourForm = document.editSejour;
  
  this.sPlage_id = "plageop_id";
  this.sSalle_id = "salle_id";
  this.sDate     = "_date";
  this.sType     = "type";
  
  this.s_hour_entree_prevue = "_hour_entree_prevue";
  this.s_min_entree_prevue  = "_min_entree_prevue";
  this.s_date_entree_prevue = "_date_entree_prevue";
  
  this.pop(oOpForm.chir_id.value, oOpForm._hour_op.value,
           oOpForm._min_op.value, oSejourForm.group_id.value,
           oOpForm.operation_id.value);
}

CCAMSelector.init = function(){
  this.sForm  = "editOp";
  this.sView  = "_codes_ccam";
  this.sChir  = "chir_id";
  this.sClass = "_class";
  this.pop();
}
</script>

<form name="editOp" action="?m={{$m}}" method="post" onsubmit="return checkFormOperation()">

<input type="hidden" name="dosql" value="do_planning_aed" />
<input type="hidden" name="m" value="dPplanningOp" />
<input type="hidden" name="del" value="0" />
{{mb_key object=$op}}

{{if $op->_id && $op->_ref_sejour->sortie_reelle && !$modules.dPbloc->_can->edit}}
<!-- <input type="hidden" name="_locked" value="1" /> -->
{{/if}}
{{mb_field object=$op field="sejour_id" hidden=1 canNull=true}}
{{mb_field object=$op field="commande_mat" hidden=1}}
{{mb_field object=$op field="rank" hidden=1}}
<input type="hidden" name="annulee" value="{{$op->annulee|default:"0"}}" />
<input type="hidden" name="salle_id" value="{{$op->salle_id}}" />

<!-- Form Fields -->
<input type="hidden" name="_group_id" value="{{$sejour->group_id}}" />
<input type="hidden" name="_class" value="COperation" />
<input type="hidden" name="_protocole_prescription_anesth_id" value="" />
<input type="hidden" name="_protocole_prescription_chir_id" value="" />
{{mb_field object=$op field="_count_actes" hidden=1}}

<table class="form">
  <tr>
    <th class="category" colspan="3">
      {{if $op->operation_id}}
        {{mb_include module=system template=inc_object_idsante400 object=$op}}
        {{mb_include module=system template=inc_object_history    object=$op}}
        {{mb_include module=system template=inc_object_notes      object=$op}}
      {{/if}}
      {{tr}}COperation-msg-informations{{/tr}}
    </th>
  </tr>
  
  {{if $op->annulee == 1}}
  <tr>
    <th class="category cancelled" colspan="3">
    {{tr}}COperation-annulee{{/tr}}
    </th>
  </tr>
  {{/if}}

  <tr>
    <th>
      {{mb_label object=$op field="chir_id"}}
    </th>
    <td colspan="2">
      <select name="chir_id" class="{{$op->_props.chir_id}}" onchange="synchroPrat(); Value.synchronize(this); removePlageOp(true);" style="max-width: 150px;">
        <option value="">&mdash; Choisir un chirurgien</option>
        {{foreach from=$listPraticiens item=curr_praticien}}
        <option class="mediuser" style="border-color: #{{$curr_praticien->_ref_function->color}};" value="{{$curr_praticien->user_id}}" {{if $chir->user_id == $curr_praticien->user_id}} selected="selected" {{/if}}>
        {{$curr_praticien->_view}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$op field="libelle"}}</th>
    <td colspan="2">{{mb_field object=$op field="libelle" form="editOp" autocomplete="true,1,50,true,true" onblur="\$V(getForm('editOpEasy').libelle, \$V(getForm('editOp').libelle));"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$op field="codes_ccam" defaultFor="_codes_ccam"}}</th>
    <td>
      <input type="text" name="_codes_ccam" ondblclick="CCAMSelector.init()" size="10" value="" class="autocomplete"/>
      <div style="display: none; width: 200px !important" class="autocomplete" id="_codes_ccam_auto_complete"></div>
      <script type="text/javascript">
      	Main.add(function(){
	        var oForm = getForm('editOp');
	        var url = new Url("dPccam", "httpreq_do_ccam_autocomplete");
	        url.autoComplete(oForm._codes_ccam, '', {
	          minChars: 1,
	          dropdown: true,
	          width: "250px",
	          updateElement: function(selected) {
	            $V(oForm._codes_ccam, selected.down("strong").innerHTML);
	            oCcamField.add($V(oForm._codes_ccam), true);
	          }
	        });
        })
      </script>
      <button class="add notext" type="button" onclick="oCcamField.add(this.form._codes_ccam.value,true)">{{tr}}Add{{/tr}}</button>
    </td>
    <td class="button">
      <button type="button" class="search" onclick="CCAMSelector.init()">{{tr}}button-CCodeCCAM-choix{{/tr}}</button>
    </td>
  </tr>
  <tr>
    <th>
      Liste des codes CCAM
      {{mb_field object=$op field="codes_ccam" onchange="refreshListCCAM('expert');" hidden=1}}
    </th>
    <td colspan="2" class="text" id="listCodesCcam">
    </td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$op field="cote"}}</th>
    <td colspan="2">
      {{mb_field object=$op field="cote" defaultOption="&mdash; Choisir" onchange="Value.synchronize(this);"}}
    </td>
  </tr> 
  
  <tr>
    <th>{{mb_label object=$op field="type_anesth"}}</th>
    <td colspan="2">
      {{mb_field object=$op field="type_anesth" options=$listAnesthType onchange="submitAnesth(this.form);"}}
    </td>
  </tr> 

  {{if $can->admin}}
  <tr>
    <th>{{mb_label object=$op field="anesth_id"}}</th>
    <td colspan="2">
      {{mb_field object=$op field="anesth_id" options=$anesthesistes}}
    </td>
  </tr> 
  {{/if}}
  

  <tr>
    <th>{{mb_label object=$op field="_hour_op"}}</th>
    <td>
      <select name="_hour_op" class="notNull num">
      {{foreach from=$hours_duree|smarty:nodefaults item=hour}}
        <option value="{{$hour}}" {{if (!$op && $hour == 1) || $op->_hour_op == $hour}} selected="selected" {{/if}}>{{$hour}}</option>
      {{/foreach}}
      </select> h
      <select name="_min_op">
      {{foreach from=$mins_duree|smarty:nodefaults item=min}}
        <option value="{{$min}}" {{if (!$op && $min == 0) || $op->_min_op == $min}} selected="selected" {{/if}}>{{$min}}</option>
      {{/foreach}}
      </select> min
    </td>
    <td id="timeEst">
    </td>
  </tr>

  <tr>
    {{if $modurgence}}
	    <th>{{mb_label object=$op field="date"}}</th>
	    <td>
	      <input type="hidden" name="plageop_id" value="" />
	      <input type="hidden" name="_date" value="{{if $op->_datetime}}{{$op->_datetime|iso_date}}{{else}}{{$today}}{{/if}}" />
	     
        {{if $can->admin}}
          {{assign var="operation_id" value=$op->operation_id}}
          {{mb_ternary var=update_entree_prevue test=$op->operation_id value="" other="updateEntreePrevue();"}}
          {{mb_field object=$op field="date" name="date" prop="date" form="editOp" register=true onchange="
            $update_entree_prevue
            Value.synchronize(this);
            document.editSejour._curr_op_date.value = this.form.date.value;
            modifSejour();  \$V(this.form._date, this.form.date.value);"}}
        {{else}}
  	      <select name="date" onchange="
  	        {{if !$op->operation_id}}updateEntreePrevue();{{/if}}
  	        Value.synchronize(this);
  	        document.editSejour._curr_op_date.value = this.value;
  	        modifSejour(); $V(this.form._date, this.value);">
  	        {{if $op->operation_id}}
  	        <option value="{{$op->_datetime|iso_date}}" selected="selected">
  	          {{$op->_datetime|date_format:"%d/%m/%Y"}} (inchang�e)
  	        </option>
  	        {{/if}}
  	        <option value="{{$today}}">elodie86
            
  	          {{$today|date_format:"%d/%m/%Y"}} (aujourd'hui)
  	        </option>
  	        <option value="{{$tomorow}}">
  	          {{$tomorow|date_format:"%d/%m/%Y"}} (demain)
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
	      </select> min
	    </td>
    {{else}}
	    <th>
	      <input type="hidden" name="plageop_id" class="notNull {{$op->_props.plageop_id}}" onchange="Value.synchronize(this);" ondblclick="PlageOpSelector.init()" value="{{$plage->plageop_id}}" />
	      {{mb_label object=$op field="plageop_id"}}
	      <input type="hidden" name="date" value="" />
	      <input type="hidden" name="_date" value="{{$plage->date}}" 
	      onchange="Value.synchronize(this); 
	                if(this.value){ 
	                  this.form._locale_date.value = Date.fromDATE(this.value).toLocaleDate();
	                } else { 
	                  this.form._locale_date.value = '';
	                }; 
	                Sejour.preselectSejour(this.value);" />
	    </th>
	    <td>
	      <input type="text" name="_locale_date" readonly="readonly" size="10" ondblclick="PlageOpSelector.init()" value="{{$plage->date|date_format:"%d/%m/%Y"}}"  />
	      {{if $op->_ref_salle && $op->_ref_salle->_id}}
	      en {{$op->_ref_salle->_view}}
	      {{/if}}
	    </td>
	    <td class="button">
	      <button type="button" class="search" onclick="PlageOpSelector.init()">Choisir une date</button>
	    </td>
    {{/if}}
  </tr>
  
  {{if !$modurgence && $conf.dPplanningOp.COperation.horaire_voulu}}
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
      </select> min
    </td>
  </tr>
  {{/if}}

  <tr>
    <td class="text">{{mb_label object=$op field="examen"}}</td>
    <td class="text">{{mb_label object=$op field="materiel"}}</td>
    <td class="text">{{mb_label object=$op field="rques"}}</td>
  </tr>

  <tr>
    <td style="width: 33%;">
      <script type="text/javascript">
        Main.add(function() {
          new AideSaisie.AutoComplete(getForm("editOp").elements.examen, {
            objectClass: "{{$op->_class}}",
            timestamp: "{{$conf.dPcompteRendu.CCompteRendu.timestamp}}",
            validateOnBlur: 0
          });
        });
      </script>
      {{mb_field object=$op field="examen"}}
    </td>
    <td style="width: 33%;">
      <script type="text/javascript">
        Main.add(function() {
          new AideSaisie.AutoComplete(getForm("editOp").elements.materiel, {
            objectClass: "{{$op->_class}}",
            timestamp: "{{$conf.dPcompteRendu.CCompteRendu.timestamp}}",
            validateOnBlur: 0
          });
        });
      </script>
      {{mb_field object=$op field="materiel" onchange="Value.synchronize(this);"}}
    </td>
    <td style="width: 33%;">
      <script type="text/javascript">
        Main.add(function() {
          new AideSaisie.AutoComplete(getForm("editOp").elements.rques, {
            objectClass: "{{$op->_class}}",
            timestamp: "{{$conf.dPcompteRendu.CCompteRendu.timestamp}}",
            validateOnBlur: 0
          });
        });
      </script>
      {{mb_field object=$op field="rques" onchange="Value.synchronize(this);"}}
    </td>
  </tr>
  
  {{if $op->_count_actes}}
  <tr>
    <td colspan="3">
      <div class="small-info">
      	L'intervention a d�j� �t� cod�e.<br/>
        Il est impossible de modifier les champs ci-dessous.
      </div>
		</td>
  </tr>
  {{/if}}
  
  <tr>
    <td class="text">{{mb_label object=$op field="depassement"}}</td>
    <td class="text">{{mb_label object=$op field="forfait"}}</td>
    <td class="text">{{mb_label object=$op field="fournitures"}}</td>
  </tr>

  <tr>
    {{if $op->_ref_actes_ccam|@count}}
    <td>{{mb_value object=$op field="depassement"}}</td>
    <td>{{mb_value object=$op field="forfait"}}</td>
    <td>{{mb_value object=$op field="fournitures"}}</td>
    {{else}}
    <td>{{mb_field object=$op field="depassement" size="4"}}</td>
    <td>{{mb_field object=$op field="forfait" size="4"}}</td>
    <td>{{mb_field object=$op field="fournitures" size="4"}}</td>
    {{/if}}
  </tr>
  
  <tr>
    <th>{{mb_label object=$op field="info"}}</th>
    <td colspan="2">
      {{mb_field object=$op field="info"}}
    </td>
  </tr>

</table>

</form>
