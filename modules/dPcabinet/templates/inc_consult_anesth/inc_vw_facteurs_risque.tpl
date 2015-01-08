{{assign var=dossier_medical_patient value=$patient->_ref_dossier_medical}}
{{assign var=dossier_medical_sejour value=$sejour->_ref_dossier_medical}}

<script>
  slowRisks = function() {
    $V(getForm('editThromboPatient').risque_thrombo_patient, 'faible');
    $V(getForm('editMCJPatient').risque_MCJ_patient, 'sans');

    {{if $sejour->_id}}
      $V(getForm('editThromboChir').risque_thrombo_chirurgie, 'faible');
      $V(getForm('editMCJChir').risque_MCJ_chirurgie, 'sans');
      $V(getForm('editAntibioSejour').risque_antibioprophylaxie, 'non');
      $V(getForm('editProphylaxieSejour').risque_prophylaxie, 'non');
    {{/if}}
  };

  razRisks = function() {
    $V(getForm('editThromboPatient').risque_thrombo_patient, 'NR');
    $V(getForm('editMCJPatient').risque_MCJ_patient, 'NR');
    $V(getForm('editFacteursRisque').facteurs_risque, '');

    {{if $sejour->_id}}
      $V(getForm('editThromboChir').risque_thrombo_chirurgie, 'NR');
      $V(getForm('editMCJChir').risque_MCJ_chirurgie, 'NR');
      $V(getForm('editAntibioSejour').risque_antibioprophylaxie, 'NR');
      $V(getForm('editProphylaxieSejour').risque_prophylaxie, 'NR');
    {{/if}}
  };
</script>


<table class="form">
  <tr>
    <th class="category" style="width: 40%;" colspan="2">Facteur de risque</th>
    <th class="category">Patient</th>
    <th class="category">Chirurgie</th>
  </tr>

  <tr>
    <td rowspan="5" style="vertical-align: middle; text-align: center">
      <p><button type="button" class="tick" onclick="slowRisks();"> Sans facteur de risque particulier</button></p>
      <p><button type="button" class="undo" onclick="razRisks()">R�initialiser</button></p>
    </td>
    <th>Maladie thromboembolique</th>
    <td style="text-align: center;">
      <form name="editThromboPatient" method="post" action="?">
        <input type="hidden" name="m" value="dPpatients" />
        <input type="hidden" name="dosql" value="do_dossierMedical_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="object_id" value="{{$patient->_id}}" />
        <input type="hidden" name="object_class" value="CPatient" />
        {{mb_field object=$dossier_medical_patient field="risque_thrombo_patient" onchange="onSubmitFormAjax(this.form);"}}
      </form>
    </td>

    {{if $sejour->_id}}
      <td style="text-align: center;">
        <form name="editThromboChir" method="post" action="?">
          <input type="hidden" name="m" value="dPpatients" />
          <input type="hidden" name="dosql" value="do_dossierMedical_aed" />
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="object_id" value="{{$sejour->_id}}" />
          <input type="hidden" name="object_class" value="CSejour" />
          {{mb_field object=$dossier_medical_sejour field="risque_thrombo_chirurgie" onchange="onSubmitFormAjax(this.form);"}}
        </form>
      </td>
    {{else}}
    <td rowspan="4">
      <div class="small-info">
        Aucun s�jour n'est associ� � cette consultation
      </div>
    </td>
    {{/if}}
  </tr>

  <tr>
    <th>Maladie de Creutzfeldt-Jakob</th>
    <td style="text-align: center;">
      <form name="editMCJPatient" method="post" action="?">
        <input type="hidden" name="m" value="dPpatients" />
        <input type="hidden" name="dosql" value="do_dossierMedical_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="object_id" value="{{$patient->_id}}" />
        <input type="hidden" name="object_class" value="CPatient" />
        {{mb_field object=$dossier_medical_patient field="risque_MCJ_patient" onchange="onSubmitFormAjax(this.form);"}}
      </form>
    </td>
    {{if $sejour->_id}}
    <td style="text-align: center;">
      <form name="editMCJChir" method="post" action="?">
        <input type="hidden" name="m" value="dPpatients" />
        <input type="hidden" name="dosql" value="do_dossierMedical_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="object_id" value="{{$sejour->_id}}" />
        <input type="hidden" name="object_class" value="CSejour" />
        {{mb_field object=$dossier_medical_sejour field="risque_MCJ_chirurgie" onchange="onSubmitFormAjax(this.form);"}}
      </form>
    </td>
    {{/if}}
  </tr>

  <tr>
    <th><strong>Risque Anesth�sique</strong>: Antibioprophylaxie</th>
    <td style="text-align: center;">&mdash;</td>
    {{if $sejour->_id}}
    <td style="text-align: center;">
      <form name="editAntibioSejour" method="post" action="?">
        <input type="hidden" name="m" value="dPpatients" />
        <input type="hidden" name="dosql" value="do_dossierMedical_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="object_id" value="{{$sejour->_id}}" />
        <input type="hidden" name="object_class" value="CSejour" />
        {{mb_field object=$dossier_medical_sejour field="risque_antibioprophylaxie" onchange="onSubmitFormAjax(this.form);"}}
      </form>
    </td>
    {{/if}}
  </tr>

  <tr>
    <th><strong>Risque Anesth�sique</strong>: Thromboprophylaxie</th>
    <td style="text-align: center;">&mdash;</td>
    {{if $sejour->_id}}
    <td style="text-align: center;">
      <form name="editProphylaxieSejour" method="post" action="?">
        <input type="hidden" name="m" value="dPpatients" />
        <input type="hidden" name="dosql" value="do_dossierMedical_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="object_id" value="{{$sejour->_id}}" />
        <input type="hidden" name="object_class" value="CSejour" />
        {{mb_field object=$dossier_medical_sejour field="risque_prophylaxie" onchange="onSubmitFormAjax(this.form);"}}
      </form>
    </td>
   {{/if}}
  </tr>

  <tr>
    <th>{{mb_label object=$dossier_medical_patient field="facteurs_risque"}}</th>
    <td colspan="2" style="text-align: center;">
      <form name="editFacteursRisque" method="post" action="?">
        <input type="hidden" name="m" value="dPpatients" />
        <input type="hidden" name="dosql" value="do_dossierMedical_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="object_id" value="{{$patient->_id}}" />
        <input type="hidden" name="object_class" value="CPatient" />
        {{mb_field object=$dossier_medical_patient field="facteurs_risque" onchange="onSubmitFormAjax(this.form);"
          form="editFacteursRisque" aidesaisie="validateOnBlur: 0" rows=5}}
      </form>
    </td>
  </tr>
</table>