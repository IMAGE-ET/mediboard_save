<!-- $Id$ -->

<script type="text/javascript">
function popFile(objectClass, objectId, elementClass, elementId){
  var url = new Url;
  url.ViewFilePopup(objectClass, objectId, elementClass, elementId, 0);
  return false;
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

{{mb_script module=dPcompteRendu script=document}}

<table style="width: 100%; border-spacing: 0; border-collapse: collapse; padding: 2px; vertical-align: middle; white-space: nowrap;">
  <thead>
    <tr>
      <td>
        <table class="tbl">
          <tr>
            <th colspan="10" class="title">
              {{$patient->_view}} <br />
              N�{{if $patient->sexe == "f"}}e{{/if}} le {{$patient->naissance|date_format:$conf.date}}
            </th>
          </tr>
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
          <td class="top">
            {{mb_include module=files template=inc_list_docitems list=$patient->_ref_files_by_cat}}
          </td>
          <td class="top">
            {{mb_include module=files template=inc_list_docitems list=$patient->_ref_documents_by_cat}}
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td>
    <!-- Dossier M�dical -->
      {{mb_include module=patients template=CDossierMedical_complete object=$patient->_ref_dossier_medical}}
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
              };
              
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
            <label title="R�glements du patient et du tiers, en rouge si non r�gl� en totalit�">
              R�glement<br />Patient - Tiers
            </label>
          </th>
        </tr>
        
        <!-- Consultations -->
        {{foreach from=$patient->_ref_consultations item=_consult}}
          {{if !$_consult->annule}}
            <tr>
              <th class="section" colspan="3">
                <div>
                  {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_consult->_ref_plageconsult->_ref_chir}}
                  &mdash;
                  {{$_consult->_datetime|date_format:"%A"}}
                  {{$_consult->_datetime|date_format:$conf.datetime}}
                </div>
              </th>
            </tr>
            <tr>
              <td class="text top">
                {{if $_consult->motif}}
                  <strong>{{mb_label object=$_consult field=motif}}</strong>
                  <div>{{mb_value object=$_consult field=motif}}</div>
                {{/if}}

                {{if $_consult->rques}}
                  <strong>{{mb_label object=$_consult field=rques}}</strong>
                  <div>{{mb_value object=$_consult field=rques}}</div>
                {{/if}}

                {{if $_consult->examen}}
                  <strong>{{mb_label object=$_consult field=examen}}</strong>
                  <div>{{mb_value object=$_consult field=examen}}</div>
                {{/if}}

                {{if $_consult->traitement}}
                  <strong>{{mb_label object=$_consult field=traitement}}</strong>
                  <div>{{mb_value object=$_consult field=traitement}}</div>
                {{/if}}

                {{if $_consult->histoire_maladie && $conf.dPcabinet.CConsultation.show_histoire_maladie}}
                  <strong>{{mb_label object=$_consult field=histoire_maladie}}</strong>
                  <div>{{mb_value object=$_consult field=histoire_maladie}}</div>
                {{/if}}

                {{if $_consult->conclusion && $conf.dPcabinet.CConsultation.show_conclusion}}
                  <strong>{{mb_label object=$_consult field=conclusion}}</strong>
                  <div>{{mb_value object=$_consult field=conclusion}}</div>
                {{/if}}

                {{if $_consult->_ref_examaudio->_id}}
                  <br />
                  <a href="#" onclick="newExam('exam_audio', {{$_consult->_id}})">
                    <strong>Audiogramme</strong>
                  </a>
                {{/if}}
              </td>

              <td class="top">
                {{mb_include module=files template=inc_list_docitems list=$_consult->_refs_docitems_by_cat}}

                {{if isset($_consult->_ref_prescriptions.externe|smarty:nodefaults)}}
                  {{assign var=_prescription value=$_consult->_ref_prescriptions.externe}}
                  {{foreach from=$_prescription->_ref_files item=_file}}
                    <div>
                      <a class="button print notext" target="_blank" href="?m=files&a=fileviewer&file_id={{$_file->_id}}"></a>
                      <a href="#" onclick="return popFile('{{$_file->object_class}}','{{$_file->object_id}}','{{$_file->_class}}','{{$_file->_id}}')" style="display: inline-block;">
                        {{$_file->file_name}}
                      </a>
                    </div>
                  {{/foreach}}
                {{/if}}
              </td>
              
              <td  class="compta" style="text-align: center">
                {{assign var=chir_id value=$_consult->_ref_plageconsult->chir_id}}
                {{if isset($listPrat.$chir_id|smarty:nodefaults)}}
                  {{if $_consult->tarif}}
                    {{if $_consult->du_patient}}
                      <div>
                        P:
                        {{if !$_consult->patient_date_reglement}}
                        <span style="color: #f00;">
                          {{mb_value object=$_consult field=_reglements_total_patient}}
                        </span>
                        {{/if}}
                        /
                        {{mb_value object=$_consult field=du_patient}}
                      </div>
                    {{/if}}

                    {{if $_consult->du_tiers}}
                      <div>
                        T:
                        {{if !$_consult->tiers_date_reglement}}
                          <span style="color: #f00;">
                          {{mb_value object=$_consult field=_reglements_total_tiers}}
                        </span>
                        {{/if}}
                        /
                        {{mb_value object=$_consult field=du_tiers}}
                      </div>
                    {{/if}}
                  {{/if}}
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
              <td class="text top">
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
              <td colspan="2" class="top text">
                <strong>S�jour {{mb_include module=system template=inc_interval_date from=$_sejour->entree to=$_sejour->sortie}}</strong>
                {{mb_include module=files template=inc_list_docitems list=$_sejour->_refs_docitems_by_cat}}

                {{foreach from=$_sejour->_ref_operations item=_op}}
                  {{if !$_op->annulee}}
                    <strong>Intervention du {{$_op->_datetime_best|date_format:$conf.date}}</strong>
                    {{mb_include module=files template=inc_list_docitems list=$_op->_refs_docitems_by_cat}}
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