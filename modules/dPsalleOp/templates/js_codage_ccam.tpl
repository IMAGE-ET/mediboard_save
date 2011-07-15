{{mb_script module="dPplanningOp" script="ccam_selector"}}

<script type="text/javascript">

ActesCCAM = {
  notifyChange: function(subject_id, chir_id) {
	  ActesCCAM.refreshList(subject_id, chir_id)
    if (Reglement) Reglement.reload(false);
  },
  refreshList: function(subject_id, chir_id) {
    if($('viewSejourHospi')){
      loadSejour(subject_id);
    }
    url_actes = new Url;
    url_actes.addParam("chir_id", chir_id);  
    url_actes.addParam("module","{{$module}}");
    url_actes.addParam("do_subject_aed","{{$do_subject_aed}}");
    url_actes.addParam("object_class", "{{$object->_class_name}}");
    url_actes.addParam("object_id", subject_id);
    url_actes.setModuleAction("dPsalleOp", "httpreq_ccam");
    url_actes.requestUpdate('ccam');
  },
  
  add: function(subject_id, chir_id, oOptions){
    var oDefaultOptions = { 
      onComplete: ActesCCAM.notifyChange.curry(subject_id, chir_id)
    }
    Object.extend(oDefaultOptions, oOptions);
    var oForm = getForm("manageCodes");
    var oCcamField = new TokenField(oForm.codes_ccam, {
      sProps : "notNull code ccam"
    } );
    
    if(oCcamField.add(oForm._codes_ccam.value, true)){
      $V(getForm("manageCodes")._codes_ccam, "");
      submitFormAjax(oForm, 'systemMsg', oDefaultOptions);
    }
  },
  
  remove: function(subject_id, oOptions){
    var oDefaultOptions = { 
      onComplete: ActesCCAM.notifyChange.curry(subject_id)
    }
		
    Object.extend(oDefaultOptions, oOptions);
    var oForm = getForm("manageCodes");
    var aListActes = null;
    var oActeForm = null;
    if(oForm._actes && oForm._actes.value != "") {
      aListActes = oForm._actes.value.split("|").without("");
      if(confirm('Des actes ont �t� valid�s pour ce code\n�tes-vous sur de vouloir le supprimer ?')) {
        aListActes.each(function(elem) {
          oActeForm = getForm('formActe-'+elem);
          oActeForm.del.value = 1;
          submitFormAjax(oActeForm, 'systemMsg');
        });
      } else {
        return;
      }
    }
    var oCcamField = new TokenField(oForm.codes_ccam);
    if(oForm._selCode.value == 0){
      alert("Aucun code selectionn�");
      return false;
    }
    if(oCcamField.remove(oForm._selCode.value)){
      submitFormAjax(oForm, 'systemMsg', oDefaultOptions);
    }
  },
}

function setCodeTemp(code){
  var oForm = getForm("manageCodes");
  oForm._codes_ccam.value = code;
  oForm.addCode.onclick();
}

function setAssociation(association, oForm, subject_id, chir_id, oOptions) {
  var oDefaultOptions = { 
    onComplete: ActesCCAM.notifyChange.curry(subject_id, chir_id)
  }
  Object.extend(oDefaultOptions, oOptions);
  oForm.code_association.value = association;
  submitFormAjax(oForm, 'systemMsg', oDefaultOptions)
}

</script>