<!-- $Id$ -->
{{mb_include_script module="dPcompteRendu" script="document"}}

<script type="text/javascript">
function view_history_patient(id){
  var url = new Url("dPpatients", "vw_history");
  url.addParam("patient_id", id);
  url.popup(600, 500, "patient history");
}

function viewPatient(form) {
  form = form || document.forms.actionPat;
  $V(form.tab, "vw_full_patients");
  form.submit();
}

function editPatient(form) {
	form = form || document.forms.actionPat;
  $V(form.tab, "vw_edit_patients");
  form.submit();
}

function printPatient(id) {
  var url = new Url("dPpatients", "print_patient");
  url.addParam("patient_id", id);
  url.popup(700, 550, "Patient");
}

function popFile(objectClass, objectId, elementClass, elementId){
  var url = new Url;
  url.ViewFilePopup(objectClass, objectId, elementClass, elementId, 0);
}

function printIntervention(id) {
  var url = new Url("dPplanningOp", "view_planning");
  url.addParam("operation_id", id);
  url.popup(700, 550, "Admission");
}

function reloadVwPatient(){
  var mainUrl = new Url("dPpatients", "httpreq_vw_patient");
  mainUrl.addParam("patient_id", document.actionPat.patient_id.value);
  mainUrl.requestUpdate('vwPatient', { waitingText : null } );
}

Document.refreshList = function(){
  reloadVwPatient();
}
</script>

<table class="form">
  <tr>
    <th class="category" colspan="3">
      
			{{if $patient->_id_vitale}}
      <div style="float:right;">
	      <img src="images/icons/carte_vitale.png" alt="lecture vitale" title="B�n�ficiaire associ� � une carte Vitale" />
      </div>
      {{/if}}

      {{mb_include module=system template=inc_object_idsante400 object=$patient}}

      <a style="float:right;" href="#" onclick="view_history_patient({{$patient->_id}})">
        <img src="images/icons/history.gif" alt="historique" />
      </a>

      <div style="float:left;" class="noteDiv {{$patient->_guid}}">
        <img alt="Ecrire une note" src="images/icons/note_grey.png" />
      </div>

      Identit� {{if $patient->_IPP}}[{{$patient->_IPP}}]{{/if}}
    </th>
    <th class="category" colspan="2">Coordonn�es</th>
  </tr>

  <tr>
    <td rowspan="3" style="width: 0.1%; vertical-align: middle; text-align: center;">
		  {{include file=../../dPpatients/templates/inc_vw_photo_identite.tpl mode="read" size="64"}}
		</td>
    <th>{{mb_label object=$patient field="nom"}}</th>
    <td>{{mb_value object=$patient field="nom"}}</td>
    <th>{{mb_label object=$patient field="adresse"}}</th>
    <td class="text">{{mb_value object=$patient field="adresse"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="prenom"}}</th>
    <td>{{mb_value object=$patient field="prenom"}}</td>
    <th>{{mb_label object=$patient field="cp"}}</th>
    <td>{{mb_value object=$patient field="cp"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="sexe"}}</th>
    <td>{{mb_value object=$patient field="sexe"}}</td>
    <th>{{mb_label object=$patient field="ville"}}</th>
    <td>{{mb_value object=$patient field="ville"}}</td>
  </tr>
  
  <tr>
    <th colspan="2">{{mb_label object=$patient field="naissance"}}</th>
    <td>{{mb_value object=$patient field="naissance"}}</td>
    <th>{{mb_label object=$patient field="tel"}}</th>
    <td>{{mb_value object=$patient field="tel"}}</td>
  </tr>
  
  <tr>
    <th colspan="2">{{mb_label object=$patient field="nom_jeune_fille"}}</th>
    <td>{{mb_value object=$patient field="nom_jeune_fille"}}</td>
    <th>{{mb_label object=$patient field="tel2"}}</th>
    <td>{{mb_value object=$patient field="tel2"}}</td>
  </tr>
  
  {{if $patient->medecin_traitant || $patient->_ref_medecins_correspondants|@count}}
  <tr>
    <th class="category" colspan="5">Correspondants m�dicaux</th>
  </tr>
  
  <tr>
    <td colspan="5" class="text">
      {{assign var=medecin value=$patient->_ref_medecin_traitant}}
      {{if $medecin->_id}}
      <span class="tooltip-trigger" onmouseover="ObjectTooltip.createEx(this, '{{$medecin->_guid}}');">
        <strong>{{$medecin}}</strong> ;
      </span>
      {{/if}}
      {{foreach from=$patient->_ref_medecins_correspondants item=curr_corresp}}
	      {{assign var=medecin value=$curr_corresp->_ref_medecin}}
	      <span class="tooltip-trigger" onmouseover="ObjectTooltip.createEx(this, '{{$medecin->_guid}}');">
          {{$medecin}} ;
        </span>
      {{/foreach}}
    </td>
  </tr>
  {{/if}}

  {{if $patient->rques}}
  <tr>
    <th class="category" colspan="5">{{mb_label object=$patient field="rques"}}</th>
  </tr>
  
  <tr>
    <td colspan="5" class="text">{{mb_value object=$patient field="rques"}}</td>
  </tr>
  {{/if}}
  
  <tr>
    <td class="button" colspan="10">
      <form name="actionPat" action="?" method="get">
      <input type="hidden" name="m" value="dPpatients" />
      <input type="hidden" name="tab" value="vw_idx_patients" />
      <input type="hidden" name="patient_id" value="{{$patient->_id}}" />
      {{if @$useVitale}}
      <input type="hidden" name="useVitale" value="1" />
      {{/if}}
      
      <button type="button" class="search" onclick="viewPatient()">
        Dossier Complet
      </button>
      <button type="button" class="print" onclick="printPatient({{$patient->_id}})">
        {{tr}}Print{{/tr}}
      </button>
      {{if $canPatients->edit}}
      <button type="button" class="edit" onclick="editPatient()">
        {{tr}}Modify{{/tr}}
        {{if @$useVitale}}avec Vitale{{/if}}
      </button>
      {{/if}}
      </form>
			
      {{if $can->admin}} 
      <form name="editPatientPurge" action="?m={{$m}}&amp;tab=vw_idx_patients" method="post" onsubmit="return confirmCreation(this)">
      <input type="hidden" name="dosql" value="do_patients_aed" />
			<input type="hidden" name="tab" value="vw_idx_patients" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="_purge" value="0" />
      <input type="hidden" name="patient_id" value="{{$patient->_id}}" />
              
       <script type="text/javascript">
         function confirmPurge() {
           var oForm = document.editPatientPurge;
           if (confirm("ATTENTION : Vous �tes sur le point de purger le dossier de ce patient")) {
             oForm._purge.value = "1";
             confirmDeletion(oForm,  {
               typeName:'le patient',
               objName:'{{$patient->_view|smarty:nodefaults|JSAttribute}}'
             } );
           }
         }
       </script>
       <button type="button" class="cancel" onclick="confirmPurge();">
         {{tr}}Purge{{/tr}}
       </button>
      </form>
      {{/if}}

    </td>
  </tr>
