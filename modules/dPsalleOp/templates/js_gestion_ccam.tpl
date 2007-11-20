{{mb_include_script module="dPplanningOp" script="ccam_selector"}}

<script type="text/javascript">



function setCodeTemp(code){
  document.manageCodes._newCode.value = code; 
}



function checkCode(oElement){
  if(oElement.value == 0){
    alert("Code manquant");
    return false;
  }
  // Test du code saisi
  var regexp = "^[A-Z]{4}[0-9]{3}$";
  var regexp2 ="^[A-Z]{4}[0-9]{3}-[0-9]-?[0-9]?$";
  if (!oElement.value.match(regexp) && !oElement.value.match(regexp2)) {
    alert("Le format du code CCAM saisi n'est pas valide");
    return false;
  }
  return true
}


function checkDelCode() {
  var oForm = document.manageCodes;
  var oField = null;
  
  if (oField = oForm._selCode) {
    if (oField.value == 0) {
      alert("Aucun code selectionn�");
      return false;
    }
  }
  return true;
}



function refreshFdr(consult_id) {
  // reload pour mettre a jour le champ codes_ccam dans la gestion des tarifs
  var url = new Url;
  url.setModuleAction("dPcabinet", "httpreq_vw_fdr_consult");
  url.addParam("selConsult", consult_id);
  url.requestUpdate('fdrConsultContent', { waitingText : null }) ;
}



function addCode(subject_id, chir_id) {    
  var oForm = document.manageCodes;
  
  if(checkCode(oForm._newCode)){ 
    var aCCAM = oForm.codes_ccam.value.split("|");
    // Si la chaine est vide, il cr�e un tableau � un �l�ment vide donc :
    aCCAM.removeByValue("");
    if(oForm._newCode.value != '')
      aCCAM.push(oForm._newCode.value);
    aCCAM.sort();
    oForm.codes_ccam.value = aCCAM.join("|");
  
    submitFormAjax(oForm, 'systemMsg', { onComplete: function() { loadActes(subject_id, chir_id) } } );
  }
}

function setAssociation(association, oForm, subject_id, chir_id) {
  oForm.code_association.value = association;
  submitFormAjax(oForm, 'systemMsg', {onComplete: function(){ loadActes(subject_id, chir_id)} })
}

function loadActes(subject_id, chir_id) {
  PairEffect.initGroup("acteEffect");

  //rafraichissement du inc_fdr
   if("{{$module}}" == "dPcabinet"){
    refreshFdr(subject_id);
  }
  
  url_actes = new Url;
  url_actes.addParam("chir_id", chir_id);  
  url_actes.addParam("module","{{$module}}");
  url_actes.addParam("do_subject_aed","{{$do_subject_aed}}");
  url_actes.addParam("object_class", "{{$object->_class_name}}");
  url_actes.addParam("object_id", subject_id);
  url_actes.setModuleAction("dPsalleOp", "httpreq_ccam");
  url_actes.requestUpdate('ccam', {
    waitingText: null
  });
}

function delCode(subject_id) {
  if(checkDelCode()){
    var oForm = document.manageCodes;
    var aCCAM = oForm.codes_ccam.value.split("|");
    // Si la chaine est vide, il cr�e un tableau � un �l�ment vide donc :
    aCCAM.removeByValue("");
    if (oForm._selCode.value != '') {
      aCCAM.removeByValue(oForm._selCode.value, true);
    }
    aCCAM.sort();
    oForm.codes_ccam.value = aCCAM.join("|");
  
    submitFormAjax(oForm, 'systemMsg', {onComplete: function() { loadActes(subject_id) } } );
  
  }
}

</script>