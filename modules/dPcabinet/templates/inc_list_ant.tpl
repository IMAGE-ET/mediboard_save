{{assign var=dossier_medical value=$patient->_ref_dossier_medical}}
{{assign var=prescription_sejour_id value=""}}
{{if $sejour->_ref_prescription_sejour}}
  {{assign var=prescription_sejour_id value=$sejour->_ref_prescription_sejour->_id}}
{{/if}}

{{mb_script module="patients" script="antecedent" ajax=true}}

<script type="text/javascript">
Traitement = {
  prescription_sejour_id: {{$prescription_sejour_id|@json}},
  remove: function(oForm, onComplete) {
    var oOptions = {
      typeName: 'ce traitement',
      ajax: 1,
      target: 'systemMsg'
    };
    
    var oOptionsAjax = {
      onComplete: onComplete
    };
    
    confirmDeletion(oForm, oOptions, oOptionsAjax);
  },  
  cancel: function(oForm, onComplete) {
    $V(oForm.annule, 1);
    onSubmitFormAjax(oForm, {
      onComplete: function(){
        if (onComplete) {
          onComplete();
        }
      }
    });
    $V(oForm.annule, '');
  },
  restore: function(oForm, onComplete) {
    $V(oForm.annule, '0');
    onSubmitFormAjax(oForm, {onComplete: function(){
      if (onComplete) {
        onComplete();
      }
    }});
    $V(oForm.annule, '');
  },
  toggleCancelled: function(list) {
    $(list).select('.cancelled').invoke('toggle');
  },
  copyTraitement: function(traitement_id) {
    var oFormPrescription = getForm("prescription-sejour-{{$patient->_id}}");
    var oFormTransfert = getForm("transfert_line_TP-{{$patient->_id}}");
    
    $V(oFormTransfert.prescription_line_medicament_id, traitement_id);

    if (!this.prescription_sejour_id) {
      return onSubmitFormAjax(oFormPrescription);
    }
    else {
      return onSubmitFormAjax(oFormTransfert, {onComplete: DossierMedical.reloadDossierSejour});
    }
  },
  copyLine: function(prescription_id) {
    this.prescription_sejour_id = prescription_id;
    var oFormTransfert = getForm("transfert_line_TP-{{$patient->_id}}");

    $V(oFormTransfert.prescription_id, prescription_id);
    onSubmitFormAjax(oFormTransfert, {onComplete: DossierMedical.reloadDossierSejour});
  }
};

showModalTP = function(dossier_medical_id, sejour_id, prescription_sejour_id) {
  window.modalUrlTp = new Url("prescription", "ajax_vw_traitements_personnels");
  window.modalUrlTp.addParam("dossier_medical_id", dossier_medical_id);
  window.modalUrlTp.addParam("sejour_id", sejour_id);
  window.modalUrlTp.addParam("prescription_sejour_id", prescription_sejour_id);
  window.modalUrlTp.addParam("refresh_prescription", true);
  window.modalUrlTp.requestModal("60%", "40%", {
    onClose: function() {
      if (window.tab_sejour) {
        window.tab_sejour.setActiveTab("prescription_sejour");
      }
      if (window.tabsConsultAnesth) {
        window.tabsConsultAnesth.setActiveTab("prescription_sejour");
      }
    Prescription.reloadPrescSejour(null, '{{$sejour->_id}}');
  } });
}

</script>

<!--  Formulaire de cr�ation de prescription si inexistante -->
<form name="prescription-sejour-{{$patient->_id}}" method="post" onsubmit="return false;">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="prescription_id" value="" />
  <input type="hidden" name="dosql" value="do_prescription_aed" />
  <input type="hidden" name="type" value="sejour" />
  <input type="hidden" name="object_class" value="CSejour" />
  <input type="hidden" name="object_id" value="{{$sejour->_id}}" />
  <input type="hidden" name="praticien_id" value="{{$sejour->praticien_id}}" />
  <input type="hidden" name="ajax" value="1" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="callback" value="Traitement.copyLine" />
</form>

<!--  Formulaire de duplication de traitement -->
<form name="transfert_line_TP-{{$patient->_id}}" action="?" method="post" onsubmit="return false;">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="do_transfert_line_tp_aed" />
  <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
  <input type="hidden" name="prescription_line_medicament_id" value="" />
  <input type="hidden" name="debut" value="{{$sejour->entree|date_format:'%Y-%m-%d'}}" />
  <input type="hidden" name="prescription_id" value="{{$prescription_sejour_id}}" />
</form>


{{if $dossier_medical->_count_cancelled_antecedents}}
  <button class="search" style="float: right" onclick="Antecedent.toggleCancelled('antecedents-{{$dossier_medical->_guid}}')">
    Afficher les {{$dossier_medical->_count_cancelled_antecedents}} ant�c�dents annul�s
  </button>
{{/if}}
<strong {{if $dossier_medical->_count_cancelled_antecedents}}style="line-height: 22px;"{{/if}}>Ant�c�dents</strong>

