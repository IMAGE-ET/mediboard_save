<!-- $Id: $ -->

<script type="text/javascript">

var oCcamField = null;
function updateTokenCcam(){
  refreshListCCAM("expert");
  refreshListCCAM("easy");
  document.editOp._codeCCAM.value="";
  modifOp();
}



function refreshListCCAM(mode) {
  if(mode=="expert"){
    oCcamNode = document.getElementById("listCodesCcam");
  }
  if(mode=="easy"){
    oCcamNode = document.getElementById("listCodesCcamEasy");
  }
  var oForm = document.editOp;
  var aCcam = oForm.codes_ccam.value.split("|");
  // Si la chaine est vide, il cr�e un tableau � un �l�ment vide donc :
  aCcam.removeByValue("");
  
  var aCodeNodes = new Array();
  var iCode = 0;
  while (sCode = aCcam[iCode++]) {
    var sCodeNode = sCode;
    if(mode=="expert"){
      sCodeNode += "<button class='cancel notext' type='button' onclick='oCcamField.remove(\"" + sCode + "\")'>";
      sCodeNode += "<\/button>";
    }
    aCodeNodes.push(sCodeNode);
  }
  oCcamNode.innerHTML = aCodeNodes.join(" &mdash; ");
  periodicalTimeUpdater.currentlyExecuting = false;
}

function updateTime() {
  var oForm = document.editOp;
  if(oForm.chir_id.value) {
    var timeUrl = new Url;
    timeUrl.setModuleAction("dPplanningOp", "httpreq_get_op_time");
    timeUrl.addElement(oForm.chir_id, "chir_id");
    timeUrl.addElement(oForm.codes_ccam, "codes");
    timeUrl.requestUpdate('timeEst', { waitingText : null });
    
    var dureeUrl = new Url;
    timeUrl.setModuleAction("dPplanningOp", "httpreq_get_hospi_time");
    timeUrl.addElement(oForm.chir_id, "chir_id");
    timeUrl.addElement(oForm.codes_ccam, "codes");
    timeUrl.requestUpdate('dureeEst', { waitingText : null });
  }
}

function checkFormOperation() {
  var oForm = document.editOp;
  
  if (!checkForm(oForm)) {
    return false;
  }

  if (!checkDuree()) {
    return false;
  }
  
  if(!checkCCAM()) {
    return false;
  }
  
  if(!checkCompatOpAdm()) {
    return false;
  }

  return true;
}

function checkCCAM() {
  var oForm = document.editOp;
  var sCcam = oForm._codeCCAM.value;
  if(sCcam != "") {
    if(!oCcamField.add(sCcam,true)) {
      return false;
    }
  }
  oCcamField.remove("XXXXXX");
  var sCodesCcam = oForm.codes_ccam.value;
  var sLibelle = oForm.libelle.value;
  if(sCodesCcam == "" && sLibelle == "") {
    alert("Vous indiquez un acte ou remplir le libell�")
    oForm.libelle.focus();
    return false
  }
  return true;
}

function checkChir() {
  var oForm = document.editOp;
  var oField = null;
  
  if (oField = oForm.chir_id) {
    if (oField.value == 0) {
      alert("Chirurgien manquant");
      return false;
    }
  }
  return true;
}

function checkDuree() {
  var form = document.editOp;
  field1 = form._hour_op;
  field2 = form._min_op;
  if (field1 && field2) {
    if (field1.value == 0 && field2.value == 0) {
      alert("Temps op�ratoire invalide");
      field1.focus();
      return false;
    }
  }
  return true
}

function checkCompatOpAdm() {
  var oOpForm     = document.editOp;
  var oSejourForm = document.editSejour;
  // cas des urgences
  if(oOpForm.date.value && oSejourForm._date_entree_prevue.value) {
    if(oOpForm.date.value < oSejourForm._date_entree_prevue.value) {
      alert("Date d'admission superieure � la date d'op�ration");
      oSejourForm._date_entree_prevue.focus();
      return false;
    }
  }
  // cas normal
  if(oOpForm._date.value && oSejourForm._date_entree_prevue.value) {
    if(oOpForm._date.value < oSejourForm._date_entree_prevue.value) {
      alert("Date d'admission superieure � la date d'op�ration");
      oSejourForm._date_entree_prevue.focus();
      return false;
    }
  }
  return true;
}

function modifOp() {
  modifSejour();
}

var Value = {
  // Synchronize elements value between Easy and Expert forms
  synchronize: function(element) {
    var oOtherForm = element.form.name == "editOp" ?
      document.editOpEasy :
      document.editOp;
    Form.Element.setValue(oOtherForm[element.name], element.value);
  }
}

function synchroPrat() {
  var oOpForm = document.editOp;
  var oSejourForm = document.editSejour;
  if(!oSejourForm.praticien_id.value) {
    oSejourForm.praticien_id.value = oOpForm.chir_id.value;
    oSejourForm.praticien_id.onchange();
  }
  updateTime();
}

function updateEntreePrevue() {
  var oOpForm = document.editOp;
  var oSejourForm = document.editSejour;
    
  if(!oSejourForm._duree_prevue.value) {
    oSejourForm._duree_prevue.value = 0;
  }

  if(oOpForm.date.value) {
    oSejourForm._date_entree_prevue.value = oOpForm.date.value;
    oDiv = document.getElementById('editSejour__date_entree_prevue_da');
    oDiv.innerHTML = Date.fromDATE(oOpForm.date.value).toLocaleDate();  
  }
  
  updateSortiePrevue();
}



function cancelOperation() {
  var oForm = document.editOp;
  var oElement = oForm.annulee;
  
  if (oElement.value == "0") {
    if (confirm("Voulez-vous vraiment annuler l'op�ration ?")) {
      if (confirm("Souhaitez-vous annuler le S�jour correspondant ?\n\nATTENTION, cette action annulera toutes les op�rations de ce s�jour !")) {
        document.editSejour.annule.value = 1;
      }
      oElement.value = "1";
      submitForms();
      return;
    }
  }
      
  if (oElement.value == "1") {
    var txtalert = "";
    if(document.editSejour.annule.value == 1){
      txtalert = "\n\n ATTENTION ! Cette op�ration va r�tablir le s�jour choisi.";
    }      
    if (confirm("Voulez-vous vraiment r�tablir l'op�ration ?" + txtalert)) {
      oElement.value = "0";
      oForm.submit();
      return;
    }
  }
}
  
var periodicalTimeUpdater = null;
  
function incFormOperationMain() {
  periodicalTimeUpdater = new PeriodicalExecuter(updateTime, 1);

  refreshListCCAM("expert");
  refreshListCCAM("easy");
  
  if({{$modurgence && !$op->operation_id}}) {
    updateEntreePrevue();
  }
    
  oCcamField = new TokenField(document.editOp.codes_ccam, { 
    onChange : updateTokenCcam,
    sProps : "notNull code ccam"
  } );
}

</script>
