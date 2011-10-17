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

<table style="border-spacing: 0; border-collapse: collapse; padding: 2px; vertical-align: middle; white-space: nowrap;">
  <thead>
    <tr>
      <td>
        <table class="tbl">
          <th colspan="10" class="title">
          	{{$patient->_view}} <br />
            N�{{if $patient->sexe == "f"}}e{{/if}} le {{$patient->naissance|date_format:$conf.date}}
          </th>
        </table>
      </td>
    </tr>
  </thead>
  <tbody>
	<tr>
	  <td>
	    <table class="tbl">
	      <tr>
          <th style="width: 50%;">Fichiers du patient</th>
          <th style="width: 50%;">Documents du patient</th>
        </tr>
        <tr>
          <td>
            {{foreach from=$patient->_ref_documents item=_doc}}
            <a href="#document-{{$_doc->_id}}" onclick="popFile('{{$_doc->object_class}}','{{$_doc->object_id}}','{{$_doc->_class}}','{{$_doc->_id}}')">
              {{$_doc->nom}}
            </a>
            {{foreachelse}}
            <div class="empty">{{tr}}None{{/tr}}</div>
            {{/foreach}}
          </td>
          <td>
            {{foreach from=$patient->_ref_files item=_file}}
            <a href="#file-{{$_file->_id}}" onclick="popFile('{{$patient->_class}}','{{$patient->_id}}','{{$_file->_class}}','{{$_file->_id}}')">
              {{$_file->file_name}}
            </a>
            {{foreachelse}}
            <div class="empty">{{tr}}None{{/tr}}</div>
            {{/foreach}}
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td>
    <!-- Dossier M�dical -->
      {{mb_include module=dPpatients template=CDossierMedical_complete object=$patient->_ref_dossier_medical}}
    </td>
  </tr>
  <tr>
    <td>
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
        {{foreach from=$patient->_ref_consultations item=_consult}}
          {{if !$_consult->annule}}
            <tr>
              <td class="text" valign="top">
              	{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_consult->_ref_plageconsult->_ref_chir}}
          			
                &mdash; {{$_consult->_ref_plageconsult->date|date_format:$conf.date}}
                  {{if $_consult->motif}}
          	      <br />
          	      <strong>{{mb_label object=$_consult field=motif}}</strong>
          	      <em>{{mb_value object=$_consult field=motif}}</em>
          	    {{/if}}
          	    {{if $_consult->rques}}
          	      <br />
          	      <strong>{{mb_label object=$_consult field=rques}}</strong>
          	      <em>{{mb_value object=$_consult field=rques}}</em>
          	    {{/if}}
          	    {{if $_consult->examen}}
          	      <br />
          	      <strong>{{mb_label object=$_consult field=examen}}</strong>
          	      <em>{{mb_value object=$_consult field=examen}}</em>
          	    {{/if}}
          	    {{if $_consult->traitement}}
          	      <br />
          	      <strong>{{mb_label object=$_consult field=traitement}}</strong>
          	      <em>{{mb_value object=$_consult field=traitement}}</em>
          	    {{/if}}
          			{{if $_consult->histoire_maladie && $conf.dPcabinet.CConsultation.show_histoire_maladie}}
                  <br />
                  <strong>{{mb_label object=$_consult field=histoire_maladie}}</strong>
                  <em>{{mb_value object=$_consult field=histoire_maladie}}</em>
                {{/if}}
          			{{if $_consult->conclusion && $conf.dPcabinet.CConsultation.show_conclusion}}
                  <br />
                  <strong>{{mb_label object=$_consult field=conclusion}}</strong>
                  <em>{{mb_value object=$_consult field=conclusion}}</em>
                {{/if}}
          	    {{if $_consult->_ref_examaudio->examaudio_id}}
          	      <br />
          	      <a href="#" onclick="newExam('exam_audio', {{$_consult->consultation_id}})">
          	        <strong>Audiogramme</strong>
          	      </a>
          	    {{/if}}
              </td>
              <td valign="top">
                {{foreach from=$_consult->_ref_documents item=_doc}}
                <a href="#" onclick="popFile('{{$_doc->object_class}}','{{$_doc->object_id}}','{{$_doc->_class}}','{{$_doc->_id}}')">
                  {{$_doc->nom}}
                </a>
                {{/foreach}}
                {{foreach from=$_consult->_ref_files item=_file}}
                <a href="#" onclick="popFile('{{$_file->object_class}}','{{$_file->object_id}}','{{$_file->_class}}','{{$_file->_id}}')">
                  {{$_file->file_name}}
                </a>
                {{/foreach}}
              </td>
              
              <td  class="compta" style="text-align: center">
              {{if $_consult->tarif}}
                {{if $_consult->du_patient}}
                <div style="display: inline; {{if !$_consult->patient_date_reglement}} color: #f00;{{/if}}">
                  {{mb_value object=$_consult field=_reglements_total_patient}}
                </div>
                /
                {{/if}}
          
                {{mb_value object=$_consult field=du_patient}}
                -
                {{if $_consult->du_tiers}}
                <div style="display: inline; {{if !$_consult->tiers_date_reglement}} color: #f00;{{/if}}">
                  {{mb_value object=$_consult field=_reglements_total_tiers}}
                </div>
                /
                {{/if}}
                {{mb_value object=$_consult field=du_tiers}}
              {{/if}}
              </td>
            </tr>
          {{/if}}
        {{/foreach}}
        
        <!-- Interventions -->
        <tr>
          <th colspan="4" class="title">S�jours</th>
        </tr>
        <tr>
          <th>R�sum�</th>
          <th colspan="3">Documents</th>
        </tr>
        {{foreach from=$patient->_ref_sejours item=_sejour}}
          {{if !$_sejour->annule}}
            <tr>
              <td class="text" valign="top">
                Hospitalisation 
                {{mb_include module=system template=inc_interval_date from=$_sejour->entree to=$_sejour->sortie}} <br />
                <strong>{{tr}}CSejour-_type_admission{{/tr}} : </strong> {{tr}}CSejour.type.{{$_sejour->type}}{{/tr}}
                <br />
                <strong>{{tr}}CSejour-praticien_id-desc{{/tr}} : </strong>
                {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_sejour->_ref_praticien}}
                <br />
                <strong>{{tr}}CSejour-libelle{{/tr}} : </strong> {{$_sejour->libelle}}
                <ul>
                  {{foreach from=$_sejour->_ref_operations item=_op}}
                    {{if !$_op->annulee}}
                      <li>
                        Dr {{$_op->_ref_chir->_view}}
                        &mdash; {{$_op->_ref_plageop->date|date_format:$conf.date}}
                        {{if $_op->libelle}}
                        <br/>
                        <strong>{{mb_label object=$_op field="libelle"}}</strong> :
                        {{mb_value object=$_op field="libelle"}}
                        {{/if}}
                        {{foreach from=$_op->_ext_codes_ccam item=_code}}
                        <br />
                        <strong>{{$_code->code}}</strong>
                        : {{$_code->libelleLong}}
                        {{/foreach}}
                      </li>
                    {{/if}}
                  {{/foreach}}
                </ul>
              </td>
              <td colspan="2" valign="top">
                {{foreach from=$_sejour->_ref_operations item=_op}}
                  {{if !$_op->annulee}}
                    {{foreach from=$_op->_ref_documents item=_doc}}
                    <a href="#" onclick="popFile('{{$_doc->object_class}}','{{$_doc->object_id}}','{{$_doc->_class}}','{{$_doc->_id}}')">
                      {{$_doc->nom}}
                    </a>
                    {{/foreach}}
                  {{/if}}
                {{/foreach}}
                {{foreach from=$_sejour->_ref_operations item=_op}}
                  {{if !$_op->annulee}}
                    {{foreach from=$_op->_ref_files item=_file}}
                    <a href="#" onclick="popFile('{{$_file->object_class}}','{{$_file->object_id}}','{{$_file->_class}}','{{$_file->_id}}')">
                      {{$_file->file_name}}
                    </a>
                    {{/foreach}}
                  {{/if}}
                {{/foreach}}
              </td>
            </tr>
          {{/if}}
        {{/foreach}}
        </table>
      </td>
    </tr>
  </tbody>
</table>