<ul id="antecedents-{{$dossier_medical->_guid}}">
  {{if $dossier_medical->_count_antecedents || $dossier_medical->_count_cancelled_antecedents}}
    {{foreach from=$dossier_medical->_all_antecedents item=_antecedent}}
    <li {{if $_antecedent->annule}}class="cancelled" style="display: none;"{{/if}}>
      <form name="Del-{{$_antecedent->_guid}}" action="?m=dPcabinet" method="post">
        <input type="hidden" name="m" value="dPpatients" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="dosql" value="do_antecedent_aed" />
        {{mb_key object=$_antecedent}}

        <input type="hidden" name="annule" value="" />
               
        <!-- Seulement si l'utilisateur est le cr�ateur -->
        {{if $_antecedent->_ref_first_log && $_antecedent->_ref_first_log->user_id == $app->user_id}}
        <button title="{{tr}}Delete{{/tr}}" class="trash notext" type="button" onclick="Antecedent.remove(this.form, DossierMedical.reloadDossierPatient)">
          {{tr}}Delete{{/tr}}
        </button>
        {{/if}}
        
        {{if $_is_anesth && $sejour->_id}}
        <button title="{{tr}}Add{{/tr}}" class="add notext" type="button" onclick="copyAntecedent({{$_antecedent->_id}})">
          {{tr}}Add{{/tr}}
        </button>
        {{/if}}         
      </form>
  
      <span onmouseover="ObjectTooltip.createEx(this, '{{$_antecedent->_guid}}')">
        <strong>
          {{if $_antecedent->type    }} {{mb_value object=$_antecedent field=type    }} {{/if}}
          {{if $_antecedent->appareil}} {{mb_value object=$_antecedent field=appareil}} {{/if}}
        </strong>
        {{if $_antecedent->date}}
          {{mb_value object=$_antecedent field=date}} : 
        {{/if}}
        {{$_antecedent->rques|nl2br}}
      </span>
    </li>
    {{/foreach}}
  {{else}}
    <li class="empty">{{tr}}CAntecedent.unknown{{/tr}}</li>
  {{/if}}
</ul>

{{assign var=display value="none"}}
{{if !($dossier_medical->_ref_prescription && $dossier_medical->_ref_prescription->_ref_prescription_lines|@count)}}
  {{assign var=display value="inline"}}
{{elseif $dossier_medical->absence_traitement}}
  <script>
    Main.add(function(){
      var form = getForm("save_absence_ttt");
      $V(form.absence_traitement, "0");
    });
  </script>
{{/if}}
<form name="save_absence_ttt" action="?" method="post" onsubmit="return onSubmitFormAjax(this);" style="float: right;display: {{$display}}">
  {{mb_key   object=$dossier_medical}}
  {{mb_class object=$dossier_medical}}
  <input type="hidden" name="object_id" value="{{$patient->_id}}" />
  <input type="hidden" name="object_class" value="{{$patient->_class}}" />
  {{mb_label object=$dossier_medical field=absence_traitement}}
  {{mb_field object=$dossier_medical field=absence_traitement typeEnum=checkbox onchange="return onSubmitFormAjax(this.form);"}}
</form>

<!-- Traitements -->
{{if is_array($dossier_medical->_ref_traitements) || $dossier_medical->_ref_prescription}}
  {{if $dossier_medical->_count_cancelled_traitements}}
    <button class="search" style="float: right;" onclick="Traitement.toggleCancelled('traitements-{{$dossier_medical->_guid}}')">
      Afficher les {{$dossier_medical->_count_cancelled_traitements}} traitements stopp�s
    </button>
  {{/if}}

  {{if  $dossier_medical->_ref_prescription && $dossier_medical->_ref_prescription->_ref_prescription_lines && $_is_anesth && ($app->_ref_user->isPraticien() || $app->_ref_user->isSageFemme() || !$conf.dPprescription.CPrescription.role_propre)}}
  <button class="tick" type="button" style="float: right" onclick="showModalTP('{{$dossier_medical->_id}}','{{$sejour->_id}}','{{$prescription_sejour_id}}');">G�rer les traitements personnels</button>
  {{/if}}

  <strong>Traitements personnels</strong>
{{/if}}

