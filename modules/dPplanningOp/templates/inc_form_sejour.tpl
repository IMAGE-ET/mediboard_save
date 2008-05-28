<script type="text/javascript">

function checkHeureSortie(){
  var oForm = document.editSejour;
  var heure_entree = parseInt(oForm._hour_entree_prevue.value, 10);
  
  if (oForm._hour_sortie_prevue.value < heure_entree + 1) {
    heure_entree = heure_entree + 1;
    oForm._hour_sortie_prevue.value = heure_entree;
  }
}

function loadTransfert(form, mode_sortie){
  // si Transfert, affichage du select
  if(mode_sortie=="transfert"){
    //Chargement de la liste des etablissement externes
    var url = new Url();
    url.setModuleAction("dPadmissions", "httpreq_vw_etab_externes");
    url.requestUpdate('listEtabExterne', { waitingText : null });
  } else {
    // sinon, on vide le contenu de la div
    $("listEtabExterne").innerHTML = "";
  }
}


function checkModeSortie(){
  var oForm = document.editSejour;
  
	if (!oForm.sortie_reelle) {
	  return true;
	}

  if (!oForm.sortie_reelle.value && !oForm.mode_sortie.value){
    return true;
  }
  
  if(oForm.sortie_reelle.value && oForm.mode_sortie.value){
    return true;
  }

  alert("Date de sortie reelle et mode de sortie incompatible");
  return false;
}

function checkSejour() {
  var oForm = document.editSejour;
  return checkDureeHospi() && checkModeSortie() && checkForm(oForm);
}

function checkPresta(){
  var oForm = document.editSejour;
  var oFormEasy = document.editOpEasy;
  if(oForm.prestation_id.value != ""){
    setRadioValue(oForm.chambre_seule, "1");
    setRadioValue(oFormEasy.chambre_seule, "1");
  } 
}


function checkChambreSejour(){
  var oForm = document.editSejour;
  var oFormEasy = document.editOpEasy;
  var valeur_chambre = getCheckedValue(oForm.chambre_seule);
  setCheckedValue(oFormEasy.chambre_seule, valeur_chambre);
  
  if(valeur_chambre == "0"){
    oForm.prestation_id.value = "";
  }
}


function checkChambreSejourEasy(){
  var oForm = document.editSejour;
  var oFormEasy = document.editOpEasy;
  var valeur_chambre = getCheckedValue(oFormEasy.chambre_seule);
  setCheckedValue(oForm.chambre_seule, valeur_chambre);
  
  if(valeur_chambre == "0"){
    oForm.prestation_id.value = "";
  }
}

{{if $mode_operation}}

// Declaration d'un objet Sejour
var Sejour = {
  sejours_collision: [],
  
  
  // Preselectionne un sejour existant en fonction de la date d'intervention choisie
  preselectSejour: function(date_plage){
	  if (!date_plage){
	    return;
	  }
	  
	  var sejours_collision = this.sejours_collision;
	  var oForm = document.editSejour;
	  var sejour_courant_id = oForm.sejour_id.value;
	  	
		// Liste des sejours
		for (sejour_id in sejours_collision){
		  var entree_prevue = sejours_collision[sejour_id]["entree_prevue"];
		  var sortie_prevue = sejours_collision[sejour_id]["sortie_prevue"];
		  if ((entree_prevue <= date_plage) && (sortie_prevue >= date_plage)) {
		    if (sejour_courant_id != sejour_id){
		      var msg = printf("Vous �tes en train de planifier une intervention pour le %s, or il existe d�j� un s�jour pour ce patient du %s au %s. Souhaitez vous placer l'intervention dans ce s�jour ?", 
		                Date.fromDATE(date_plage).toLocaleDate(), 
		                Date.fromDATE(entree_prevue).toLocaleDate(),
		                Date.fromDATE(sortie_prevue).toLocaleDate());
		      
		      if (confirm(msg)){
		        Form.Element.setValue(oForm.sejour_id, sejour_id);
		      }
		    }
		  }
		}
  }
  
  
}


Main.add( function(){
  Sejour.sejours_collision = {{$sejours_collision|@json}};
  var oForm = document.editOp;
  Sejour.preselectSejour(oForm._date.value);
} );

{{/if}}
</script>

<!-- $Id: $ -->
{{mb_include_script module="dPpatients" script="pat_selector"}}
{{mb_include_script module="dPplanningOp" script="cim10_selector"}}

