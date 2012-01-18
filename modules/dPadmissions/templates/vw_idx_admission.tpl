{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=hprim21 script=pat_hprim_selector}}
{{mb_script module=hprim21 script=sejour_hprim_selector}}
{{mb_script module=dPadmissions script=admissions}}

<script type="text/javascript">

function showLegend() {
  var url = new Url("dPadmissions", "vw_legende");
  url.popup(300, 170, "Legende");
}

function printPlanning() {
  var oForm = document.selType;
  var url = new Url("dPadmissions", "print_entrees");
  url.addParam("date"      , "{{$date}}");
  url.addParam("type"      , $V(oForm._type_admission));
  url.addParam("service_id", $V(oForm.service_id));
  url.popup(700, 550, "Entrees");
}

function printDHE(type, object_id) {
  var url = new Url("dPplanningOp", "view_planning");
  url.addParam(type, object_id);
  url.popup(700, 550, "DHE");
}

function printDepassement(id) {
  var url = new Url("dPadmissions", "print_depassement");
  url.addParam("id", id);
  url.popup(700, 550, "Depassement");
}

function reloadFullAdmissions(filterFunction) {
  var oForm = getForm("selType");
  var url = new Url("dPadmissions", "httpreq_vw_all_admissions");
  url.addParam("date"      , "{{$date}}");
  url.addParam("type"      , $V(oForm._type_admission));
  url.addParam("service_id", $V(oForm.service_id));
  url.addParam("prat_id", $V(oForm.prat_id));
  url.requestUpdate('allAdmissions');
	reloadAdmission(filterFunction);
}

function reloadAdmission(filterFunction) {
  var oForm = getForm("selType");
  var url = new Url("dPadmissions", "httpreq_vw_admissions");
  url.addParam("selAdmis", "{{$selAdmis}}");
  url.addParam("selSaisis", "{{$selSaisis}}");
  url.addParam("date"      , "{{$date}}");
  url.addParam("type"      , $V(oForm._type_admission));
  url.addParam("service_id", $V(oForm.service_id));
  url.addParam("prat_id", $V(oForm.prat_id));
	if(!Object.isUndefined(filterFunction)){
	  url.addParam("filterFunction", filterFunction);
	}
  url.requestUpdate('listAdmissions');
}

function confirmation(oForm){
  if(confirm('La date enregistr�e d\'admission est diff�rente de la date pr�vue, souhaitez vous confimer l\'admission du patient ?')){
    submitAdmission(oForm);
  }
}

function submitCote(oForm) {
  return onSubmitFormAjax(oForm, { onComplete : reloadAdmission });
}

function submitAdmission(oForm, bPassCheck) {
  {{if @$modules.hprim21 && $conf.hprim21.mandatory_num_dos_ipp_adm}}
    var oIPPForm = document.forms["editIPP" + oForm.patient_id.value];
    var oNumDosForm = document.forms["editNumdos" + oForm.sejour_id.value];
    if(!bPassCheck && oIPPForm && oNumDosForm && (!$V(oIPPForm.id400) || !$V(oNumDosForm.id400)) ) {
      setExternalIds(oForm);
    } else {
      return onSubmitFormAjax(oForm, { onComplete : reloadAdmission });
    }
  {{else}}
    return onSubmitFormAjax(oForm, { onComplete : reloadAdmission });
  {{/if}}
}

var ExtRefManager = {
  sejour_id : null,
  patient_id: null,
  
  submitIPPForm: function(patient_id) {
    ExtRefManager.patient_id = patient_id;
    var oForm = document.forms["editIPP" + patient_id];
    return onSubmitFormAjax(oForm, {onComplete: ExtRefManager.reloadIPPForm});
  },
  
  reloadIPPForm: function() {
    reloadAdmission();
  },
  
  submitNumdosForm: function(sejour_id) {
    ExtRefManager.sejour_id = sejour_id;
    var oForm = document.forms["editNumdos" + this.sejour_id];
    return onSubmitFormAjax(oForm, {onComplete: ExtRefManager.reloadNumdosForm});
  },

  reloadNumdosForm: function() {
    reloadAdmission();
  }
}

function setExternalIds(oForm) {
  SejourHprimSelector["init"+oForm.sejour_id.value]();
}

PatHprimSelector.doSet = function(){
  var oForm = document[PatHprimSelector.sForm];
  $V(oForm[PatHprimSelector.sId], PatHprimSelector.prepared.id);
  ExtRefManager.submitIPPForm(oForm.patient_id.value);
}

SejourHprimSelector.doSet = function(){
  var oFormSejour = document[SejourHprimSelector.sForm];
  $V(oFormSejour[SejourHprimSelector.sId]  , SejourHprimSelector.prepared.id);
  ExtRefManager.submitNumdosForm(oFormSejour.object_id.value);
  if(SejourHprimSelector.prepared.IPPid) {
    var oFormIPP = document[SejourHprimSelector.sIPPForm];
    $V(oFormIPP[SejourHprimSelector.sIPPId]  , SejourHprimSelector.prepared.IPPid);
    ExtRefManager.submitIPPForm(oFormIPP.object_id.value);
  }
  //submitAdmission(document["editAdmFrm"+oFormSejour.object_id.value]);
}  

Main.add(function () {
  var totalUpdater = new Url("dPadmissions", "httpreq_vw_all_admissions");
  totalUpdater.addParam("date", "{{$date}}");
  totalUpdater.periodicalUpdate('allAdmissions', { frequency: 120 });
  
  var listUpdater = new Url("dPadmissions", "httpreq_vw_admissions");
  listUpdater.addParam("selAdmis", "{{$selAdmis}}");
  listUpdater.addParam("selSaisis", "{{$selSaisis}}");
  listUpdater.addParam("date", "{{$date}}");
  listUpdater.periodicalUpdate('listAdmissions', { frequency: 120 });
});

{{if $web100T_active}}
  {{assign var=url_web100T value=$conf.web100T.url_application}}
  openWeb100T = function(sejour_id) {
    var url = new Url("web100T", "ajax_vw_sejour");
    url.addParam("sejour_id", sejour_id);
    url.requestModal(700,500);
  }
{{/if}}
</script>

<table class="main">
<tr>
  <td>
    <a href="#" onclick="showLegend()" class="button search">L�gende</a>
  </td>
  <td style="float: right">
    <form action="?" name="selType" method="get">
      {{mb_field object=$sejour field="_type_admission" emptyLabel="CSejour.all" onchange="reloadFullAdmissions()"}}
      <select name="service_id" onchange="reloadFullAdmissions();">
        <option value="">&mdash; Tous les services</option>
        {{foreach from=$services item=_service}}
          <option value="{{$_service->_id}}"{{if $_service->_id == $sejour->service_id}}selected="selected"{{/if}}}>{{$_service}}</option>
        {{/foreach}}
      </select>
      <select name="prat_id" onchange="reloadFullAdmissions();">
        <option value="">&mdash; Tous les praticiens</option>
        {{foreach from=$prats item=_prat}}
          <option value="{{$_prat->_id}}"{{if $_prat->_id == $sejour->praticien_id}}selected="selected"{{/if}}>{{$_prat}}</option>
        {{/foreach}}
      </select>
    </form>
    <a href="#" onclick="printPlanning()" class="button print">Imprimer</a>
  </td>
</tr>
  <tr>
    <td id="allAdmissions" style="width: 250px">
    </td>
    <td id="listAdmissions" style="width: 100%">
    </td>
  </tr>
</table>