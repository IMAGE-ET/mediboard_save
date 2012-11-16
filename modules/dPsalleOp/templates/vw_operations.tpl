{{mb_script module="dPplanningOp" script="cim10_selector"}}
{{if $op}}
{{assign var="chir_id" value=$selOp->_ref_chir->_id}}
{{/if}}
{{assign var="do_subject_aed" value="do_planning_aed"}}
{{assign var="module" value="dPsalleOp"}}
{{assign var="object" value=$selOp}}
{{mb_include module=salleOp template=js_codage_ccam}}
{{mb_script module="soins" script="plan_soins"}}
{{mb_include module=planningOp template=js_form_sejour only_ald=1}}

<script type="text/javascript">

function printFicheAnesth(consult_id) {
  var url = new Url("dPcabinet", "print_fiche"); 
  url.addParam("consultation_id", consult_id);
  url.popup(700, 500, "printFiche");
}

function submitTiming(oForm) {
  submitFormAjax(oForm, 'systemMsg', { 
    onComplete : function() { 
      reloadTiming(oForm.operation_id.value) 
    } 
  });
}

function reloadTiming(operation_id){
  {{if $object->_id}}
  var url = new Url("dPsalleOp", "httpreq_vw_timing");
  url.addParam("operation_id", operation_id);
  url.requestUpdate("timing", { onComplete: function() { ActesCCAM.refreshList({{$object->_id}},{{$object->_praticien_id}});} } );
  {{/if}}
}

function submitAnesth(oForm) {
  submitFormAjax(oForm, 'systemMsg', { 
    onComplete: function() {
      if(Prescription.updatePerop){
        Prescription.updatePerop('{{$selOp->sejour_id}}');
      }
      reloadAnesth(oForm.operation_id.value);
      if(document.visiteAnesth && document.visiteAnesth.date_visite_anesth.value == 'current'){
        $V(document.visiteAnesth.prat_visite_anesth_id, oForm.anesth_id.value);
      }
    }
  });
}

function signVisiteAnesth(anesth_id) {
  alert('anesth num�ro ' + anesth_id);
}

function reloadAnesth(operation_id){
  var url = new Url("dPsalleOp", "httpreq_vw_anesth");
  url.addParam("operation_id", operation_id);
  url.requestUpdate("anesth", { 
    onComplete: function() { 
      if(reloadDocumentsAnesth) {
        reloadDocumentsAnesth();
      }
      ActesCCAM.refreshList(operation_id,"{{$selOp->chir_id}}"); 
    }
  } );  
}

function reloadDiagnostic(sejour_id, modeDAS) {
  var url = new Url("dPsalleOp", "httpreq_diagnostic_principal");
  url.addParam("sejour_id", sejour_id);
  url.addParam("modeDAS", modeDAS);
  url.requestUpdate("cim");
}

function reloadPersonnel(operation_id){
  var url = new Url("dPsalleOp", "httpreq_vw_personnel");
  url.addParam("operation_id", operation_id);
  url.requestUpdate("listPersonnel");
}

function confirmeCloture() {
  return confirm("Action irr�versible. Seul le service PSMI pourra modifier le codage de vos actes. Confirmez-vous la cloture de votre cotation pour aujourd'hui ?");
}

Main.add(function () {
  var url = new Url;
  {{if $conf.dPsalleOp.COperation.mode || ($currUser->_is_praticien && !$currUser->_is_anesth)}}
  url.setModuleAction("dPsalleOp", "httpreq_liste_op_prat");
  {{else}}
  url.setModuleAction("dPsalleOp", "httpreq_liste_plages");
  {{/if}}
  url.addParam("date", "{{$date}}");
  url.addParam("operation_id", "{{$selOp->_id}}");
  url.addParam("hide_finished", "{{$hide_finished}}");
  
  url.periodicalUpdate('listplages', { frequency: 90 });
  
  {{if $selOp->_id}}
  // Initialisation des onglets
  if ($('main_tab_group')){
    Control.Tabs.create('main_tab_group', true);
    var tabName = Control.Tabs.loadTab('main_tab_group');
    {{if "maternite"|module_active}}
      if (tabName && tabName == "grossesse") {
        refreshGrossesse('{{$selOp->_id}}');
      }
    {{/if}}      
  }

  // Effet sur le programme
  if ($('listplages') && $('listplages-trigger')){
    new PairEffect("listplages", { sEffect : "appear", bStartVisible : true });
  }    
  {{/if}}
  
  // Sauvegarde de l'operation_id selectionn� (utile pour l'ajout de DMI dans la prescription)
  window.DMI_operation_id = "{{$selOp->_id}}";
});

</script>

<table class="main">
  <tr>
    <td style="width: 220px;" id="listplages"></td>
    <td>
    {{if $selOp->_id}}
      {{if $conf.dPsalleOp.CDailyCheckList.active != '1' || 
           $date < $smarty.now|date_format:'%Y-%m-%d' || 
           $daily_check_list->_id && $daily_check_list->validator_id || 
           $currUser->_is_praticien}}
        {{include file=inc_operation.tpl}}
      {{else}}
        {{include file=inc_edit_check_list.tpl 
                  check_list=$daily_check_list 
                  check_item_categories=$daily_check_item_categories
                  personnel=$listValidateurs}}
      {{/if}}
    {{else}}
      <div class="big-info">
        Veuillez s�lectionner une intervention dans la liste pour pouvoir :
        <ul>
          <li>s�lectionner le personnel en salle</li>
          <li>effectuer l'horodatage</li>
          <li>coder les diagnostics</li>
          <li>coder les actes</li>
          <li>consulter le dossier</li>
        </ul>
      </div>
    {{/if}}
    </td>
  </tr>
</table>