<form name="editSejour" action="?m={{$m}}" method="post" onsubmit="return checkSejour()">

<input type="hidden" name="m" value="dPplanningOp" />
<input type="hidden" name="dosql" value="do_sejour_aed" />
<input type="hidden" name="del" value="0" />
{{if $mode_operation}}
  <input type="hidden" name="callback" value="submitFormOperation" />
{{/if}}

<!-- Champ de copie des informations de l'intervention dans le cas ou il y en une -->
{{if $op->_id}}
  <input type="hidden" name="_curr_op_id"   value="{{$op->_id}}" />
  <input type="hidden" name="_curr_op_date" value="{{$op->_ref_plageop->date}}" />
{{else}}
  <input type="hidden" name="_curr_op_id"   value="" />
  <input type="hidden" name="_curr_op_date" value="" />
{{/if}}

{{mb_field object=$sejour field="saisi_SHS" hidden=1 prop=""}}
{{mb_field object=$sejour field="modif_SHS" hidden=1 prop=""}}
<input type="hidden" name="annule" value="{{$sejour->annule|default:"0"}}" />
<input type="hidden" name="septique" value="{{$sejour->septique|default:"0"}}" />
<input type="hidden" name="pathologie" value="{{$sejour->pathologie}}" />
{{if !$mode_operation}}
  {{mb_field object=$sejour field="sejour_id" hidden=1 prop=""}}
{{/if}}


<table class="form">

<tr>
  <th class="category" colspan="4">
    {{if $mode_operation && $sejour->sejour_id}}
    
    <div class="idsante400" id="CSejour-{{$sejour->sejour_id}}"></div>
  
    <a style="float:right;" href="#" onclick="view_log('CSejour',{{$sejour->sejour_id}})">
      <img src="images/icons/history.gif" alt="{{tr}}History.desc{{/tr}}" />
    </a>
    <div style="float:left;" class="noteDiv {{$sejour->_class_name}}-{{$sejour->_id}}">
        <img alt="Ecrire une note" src="images/icons/note_grey.png" />
    </div>
    <a class="action" style="float: right"  title="Modifier uniquement le sejour" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$sejour->_id}}">
      <img src="images/icons/edit.png" alt="modifier" />
     </a>
    {{/if}}
    {{tr}}msg-CSejour-informations{{/tr}} {{if $mode_operation && $sejour->_num_dossier}}[{{$sejour->_num_dossier}}]{{/if}}
  </th>
</tr>

{{if $sejour->annule}}
<tr>
  <th class="category cancelled" colspan="4">
  {{tr}}CSejour-annule{{/tr}}
  </th>
</tr>
{{/if}}

{{if $mode_operation}}
<tr>
  <th>
    Sejours existants
  </th>
  <td colspan="3" id="selectSejours">
    <select name="sejour_id" onchange="reloadSejour(this.value)">
      <option value="" {{if !$sejour->sejour_id}} selected="selected" {{/if}}>
        &mdash; Cr�er un nouveau s�jour
      </option>
      {{foreach from=$sejours item=curr_sejour}}
      <option value="{{$curr_sejour->_id}}" {{if $sejour->_id == $curr_sejour->_id}} selected="selected" {{/if}}>
        {{$curr_sejour->_view}}
        {{if $curr_sejour->annule}}({{tr}}Cancelled{{/tr}}){{/if}}
      </option>
      {{/foreach}}
    </select>
  </td>
</tr>
{{/if}}

<tr>
  <th>
    {{mb_label object=$sejour field="group_id"}}
  </th>
  <td colspan="3">
    <select class="{{$sejour->_props.group_id}}" name="group_id" onchange="removePlageOp(true);">
    {{foreach from=$etablissements item=curr_etab}}
      <option value="{{$curr_etab->group_id}}" {{if ($sejour->sejour_id && $sejour->group_id==$curr_etab->group_id) || (!$sejour->sejour_id && $g==$curr_etab->group_id)}} selected="selected"{{/if}}>{{$curr_etab->text}}</option>
    {{/foreach}}
    </select>
  </td>
</tr>

