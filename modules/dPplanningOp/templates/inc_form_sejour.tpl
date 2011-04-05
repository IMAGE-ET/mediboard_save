{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage [subpackage]
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}
 
{{mb_script module="dPpatients" script="pat_selector"}}
{{mb_script module="dPplanningOp" script="cim10_selector"}}

<script type="text/javascript">
function checkHeureSortie(){
  var oForm = getForm("editSejour");
  var heure_entree = parseInt(oForm._hour_entree_prevue.value, 10);
  
  if (oForm._hour_sortie_prevue.value < heure_entree + 1) {
    heure_entree++;
    oForm._hour_sortie_prevue.value = heure_entree;
  }
}

function loadTransfert(mode_sortie){
  $('listEtabExterne').setVisible(mode_sortie == "transfert");
}

function loadServiceMutation(mode_sortie){
  $('services').setVisible(mode_sortie == "mutation");
}

function changeModeSortie(mode_sortie){
  loadTransfert(mode_sortie);
  loadServiceMutation(mode_sortie);
}

function checkModeSortie(){
  var oForm = getForm("editSejour");
  
  if(oForm.sortie_reelle && oForm.sortie_reelle.value && !oForm.mode_sortie.value) {
    alert("Date de sortie r�elle et mode de sortie incompatibles");
    return false;
  }
  
  return true;
}

function checkSejour() {
  var oForm = getForm("editSejour");
  return checkDureeHospi() && checkModeSortie() && OccupationServices.testOccupation() && checkForm(oForm);
}

function checkPresta(){
  var oForm = getForm("editSejour");
  var oFormEasy = getForm("editOpEasy");
  if($V(oForm.prestation_id) != ""){
    if (oForm) {
      $V(oForm.chambre_seule, "1");
    }
    if (oFormEasy) {
      $V(oFormEasy.chambre_seule, "1");
    }
  } 
}

function checkChambreSejour(){
  var oForm = getForm("editSejour");
  var oFormEasy = getForm("editOpEasy");
  var valeur_chambre = $V(oForm.chambre_seule);
  
  if (oFormEasy)
    $V(oFormEasy.chambre_seule, valeur_chambre, false);
  
  if(valeur_chambre == "0")
    $V(oForm.prestation_id, "", false);
}


function checkChambreSejourEasy(){
  var oForm = getForm("editSejour");
  var oFormEasy = getForm("editOpEasy");
  
  if (oFormEasy){
    var valeur_chambre = $V(oFormEasy.chambre_seule);
    $V(oForm.chambre_seule, valeur_chambre);
    
    if(valeur_chambre == "0"){
      $V(oForm.prestation_id, "", false);
    }
  }
}

function checkConsultAccompSejour(){
  var oForm = getForm("editSejour");
  var oFormEasy = getForm("editOpEasy");
  var valeur_consult = $V(oForm.consult_accomp);
  
  if (oFormEasy)
    $V(oFormEasy.consult_accomp, valeur_consult, false);
}


function checkConsultAccompSejourEasy(){
  var oForm = getForm("editSejour");
  var oFormEasy = getForm("editOpEasy");
  
  if (oFormEasy){
    var valeur_consult = $V(oFormEasy.consult_accomp);
    $V(oForm.consult_accomp, valeur_consult);
  }
}

function printFormSejour() {
  var url = new Url;
  url.setModuleAction("dPplanningOp", "view_planning"); 
  url.addParam("sejour_id", $V(getForm("editSejour").sejour_id));
  url.popup(700, 500, "printSejour");
  return;
}

PatSelector.init = function(){
  bOldPat = $V(getForm("editSejour").patient_id);
  this.sForm     = "editSejour";
  this.sFormEasy = "editOpEasy";

  this.sView_easy = "_patient_view"; 
  this.sId_easy   = "patient_id";

  this.sId   = "patient_id";
  this.sView = "_patient_view";

  this.pop();
}

checkCorrespondantMedical = function(form){
  var url = new Url("dPplanningOp", "ajax_check_correspondant_medical");
  url.addParam("patient_id", $V(form.patient_id));
  url.addParam("object_id" , $V(form.sejour_id));
  url.addParam("object_class", '{{$sejour->_class_name}}');
  url.requestUpdate("correspondant_medical");
}

