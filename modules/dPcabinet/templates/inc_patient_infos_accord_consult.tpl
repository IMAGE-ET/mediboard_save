<script type="text/javascript">

function newOperation(chir_id, pat_id) {
  var url = new Url;
  url.setModuleTab("dPplanningOp", "vw_edit_planning");
  url.addParam("chir_id", chir_id);
  url.addParam("pat_id", pat_id);
  url.addParam("operation_id", 0);
  url.addParam("sejour_id", 0);
  url.redirect();
}

function newHospitalisation(chir_id, pat_id) {
  var url = new Url;
  url.setModuleTab("dPplanningOp", "vw_edit_sejour");
  url.addParam("praticien_id", chir_id);
  url.addParam("patient_id", pat_id);
  url.addParam("sejour_id", 0);
  url.redirect();
}

function newConsultation(chir_id, pat_id, consult_urgence_id) {
  var url = new Url;
  url.setModuleTab("dPcabinet", "edit_planning");
  url.addParam("chir_id", chir_id);
  url.addParam("pat_id", pat_id);
  url.addParam("consult_urgence_id", consult_urgence_id);
  url.addParam("consultation_id", 0);
  url.redirect();
}

</script>

<table class="form" style="table-layout: fixed;">
  <tr>
    <th class="category">
      {{mb_include module=system template=inc_object_notes object=$patient}}
      {{if $patient->date_lecture_vitale}}
      <div style="float: right;">
        <img src="images/icons/carte_vitale.png" title="{{tr}}CPatient-date-lecture-vitale{{/tr}} : {{mb_value object=$patient field="date_lecture_vitale" format=relative}}" />
      </div>
      {{/if}}
      Patient
    </th>
    <th class="category">
    	Correspondants
    </th>
    <th class="category">
      {{mb_include module=system template=inc_object_idsante400 object=$consult}}
      {{mb_include module=system template=inc_object_history object=$consult}}
      Historique
    </th>
    <th class="category">Planification</th>
  </tr>
  
  <tr>
    <td class="text">
      {{include file="../../dPcabinet/templates/inc_patient_infos.tpl"}}
    </td>
    <td class="text">
      {{include file="../../dPcabinet/templates/inc_patient_medecins.tpl"}}
    </td>
    <td class="text">
      {{include file="../../dPcabinet/templates/inc_patient_history.tpl"}}
    </td>
    <td class="button">
      {{if !$app->user_prefs.simpleCabinet}}
	      {{if @$modules.ecap->mod_active}}
	      {{mb_script module=ecap script=dhe}}
	      <div id="dhe"></div>
	      <script type="text/javascript">DHE.register("{{$consult->patient_id}}", "{{$consult->_praticien_id}}", "dhe");</script>
	      {{else}}
	      {{if $m != "dPurgences"}}
	      <button class="new" type="button" onclick="newOperation({{$consult->_praticien_id}},{{$consult->patient_id}})" style="width: 12em;">
          {{tr}}COperation-title-create{{/tr}}
        </button>
	      <br/>
	      <button class="new" type="button" onclick="newHospitalisation({{$consult->_praticien_id}},{{$consult->patient_id}})" style="width: 12em;">
          {{tr}}CSejour-title-create{{/tr}}
        </button>
	      <br/>
	      {{/if}}
	    	{{/if}}
    	{{/if}}
    	{{if $m != "dPurgences"}}
      <button class="new" type="button" onclick="newConsultation({{$consult->_praticien_id}},{{$consult->patient_id}})" style="width: 12em;">
        {{tr}}CConsultation-title-create{{/tr}}
      </button>
      {{/if}}
    </td>
  </tr>
</table>
