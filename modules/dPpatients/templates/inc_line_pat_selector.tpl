<!-- $Id: inc_vw_patient.tpl 1738 2007-03-19 16:33:47Z maskas $ -->

<tbody class="hoverable">
{{assign var="trClass" value=""}}
{{if $patVitale}}
  {{if $_patient->nom == $patVitale->nom && $_patient->prenom == $patVitale->prenom && $_patient->naissance == $patVitale->naissance}}
  {{assign var="trClass" value="selected"}}
  {{/if}}
{{/if}}
<tr class="{{$trClass}}">
  {{assign var="nbConsults" value=$_patient->_ref_consultations|@count}}
  {{assign var="nbSejours" value=$_patient->_ref_sejours|@count}}
  {{assign var="rowspan" value=$nbConsults+$nbSejours+1}}
  <td rowspan="{{$rowspan}}">
    {{$_patient->_view}}</td>
  <td>{{$_patient->_naissance}}</td>
  
  {{if $patVitale}}
  <td>{{mb_value object=$_patient field="matricule"}}</td>
  <td>{{mb_value object=$_patient field="adresse"}}</td>
  <td class="button" rowspan="{{$rowspan}}">
    <button class="tick" type="button" onclick="Patient.selectAndUpdate({{$_patient->_id}})">
      {{tr}}Update and select{{/tr}}
    </button>
  </td>

  {{else}}
  <td>{{$_patient->tel}}</td>
  <td>{{$_patient->tel2}}</td>
  <td class="button" rowspan="{{$rowspan}}">
    <button class="edit" type="button" onclick="Patient.edit({{$_patient->_id}})">
      {{tr}}Edit{{/tr}}
    </button>
    <button class="tick" type="button" onclick="Patient.select({{$_patient->_id}}, '{{$_patient->_view|smarty:nodefaults|JSAttribute}}')">
      {{tr}}Select{{/tr}}
    </button>
  </td>

  {{/if}}
</tr>

<!-- Consultations du jour -->
{{foreach from=$_patient->_ref_consultations item=_consult}}
<tr>
  <td colspan="3" class="text">
    Consult. aujourd'hui � {{mb_value object=$_consult field=heure}}
    avec le Dr. {{$_consult->_ref_plageconsult->_ref_chir->_view}}
  </td>
</tr>
{{/foreach}}

<!-- Admissions du jour -->
{{foreach from=$_patient->_ref_sejours item=_sejour}}
<tr>
  <td colspan="3" class="text">
    Admission aujourd'hui � {{mb_value object=$_sejour field=entree_prevue format="%H:%M"}}
    pour le Dr. {{$_sejour->_ref_praticien->_view}}
  </td>
</tr>
{{/foreach}}

</tbody>