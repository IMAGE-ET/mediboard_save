// $Id: $

/** TODO: Factoriser ceci pour ne pas avoir a etendre l'objet (sinon Patient.create est ecras�) */
Patient = Object.extend({
  view: function(patient_id) {
    new Url().
      setModuleTab("dPpatients", "vw_full_patients").
      addParam("patient_id", patient_id).
      redirectOpener();
  },
  
  history: function(patient_id) {
    new Url("dPpatients", "vw_history").
      addParam("patient_id", patient_id).
      popup(600, 500, "patient history");
  },
  
  print: function (patient_id) {
    new Url("dPpatients", "print_patient").
      addParam("patient_id", patient_id).
      popup(700, 550, "Patient");
  },
  
  edit: function(patient_id, use_vitale) {
    new Url().
      setModuleTab("dPpatients", "vw_edit_patients").
      addParam("patient_id", patient_id).
      addParam("use_vitale", use_vitale).
      redirectOpener();
  },
  
  exportVcard: function(patient_id) {
    new Url("dPpatients", "ajax_export_vcard").
      addParam("patient_id", patient_id).
      addParam("suppressHeaders", 1).
      pop(700, 550, "Patient");
  },
  
  doUnlink: function(patient_id) {
    var url = new Url();
    url.addParam("m", "dPpatients");
    url.addParam("dosql", "do_unlink");
    url.addParam("patient_id", patient_id);
    url.requestUpdate("systemMsg", { 
      method: 'post',
      onComplete : function() {
        if (window.reloadPatient) {
          reloadPatient(patient_id);
        }
      }
    });
  }
}, window.Patient);