<tr>
  <th>
    {{mb_label object=$sejour field="praticien_id"}}
  </th>
  <td colspan="3">
    <select name="praticien_id" onchange="modifPrat()" class="{{$sejour->_props.praticien_id}}">
      <option value="">&mdash; Choisir un praticien</option>
      {{foreach from=$listPraticiens item=curr_praticien}}
      <option class="mediuser" style="border-color: #{{$curr_praticien->_ref_function->color}};" value="{{$curr_praticien->user_id}}" {{if $praticien->_id == $curr_praticien->user_id}} selected="selected" {{/if}}>
        {{$curr_praticien->_view}}
      </option>
      {{/foreach}}
    </select>
  </td>
</tr>

<tr>
  <th>
    <input type="hidden" name="patient_id" class="{{$sejour->_props.patient_id}}" ondblclick="PatSelector.init()" value="{{$patient->patient_id}}" onchange="changePat(); checkSejoursToReload();" />
    {{mb_label object=$sejour field="patient_id"}}
  </th>
  <td class="readonly">
  	<input type="text" name="_patient_view" size="20" value="{{$patient->_view}}" readonly="readonly"
  	  {{if $dPconfig.dPplanningOp.CSejour.patient_id || !$sejour->_id || $app->user_type == 1}}
  	    ondblclick="PatSelector.init()"
  	  {{/if}}
  	/>
  </td>
  <td colspan="2" class="button">
    {{if $dPconfig.dPplanningOp.CSejour.patient_id || !$sejour->_id || $app->user_type == 1}}
  	<button type="button" class="search" onclick="PatSelector.init()">Choisir un patient</button>
  	{{/if}}
    <script type="text/javascript">
      PatSelector.init = function(){
        bOldPat = document.editSejour.patient_id.value;
        
        this.sForm     = "editSejour";
        this.sFormEasy = "editOpEasy";
      
        this.sView_easy = "_patient_view"; 
        this.sId_easy   = "patient_id";
      
        this.sId   = "patient_id";
        this.sView = "_patient_view";
        this.pop();
      }
    </script>
  </td>
  
  
</tr>

<tr>
  <th>{{mb_label object=$sejour field="DP"}}</th>
  <td>{{mb_field object=$sejour field="DP" size="10"}}</td>
  <td colspan="2" class="button"><button type="button" class="search" onclick="CIM10Selector.init()">{{tr}}button-CCodeCIM10-choix{{/tr}}</button>
  <script type="text/javascript">
  CIM10Selector.init = function(){
    this.sForm = "editSejour";
    this.sView = "DP";
    this.sChir = "praticien_id";
    this.pop();
  }
  </script>
  </td>
</tr>
<tr>
  <th class="category" colspan="4">Admission</th>
</tr>

<tr>
  <th>{{mb_label object=$sejour field="_date_entree_prevue"}}</th>
  <td class="date">
    {{mb_field object=$sejour form="editSejour" field="_date_entree_prevue" prop="notNull date" onchange="modifSejour(); updateSortiePrevue();"}}
  </td>
  <td colspan="2">
    �
    <select name="_hour_entree_prevue" onchange="updateHeureSortie(); checkHeureSortie();">
    {{foreach from=$hours item=hour}}
      <option value="{{$hour}}" {{if $sejour->_hour_entree_prevue == $hour || (!$sejour->sejour_id && $hour == $heure_entree_jour)}} selected="selected" {{/if}}>{{$hour}}</option>
    {{/foreach}}
    </select> h
    <select name="_min_entree_prevue" onchange="updateHeureSortie()">
    {{foreach from=$mins item=min}}
      <option value="{{$min}}" {{if $sejour->_min_entree_prevue == $min}} selected="selected" {{/if}}>{{$min}}</option>
    {{/foreach}}
    </select> mn
  </td>
</tr>

<tr>
  <th>{{mb_label object=$sejour field="_duree_prevue"}}</th>
  <td>
    <input type="text" name="_duree_prevue" class="num min|0" value="{{if $sejour->sejour_id}}{{$sejour->_duree_prevue}}{{else}}0{{/if}}" size="4" onchange="updateSortiePrevue()" />
    nuits
  </td>
  <td id="dureeEst" colspan="2">
  </td>
</tr>

