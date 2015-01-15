{{assign var="consult" value=$object->_ref_consultation}}
<script>
newExam = function(sAction, consultation_id) {
  if (sAction) {
    var url = new Url;
    url.setModuleAction("dPcabinet", sAction);
    url.addParam("consultation_id", consultation_id);
    url.popup(900, 600, "Examen");  
  }
}
</script>

<table class="form">
  <tr>
    <th class="title" colspan="4">
      {{mb_include module=system template=inc_object_idsante400 object=$consult}}
      {{mb_include module=system template=inc_object_history    object=$consult}}
      {{mb_include module=system template=inc_object_notes      object=$consult}}
      
      {{$consult}}
    </th>
  </tr>
  <tr>
    <td colspan="2">
      <strong>Date :</strong>
      <i>le {{$object->_ref_plageconsult->date|date_format:"%d %B %Y"}} � {{$consult->heure|date_format:$conf.time}}</i>
    </td>
    <td colspan="2">
      <strong>Praticien :</strong>
      <i>Dr {{$object->_ref_plageconsult->_ref_chir->_view}}</i>
    </td>
  </tr>
  <tr>
    <td class="text" colspan="2">
      <strong>Motif :</strong>
      <i>{{$consult->motif|nl2br}}</i>
    </td>
    <td class="text" colspan="2">
      <strong>Remarques :</strong>
      <i>{{$consult->rques|nl2br}}</i>
    </td>
  </tr>
  
  <tr>
    <td class="text" colspan="4">
      <strong>Intervention :</strong>
      {{if $object->operation_id}}
        le <i>{{$object->_ref_operation->_ref_plageop->date|date_format:"%a %d %b %Y"}}</i>
        par le <i>Dr {{$object->_ref_operation->_ref_chir->_view}}</i>
        (cot� {{tr}}COperation.cote.{{$object->_ref_operation->cote}}{{/tr}})
        <ul>
          {{if $object->_ref_operation->libelle}}
            <li><em>[{{$object->_ref_operation->libelle}}]</em></li>
          {{/if}}
          {{foreach from=$object->_ref_operation->_ext_codes_ccam item=curr_code}}
            <li><em>{{$curr_code->libelleLong}}</em> ({{$curr_code->code}})</li>
          {{/foreach}}
        </ul>
      {{else}}
        Aucune Intervention
      {{/if}}
    </td>
  </tr>
  
  {{if $object->operation_id}}
  <tr>
    <td class="text" colspan="2">
      <strong>Admission : </strong>
      <i>
        {{tr}}CSejour.type.{{$object->_ref_operation->_ref_sejour->type}}{{/tr}}
        {{if $object->_ref_operation->_ref_sejour->type!="ambu" && $object->_ref_operation->_ref_sejour->type!="exte"}}
          &mdash; {{$object->_ref_operation->_ref_sejour->_duree_prevue}} jour(s)
        {{/if}}
      </i>
    </td>
    <td class="text" colspan="2">
      <strong>Anesth�sie pr�vue :</strong>
      <i>{{$object->_ref_operation->_lu_type_anesth}}</i>
    </td>
  </tr>
  {{/if}}
  
  <tr>
    <td class="text" colspan="2">
      <strong>Position :</strong>
      {{if $object->operation_id}}
        <i>{{tr}}COperation.position.{{$object->_ref_operation->position}}{{/tr}}</i>
      {{/if}}
    </td>
    <td class="text" colspan="2">
      <strong>Techniques Compl�mentaires :</strong>
      <ul>
        {{foreach from=$object->_ref_techniques item=curr_tech}}
        <li>
          <i>{{$curr_tech->technique}}</i>
        </li>
        {{foreachelse}}
          <li><i>Pas de technique compl�mentaire</i></li>
        {{/foreach}}
      </ul>
    </td>
  </tr>
  
  {{assign var=const_med value=$object->_ref_consultation->_ref_patient->_ref_constantes_medicales}}
  {{assign var=dossier_medical value=$object->_ref_consultation->_ref_patient->_ref_dossier_medical}}
  <tr>
    <th class="title" colspan="4">
      Informations sur le patient
    </th>
  </tr>
  <tr>
    <td class="text">
      <strong>Poids :</strong>
      {{if $const_med->poids}}<i>{{$const_med->poids}} kg</i>{{/if}}
    </td>
    <td class="text" rowspan="2">
      <strong>IMC :</strong>
      {{if $const_med->_imc}}<i>{{$const_med->_imc}}</i>{{/if}}
      {{if $const_med->_imc_valeur}}<br/><i>{{$const_med->_imc_valeur}}</i>{{/if}}
    </td>
    <td class="text">
      <strong>Groupe sanguin :</strong>
      <i>{{tr}}CDossierMedical.groupe_sanguin.{{$dossier_medical->groupe_sanguin}}{{/tr}} &nbsp;{{tr}}CDossierMedical.rhesus.{{$dossier_medical->rhesus}}{{/tr}}</i>
    </td>
    <td class="text">
      <strong>RAI :</strong>
      <i>{{tr}}CConsultAnesth.rai.{{$object->rai}}{{/tr}}</i>
    </td>
  </tr>
  
  <tr>
    <td class="text">
      <strong>Taille :</strong>
      {{if $const_med->taille}}<i>{{$const_med->taille}} cm</i>{{/if}}
    </td>
    <td class="text">
      <strong>ASA :</strong>
      {{if $object->operation_id}}
        <i>{{tr}}COperation.ASA.{{$object->_ref_operation->ASA}}{{/tr}}</i>
      {{/if}}
    </td>
    <td class="text">
      <strong>VST :</strong>
      <i>{{if $const_med->_vst}}{{$const_med->_vst}} ml{{/if}}</i>
    </td>
  </tr>
  
  <tr>
    <td class="text">
      <strong>Pouls :</strong>
      {{if $const_med->pouls}}<i>{{$const_med->pouls}} / min</i>{{/if}}
    </td>
    <td class="text">
      <strong>TA :</strong>
      {{if $const_med->ta_gauche}}
      <i>
        {{$const_med->_ta_gauche_systole}} / {{$const_med->_ta_gauche_diastole}} cm Hg
      </i>
      {{/if}}
    </td>
    <td class="text">
      <strong>Spo2 :</strong>
      {{if $const_med->spo2}}<i>{{$const_med->spo2}} %</i>{{/if}}
    </td>
    <td class="text">
      <strong>PSA :</strong>
      {{if $object->_psa}}<i>{{$object->_psa}} ml/GR</i>{{/if}}
    </td>
  </tr>
  <tr>
    <td class="text" colspan="2">
      <strong>Tabac :</strong>
      <i>{{$object->tabac|nl2br}}</i>
    </td>
    <td class="text" colspan="2">
      <strong>Oenolisme:</strong>
      <i>{{$object->oenolisme|nl2br}}</i>
    </td>
  </tr>
  
  {{if $object->mallampati || $object->bouche || $object->distThyro || $object->mob_cervicale || $object->etatBucco ||
       $object->examenCardio || $object->examenPulmo || $object->examenDigest || $object->examenAutre ||
       $object->conclusion}}
  <tr>
    <th class="title" colspan="4">
      Conditions d'intubation
    </th>
  </tr>
  <tr>
    <td class="text" colspan="2">
      <strong>Mallampati :</strong>
      {{if $object->mallampati}}<i>{{tr}}CConsultAnesth.mallampati.{{$object->mallampati}}{{/tr}}</i>{{/if}}
    </td>
    <td class="text" colspan="2">
      <strong>Etat bucco-dentaire :</strong>
      <i>{{$object->etatBucco|nl2br}}</i>
    </td>
  </tr>
  <tr>
    <td class="text" colspan="2">
      <strong>Examen cardiovasculaire :</strong>
      <i>{{$object->examenCardio|nl2br}}</i>
    </td>
    <td class="text" colspan="2">
      <strong>Examen pulmonaire :</strong>
      <i>{{$object->examenPulmo|nl2br}}</i>
    </td>
  </tr>
  <tr>
    <td class="text" colspan="2">
      <strong>Examen digestif :</strong>
      <i>{{$object->examenDigest|nl2br}}</i>
    </td>
    <td class="text" colspan="2">
      <strong>Examen autre :</strong>
      <i>{{$object->examenAutre|nl2br}}</i>
    </td>
  </tr>
  <tr>
    <td class="text" colspan="2">
      <strong>Ouverture de la bouche :</strong>
      {{if $object->bouche}}<i>{{tr}}CConsultAnesth.bouche.{{$object->bouche}}{{/tr}}</i>{{/if}}
    </td>
    <td class="text" colspan="2">
      <strong>Conclusion :</strong>
      <i>{{$object->conclusion|nl2br}}</i>
    </td>
  </tr>
  <tr>
    <td class="text" colspan="4">
      <strong>Mobilit� cervicale :</strong>
      {{if $object->mob_cervicale}}<i>{{tr}}CConsultAnesth.mob_cervicale.{{$object->mob_cervicale}}{{/tr}}</i>{{/if}}
    </td>
  </tr>
  <tr>
    <td class="text" colspan="2">
      <strong>Distance thyro-mentonni�re :</strong>
      {{if $object->distThyro}}<i>{{tr}}CConsultAnesth.distThyro.{{$object->distThyro}}{{/tr}}</i>{{/if}}
    </td>
    <td class="text" colspan="2">
      <i>
        {{if $object->_intub_difficile}}
          Intubation Difficile Pr�visible
        {{else}}
          Pas Intubation Difficile Pr�visible
        {{/if}}
      </i>
    </td>
  </tr>
  {{/if}}
  
  <tr>
    <th class="title" colspan="4">
      Examens Compl�mentaires
    </th>
  </tr>
  
  <tr>
    <td class="text">
      <strong>Hb :</strong>
      {{if $object->hb}}<i>{{$object->hb}} g/dl</i>{{/if}}
    </td>
    <td class="text">
      <strong>Plaquettes :</strong>
      {{if $object->plaquettes}}<i>{{$object->plaquettes}}</i>{{/if}}
    </td>
    <td class="text">
      <strong>Na+ :</strong>
      {{if $object->na}}<i>{{$object->na}} mmol/l</i>{{/if}}
    </td>
    <td class="text">
      <strong>TCA :</strong>
      {{if $object->tca}}
        <i>{{$object->tca_temoin}} s / {{$object->tca}} s</i>
      {{/if}}
    </td>  
  </tr>
  <tr>
    <td class="text">
      <strong>Ht :</strong>
      {{if $object->ht}}<i>{{$object->ht}} %</i>{{/if}}
    </td>
    <td class="text">
      <strong>Cr�atinine :</strong>
      {{if $object->creatinine}}<i>{{$object->creatinine}} mg/l</i>{{/if}}
    </td>
    <td class="text">
      <strong>K+ :</strong>
      {{if $object->k}}<i>{{$object->k}} mmol/l</i>{{/if}}
    </td>
    <td class="text">
      <strong>TS Ivy :</strong>
      {{if $object->tsivy}}<i>{{$object->tsivy|date_format:"%Mm%Ss"}}</i>{{/if}}
    </td>  
  </tr>
  <tr>
    <td class="text">
      <strong>Ht final :</strong>
      {{if $object->ht_final}}<i>{{$object->ht_final}} %</i>{{/if}}
    </td>
    <td class="text">
      <strong>Clairance de Cr�atinine :</strong>
      {{if $object->_clairance}}<i>{{$object->_clairance}} ml/min</i>{{/if}}
    </td>
    <td class="text">
      <strong>TP :</strong>
      {{if $object->tp}}<i>{{$object->tp}} %</i>{{/if}}
    </td>
    <td class="text">
      <strong>ECBU :</strong>
      {{if $object->ecbu}}<i>{{tr}}CConsultAnesth.ecbu.{{$object->ecbu}}{{/tr}}</i>{{/if}}
    </td>  
  </tr>
  
  <tr>
    <td class="text" colspan="2">
      <strong>Examens Compl�mentaires :</strong>
      {{foreach from=$consult->_types_examen key=curr_type item=list_exams}}
      {{if $list_exams|@count}}
        <br/><i>{{tr}}CExamComp.realisation.{{$curr_type}}{{/tr}}</i>
        <ul>
          {{foreach from=$list_exams item=curr_examcomp}}
          <li>
            {{$curr_examcomp->examen}}
            {{if $curr_examcomp->fait}}
              (Fait)
            {{else}}
              (A Faire)
            {{/if}}
          </li>
          {{/foreach}}
        </ul>
      {{/if}}
      {{foreachelse}}
       <i>Pas d'examen compl�mentaire</i>
      {{/foreach}}
    </td>
    <td class="text" colspan="2">
      {{if $consult->_ref_exampossum->_id}}
        <strong>Score Possum :</strong>
        <i>
          Morbidit� : {{mb_value object=$consult->_ref_exampossum field="_morbidite"}}% &mdash;
          Mortalit� : {{mb_value object=$consult->_ref_exampossum field="_mortalite"}}%
        </i><br />
      {{/if}}
      {{if $consult->_ref_examnyha->_id}}
        <strong>Clasification NYHA :</strong>
        <i>{{mb_value object=$consult->_ref_examnyha field="_classeNyha"}}</i>   
      {{/if}}
    </td>
  </tr>
  <tr>
    <td class="text" colspan="2">
      <strong>Pr�m�dication :</strong>
      <i>{{$object->premedication|nl2br}}</i>
    </td>
    <td class="text" colspan="2">
      <strong>Pr�paration pr�-op�ratoire :</strong>
      <i>{{$object->prepa_preop|nl2br}}</i>
    </td>
  </tr>  
</table>

<table class="tbl">
  {{mb_include module=cabinet template=inc_list_actes_ccam subject=$object->_ref_consultation vue=complete}}
</table>
  
<!-- Dossier M�dical -->
{{assign var=sejour value=$object->_ref_sejour}}

{{if !$sejour->_id}}
<div class="big-info">
  La consultation d'anesth�sie n'est associ� � aucun s�jour, 
  il n'y a donc pas de dossier m�dical disponible.
</div>
{{elseif $sejour->_ref_dossier_medical->_id}}
  {{mb_include module=patients template=CDossierMedical_complete object=$sejour->_ref_dossier_medical}}
{{/if}}
