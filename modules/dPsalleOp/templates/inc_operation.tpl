<!-- Informations g�n�rales sur l'intervention et le patient -->
<table class="form">
  {{assign var=patient value=$selOp->_ref_sejour->_ref_patient}}
  <tr>
    <th class="title text" colspan="2">
      <button class="hslip notext" id="listplages-trigger" type="button" style="float:left">
        {{tr}}Programme{{/tr}}
      </button>
      <a class="action" style="float: right;" title="Modifier le dossier administratif" href="?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id={{$patient->_id}}">
        <img src="images/icons/edit.png" alt="modifier" />
 			</a>
      {{$patient->_view}}
      ({{$patient->_age}} ans
      {{if $patient->_age != "??"}}- {{mb_value object=$patient field="naissance"}}{{/if}})
      &mdash; Dr. {{$selOp->_ref_chir->_view}}
      <br />
      {{if $selOp->libelle}}{{$selOp->libelle}} &mdash;{{/if}}
      {{mb_label object=$selOp field=cote}} : {{mb_value object=$selOp field=cote}}
      &mdash; {{mb_label object=$selOp field=temp_operation}} : {{mb_value object=$selOp field=temp_operation}}
      
    </th>
  </tr>
  
  <!-- Mise en avant du mat�riel et remarques -->
  {{if $selOp->materiel}}
  <tr>
    <td class="text">
      <strong>{{mb_label object=$selOp field=materiel}} :</strong> 
      {{mb_value object=$selOp field=materiel}}
    </td>
  </tr>
  {{/if}}
  
  {{if $selOp->rques}}
  <tr>
    <td class="text">
      <strong>{{mb_label object=$selOp field=rques}} :</strong> 
      {{mb_value object=$selOp field=rques}}
    </td>
  </tr>
  {{/if}}
</table>

<!-- Tabulations -->
<ul id="main_tab_group" class="control_tabs">
  <li><a href="#one">Timings</a></li>
  <li><a href="#two">Anesth�sie</a></li>
  <li><a href="#threebis">Diagnostics</a></li>
  <li><a href="#three">CCAM</a></li>
  <li><a href="#four">NGAP</a></li>
  <li><a href="#five">Dossier</a></li>
</ul>
  
<hr class="control_tabs" />

<!-- Premier onglet => Timings + Personnel -->
<div id="one" style="display:none">
 	<div id="timing">
    {{include file="inc_vw_timing.tpl"}}
  </div>
  <div id="listPersonnel">
    {{include file="inc_vw_personnel.tpl"}}
  </div>
</div>

<!-- Deuxieme onglet => Anesthesie -->
<div id="two" style="display:none">
  <div id="anesth">
  {{include file="inc_vw_anesth.tpl"}}
  </div>
  <div id="info_anesth">
  {{include file="inc_vw_info_anesth.tpl"}}
  </div>
</div>

<!-- Troisieme onglet bis: codage diagnostics CIM -->
<div id="threebis" style="display:none">
  <div id="cim">
    {{assign var="sejour" value=$selOp->_ref_sejour}}
    {{include file="inc_diagnostic_principal.tpl" modeDAS=true}}
  </div>
</div>

<!-- Troisieme onglet: codage acte ccam -->
<div id="three" style="display:none">
  <div id="ccam">
    {{assign var="subject" value=$selOp}}
    {{include file="inc_gestion_ccam.tpl"}}
  </div>
</div>
<!-- Fin du troisieme onglet -->

<!-- Quatri�me onglet => Codage acte NGAP -->
<div id="four" style="display:none">
  <div id="listActesNGAP">
    {{assign var="object" value=$selOp}}
    {{include file="../../dPcabinet/templates/inc_acte_ngap.tpl"}}
  </div>
</div>

<!-- Cinquieme onglet => Dossier Medical -->
{{assign var="dossier_medical" value=$selOp->_ref_sejour->_ref_dossier_medical}}
<div id="five" style="display:none">
  <div class="text">
    {{include file="inc_vw_dossier.tpl"}}
  </div>
</div>
