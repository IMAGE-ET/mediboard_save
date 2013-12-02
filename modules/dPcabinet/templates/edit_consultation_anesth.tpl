{{assign var=auto_refresh_frequency value=$conf.dPcabinet.CConsultation.auto_refresh_frequency}}

<script type="text/javascript">

Main.add(function () {
  ListConsults.init("{{$consult->_id}}", "{{$userSel->_id}}", "{{$date}}", "{{$vue}}", "{{$current_m}}", "{{$auto_refresh_frequency}}");

  // @todo : Chargements inutiles ?
  // Chargement pour le sejour
  // DossierMedical.reloadDossierSejour();

  {{if $consult->_id}}
  // Chargement des antecedents, traitements, diagnostics du patients
  // DossierMedical.reloadDossierPatient();
  {{/if}}

  if (document.editAntFrm) {
    document.editAntFrm.type.onchange();
  }
});

</script>

<table class="main">
  <tr>
    <td id="listConsult" style="width: 240px;"></td>
    <td>{{mb_include module=dPcabinet template=inc_full_consult}}</td>
  </tr>
</table>