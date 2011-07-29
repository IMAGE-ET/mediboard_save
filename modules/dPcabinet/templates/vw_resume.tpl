<!-- $Id$ -->

<script type="text/javascript">

function popFile(objectClass, objectId, elementClass, elementId){
  var url = new Url;
  url.ViewFilePopup(objectClass, objectId, elementClass, elementId, 0);
}

function newExam(sAction, consultation_id) {
  if (sAction) {
    var url = new Url;
    url.setModuleAction("dPcabinet", sAction);
    url.addParam("consultation_id", consultation_id);
    url.popup(900, 600, "Examen");  
  }
}

</script>

<table class="tbl">
  <tr>
    <th colspan="10" class="title">
    	{{$patient->_view}} <br />
      N�{{if $patient->sexe == "f"}}e{{/if}} le {{$patient->naissance|date_format:$conf.date}}
    </th>
  </tr>

	<tr>
    <th style="width: 50%;">Fichiers du patient</th>
    <th style="width: 50%;">Documents du patient</th>
  </tr>

  <tr>
    <td>
      {{foreach from=$patient->_ref_documents item=curr_doc}}
      <a href="#document-{{$curr_doc->_id}}" onclick="popFile('{{$curr_doc->object_class}}','{{$curr_doc->object_id}}','{{$curr_doc->_class}}','{{$curr_doc->_id}}')">
        {{$curr_doc->nom}}
      </a>
      {{foreachelse}}
      <div class="empty">{{tr}}None{{/tr}}</div>
      {{/foreach}}
    </td>
    <td>
      {{foreach from=$patient->_ref_files item=curr_file}}
      <a href="#file-{{$curr_file->_id}}" onclick="popFile('{{$patient->_class}}','{{$patient->_id}}','{{$curr_file->_class}}','{{$curr_file->_id}}')">
        {{$curr_file->file_name}}
      </a>
      {{foreachelse}}
      <div class="empty">{{tr}}None{{/tr}}</div>
      {{/foreach}}
    </td>
  </tr>
</table>

<!-- Dossier M�dical -->
{{include file=../../dPpatients/templates/CDossierMedical_complete.tpl object=$patient->_ref_dossier_medical}}