CIM10Selector.init = function(){
  this.sForm = "editSejour";
  this.sView = "DP";
  this.sChir = "praticien_id";
  this.pop();
}

Medecin = {
  form: null,
  edit : function() {
    this.form = getForm("editSejour");
    var url = new Url("dPpatients", "vw_medecins");
    url.popup(700, 450, "Medecin");
  },
  
  set: function(id, view) {
    $('_adresse_par_prat').show().update('Autres : '+view);
    $V(this.form.adresse_par_prat_id, id);
    $V(this.form._correspondants_medicaux, '', false);
  }
};

{{if $mode_operation}}
// Declaration d'un objet Sejour
var Sejour = {
  sejours_collision: null,
  
  // Preselectionne un sejour existant en fonction de la date d'intervention choisie
  preselectSejour: function(date_plage){
    if (!date_plage){
      return;
    }
    
    var sejours_collision = this.sejours_collision;
    var oForm = getForm("editSejour");
    var sejour_courant_id = $V(oForm.sejour_id);
    // Liste des sejours
    if(sejours_collision instanceof Array) {
      return;
    }
    for (sejour_id in sejours_collision){
      var entree = sejours_collision[sejour_id]["entree"];
      var sortie = sejours_collision[sejour_id]["sortie"];
      if ((entree <= date_plage) && (sortie >= date_plage)) {
        if (sejour_courant_id != sejour_id){
          var msg = printf("Vous �tes en train de planifier une intervention pour le %s, or il existe d�j� un s�jour pour ce patient du %s au %s. Souhaitez-vous placer l'intervention dans ce s�jour ?", 
                    Date.fromDATE(date_plage).toLocaleDate(), 
                    Date.fromDATE(entree).toLocaleDate(),
                    Date.fromDATE(sortie).toLocaleDate());
                    
          if (confirm(msg)){
            $V(oForm.sejour_id, sejour_id);
          }
        }
      }
    }
  }
}

Main.add( function(){
  Sejour.sejours_collision = {{$sejours_collision|@json}};
  var oForm = getForm("editOp");
  Sejour.preselectSejour($V(oForm._date));
});
{{/if}}

Main.add( function(){
  var form = getForm("editSejour");
  
  dates = {
    current: {
      start: "{{$sejour->_date_entree_prevue}}",
      stop: "{{$sejour->_date_sortie_prevue}}"
    },
    spots: []
  };

  var options = {
    exactMinutes: false, 
    minInterval: {{$conf.dPplanningOp.CSejour.min_intervalle}},
    minHours: {{$conf.dPplanningOp.CSejour.heure_deb}},
    maxHours: {{$conf.dPplanningOp.CSejour.heure_fin}}
  };
  
  // Object.value takes the internal functions too :(
  var dates_operations = {{$sejour->_dates_operations|@json}};
  $H(dates_operations).each(function(p){
    if (!Object.isFunction(p.value))
      dates.spots.push(p.value);
  });
  
  Calendar.regField(form.entree_reelle, dates);
  Calendar.regField(form.sortie_reelle, dates);
  
// Constraints make intervention moving fastidious
//  dates.limit = {
//    start: null,
//    stop: dates.spots.first()
//  };
  
  Calendar.regField(form._date_entree_prevue, dates);
  
// Constraints make intervention moving fastidious
//  dates.limit = {
//    start: dates.spots.last(),
//    stop: null 
//  };
  
  Calendar.regField(form._date_sortie_prevue, dates);
  
  removePlageOp(false);
  OccupationServices.initOccupation();
  OccupationServices.configBlocage = ({{$conf.dPplanningOp.CSejour.blocage_occupation|@json}} == "1") && !{{$modules.dPcabinet->_can->edit|@json}};
  
});
</script>

<!-- div de confirmation de changement de patient lorsqu'on a un sejour_id -->
{{mb_include template=inc_modal_change_patient}}


<form name="editSejour" action="?m={{$m}}" method="post" onsubmit="return checkSejour()">

<input type="hidden" name="m" value="dPplanningOp" />
<input type="hidden" name="dosql" value="do_sejour_aed" />
<input type="hidden" name="del" value="0" />
{{if $sejour->sortie_reelle && !$can->admin}}
<!-- <input type="hidden" name="_locked" value="1" /> -->
{{/if}}

