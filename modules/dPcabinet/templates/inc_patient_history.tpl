<!-- S�jours et interventions -->

<script>
  ObjectTooltip.modes.sejours_op = {
    module: 'cabinet',
    action: 'ajax_vw_historique_patient',
    sClass: 'tooltip'
  }
</script>

{{if !$app->user_prefs.simpleCabinet}}
  <div>
    <span onmouseover="ObjectTooltip.createEx(this, 'sejours', 'sejours_op', {patient_id: '{{$patient->_id}}',type: 'sejour'});">
      {{$patient->_count.sejours}} {{tr}}CSejour{{/tr}}(s)
    </span>
  </div>
{{/if}}
  
<!-- Consultations -->
<div>
  <span  onmouseover="ObjectTooltip.createEx(this, 'consultations', 'sejours_op', {patient_id: '{{$patient->_id}}', type: 'consultation'});">
    {{$patient->_count.consultations}} {{tr}}CConsultation{{/tr}}(s)
  </span>
</div>
