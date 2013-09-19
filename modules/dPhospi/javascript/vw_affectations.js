flipChambre = function(chambre_id) {
  Element.classNames("chambre-" + chambre_id).flip("chambrecollapse", "chambreexpand");
};

flipSejour = function(sejour_id) {
  Element.classNames("sejour_" + sejour_id).flip("sejourcollapse", "sejourexpand");
};

flipAffectationCouloir = function(affectation_id) {
  Element.classNames("affectation_" + affectation_id).flip("sejourcollapse", "sejourexpand");
};

var selected_hospitalisation = null;
var selected_hospi = false;
selectHospitalisation = function(sejour_id) {
  var element = $("hospitalisation" + selected_hospitalisation);
  if (element) {
    element.checked = false;
  }
  selected_hospitalisation = sejour_id;
  selected_hospi = true;
  submitAffectation();
};

var selected_lit = null;
selectLit = function(lit_id) {
  var element = $("lit" + selected_lit);
  if (element) {
    element.checked = false;
  }
  selected_lit = lit_id;
  submitAffectation();
};

var selected_affectation = null;
selectAffectation = function(affectation_id) {
  var element= $("affectation" + selected_affectation);

  if (element) {
    element.checked = false;
  }
  selected_affectation = affectation_id;
  submitAffectation();
};

submitAffectation = function() {
  if (selected_lit) {
    // S�jour
    if (selected_hospi) {
      var oForm = selected_hospitalisation ?
        getForm("addAffectationsejour_" + selected_hospitalisation) :
        getForm("addAffectationsejour") ;
      oForm.lit_id.value = selected_lit;

      return onSubmitFormAjax(oForm, reloadTableau);
    }
    // Affectation dans un couloir
    else if (selected_affectation) {
      var form = getForm("addAffectationaffectation_"+selected_affectation);
      $V(form.lit_id, selected_lit);
      return onSubmitFormAjax(form, reloadTableau);
    }
  }
  return false;
};

Droppables.addLit = function(lit_id) {
  Droppables.add("lit-" + lit_id, { 
    onDrop:function(element){
      DragDropSejour(element.id, lit_id);
    }, 
    hoverclass:'dropover'
  });
};

DragDropSejour = function(sejour_id, lit_id) {
  $(sejour_id).style.display="none";
  if(sejour_id == 'sejour_bloque') {
    sejour_id = "sejour";
  }
  var oForm = getForm("addAffectation" + sejour_id);
  oForm.lit_id.value = lit_id;
  return onSubmitFormAjax(oForm, {onComplete: reloadTableau});
};

submitAffectationSplit = function(oForm) {
  oForm._new_lit_id.value = selected_lit;
  if (!selected_lit) {
    alert("Veuillez s�lectionner un nouveau lit et revalider la date");
    return false;
  }
  
  if (oForm._date_split.value <= oForm.entree.value || 
      oForm._date_split.value >= oForm.sortie.value) {
    var msg = "La date de d�placement (" + oForm._date_split.value + ") doit �tre comprise entre";
    msg += "\n- la date d'entr�e: " + oForm.entree.value;
    msg += "\n- la date de sortie: " + oForm.sortie.value;
    alert(msg);
    return false;
  }
  return onSubmitFormAjax(oForm, {onComplete: reloadTableau});
};

Calendar.setupAffectation = function(affectation_id, options) {
  options = Object.extend({
    currAffect : {
      start : null,
      stop : null
    },
    outerAffect : {
      start : null,
      stop : null
    }
  }, options);

  var dates = {
    limit: {// Entr�e affectation
      start: options.outerAffect.start,
      stop: options.currAffect.stop
    }
  };

  var form;
  
  if (form = getForm("entreeAffectation" + affectation_id)) {
    Calendar.regField(form.entree, dates, {noView: true, icon: 'images/icons/calendar.gif'});
  }

  // Sortie affectation
  dates.limit = {
    start: options.currAffect.start,
    stop: options.outerAffect.stop
  };

  if (form = getForm("sortieAffectation" + affectation_id)) {
    Calendar.regField(form.sortie, dates, {noView: true, icon: 'images/icons/calendar.gif'});
  }
  
  // D�placement affectation
  dates.limit = {
    start: options.currAffect.start,
    stop: options.currAffect.stop
  };

  if (form = getForm("splitAffectation" + affectation_id)) {
    Calendar.regField(form._date_split, dates, {noView: true, icon: 'images/icons/move.gif'});
  }
};

popPlanning = function() {
  var url = new Url("dPhospi", "vw_affectations");
  url.popup(700, 550, "Planning");
};

showRapport = function(date) {
  var url = new Url("dPhospi", "vw_rapport");
  url.addParam("date", date);
  url.popup(800, 600, "Rapport");
};

showAlerte = function(sType_admission) {
  var url = new Url("dPhospi", "vw_etat_semaine");
  url.addParam("type_admission", sType_admission);
  url.popup(500, 250, "Alerte");
};

toggleService = function(trigger, mode) {
  var cookie = new CookieJar(),
      service_id = trigger.value,
      container_id = "service" + service_id;
      
  if (trigger.checked) {
    var url = new Url("dPhospi", "httpreq_vw_aff_service");
    url.addParam("service_id", service_id);
    url.addParam("mode", mode);
    url.requestUpdate(container_id);
  }
  
  $(container_id).setVisible(trigger.checked);
  cookie.setValue("fullService", container_id, trigger.checked);
};

ObjectTooltip.modes.timeHospi = {
  module: "dPplanningOp",
  action: "httpreq_get_hospi_time",
  sClass: "tooltip"
};

ObjectTooltip.createTimeHospi = function(element, chir_id, codes) {
  ObjectTooltip.createEx(element, null, 'timeHospi', {
    chir_id : chir_id,
    codes : codes,
    javascript : 0
  } );
};

printTableau = function() {
  var oForm = getForm("chgAff");
  var url = new Url;
  url.setModuleAction("dPhospi", "print_tableau");
  url.addParam("date", $V(oForm.date));
  url.addParam("mode", $V(oForm.mode));
  url.popup(850, 600, "printAffService");
};

reloadTableau = function() {
  $("hospitalisation").checked = false;
  if(selected_hospi && selected_hospitalisation) {
    $("hospitalisation" + selected_hospitalisation).checked = false;
  }
  if(selected_hospi && selected_lit && selected_hospitalisation) {
    $("sejour_"+selected_hospitalisation).remove();
    selected_hospitalisation = null;
    selected_hospi = false;
  }
  if (selected_affectation) {
    $("affectation_"+selected_affectation).up('tr').remove();
    selected_affectation = null;
  }
  selected_lit = null;
  
  var oForm  = getForm("chgAff");
  var form = getForm("chgFilter");
  url = new Url("dPhospi", "ajax_tableau_affectations_lits");
  url.addElement(oForm.date);
  url.addElement(oForm.mode);
  url.addElement(form.prestation_id);
  url.requestUpdate("tableauAffectations");
};