{{mb_field object=$sejour field="codes_ccam" hidden=1}}

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

{{mb_field object=$sejour field="saisi_SHS" hidden=1}}
{{mb_field object=$sejour field="modif_SHS" hidden=1}}
<input type="hidden" name="annule" value="{{$sejour->annule|default:"0"}}" />
<input type="hidden" name="septique" value="{{$sejour->septique|default:"0"}}" />
<input type="hidden" name="pathologie" value="{{$sejour->pathologie}}" />

<input type="hidden" name="adresse_par_prat_id" value="{{$sejour->adresse_par_prat_id}}" />
{{if !$mode_operation}}
  {{mb_key object=$sejour}}
{{/if}}

<table class="form">
<col style="width:25%" />
<col style="width:55%" />
<col style="width:10%" />
<col style="width:10%" />
<tr>
  <th class="category" colspan="4">
    {{if $mode_operation && $sejour->_id}}

    {{mb_include module=system template=inc_object_idsante400 object=$sejour}}
    {{mb_include module=system template=inc_object_history    object=$sejour}}
    {{mb_include module=system template=inc_object_notes      object=$sejour}}

    <a class="action" style="float: right"  title="Modifier uniquement le sejour" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$sejour->_id}}">
      <img src="images/icons/edit.png" alt="modifier" />
     </a>
    {{/if}}
    {{tr}}CSejour-msg-informations{{/tr}} 
		{{if $mode_operation && $sejour->_num_dossier}}
		{{mb_include module=dPplanningOp template=inc_vw_numdos num_dossier=$sejour->_num_dossier}}
		{{/if}}
  </th>
</tr>

{{if $sejour->annule}}
<tr>
  <th class="category cancelled" colspan="4">
  {{tr}}CSejour-{{$sejour->recuse|ternary:"recuse":"annule"}}{{/tr}}
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
      <option value="" {{if !$sejour->_id}} selected="selected" {{/if}}>
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
    {{if !$sejour->_id || $can->admin}}
    <select class="{{$sejour->_props.group_id}}" name="group_id" onchange="removePlageOp(true);">
    {{foreach from=$etablissements item=curr_etab}}
      <option value="{{$curr_etab->group_id}}" {{if ($sejour->sejour_id && $sejour->group_id==$curr_etab->group_id) || (!$sejour->sejour_id && $g==$curr_etab->group_id)}} selected="selected"{{/if}}>{{$curr_etab->_view}}</option>
    {{/foreach}}
    </select>
    {{else}}
      {{$sejour->_ref_group->_view}}
      {{mb_field object=$sejour field=group_id hidden=hidden}}
    {{/if}}
  </td>
</tr>

<tr>
  <th>
    {{mb_label object=$sejour field=praticien_id}}
  </th>
  <td colspan="3">
    {{if $sejour->praticien_id && !array_key_exists($sejour->praticien_id, $listPraticiens)}}
    {{mb_field object=$sejour field=praticien_id hidden=1}}
    {{mb_value object=$sejour field=praticien_id}}
    {{else}} 
    <select name="praticien_id" onchange="modifPrat()" class="{{$sejour->_props.praticien_id}}" style="max-width: 150px;">
      <option value="">&mdash; Choisir un praticien</option>
      {{foreach from=$listPraticiens item=curr_praticien}}
      <option class="mediuser" style="border-color: #{{$curr_praticien->_ref_function->color}};" value="{{$curr_praticien->user_id}}" {{if $praticien->_id == $curr_praticien->user_id}} selected="selected" {{/if}}>
        {{$curr_praticien}}
      </option>
      {{/foreach}}
    </select>
    {{/if}}
  </td>
</tr>

<tr>
  <th>
  	<script type="text/javascript">
  		if (window.reloadSejours == null) {
			  reloadSejours = Prototype.emptyFunction;
			}
  	</script>
    <input type="hidden" name="patient_id" class="{{$sejour->_props.patient_id}}" value="{{$patient->_id}}" 
		  onchange="changePat(); checkSejoursToReload(); checkCorrespondantMedical(this.form); reloadSejours();" />
    {{mb_label object=$sejour field="patient_id"}}
  </th>
  <td>
    <input type="text" name="_patient_view" size="20" value="{{$patient->_view}}" readonly="readonly"
           ondblclick="
    {{if !$sejour->_id}}
      PatSelector.init();
    {{else}}
      confirmChangePatient();
    {{/if}}"/>
  </td>
  <td colspan="2" class="button">
  <button type="button" class="search" onclick="
	  {{if !$sejour->_id}}
      PatSelector.init();
    {{else}}
      confirmChangePatient();
    {{/if}}">Choisir un patient</button>
  </td>
