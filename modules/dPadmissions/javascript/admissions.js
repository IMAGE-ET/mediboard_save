Admissions = {
  totalUpdater: null,
  listUpdater:  null,
  target_date: null,
  pre_admission_filter: null,

  filter: function(input, table) {
    table = $(table);
    table.select("tr").invoke("show");
    
    var term = $V(input);
    if (!term) return;
    
    table.select(".CPatient-view").each(function(e) {
      if (!e.innerHTML.like(term)) {
        e.up("tr").hide();
      }
    });
  },
  
  togglePrint: function(table_id, status) {
    var table = $(table_id);!
    table.select("input[name=print_doc]").each(function(elt) {
      elt.checked = status ? "checked" : "";
    });
  },
  
  printDHE: function(type, object_id) {
    var url = new Url("dPplanningOp", "view_planning");
    url.addParam(type, object_id);
    url.popup(700, 550, "DHE");
  },
  
  printForSelection: function(modele_id, table_id) {
    if (!modele_id) {
      alert("Veuillez choisir un mod�le avant de lancer l'impression");
      return false;
    }
    var table = $(table_id);
    var sejours_ids = table.select("input[name=print_doc]:checked").pluck("value");
    
    if (sejours_ids == "") {
      alert("Veuillez s�lectionner au minimum un patient pour l'impression");
      return false;
    }
    
    var oForm = getForm("chooseDoc");
    $V(oForm.sejours_ids, sejours_ids.join(","));
    oForm.submit();
    return true;
  },
  
  rememberSelection: function(table_id) {
    var table = $(table_id);
    window.sejours_ids = table.select("input[name=print_doc]:checked").pluck("value");
  },
  
  restoreSelection: function(table_id) {
    var table = $(table_id);
    
    table.select("input[name=print_doc]").each(function(elt) {
      if ($H(window.sejours_ids).index(elt.value)) {
        elt.checked = true;
      }
    });
  },
  printFichesAnesth: function(table_id) {
    var url = new Url("admissions", "print_fiches_anesth");
    var table = $(table_id);
    var sejours_ids = table.select("input[name=print_doc]:checked").pluck("value");
    
    if (sejours_ids == "") {
      alert("Veuillez s�lectionner au minimum un patient pour l'impression");
      return false;
    }
    
    url.addParam("sejours_ids", sejours_ids.join(","));
    url.addParam("pdf", 0);
    url.popup(700, 500);
  }
  ,
  beforePrint: function() {
    Admissions.totalUpdater.stop();
    Admissions.listUpdater.stop();
  },
  
  afterPrint: function() {
    Control.Modal.close();
    Admissions.totalUpdater.resume();
    Admissions.listUpdater.resume();
  },
  
  toggleMultipleServices: function(elt) {
    var status = elt.checked;
    var form = elt.form;
    var elt_service_id = form.service_id;
    elt_service_id.multiple = status;
    elt_service_id.size = status ? 5 : 1;
  },
  
  showLegend: function() {
    new Url("dPadmissions", "vw_legende").requestModal();
  },

  showDocs: function(sejour_id) {
    Admissions.totalUpdater.stop();
    Admissions.listUpdater.stop();
    var url = new Url("dPhospi", "httpreq_documents_sejour");
    url.addParam("sejour_id", sejour_id);
    url.addParam("only_sejour", 1);
    url.addParam("with_patient", 1);
    url.requestModal(700, 400);
    url.modalObject.observe("afterClose", function() {
      Admissions.totalUpdater.resume();
      Admissions.listUpdater.resume();
    });
  },


  updateSummaryPreAdmissions : function (sdate) {
    if (sdate) {this.target_date = sdate;}
    var admUrl = new Url("admissions", "httpreq_vw_all_preadmissions");
    admUrl.addParam("date", this.target_date);
    admUrl.requestUpdate('allPreAdmissions');
  },

  updatePeriodicalSummaryPreAdmissions : function() {
    setInterval(function(){
      Admissions.updateSummaryPreAdmissions();
    }, 120000);
  },

  updateListPreAdmissions : function (sdate) {
    var admUrl = new Url("admissions", "httpreq_vw_preadmissions");
    if (sdate) {
      this.target_date = sdate;
      admUrl.addParam("date", this.target_date);
    }
    admUrl.addParam("filter", this.pre_admission_filter);
    admUrl.requestUpdate('listPreAdmissions');

    //update du selecteur
    var lines = $("allPreAdmissions").select('table tbody tr.preAdmission-day').invoke("removeClassName", "selected");
    var target_td = $('paday_'+this.target_date);
    if (target_td) {
      target_td.addClassName("selected");
    }
  },

  updatePeriodicalPreAdmissions : function() {
    setInterval(function(){
      Admissions.updateListPreAdmissions();
    }, 120000);
  }
};
