<!-- $Id$ -->

{{mb_script module="dPcompteRendu" script="document"}}
{{mb_script module="dPfiles" script="files"}}
{{mb_script module=dPcabinet script=file}}
{{mb_include module=dPfiles template=yoplet_uploader object=$object}}

<script type="text/javascript">

function viewCompleteItem(object_guid) {
  var url = new Url("system", "httpreq_vw_complete_object");
  url.addParam("object_guid", object_guid);
  url.requestUpdate("listView", { 
    onComplete: initNotes
  } );
}

function loadSejour(sejour_id){
  var url = new Url("dPpatients","httpreq_vw_dossier_sejour");
  url.addParam("sejour_id",sejour_id);
  url.requestUpdate("listView", {
    onComplete: initNotes
  } );
}


function reloadListFile(sAction){
  if(sAction == "delete" && file_preview == file_deleted){
    ZoomAjax("","","","", 0);
  }
  var url = new Url("dPfiles", "httpreq_vw_listfiles");
  url.addParam("selKey", document.FrmClass.selKey.value);
  url.addParam("selClass", document.FrmClass.selClass.value);  
  url.addParam("typeVue", document.FrmClass.typeVue.value);
  url.requestUpdate('listView');
  
  if(sAction == "add" || sAction == "delete"){
    url = new Url("dPpatients", "httpreq_vw_full_patient");
    url.addParam("patient_id", "{{$patient->_id}}");
    url.requestUpdate('listInfosPat', { 
      onComplete: ViewFullPatient.main
    } );
  }
}

function saveObjectInfos(oObject){
  var url = new Url("dPpatients", "httpreq_save_classKey");
  url.addParam("selClass", oObject.objClass);
  url.addParam("selKey", oObject.id);
  url.requestUpdate('systemMsg');
}

function view_labo_patient() {
  var url = new Url("dPImeds", "httpreq_vw_patient_results");
  url.addParam("patient_id", "{{$patient->_id}}");
  url.requestUpdate('listView');
}

function view_labo_sejour(sejour_id) {
  var url = new Url("dPImeds", "httpreq_vw_sejour_results");
  url.addParam("sejour_id", sejour_id);
  url.requestUpdate('listView');
}

Main.add(function () {
  
  {{if $consultation_id}}
  viewCompleteItem('CConsultation-{{$consultation_id}}');
  {{/if}}
  
  {{if $operation_id}}
  viewCompleteItem('COperation-{{$operation_id}}');
  {{/if}}
  
  {{if $sejour_id}}
  loadSejour('{{$sejour_id}}');
  {{/if}}
  
  initNotes();
});

</script>

<table class="main">
  <tr>
    <td style="display: none;">
      <form name="FrmClass" action="?m={{$m}}" method="get" onsubmit="reloadListFile('load'); return false;">
        <input type="hidden" name="selKey"   value="" />
        <input type="hidden" name="selClass" value="" />
        <input type="hidden" name="selView"  value="" />
        <input type="hidden" name="keywords" value="" />
        <input type="hidden" name="file_id"  value="" />
        <input type="hidden" name="typeVue"  value="1" />
      </form>
    </td>

    <td id="listInfosPat" style="width:200px;">
      {{assign var="href" value="?m=dPpatients&tab=vw_full_patients"}}      
      {{include file="inc_vw_full_patients.tpl"}}
    </td>

    <td class="greedyPane" id="listView">
      {{include file="../../dPpatients/templates/CPatient_complete.tpl"}}
    </td>
  </tr>
</table>