</tr>

<tr>
  <th>{{mb_label object=$sejour field="libelle"}}</th>
  <td colspan="3">{{mb_field object=$sejour field="libelle" form="editSejour" autocomplete="true,1,50,true,true"}}</td>
</tr>

<tr>
  <th>
    {{mb_label object=$sejour field="service_id"}}
  </th>
  <td colspan="3">
    <select name="service_id" class="{{$sejour->_props.service_id}}" onchange="if(getForm('editOp')) {synchroService(this)};" style="max-width: 150px;">
      <option value="">&mdash; Choisir un service</option>
      {{foreach from=$listServices item=_service}}
      <option value="{{$_service->_id}}" {{if $sejour->service_id == $_service->_id}} selected="selected" {{/if}}>
        {{$_service->_view}}
      </option>
      {{/foreach}}
    </select>
  </td>
</tr>

<tr>
  <th>{{mb_label object=$sejour field="DP"}}</th>
  <td colspan="3">
    <script type="text/javascript">
    Main.add(function(){
      var url = new Url("dPcim10", "ajax_code_cim10_autocomplete");
      url.autoComplete(getForm("editSejour").keywords_code, '', {
        minChars: 1,
        dropdown: true,
        width: "250px",
        select: "code",
        afterUpdateElement: function(oHidden) {
          $V(getForm("editSejour").DP, oHidden.value);
        }
      });
    });
    </script>
    
    <input type="text" name="keywords_code" class="autocomplete str code cim10" value="{{$sejour->DP}}" size="10"/>
    <button type="button" class="search" onclick="CIM10Selector.init()">{{tr}}button-CCodeCIM10-choix{{/tr}}</button>
    <input type="hidden" name="DP" onchange="$V(this.form.keywords_code, this.value);"/>
  </td>
</tr>

{{if $sejour->annule}}
<tr>
  <th>{{mb_label object=$sejour field="recuse"}}</th>
  <td colspan="3">{{mb_field object=$sejour field="recuse"}}</td>
</tr>
{{/if}}

<tr>
  <th class="category" colspan="4">Admission</th>
</tr>

<tr>
  <th>{{mb_label object=$sejour field="_date_entree_prevue"}}</th>
  <td>
    {{mb_field object=$sejour form=editSejour field=_date_entree_prevue canNull=false 
		  onchange="OccupationServices.updateOccupation(); modifSejour(); updateSortiePrevue(); reloadSejours(true);"}}
  </td>
  <td colspan="2">
    �
    <select name="_hour_entree_prevue" onchange="updateHeureSortie(); checkHeureSortie(); reloadSejours(true);">
    {{foreach from=$hours item=hour}}
      <option value="{{$hour}}" {{if $sejour->_hour_entree_prevue == $hour || (!$sejour->sejour_id && $hour == $heure_entree_jour)}} selected="selected" {{/if}}>{{$hour}}</option>
    {{/foreach}}
    </select> h
    <select name="_min_entree_prevue" onchange="updateHeureSortie(); checkHeureSortie(); reloadSejours(true);">
    {{foreach from=$mins item=min}}
      <option value="{{$min}}" {{if $sejour->_min_entree_prevue == $min}} selected="selected" {{/if}}>{{$min}}</option>
    {{/foreach}}
    </select> min
  </td>
</tr>

<tr>
  <th>{{mb_label object=$sejour field="_duree_prevue"}}</th>
  <td>
    {{mb_field object=$sejour field="_duree_prevue" increment=true form=editSejour prop="num min|0" size=2 onchange="updateSortiePrevue(); checkDureeHospi('syncType');" value=$sejour->sejour_id|ternary:$sejour->_duree_prevue:0}}
    nuits
  </td>
  <td id="dureeEst" colspan="2">
  </td>
</tr>