<table class="tbl">
  <tr>
    <th colspan="4" class="title">
    	<div style="float:right">
	    	<input type="checkbox" id="toggle_compta" onchange="Compta.toggle();" 
	    		{{if $app->user_prefs.resumeCompta == 1}}checked="checked"{{/if}}
	    		/>
	    	<label for="toggle_compta" title="Afficher/masquer les donn�es comptables">Compta</label>
	    	<script type="text/javascript">
	    	var Compta = {
	    	  toggle: function() {
	    	    $$(".compta").each(Element[($V("toggle_compta") ? "show" : "hide")]);
	    	  }
	    	}
	    	
	    	Main.add(Compta.toggle);
	    	</script>
    	</div>

    	Consultations
    </th>
  </tr>
  <tr>
    <th>R�sum�</th>
    <th>Documents</th>
    <th class="compta">
      <label title="R�glements du patient et l'assurance maladie, en rouge si non r�gl� en totalit�">
        R�glement<br />Patient - AM
      </label>
    </th>
  </tr>
  
  <!-- Consultations -->
  {{foreach from=$patient->_ref_consultations item=curr_consult}}
  <tr>
    <td class="text" valign="top">
    	{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$curr_consult->_ref_plageconsult->_ref_chir}}
			
      &mdash; {{$curr_consult->_ref_plageconsult->date|date_format:"%d/%m/%Y"}}
        {{if $curr_consult->motif}}
	      <br />
	      <strong>{{mb_label object=$curr_consult field=motif}}</strong>
	      <em>{{mb_value object=$curr_consult field=motif}}</em>
	    {{/if}}
	    {{if $curr_consult->rques}}
	      <br />
	      <strong>{{mb_label object=$curr_consult field=rques}}</strong>
	      <em>{{mb_value object=$curr_consult field=rques}}</em>
	    {{/if}}
	    {{if $curr_consult->examen}}
	      <br />
	      <strong>{{mb_label object=$curr_consult field=examen}}</strong>
	      <em>{{mb_value object=$curr_consult field=examen}}</em>
	    {{/if}}
	    {{if $curr_consult->traitement}}
	      <br />
	      <strong>{{mb_label object=$curr_consult field=traitement}}</strong>
	      <em>{{mb_value object=$curr_consult field=traitement}}</em>
	    {{/if}}
			{{if $curr_consult->histoire_maladie && $conf.dPcabinet.CConsultation.show_histoire_maladie}}
        <br />
        <strong>{{mb_label object=$curr_consult field=histoire_maladie}}</strong>
        <em>{{mb_value object=$curr_consult field=histoire_maladie}}</em>
      {{/if}}
			{{if $curr_consult->conclusion && $conf.dPcabinet.CConsultation.show_conclusion}}
        <br />
        <strong>{{mb_label object=$curr_consult field=conclusion}}</strong>
        <em>{{mb_value object=$curr_consult field=conclusion}}</em>
      {{/if}}
	    {{if $curr_consult->_ref_examaudio->examaudio_id}}
	      <br />
	      <a href="#" onclick="newExam('exam_audio', {{$curr_consult->consultation_id}})">
	        <strong>Audiogramme</strong>
	      </a>
	    {{/if}}
    </td>
    <td valign="top">
      {{foreach from=$curr_consult->_ref_documents item=curr_doc}}
      <a href="#" onclick="popFile('{{$curr_doc->object_class}}','{{$curr_doc->object_id}}','{{$curr_doc->_class}}','{{$curr_doc->_id}}')">
        {{$curr_doc->nom}}
      </a>
      {{/foreach}}
      {{foreach from=$curr_consult->_ref_files item=curr_file}}
      <a href="#" onclick="popFile('{{$curr_file->object_class}}','{{$curr_file->object_id}}','{{$curr_file->_class}}','{{$curr_file->_id}}')">
        {{$curr_file->file_name}}
      </a>
      {{/foreach}}
    </td>
    
    <td  class="compta" style="text-align: center">
    {{if $curr_consult->tarif}}
      {{if $curr_consult->du_patient}}
      <div style="display: inline; {{if !$curr_consult->patient_date_reglement}} color: #f00;{{/if}}">
        {{mb_value object=$curr_consult field=_reglements_total_patient}}
      </div>
      /
      {{/if}}

      {{mb_value object=$curr_consult field=du_patient}}
      -
      {{if $curr_consult->du_tiers}}
      <div style="display: inline; {{if !$curr_consult->tiers_date_reglement}} color: #f00;{{/if}}">
        {{mb_value object=$curr_consult field=_reglements_total_tiers}}
      </div>
      /
      {{/if}}
      {{mb_value object=$curr_consult field=du_tiers}}
    {{/if}}
    </td>
  </tr>
  {{/foreach}}
  
  <!-- Interventions -->
  <tr>
    <th colspan="4" class="title">S�jours</th>
  </tr>
  <tr>
    <th>R�sum�</th>
    <th colspan="3">Documents</th>
  </tr>
  {{foreach from=$patient->_ref_sejours item=curr_sejour}}
  <tr>
    <td class="text" valign="top">
      <ul>
        {{foreach from=$curr_sejour->_ref_operations item=curr_op}}
        <li>
          Dr {{$curr_op->_ref_chir->_view}}
          &mdash; {{$curr_op->_ref_plageop->date|date_format:"%d/%m/%Y"}}
          {{if $curr_op->libelle}}
          <br/>
          <strong>{{mb_label object=$curr_op field="libelle"}}</strong> :
          {{mb_value object=$curr_op field="libelle"}}
          {{/if}}
          {{foreach from=$curr_op->_ext_codes_ccam item=curr_code}}
          <br />
          <strong>{{$curr_code->code}}</strong>
          : {{$curr_code->libelleLong}}
          {{/foreach}}
        </li>
        {{foreachelse}}
        <li>
          Hospitalisation simple du {{$curr_sejour->entree_prevue|date_format:"%d/%m/%Y"}}
          au {{$curr_sejour->sortie_prevue|date_format:"%d/%m/%Y"}}
        </li>
        {{/foreach}}
      </ul>
    </td>
    <td colspan="2" valign="top">
      {{foreach from=$curr_sejour->_ref_operations item=curr_op}}
      {{foreach from=$curr_op->_ref_documents item=curr_doc}}
      <a href="#" onclick="popFile('{{$curr_doc->object_class}}','{{$curr_doc->object_id}}','{{$curr_doc->_class}}','{{$curr_doc->_id}}')">
        {{$curr_doc->nom}}
      </a>
      {{/foreach}}
      {{/foreach}}
      {{foreach from=$curr_sejour->_ref_operations item=curr_op}}
      {{foreach from=$curr_op->_ref_files item=curr_file}}
      <a href="#" onclick="popFile('{{$curr_file->object_class}}','{{$curr_file->object_id}}','{{$curr_file->_class}}','{{$curr_file->_id}}')">
        {{$curr_file->file_name}}
      </a>
      {{/foreach}}
      {{/foreach}}
    </td>
  </tr>
  {{/foreach}}
</table>