<tr {{if $mode_operation && !$can->admin}} style="display: none" {{/if}}>
  <th>{{mb_label object=$sejour field="_date_sortie_prevue"}}</th>
  <td class="date">
    {{mb_field object=$sejour form="editSejour" field="_date_sortie_prevue" prop="notNull date moreEquals|_date_entree_prevue" onchange="updateDureePrevue(); modifSejour()"}}
  </td>
  <td colspan="2">
    � 
    <select name="_hour_sortie_prevue">
    {{foreach from=$hours item=hour}}
      <option value="{{$hour}}" {{if $sejour->_hour_sortie_prevue == $hour  || (!$sejour->sejour_id && $hour == $heure_sortie_ambu)}} selected="selected" {{/if}}>{{$hour}}</option>
    {{/foreach}}
    </select> h
    <select name="_min_sortie_prevue">
    {{foreach from=$mins item=min}}
      <option value="{{$min}}" {{if $sejour->_min_sortie_prevue == $min}} selected="selected" {{/if}}>{{$min}}</option>
    {{/foreach}}
    </select> mn
  </td>
</tr>

{{if !$mode_operation}}
<tr>
  <th>{{mb_label object=$sejour field=entree_reelle}}</th>
  <td class="date" colspan="3">
    {{if $can->edit}}
    {{mb_field object=$sejour field=entree_reelle form=editSejour}}
    {{else}}
		{{mb_value object=$sejour field=entree_reelle}}
		{{/if}}    
  </td>
</tr>

<tr>
  <th>{{mb_label object=$sejour field=sortie_reelle}}</th>
  <td class="date" colspan="3">
    {{if $can->edit}}
    {{mb_field object=$sejour field=sortie_reelle form=editSejour}}
    {{else}}
		{{mb_value object=$sejour field=sortie_reelle}}
		{{/if}}    
  </td>
</tr>

<tr>
  <th>{{mb_label object=$sejour field=mode_sortie}}</th>
  <td colspan="3">
    {{if $can->view}}
      {{mb_field object=$sejour defaultOption="&mdash; Mode de sortie" field=mode_sortie onchange="loadTransfert(this.form, this.value);"}}
      <span id="listEtabExterne">
        {{if $sejour->_id}}
          {{$sejour->_ref_etabExterne->_view}}
        {{/if}}
      </span>
    {{else}}
	  {{mb_value object=$sejour field=mode_sortie}}
	{{/if}}    
  </td>
</tr>



{{/if}}


<tr>
  <th>{{mb_label object=$sejour field="type"}}</th>
  <td>
    <select name="type" onchange="changeTypeHospi(this.value)">
    {{foreach from=$sejour->_enumsTrans.type item="curr_type" key="key"}}
     {{if $key == 'urg'}}
       {{if $urgInstalled}}
       <option value="{{$key}}" {{if $sejour->type == $key}}selected="selected"{{/if}}>{{$curr_type}}</option>
       {{/if}}
     {{else}}
       <option value="{{$key}}" {{if $sejour->type == $key}}selected="selected"{{/if}}>{{$curr_type}}</option>
     {{/if}}
    {{/foreach}}
    </select>
  
  </td>
  <td colspan="2">
    <div id="showFor-comp" {{if !$sejour->_id || $sejour->type != "comp"}} style="display: none;" {{/if}}>
      {{mb_label object=$sejour field="reanimation"}}{{mb_field object=$sejour field="reanimation"}}
    </div>
  </td>
</tr>

<tr>
  <th>{{mb_label object=$sejour field="zt"}}</th>
  <td colspan="3">
    {{mb_field object=$sejour field="zt"}}
  </td>
</tr>

<tr>
  <th>{{mb_label object=$sejour field="facturable"}}</th>
  <td colspan="3">
  {{mb_field object=$sejour field="facturable"}}
  </td>
</tr>

<tr {{if $mode_operation}}style="display: none;"{{/if}}>
  <th>{{mb_label object=$sejour field="modalite" typeEnum="radio"}}</th>
  <td colspan="3">
    {{mb_field object=$sejour field="modalite" typeEnum="radio"}}
  </td>
</tr>

{{if $can->edit}}
<tr {{if $mode_operation}}style="display: none;"{{/if}}>
  <th>{{mb_label object=$sejour field="ATNC"}}</th>
  <td>
    {{mb_field object=$sejour field="ATNC"}}
  </td>
  <th>{{mb_label object=$sejour field="hormone_croissance"}}</th>
  <td>
    {{mb_field object=$sejour field="hormone_croissance"}}
  </td>  
</tr>
{{/if}}