<tr {{if $mode_operation && !$can->admin}} style="display: none" {{/if}}>
  <th>{{mb_label object=$sejour field="_date_sortie_prevue"}}</th>
  <td>
    {{mb_field object=$sejour form=editSejour field=_date_sortie_prevue canNull=false 
		  onchange="updateDureePrevue(); modifSejour(); reloadSejours(true);"}}
  </td>
  <td colspan="2">
    � 
    <select name="_hour_sortie_prevue" onchange="reloadSejours(true);">
    {{foreach from=$hours item=hour}}
      <option value="{{$hour}}" {{if $sejour->_hour_sortie_prevue == $hour  || (!$sejour->sejour_id && $hour == $heure_sortie_ambu)}} selected="selected" {{/if}}>{{$hour}}</option>
    {{/foreach}}
    </select> h
    <select name="_min_sortie_prevue"  onchange="reloadSejours(true);">
    {{foreach from=$mins item=min}}
      <option value="{{$min}}" {{if $sejour->_min_sortie_prevue == $min}} selected="selected" {{/if}}>{{$min}}</option>
    {{/foreach}}
    </select> min
  </td>
</tr>

<tr>
  <th>{{mb_label object=$sejour field="type"}}</th>
  <td>
    <select name="type" onchange="changeTypeHospi(); OccupationServices.updateOccupation(); checkDureeHospi('syncDuree');">
    {{assign var=specType value=$sejour->_specs.type}}
    {{foreach from=$specType->_locales item="curr_type" key="key"}}
      <option value="{{$key}}"
        {{if $sejour->type == $key || (!$sejour->type && $key == $specType->default)}}selected="selected"{{/if}}>
        {{$curr_type}}
      </option>
    {{/foreach}}
    </select>
  </td>
  
  <th class="reanimation">{{mb_label object=$sejour field="reanimation"}}</th>
  <td class="reanimation">
    {{mb_field object=$sejour field="reanimation"}}
    <script type="text/javascript">
      function changeTypeHospi() {
        var oForm = getForm("editSejour");
        var sValue = $V(oForm.type);
        if (sValue != "comp") {
          $V(oForm.reanimation, '0');
        }
        
        $(oForm).select(".reanimation").invoke(sValue == "comp" ? "show" : "hide");
      }

    Main.add(changeTypeHospi);
    </script>
  </td>
</tr>

<tr>
  <th>Taux d'occupation</th>
  <td colspan="3" id="occupation">
  </td>
</tr>

{{if $conf.dPplanningOp.CSejour.consult_accomp}}
<tr>
  <th>{{mb_label object=$sejour field=consult_accomp}}</th>
  <td colspan="3">{{mb_field object=$sejour field=consult_accomp typeEnum=radio onchange="checkConsultAccompSejour();"}}</td>
</tr>
{{/if}}

<tbody class="modeExpert">
{{if !$mode_operation}}

<tr>
  <th>{{mb_label object=$sejour field=entree_reelle}}</th>
  <td colspan="3">
    {{if $can->edit}}
    {{mb_field object=$sejour field=entree_reelle form=editSejour}}
    {{else}}
    {{mb_value object=$sejour field=entree_reelle}}
    {{/if}}    
  </td>
</tr>

<tr>
  <th>{{mb_label object=$sejour field=sortie_reelle}}</th>
  <td colspan="3">
    {{if $can->edit}}
    {{mb_field object=$sejour field=sortie_reelle form=editSejour}}
    {{else}}
    {{mb_value object=$sejour field=sortie_reelle}}
    {{/if}}    
  </td>
</tr>

<tr>
  <th>{{mb_label object=$sejour field=mode_sortie}}</th>
  <td>
    {{if $can->view}}
      {{mb_field object=$sejour defaultOption="&mdash; Mode de sortie" field=mode_sortie onchange="changeModeSortie(this.value);"}}
      <div id="listEtabExterne" {{if !$sejour->etablissement_transfert_id}}style="display:none"{{/if}}>
        {{mb_field object=$sejour field="etablissement_transfert_id" form="editSejour" autocomplete="true,1,50,true,true"}}
      </div>
      <div id="services" {{if !$sejour->service_mutation_id}}style="display:none"{{/if}}>
        {{mb_field object=$sejour field="service_mutation_id" form="editSejour" autocomplete="true,1,50,true,true"}}
      </div>
    {{else}}
      {{mb_value object=$sejour field=mode_sortie}}
    {{/if}}    
  </td>
  <th><strong>{{mb_label object=$sejour field=_sortie_autorisee}}</strong></th>
  <td><strong>{{mb_value object=$sejour field=_sortie_autorisee}}</strong></td>
