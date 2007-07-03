<!-- $Id: $ -->

<script type="text/javascript">

var listCategoriePrat = {{$categorie_prat|@json}};

function modifPrat(){
  var oForm = document.editSejour;
  var sValue = document.editSejour.praticien_id.value;
  
  oForm.pathologie.value= sValue ? 
    listCategoriePrat[sValue] || "" : "";
}

function checkDureeHospi() {
  var form = document.editSejour;
  field1 = form.type;
  field2 = form._duree_prevue;
  if (field1 && field2) {
    if (field1.value=="comp" && (field2.value == 0 || field2.value == '')) {
      field2.value = prompt("Veuillez saisir une dur�e pr�vue d'hospitalisation d'au moins 1 jour", "1");
      field2.onchange();
      field2.focus();
      return false;
    }
    if (field1.value=="ambu" && field2.value != 0 && field2.value != '') {
      alert('Pour une admission de type Ambulatoire, la dur�e du s�jour doit �tre de 0 jour.');
      field2.focus();
      return false;
    }
  }
  return true;
}

function reinitDureeSejour(){
  var form = document.editSejour;
  field2 = form._duree_prevue;
  field2.value = '0';
}

function removePlageOp(bIgnoreGroup){
  var oFormOp = document.editOp;  
  var oFormSejour = document.editSejour;
  if(oFormOp){
    if((oFormOp._group_id.value != oFormSejour.group_id.value) || bIgnoreGroup){
      oFormOp._group_id.value = oFormSejour.group_id.value;
      oFormOp._datestr.value = "";
      oFormOp.plageop_id.value = "";
      if(oFormOp.plageop_id.onchange) {
        oFormOp.plageop_id.onchange();
      }
      oFormOp._date.value = "";
      oFormOp.date.value = "";
    }
  }
}

function cancelSejour() {
  var oForm = document.editSejour;
  var oElement = oForm.annule;
  
  if (oElement.value == "0") {
    if (confirm("Voulez-vous vraiment annuler le s�jour ?\nTous les placements dans les lits seront supprim�s.\n{{$msg_alert|smarty:nodefaults|escape:"javascript"}}")) {
      oElement.value = "1";
      oForm.submit();
      return;
    }
  }
      
  if (oElement.value == "1") {
    if (confirm("Voulez-vous vraiment r�tablir le s�jour ?")) {
      oElement.value = "0";
      oForm.submit();
      return;
    }
  }
}

function modifSejour() {
  var oForm = document.editSejour;
  if (oForm.saisi_SHS.value == '1') {
    oForm.modif_SHS.value = 1;
    oForm.saisi_SHS.value = '0';
  }
}

function updateSortiePrevue() {
  var oForm = document.editSejour;
    
  if(!oForm._duree_prevue.value) {
    oForm._duree_prevue.value = 0;
  }
  
  if(oForm._date_entree_prevue.value) {
    var dDate = makeDateFromDATE(oForm._date_entree_prevue.value);
    var iDelta = parseInt(oForm._duree_prevue.value, 10);
    if(iDelta) {
      dDate.setDate(dDate.getDate() + iDelta);
    }
    oForm._date_sortie_prevue.value = makeDATEFromDate(dDate);
    oForm._date_sortie_prevue.onchange();
    oDiv = document.getElementById('editSejour__date_sortie_prevue_da');
    oDiv.innerHTML = makeLocaleDateFromDate(dDate);
    updateHeureSortie();
  }
}

function updateDureePrevue() {
  var oForm = document.editSejour;
  
  if(oForm._date_entree_prevue.value) {
    var dEntreePrevue = makeDateFromDATE(oForm._date_entree_prevue.value);
    var dSortiePrevue = makeDateFromDATE(oForm._date_sortie_prevue.value);
    var iSecondsDelta = dSortiePrevue - dEntreePrevue;
    var iDaysDelta = iSecondsDelta / (24 * 60 * 60 * 1000);
    oForm._duree_prevue.value = Math.floor(iDaysDelta);
  }
}

function updateHeureSortie() {
  var oForm = document.editSejour

  duree_prevu  = oForm._duree_prevue; 
  heure_sortie = oForm._hour_sortie_prevue;
  min_sortie   = oForm._min_sortie_prevue;
  
  if(duree_prevu.value < 1){
    heure_sortie.value = "{{$heure_sortie_ambu}}";
  }else{
    heure_sortie.value = "{{$heure_sortie_autre}}";
  }
  min_sortie.value = "00";
}

function checkSejoursToReload() {
  if(bChangePat) {
    reloadListSejours();
    bChangePat = 0;
  }
}

function reloadListSejours() {
  var sejoursUrl = new Url;
  var oForm = document.editSejour;
  var iPatient_id = oForm.patient_id.value;
  sejoursUrl.setModuleAction("dPplanningOp", "httpreq_get_sejours");
  sejoursUrl.addParam("patient_id", iPatient_id);
  sejoursUrl.requestUpdate('selectSejours', { waitingText : null });
}

function reloadSejour(sejour_id) {
  var sejoursUrl = new Url;
  var oForm = document.editSejour;
  var iPatient_id = oForm.patient_id.value;
  var iSejour_id = oForm.sejour_id.value;
  sejoursUrl.setModuleAction("dPplanningOp", "httpreq_vw_sejour");
  sejoursUrl.addParam("sejour_id", iSejour_id);
  sejoursUrl.addParam("patient_id", iPatient_id);
  if(document.editOp) {
    sejoursUrl.addParam("mode_operation", 1);
  }
  sejoursUrl.requestUpdate('inc_form_sejour', { waitingText : null });
}

function incFormSejourMain() {
}

var bChangePat = 0;

</script>