<tr>
  <th>{{mb_label object=$sejour field="chambre_seule"}}</th>
  <td {{if $mode_operation}}colspan="3"{{/if}}>
    {{mb_field object=$sejour field="chambre_seule" onchange="checkChambreSejour();"}}
  </td>
  
  {{if !$mode_operation}}
  <th>{{mb_label object=$sejour field="lit_accompagnant"}}</th>
  <td>
    {{mb_field object=$sejour field="lit_accompagnant"}}
  </td>
  {{/if}}
</tr>

{{if $prestations}}
<tr>
<th>{{mb_label object=$sejour field=prestation_id}}</th>
  <td>
  <select name="prestation_id" onchange="checkPresta();">
  <option value="">&mdash; Choix d'une prestation</option>
  {{foreach from=$prestations item="_prestation"}}
    <option value="{{$_prestation->_id}}" {{if $sejour->prestation_id == $_prestation->_id}}selected = selected{{/if}}>{{$_prestation->_view}}</option>
  {{/foreach}}
  </select>
  </td>
  {{if $mode_operation}}
  <td colspan="2"></td>
  {{/if}}
</tr>
{{/if}}

<!-- Si on est pas en mode operation, on affiche la suite -->
{{if !$mode_operation}}
<tr>
	{{if $can->edit}}
  <th>{{mb_label object=$sejour field="repas_sans_sel"}}</th>
  <td>
    {{mb_field object=$sejour field="repas_sans_sel"}}
  </td>
	{{/if}}

</tr>

{{if $can->edit}}
<tr>
  <th>{{mb_label object=$sejour field="isolement"}}</th>
  <td>
    {{mb_field object=$sejour field="isolement"}}
  </td>
  <th>{{mb_label object=$sejour field="repas_diabete"}}</th>
  <td>
    {{mb_field object=$sejour field="repas_diabete"}}
  </td>
</tr>
{{/if}}

<tr>
  <th>{{mb_label object=$sejour field="television"}}</th>
  <td>
    {{mb_field object=$sejour field="television"}}
  </td>
	{{if $can->edit}}
  <th>{{mb_label object=$sejour field="repas_sans_residu"}}</th>
  <td>
    {{mb_field object=$sejour field="repas_sans_residu"}}
  </td>
	{{/if}}
</tr>

{{/if}}

<tr>
  <td class="text">
    {{mb_label object=$sejour field="convalescence"}}
  </td>
  <td class="text" colspan="3">
    {{mb_label object=$sejour field="rques"}}
  </td>
</tr>

<tr>
  <td>
    {{mb_field object=$sejour field="convalescence" rows="3"}}
  </td>
  <td colspan="3">
    {{mb_field object=$sejour field="rques" rows="3"}}
  </td>
</tr>


{{if !$mode_operation}}
<tr>
  <td class="button" colspan="4">
  {{if $sejour->sejour_id}}
    <button class="modify" type="submit">Modifier</button>
    <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le {{$sejour->_view|smarty:nodefaults|JSAttribute}}'});">
      Supprimer
    </button>
    {{mb_ternary var=annule_text test=$sejour->annule value="R�tablir" other="Annuler"}}
    {{mb_ternary var=annule_class test=$sejour->annule value="change" other="cancel"}}
    <button class="{{$annule_class}}" type="button" onclick="cancelSejour();">
      {{$annule_text}}
    </button>
  {{else}}
    <button class="submit" type="submit">Cr�er</button>
  {{/if}}
  </td>
</tr>
{{/if}}

</table>

</form>

<script type="text/javascript">

// Explicit form preparation for Ajax loading
prepareForm(document.editSejour);

dates = {
  current: {
    start: "{{$sejour->_date_entree_prevue}}",
    stop: "{{$sejour->_date_sortie_prevue}}"
  },
  spots: {{$sejour->_dates_operations|@json}}
}

Calendar.regField("editSejour", "entree_reelle", true, dates);
Calendar.regField("editSejour", "sortie_reelle", true, dates);

dates.limit = {
  start: null,
  stop: dates.spots.first()
}

Calendar.regField("editSejour", "_date_entree_prevue", false, dates);

dates.limit = {
  start: dates.spots.last(),
  stop: null 
}

Calendar.regField("editSejour", "_date_sortie_prevue", false, dates);

removePlageOp(false);

</script>