</tr>
{{/if}}

<tr id="correspondant_medical">
  {{assign var="object" value=$sejour}}
  {{include file="inc_check_correspondant_medical.tpl"}}
</tr>
<tr>
  <td></td>
  <td colspan="3">
    <div id="_adresse_par_prat" style="{{if !$medecin_adresse_par}}display:none{{/if}}; width: 300px;">
      {{if $medecin_adresse_par}}Autres : {{$medecin_adresse_par->_view}}{{/if}}
    </div>
  </td>
</tr>

{{if $listEtab|@count}}
<tr>
  <th>{{mb_label object=$sejour field=adresse_par_etab_id}}</th>
  <td colspan="3">
	{{mb_field object=$sejour field=adresse_par_etab_id form="editSejour" autocomplete="true,1,50,true,true"}}
	</td>
</tr>
{{/if}}

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

<tr {{if $mode_operation}}style="display: none;"{{/if}}>
  <th>{{mb_label object=$sejour field="ATNC"}}</th>
  <td>{{mb_field object=$sejour field="ATNC"}}</td>
  <th>{{mb_label object=$sejour field="hormone_croissance"}}</th>
  <td>{{mb_field object=$sejour field="hormone_croissance"}}</td>  
</tr>
</tbody>
<tr>
  <th>{{mb_label object=$sejour field="chambre_seule"}}</th>
  <td>
    {{mb_field object=$sejour field="chambre_seule" onchange="checkChambreSejour();"}}
    {{if $mode_operation}}
      {{mb_field object=$sejour field="repas_sans_sel"     hidden="hidden"}}
      {{mb_field object=$sejour field="repas_sans_porc"    hidden="hidden"}}
      {{mb_field object=$sejour field="repas_diabete"      hidden="hidden"}}
      {{mb_field object=$sejour field="repas_sans_residu"  hidden="hidden"}}
    {{/if}}
  </td>
  
  {{if $mode_operation}}
  <td colspan="2" class="button">
    <button type="button" class="new" onclick="popRegimes()">R�gime alimentaire</button>
  </td>
  {{else}}
  <th class="modeExpert">{{mb_label object=$sejour field="repas_sans_sel"}}</th>
  <td class="modeExpert">{{mb_field object=$sejour field="repas_sans_sel"}}</td>
  {{/if}}
</tr>
<tbody class="modeExpert">
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
  <td colspan="2" />
  {{/if}}
</tr>
{{/if}}

<!-- Si on est pas en mode operation, on affiche la suite -->
{{if !$mode_operation}}
<tr>
  <th>{{mb_label object=$sejour field="lit_accompagnant"}}</th>
  <td>{{mb_field object=$sejour field="lit_accompagnant"}}</td>
  <th>{{mb_label object=$sejour field="repas_sans_porc"}}</th>
  <td>{{mb_field object=$sejour field="repas_sans_porc"}}</td>
</tr>

<tr>
  <th>{{mb_label object=$sejour field="isolement"}}</th>
  <td>{{mb_field object=$sejour field="isolement"}}</td>
  <th>{{mb_label object=$sejour field="repas_diabete"}}</th>
  <td>{{mb_field object=$sejour field="repas_diabete"}}</td>
</tr>

<tr>
  <th>{{mb_label object=$sejour field="television"}}</th>
  <td>{{mb_field object=$sejour field="television"}}</td>
  <th>{{mb_label object=$sejour field="repas_sans_residu"}}</th>
  <td>{{mb_field object=$sejour field="repas_sans_residu"}}</td>
</tr>

<tr>
  <th>{{mb_label object=$sejour field="forfait_se"}}</th>
  <td>{{mb_field object=$sejour field="forfait_se"}}</td>
  <th></th>
  <td></td>
</tr>
{{/if}}
</tbody>
<tr>
  <td class="text">{{mb_label object=$sejour field="convalescence"}}</td>
  <td class="text" colspan="3">{{mb_label object=$sejour field="rques"}}</td>
