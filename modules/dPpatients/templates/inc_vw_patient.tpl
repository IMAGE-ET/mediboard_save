<!-- $Id$ -->

{{mb_script module="dPcompteRendu" script="document"}}
{{mb_script module="dPpatients" script="patient" ajax=true}}

<script type="text/javascript">

Document.refreshList = function() {
  if (document.actionPat) {
    new Url("dPpatients", "httpreq_vw_patient").
      addParam("patient_id", document.actionPat.patient_id.value).
      requestUpdate('vwPatient');
  }
}

</script>

{{if $patient->_vip}}
  <div class="big-warning">
    Vous n'avez pas acc�s � l'identit� de ce patient.
    Veuillez contacter un administrateur de la clinique
    pour avoir plus d'information sur ce probl�me.
  </div>
  {{mb_return}}
{{/if}}

{{if $conf.dPpatients.CPatient.show_patient_link && $patient->_ref_patient_links|@count}}
  <div class="small-info">
    Patient associ� avec le(s) patient(s) suivant(s) : 
    <ul>
      {{foreach from=$patient->_ref_patient_links item=_patient_link}}
        <li>
          <button type="button" class="unlink notext compact" title="{{tr}}Unlink{{/tr}}" onclick="Patient.doUnlink('{{$patient->_id}}');">
            {{tr}}Unlink{{/tr}}
          </button>
          <a href="?m=patients&tab=vw_edit_patients&patient_id={{$_patient_link->_id}}">{{$_patient_link->_view}}</a></li>
      {{/foreach}}
    </ul>
  </div>
{{/if}}

{{mb_include module=patients template=inc_vw_identite_patient}}

<table class="form">
  <tr>
    <th class="category" colspan="4">Planifier</th>
  </tr>
  <tr>
    <td class="button" colspan="10">
      {{math assign=ecap_dhe equation="a * b" a='ecap'|module_active|strlen b=$current_group|idex:'ecap'|strlen}}
      {{if $ecap_dhe}}
        {{mb_include module=ecap template=inc_button_dhe patient_id=$patient->_id praticien_id=""}}
      {{else}}
        {{if !$app->user_prefs.simpleCabinet}}
          {{if $canPlanningOp->edit}}
            <a class="button new" href="?m=dPplanningOp&amp;tab=vw_edit_planning&amp;pat_id={{$patient->_id}}&amp;operation_id=0&amp;sejour_id=0">
              {{tr}}COperation{{/tr}}
            </a>
          {{/if}}
          {{if $canPlanningOp->read}}
            <a class="button new" href="?m=dPplanningOp&amp;tab=vw_edit_urgence&amp;pat_id={{$patient->_id}}&amp;operation_id=0&amp;sejour_id=0">
              Interv. hors plage
            </a>
            <a class="button new" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;patient_id={{$patient->_id}}&amp;sejour_id=0">
              {{tr}}CSejour{{/tr}}
            </a>
          {{/if}}
        {{/if}}
      {{/if}}

      {{if $canCabinet->read}}
        <a class="button new" href="?m=dPcabinet&amp;tab=edit_planning&amp;pat_id={{$patient->_id}}&amp;consultation_id=0">
          {{tr}}CConsultation{{/tr}}
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

      <label for="prat_id" class="checkNull" title="Praticien pour la consultation imm�diate. Obligatoire">Praticien</label>

      <select name="prat_id" class="notNull ref">
        <option value="">&mdash; Choisir un praticien</option>
        {{mb_include module=mediusers template=inc_options_mediuser selected=$app->user_id list=$listPrat}}
      </select>

      <button id="inc_vw_patient_button_consult_now" class="new" type="submit">Consulter</button>

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
    <th colspan="2" class="category">
      S�jours
      {{if $nb_sejours_annules || $nb_ops_annulees}}
        (
          {{if $nb_sejours_annules}}
            {{$nb_sejours_annules}} s�jour(s) annul�(s) {{if $nb_ops_annulees}}&mdash;{{/if}}
          {{/if}}
          {{if $nb_ops_annulees}}
            {{$nb_ops_annulees}} intervention(s) annul�e(s)
          {{/if}}
        )
      {{/if}}
    </th>
  </tr>
  {{foreach from=$patient->_ref_sejours item=object}}
    {{mb_include module=patients template=inc_vw_elem_dossier}}
  {{/foreach}}
  {{/if}}

  {{if $patient->_ref_consultations}}
  <tr>
    <th id="inc_vw_patient_th_consult" colspan="2" class="category">Consultations</th>
  </tr>
  {{foreach from=$patient->_ref_consultations item=object}}
    {{mb_include module=patients template=inc_vw_elem_dossier}}
  {{/foreach}}
  {{/if}}

  {{if "maternite"|module_active && $patient->_ref_grossesses}}
    {{foreach from=$patient->_ref_grossesses item=grossesse}}
      <tr>
        <th colspan="2" class="category">Grossesse (terme pr�vu : {{$grossesse->terme_prevu|date_format:$conf.date}})</th>
      </tr>
      <tr>
        <td colspan="2">S�jours</td>
      </tr>
      {{foreach from=$grossesse->_ref_sejours item=object}}
        {{mb_include module=patients template=inc_vw_elem_dossier}}
      {{foreachelse}}
        <td colspan="2" class="empty">{{tr}}CSejour.none{{/tr}}</td>
      {{/foreach}}
      <tr>
        <td colspan="2">Consultations</td>
      </tr>
      {{foreach from=$grossesse->_ref_consultations item=object}}
        {{mb_include module=patients template=inc_vw_elem_dossier show_semaine_grossesse=1}}
      {{foreachelse}}
        <td colspan="2" class="empty">{{tr}}CConsultation.none{{/tr}}</td>
      {{/foreach}}
    {{/foreach}}
  {{/if}}
</table>