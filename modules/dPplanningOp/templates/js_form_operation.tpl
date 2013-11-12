<!-- $Id$ -->

<script type="text/javascript">

window.oCcamField = null;

function updateTokenCcam(){
  refreshListCCAM("expert");
  refreshListCCAM("easy");
  $V(document.editOp._codes_ccam, "");
  modifOp();
}

function refreshListCCAM(mode) {
  if (mode=="expert") {
    var oCcamNode = $("listCodesCcam");
  }
  if (mode=="easy") {
    var oCcamNode = $("listCodesCcamEasy");
  }
  var oForm = document.editOp;
  var aCcam = oForm.codes_ccam.value.split("|");
  // Si la chaine est vide, il cr�e un tableau � un �l�ment vide donc :
  aCcam = aCcam.without("");
  
  var aCodeNodes = new Array();
  var iCode = 0;
  while (sCode = aCcam[iCode++]) {
    var sCodeNode = mode == "expert" ? 
     printf("<button class='remove' type='button' onclick='oCcamField.remove(\"%s\")'>%s<\/button>", sCode, sCode) :
     sCode;
     
    aCodeNodes.push(sCodeNode);
  }
  oCcamNode.innerHTML = aCodeNodes.join(mode == "easy" ? " &mdash; " : "");
  periodicalTimeUpdater.currentlyExecuting = false;
}

function updateTime() {
  var oForm = document.editOp;
	
  if(oForm.chir_id.value) {
    var timeUrl = new Url("dPplanningOp", "httpreq_get_op_time");
    timeUrl.addElement(oForm.chir_id, "chir_id");
    timeUrl.addElement(oForm.codes_ccam, "codes");
    timeUrl.requestUpdate('timeEst');
    
    var dureeUrl = new Url("dPplanningOp", "httpreq_get_hospi_time");
    timeUrl.addElement(oForm.chir_id, "chir_id");
    timeUrl.addElement(oForm.codes_ccam, "codes");
    timeUrl.requestUpdate('dureeEst');
  }
}

function checkFormOperation() {
  var oForm = document.editOp;
  return checkForm(oForm) && checkDuree() && checkCCAM() && checkCompatOpAdm();
}

function checkCCAM() {
  var oForm = document.editOp;
  var sCcam = $V(oForm._codes_ccam);
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
  var field1 = form._time_op;

  if (field1 && field1.value == "00:00:00") {
    alert("Temps op�ratoire invalide");
    return false;
  }

  return true;
}

function checkCompatOpAdm() {
  var oOpForm     = document.editOp;
  var oSejourForm = document.editSejour;
  // cas des urgences
  if(oOpForm.date.value && oSejourForm._date_entree_prevue.value) {
    if(oOpForm.date.value < oSejourForm._date_entree_prevue.value) {
      alert("Date d'admission superieure � la date d'intervention");
      oSejourForm._date_entree_prevue.focus();
      return false;
    }
  }
  // cas normal
  if(oOpForm._date.value && oSejourForm._date_entree_prevue.value) {
    if(oOpForm._date.value < oSejourForm._date_entree_prevue.value) {
      alert("Date d'admission superieure � la date d'intervention");
      oSejourForm._date_entree_prevue.focus();
      return false;
    }
  }
  return true;
}

function modifOp() {
  modifSejour();
}

function synchroPrat() {
  var oOpForm = document.editOp;
  var oSejourForm = document.editSejour;
  if (oOpForm.chir_id.value == 0) {
    oOpForm.chir_id.value = '';
    oOpForm.chir_id_view.value = '';
  }
  if (!oSejourForm.sejour_id.value) {
    $V(oSejourForm.praticien_id, oOpForm.chir_id.value);
    $V(oSejourForm.praticien_id_view, oOpForm.chir_id_view.value);
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
    if(!oSejourForm._date_entree_prevue.value || !(oSejourForm._date_entree_prevue.value <= oOpForm.date.value && oSejourForm._date_sortie_prevue.value >= oOpForm.date.value)) {
      oSejourForm._date_entree_prevue.value = oOpForm.date.value;
      oView = getForm('editSejour')._date_entree_prevue_da;
      oView.value = Date.fromDATE(oOpForm.date.value).toLocaleDate();
    }
  }
  
  updateSortiePrevue();
}

CanBloc = {{$modules.dPbloc->_can|json}};  

function cancelOperation() {  
  var oForm = document.editOp;
  var oElement = oForm.annulee;
  
  if (oElement.value == "0") {
    // Tester sup�rieur � 0 semble obligatoire
    if (oForm._count_actes.value > 0) {
      var msg = "Attention, l'intervention a probablement d�j� eu lieu.\n\n";
  
      if (CanBloc.edit) {
        if (!confirm(msg + "Voulez-vous malgr� tout l'annuler ?")) {
          return;
         }
      }
      else {
        alert(msg + "Veuillez vous adresser au responsable de bloc pour annuler cette intervention");
        return;
      }
    }
    
    if (confirm("Voulez-vous vraiment annuler l'intervention ?")) {
      if (confirm("Souhaitez-vous annuler le S�jour correspondant ?\n\nATTENTION, cette action annulera toutes les interventions de ce s�jour !")) {
        if (checkCancelAlerts()) {
           document.editSejour.annule.value = 1;
        }
      }
      
      {{if $conf.dPplanningOp.COperation.cancel_only_for_resp_bloc && $modules.dPbloc->_can->edit && $op->_id && $op->_ref_sejour->entree_reelle && $op->rank}}
        // Si annulation d'une intervention valid�e que par le chef de bloc
        // alors, compl�tion des remarques pour ajouter R�cus�e
        var rques = $V(oForm.rques);
        if (rques) {
          $V(oForm.rques, "R�cus�e\n" + rques);
        }
        else {        
          $V(oForm.rques,  "R�cus�e");
        }
      {{/if}}
      oElement.value = "1";
      submitForms();
      return;
    }
  }
      
  if (oElement.value == "1") {
    var txtalert = "";
    if(document.editSejour.annule.value == 1){
      txtalert = "\n\n ATTENTION ! Cette intervention va r�tablir le s�jour choisi.";
    }      
    if (confirm("Voulez-vous vraiment r�tablir l'intervention ?" + txtalert)) {
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
  
  {{if $modurgence && !$op->operation_id && !$sejour->entree_reelle}}
    updateEntreePrevue();
  {{/if}}
  
  oCcamField = new TokenField(document.editOp.codes_ccam, { 
    onChange : updateTokenCcam,
    sProps : "notNull code ccam"
  } );
}

function toggleOtherPrats(elt) {
  var form = getForm("editOp");
  var formEasy = getForm("editOpEasy");
  form.select('.other_prats').invoke('toggle');
  formEasy.select('.other_prats').invoke('toggle');
  Element.classNames(form.chir_id.next('button')).flip('up', 'down');
  Element.classNames(formEasy.chir_id.next('button')).flip('up', 'down');
}

Main.add(incFormOperationMain);

</script>