</tr>
<tr>
  <td>
    <script type="text/javascript">
      Main.add(function() {
        new AideSaisie.AutoComplete(getForm("editSejour").elements.convalescence, {
          objectClass: "{{$sejour->_class_name}}",
          timestamp: "{{$conf.dPcompteRendu.CCompteRendu.timestamp}}",
          validateOnBlur: 0
        });
      });
    </script>
    {{mb_field object=$sejour field="convalescence" rows="3"}}
  </td>
  <td colspan="3">
    <script type="text/javascript">
      Main.add(function() {
        new AideSaisie.AutoComplete(getForm("editSejour").elements.rques, {
          objectClass: "{{$sejour->_class_name}}",
          timestamp: "{{$conf.dPcompteRendu.CCompteRendu.timestamp}}",
          validateOnBlur: 0
        });
      });
    </script>
    {{mb_field object=$sejour field="rques" rows="3"}}
  </td>
</tr>
<tbody class="modeExpert">
{{if !$sejour->_id && array_key_exists("dPprescription", $modules)}}
<tr>
  <th>{{tr}}CProtocole-protocole_prescription_chir_id{{/tr}}</th>
  <td colspan="3">
    <script type="text/javascript">
    Main.add(function(){
      var form = getForm("editSejour");
      var url = new Url("dPprescription", "httpreq_vw_select_protocole");
      var autocompleter = url.autoComplete(form.libelle_protocole, 'protocole_auto_complete', {
        minChars: 1,
        dropdown: true,
        width: "250px",
        updateElement: function(selectedElement) {
          var node = $(selectedElement).down('.view');
          $V(form.libelle_protocole, node.innerHTML.replace("&lt;", "<").replace("&gt;",">"));
          if (autocompleter.options.afterUpdateElement)
            autocompleter.options.afterUpdateElement(autocompleter.element, selectedElement);
        },
        callback: function(input, queryString){
          return (queryString + "&praticien_id=" + $V(form.praticien_id));
        },
        valueElement: form.elements._protocole_prescription_chir_id
      });
    });
    </script>
    
    <input type="text" name="libelle_protocole" class="autocomplete str" value=""/>
    <div style="display:none; width: 150px;" class="autocomplete" id="protocole_auto_complete"></div>
    <input type="hidden" name="_protocole_prescription_chir_id" />
  </td>
</tr>
{{/if}}
</tbody>


{{if !$mode_operation}}
<tr>
  <td class="button text" colspan="4">
  {{if $sejour->sejour_id}}
  {{if !$sejour->sortie_reelle || $can->admin}}
    <button class="submit" type="submit">{{tr}}Save{{/tr}}</button>
    {{mb_ternary var=annule_text test=$sejour->annule value="Restore" other="Cancel"}}
    {{mb_ternary var=annule_class test=$sejour->annule value="change" other="cancel"}}
    <button class="{{$annule_class}}" type="button" onclick="cancelSejour();">
      {{tr}}{{$annule_text}}{{/tr}}
    </button>
    {{if !$conf.dPplanningOp.CSejour.delete_only_admin || $can->admin}}
      <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le {{$sejour->_view|smarty:nodefaults|JSAttribute}}'});">
        {{tr}}Delete{{/tr}}
      </button>
    {{/if}}
    
    <button class="print" type="button" onclick="printFormSejour();">{{tr}}Print{{/tr}}</button>
  {{else}}
    <div class="big-info">
      Les informations sur le s�jour ne peuvent plus �tre modifi�es car <strong>le patient est d�j� sorti de l'�tablissement</strong>.
      Veuillez contacter le <strong>responsable du service d'hospitalisation</strong> pour annuler la sortie ou
      <strong>un administrateur</strong> si vous devez tout de m�me modifier certaines informations.
    </div>
  {{/if}}
  {{else}}
    <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
  {{/if}}
  </td>
</tr>
{{/if}}
</table>

</form>

{{if $mode_operation && $isPrescriptionInstalled}}
<table style="width:100%" class="form"> 
  <tr>
    <td id="prescription_register">
      <script type="text/javascript">
        PrescriptionEditor.register('{{$sejour->_id}}','{{$sejour->_class_name}}','dhe','{{$sejour->praticien_id}}');
      </script>
    </td>
  </tr>  
</table>
{{/if}}