</table>

<table class="form">
  <tr>
    <th class="category" colspan="4">Planifier</th>
  </tr>
  <tr>
    {{if !$app->user_prefs.simpleCabinet}}
    {{if $canPlanningOp->edit}}
    <td class="button">
      <a class="button new" href="?m=dPplanningOp&amp;tab=vw_edit_planning&amp;pat_id={{$patient->_id}}&amp;operation_id=0&amp;sejour_id=0">
        Intervention
      </a>
    </td>
    {{/if}}
    {{if $canPlanningOp->read}}
    <td class="button">
      <a class="button new" href="?m=dPplanningOp&amp;tab=vw_edit_urgence&amp;pat_id={{$patient->_id}}&amp;operation_id=0&amp;sejour_id=0">
        Interv. hors plage
      </a>
    </td>
    <td class="button">
      <a class="button new" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;patient_id={{$patient->_id}}&amp;sejour_id=0">
        S�jour
      </a>
    </td>
    {{/if}}
    <td class="button">
    {{else}}
    <td colspan="4" class="button">
    {{/if}}
      {{if $canCabinet->edit}}
      <a class="button new" href="?m=dPcabinet&amp;tab=edit_planning&amp;pat_id={{$patient->_id}}&amp;consultation_id=0">
        Consultation
      </a>
      {{/if}}
    </td>
  </tr>
  {{if $listPrat|@count && $canCabinet->edit}}
  <tr><th class="category" colspan="4">Consultation imm�diate</th></tr>
  <tr>
    <td class="button" colspan="4">
      <form name="addConsFrm" action="?m=dPcabinet" method="post" onsubmit="return checkForm(this)">

      <input type="hidden" name="m" value="dPcabinet" />
      <input type="hidden" name="dosql" value="do_consult_now" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="patient_id" class="notNull ref" value="{{$patient->_id}}" />

      <label for="prat_id" title="Praticien pour la consultation imm�diate. Obligatoire">Praticien</label>

      <select name="prat_id" class="notNull ref">
        <option value="">&mdash; Choisir un praticien</option>
        {{foreach from=$listPrat item=curr_prat}}
          <option class="mediuser" style="border-color: #{{$curr_prat->_ref_function->color}};" value="{{$curr_prat->user_id}}" 
            {{if $curr_prat->user_id == $app->user_id}} selected="selected" {{/if}}>
            {{$curr_prat->_view}}
          </option>
        {{/foreach}}
      </select>

      <button class="new" type="submit">Consulter</button>

      </form>
    </td>
  </tr>
  {{/if}}
</table>

