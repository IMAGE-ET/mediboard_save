/**
 * JS function PMSI
 *  
 * @category dPpmsi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

PMSI = {
  confirmCloture: 0,
  
  loadExportActes: function(object_id, object_class, confirmCloture, module) {
    var url = new Url("pmsi", "ajax_view_export_actes");
    url.addParam("object_id"   , object_id);
    url.addParam("object_class", object_class);
    url.addParam("module"      , module);
    if(confirmCloture == 1) {
      PMSI.confirmCloture = 1;
      url.addParam("confirmCloture", confirmCloture);
    }

    url.requestUpdate("export_" + object_class + "_" + object_id); 
  },
  
  exportActes: function(object_id, object_class, oOptions, module){
    if ((PMSI.confirmCloture == 1) && !confirm("L'envoi des actes cloturera d�finitivement le codage de cette intervention pour le chirurgien et l'anesth�siste." +
          "\nConfirmez-vous l'envoi en facturation ?")) {
      return;
    } 

    var oDefaultOptions = {
      onlySentFiles : false
    };
  
    Object.extend(oDefaultOptions, oOptions);
  
    var url = new Url("pmsi", "export_actes_pmsi");
    url.addParam("object_id", object_id);
    url.addParam("object_class", object_class);
    url.addParam("sent_files", oDefaultOptions.onlySentFiles ? 1 : 0);
    url.addParam("module"      , module);

    var oRequestOptions = {
      waitingText: oDefaultOptions.onlySentFiles ? 
        "Chargement des fichers envoy�s" : 
        "Export des actes..."
    };

    if (PMSI.confirmCloture == 1) {
      oRequestOptions.onComplete = function() {
        PMSI.reloadActes(object_id, module);
      }
    }

    url.requestUpdate("export_" + object_class + "_" + object_id, oRequestOptions); 
  },
  
  deverouilleDossier: function(object_id, object_class, module) {
    var url = new Url("pmsi", "export_actes_pmsi");
    url.addParam("object_id"     , object_id);
    url.addParam("object_class"  , object_class);
    url.addParam("unlock_dossier", 1);

    var oRequestOptions = {
      waitingText: "D�v�rouillage du dossier..."
    };
    if (PMSI.confirmCloture == 1) {
      oRequestOptions.onComplete = function() {
        PMSI.reloadActes(object_id, module);
      }
    }

    url.requestUpdate("export_" + object_class + "_" + object_id, oRequestOptions);     
  },

  reloadActes: function(operation_id, module) {
    var url = new Url("salleOp", "ajax_refresh_actes");
    url.addParam("operation_id", operation_id);
    url.addParam("module", module);
    url.requestUpdate("codage_actes");
  },

  checkActivites: function(object_id, object_class, oOptions, module) {
    var url = new Url("salleOp", "ajax_check_activites_cloture");
    url.addParam("object_class", object_class);
    url.addParam("object_id", object_id);
    url.addParam("suppressHeaders", 1);
    url.addParam("dialog", 1);
    url.requestJSON(function(completed_activite) {
      if (completed_activite.activite_1 == 0 && !confirm($T('CActeCCAM-_no_activite_1_cloture'))) {
        return;
      }
      if (completed_activite.activite_4 == 0 && !confirm($T('CActeCCAM-_no_activite_4_cloture'))) {
        return;
      }
      PMSI.exportActes(object_id, object_class, oOptions, module);
    });
  },

  reloadFacturationLine: function (sejour_id) {
    new Url("dPpmsi", "ajax_sortie_line")
      .addParam("sejour_id", sejour_id)
      .requestUpdate("CSejour-"+sejour_id);
  },

  // The new PMSI view part

  setSejour: function(sejour_id) {
    var oForm = getForm("dossier_pmsi_selector");
    $V(oForm.sejour_id, sejour_id);
    oForm.submit();
  },

  printFicheBloc: function(oper_id) {
    var url = new Url("salleOp", "print_feuille_bloc");
    url.addParam("operation_id", oper_id);
    url.popup(700, 600, 'FeuilleBloc');
  },

  printFicheAnesth: function(dossier_anesth_id, operation_id) {
    var url = new Url("cabinet", "print_fiche");
    url.addParam("dossier_anesth_id", dossier_anesth_id);
    url.addParam("operation_id", operation_id);
    url.popup(700, 500, "printFicheAnesth");
  },

  loadPatient: function(patient_id, sejour_id) {
    var url = new Url("pmsi", "ajax_view_patient_pmsi");
    url.addParam("patient_id", patient_id);
    url.addParam("sejour_id" , sejour_id);
    url.requestUpdate("tab-patient");
  },

  loadDiagnostics: function(sejour_id) {
    alert('Fonction pour recharger les diagnostics pour le s�jour ' + sejour_id);
  },

  loadActes: function(sejour_id) {
    var url = new Url("pmsi", "ajax_view_actes_pmsi");
    url.addParam("sejour_id" , sejour_id);
    url.requestUpdate("tab-actes");
  },

  loadDocuments: function(sejour_id) {
    var url = new Url("hospi", "httpreq_documents_sejour");
    url.addParam("sejour_id" , sejour_id);
    url.requestUpdate("tab-documents");
  },

  loadDMI: function(sejour_id) {
    var url = new Url("dmi", "ajax_list_dmis");
    url.addParam("sejour_id" , sejour_id);
    url.requestUpdate("tab-dmi");
  },

  loadSearch: function(sejour_id) {
    var url = new Url("search", "vw_search_pmsi");
    url.addParam("sejour_id" , sejour_id);
    url.requestUpdate("tab-search");
  },

  loadRSS: function(sejour_id) {
    if($('tab-rss')) {
      var url = new Url("atih", "vw_rss");
      url.addParam("sejour_id", sejour_id);
      url.requestUpdate("tab-rss");
    }

  },

  loadGroupage: function(sejour_id) {
    if($('tab-groupage')) {
      var url = new Url("atih", "vw_groupage");
      url.addParam("sejour_id", sejour_id);
      url.requestUpdate("tab-groupage");
    }
  },

  loadDiagsPMSI: function(sejour_id) {
    var url = new Url("pmsi", "ajax_diags_pmsi");
    url.addParam("sejour_id", sejour_id);
    url.requestUpdate("diags_pmsi");
  },

  loadDiagsDossier: function(sejour_id) {
    var url = new Url("pmsi", "ajax_diags_dossier");
    url.addParam("sejour_id", sejour_id);
    url.requestUpdate("diags_dossier");
  },

  afterEditDiag: function(sejour_id) {
    PMSI.loadDiagsPMSI(sejour_id);
    PMSI.loadDiagsDossier(sejour_id);
    PMSI.loadRSS(sejour_id);
    PMSI.loadGroupage(sejour_id);
  },
  reloadActesCCAM: function(subject_guid, read_only) {
    var url = new Url("pmsi", "ajax_actes_ccam");
    url.addParam("subject_guid", subject_guid);
    url.addNotNullParam("read_only", read_only);
    url.requestUpdate("codes_ccam_"+subject_guid);
  },

  printDossierComplet: function(sejour_id) {
    var url = new Url("soins", "print_dossier_soins");
    url.addParam("sejour_id", sejour_id);
    url.popup(850, 600, "Dossier complet");
  },

  choosePreselection: function (oSelect) {
    if (!oSelect.value) {
      return;
    }
    var aParts = oSelect.value.split("|");
    var sLibelle = aParts.pop();
    var sCode = aParts.pop();
    var oForm = oSelect.form;
    $V(oForm.code_uf, sCode);
    $V(oForm.libelle_uf, sLibelle);

    oSelect.value = "";
  },

  listHospi: function (form , change_page) {
    if (form) {
      $V(form.page, change_page);
    }
    else {
      form = getForm("changeDate");
      $V(form.page, change_page);
    }
    var url = new Url("pmsi", "ajax_list_hospi");
    url.addFormData(form);
    url.requestUpdate("list-hospi");
    return false;
  },

  changePage : function (page) {
      PMSI.listHospi(null, page);
  }
};