<div id="traitements-{{$dossier_medical->_guid}}">
{{if $dossier_medical->_ref_prescription}}
  <ul>
  {{foreach from=$dossier_medical->_ref_prescription->_ref_prescription_lines item=_line}}
    <li {{if $_line->_stopped}}class="cancelled" style="display: none;"{{/if}}>
      <form name="delTraitementDossierMedPat-{{$_line->_id}}"  action="?" method="post">
        <input type="hidden" name="m" value="dPprescription" />
        <input type="hidden" name="del" value="1" />
        <input type="hidden" name="dosql" value="do_prescription_line_medicament_aed" />
        <input type="hidden" name="prescription_line_medicament_id" value="{{$_line->_id}}" />
        
        {{if $_line->creator_id == $app->user_id}}
        <button class="trash notext" type="button" onclick="Traitement.remove(this.form, DossierMedical.reloadDossierPatient)">
          {{tr}}Delete{{/tr}}
        </button>
        {{/if}}
        
        {{if $sejour->_id && $user->_is_praticien}}
          <button class="add notext" type="button" onclick="Traitement.copyTraitement('{{$_line->_id}}')">
            {{tr}}Add{{/tr}}
          </button>
        {{/if}}
        {{mb_include module=system template=inc_interval_date from=$_line->debut to=$_line->fin}}
        <span onmouseover="ObjectTooltip.createEx(this, '{{$_line->_guid}}', 'objectView')">
          <a href="#1" onclick="Prescription.showMonographyMedicament(null,'{{$_line->code_ucd}}','{{$_line->code_cis}}');">
            {{$_line->_ucd_view}}
          </a>
        </span>
        
        <span class="compact" style="display: inline;">
          {{$_line->commentaire}}
          {{if $_line->_ref_prises|@count}}
            <br />
            ({{foreach from=`$_line->_ref_prises` item=_prise name=foreach_prise}}
            {{$_prise}}{{if !$smarty.foreach.foreach_prise.last}},{{/if}}
            {{/foreach}})
          {{/if}}

          {{if $_line->long_cours}}
            (Long cours)
          {{/if}}
        </span>
      </form>
    </li>
  {{/foreach}}
  </ul>
  {{if $dossier_medical->_ref_traitements|@count && $dossier_medical->_ref_prescription->_ref_prescription_lines|@count}}
  <hr style="width: 50%;" />
  {{/if}}
{{/if}}


{{if is_array($dossier_medical->_ref_traitements)}}
<ul>
  {{foreach from=$dossier_medical->_ref_traitements item=_traitement}}
  <li {{if $_traitement->annule}}class="cancelled" style="display: none;"{{/if}}>
    <form name="delTrmtFrm-{{$_traitement->_id}}" action="?m=dPcabinet" method="post">
    <input type="hidden" name="m" value="dPpatients" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="dosql" value="do_traitement_aed" />
    {{mb_key object=$_traitement}}
    
    {{if $_traitement->_ref_first_log && $_traitement->_ref_first_log->user_id == $app->user_id}}
    <button class="trash notext" type="button" onclick="Traitement.remove(this.form, DossierMedical.reloadDossierPatient)">
      {{tr}}delete{{/tr}}
    </button>
    {{/if}}
    
    {{if $_is_anesth && $sejour->_id}}
    <button class="add notext" type="button" onclick="copyTraitement('{{$_traitement->_id}}')">
      {{tr}}Add{{/tr}}
    </button>
    {{/if}}

    {{mb_include module=system template=inc_interval_date_progressive object=$_traitement from_field=debut to_field=fin}}

    <span onmouseover="ObjectTooltip.createEx(this, '{{$_traitement->_guid}}')">
      {{$_traitement->traitement|nl2br}}
    </span>

    </form>
  </li>
  {{foreachelse}}
  {{if !($dossier_medical->_ref_prescription && $dossier_medical->_ref_prescription->_ref_prescription_lines|@count)}}
  <li class="empty">{{tr}}CTraitement.unknown{{/tr}}</li>
  {{/if}}
  {{/foreach}}
</ul>
{{/if}}
</div>

<strong>Diagnostics CIM</strong>
<ul>
  {{foreach from=$dossier_medical->_ext_codes_cim item=_code}}
  <li>
    <button class="trash notext" type="button" onclick="oCimField.remove('{{$_code->code}}')">
      {{tr}}Delete{{/tr}}
    </button>
    {{if $_is_anesth || $sejour->_id}}
    <button class="add notext" type="button" onclick="oCimAnesthField.add('{{$_code->code}}')">
      {{tr}}Add{{/tr}}
    </button>
    {{/if}}
    {{$_code->code}}: {{$_code->libelle}}
  </li>
  {{foreachelse}}
  <li class="empty">{{tr}}CDossierMedical-codes_cim.unknown{{/tr}}</li>
  {{/foreach}}
</ul>

<!-- Gestion des diagnostics pour le dossier medical du patient -->
<form name="editDiagFrm" action="?m=dPcabinet" method="post">
  <input type="hidden" name="m" value="dPpatients" />
  <input type="hidden" name="tab" value="edit_consultation" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="dosql" value="do_dossierMedical_aed" />
  <input type="hidden" name="object_id" value="{{$patient->_id}}" />
  <input type="hidden" name="object_class" value="CPatient" />
  <input type="hidden" name="codes_cim" value="{{$dossier_medical->codes_cim}}" />
</form>

<script type="text/javascript">
// FIXME : Modifier le tokenfield, car deux appels � onchange
Main.add(function(){
  var form = getForm("editDiagFrm");
  
  // form may be undefined if the page is changed while loading
  if (form) {
    oCimField = new TokenField(form.codes_cim, {
      confirm  : 'Voulez-vous r�ellement supprimer ce diagnostic ?',
      onChange : updateTokenCim10
    });
  }
});
</script>      