<table class="form">
  {{assign var="affectation" value=$patient->_ref_curr_affectation}}
  {{if $affectation && $affectation->affectation_id}}
  <tr>
  	<th colspan="3" class="category">Chambre actuelle</th>
  </tr>
  <tr>
    <td colspan="3">
      {{$affectation->_ref_lit}}
      depuis le {{mb_value object=$affectation field=entree}}
    </td>
  </tr>
  {{assign var="affectation" value=$patient->_ref_next_affectation}}
  {{elseif $affectation && $affectation->affectation_id}}
  <tr>
    <th colspan="3" class="category">Prochaine chambre</th>
  </tr>
  <tr>
    <td colspan="3">
      {{$affectation->_ref_lit}}
      depuis le {{mb_value object=$affectation field=entree}}
    </td>
  </tr>
  {{/if}}

  {{if $patient->_ref_sejours}}
  <tr>
    <th colspan="2" class="category">S�jours</th>
  </tr>
  {{foreach from=$patient->_ref_sejours item=_sejour}}
  <tr>
    <td class="text">
      {{if $_sejour->group_id == $g && $_sejour->_canEdit}}
      <a class="actionPat" title="Modifier le s�jour" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$_sejour->_id}}">
        <img src="images/icons/planning.png" alt="Planifier"/>
      </a>
      <a class="tooltip-trigger"
         {{if $canAdmissions->view}}
         href="?m=dPadmissions&amp;tab=vw_idx_admission&amp;date={{$_sejour->_date_entree_prevue}}#adm{{$_sejour->_id}}"
         {{else}}
         href="#nothing"
         {{/if}}
         onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}')"
      >
      {{else}}
      <a href="#nothing">
      {{/if}}
        {{if $_sejour->_num_dossier && $_sejour->group_id == $g}}[{{$_sejour->_num_dossier}}]{{/if}}
        {{$_sejour->_shortview}}
        {{if $_sejour->_nb_files_docs}}
          - ({{$_sejour->_nb_files_docs}} Doc.)
        {{/if}}
      </a>
    </td>
    {{if $_sejour->group_id == $g}}
    <td {{if $_sejour->annule}}class="cancelled"{{/if}}>
      {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_sejour->_ref_praticien}}
    </td>
    {{else}}
    <td style="background-color:#afa">
      {{$_sejour->_ref_group->text|upper}}
    </td>
    {{/if}}
  </tr>
  
  {{foreach from=$_sejour->_ref_operations item=curr_op}}
  <tr>
    <td class="text">
      <ul>
      <li>
      <a class="actionPat" href="#" onclick="printIntervention({{$curr_op->_id}})">
        <img src="images/icons/print.png" alt="Imprimer" title="Imprimer l'intervention" />
      </a>
      {{if $_sejour->group_id == $g && $curr_op->_canEdit}}
      <a class="actionPat" title="Modifier l'intervention" href="{{$curr_op->_link_editor}}">
        <img src="images/icons/planning.png" alt="modifier"/>
      </a>
      <a class="tooltip-trigger"
         href="{{$curr_op->_link_editor}}"
         onmouseover="ObjectTooltip.createEx(this, '{{$curr_op->_guid}}')"
      >
      {{else}}
      <a class="tooltip-trigger" title="Modification d'intervention non autoris�e" href="#nothing">
      {{/if}}
        Intervention le {{$curr_op->_datetime|date_format:$dPconfig.date}}
        {{if $curr_op->_nb_files_docs}}
          - ({{$curr_op->_nb_files_docs}} Doc.)
        {{/if}}
      </a>
      </li>
      </ul>
    </td>
    {{if $_sejour->group_id == $g}}
    <td {{if $curr_op->annulee}}class="cancelled"{{/if}}>
      {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$curr_op->_ref_chir}}
    </td>
    {{else}}
    <td style="background-color:#afa">
      {{$_sejour->_ref_group->_view|upper}}
    </td>
    {{/if}}
  </tr>
  {{/foreach}}
  {{/foreach}}
  {{/if}}
  
  {{if $patient->_ref_consultations}}
  <tr><th class="category" colspan="2">Consultations</th></tr>
  {{foreach from=$patient->_ref_consultations item=curr_consult}}
  <tr>
    <td class="text">
      <a class="actionPat" title="Modifier la consultation" href="?m=dPcabinet&amp;tab=edit_planning&amp;consultation_id={{$curr_consult->_id}}">
        <img src="images/icons/planning.png" alt="modifier" />
      </a>
        {{if $curr_consult->_canEdit}}
          <a class="tooltip-trigger"
             href="?m=dPcabinet&amp;tab=edit_consultation&amp;selConsult={{$curr_consult->_id}}&amp;chirSel={{$curr_consult->_ref_plageconsult->chir_id}}"
             onmouseover="ObjectTooltip.createEx(this, '{{$curr_consult->_guid}}')"
           >
        {{else}}
          <a href="#nothing">
        {{/if}}
        Le {{$curr_consult->_datetime|date_format:$dPconfig.datetime}} - {{$curr_consult->_etat}}
      </a>
    </td>

    <td {{if $curr_consult->annule}}class="cancelled"{{/if}}>
      {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$curr_consult->_ref_chir}}
    </td>
  </tr>

  {{/foreach}}
  {{/if}}
</table>