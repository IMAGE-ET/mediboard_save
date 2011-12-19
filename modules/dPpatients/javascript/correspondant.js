Correspondant = {
  edit: function(correspondant_id, patient_id) {
    var url = new Url('dPpatients', 'ajax_form_correspondant');
    url.addParam('correspondant_id', correspondant_id);
    url.addParam("patient_id", patient_id);
    url.requestModal(500);
  },

  onSubmit: function(form) {
    return onSubmitFormAjax(form, { 
      onComplete: function() {
        Correspondant.refreshList($V(form.patient_id));
        Control.Modal.close();
      }
    });
  },

  confirmDeletion: function(form) {
    var options = {
      typeName:'correspondant', 
      objName: $V(form.nom),
      ajax: 1
    };
    
    var ajax = {
      onComplete: function() {
        Correspondant.refreshList($V(form.patient_id));
        Control.Modal.close();
      }
    };
    
    confirmDeletion(form, options, ajax);
  },
  
  refreshList: function(patient_id) {
    var url = new Url('dPpatients', 'ajax_list_correspondants');
    url.addParam("patient_id", patient_id);
    url.requestUpdate('list-correspondants');
  }
};