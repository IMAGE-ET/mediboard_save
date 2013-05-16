window.Facture = {
  modal: null,
  reload: function(patient_id, object_class, not_load_banque, facture_id) {
    var url = new Url('facturation', 'ajax_view_facture');
    url.addParam('patient_id'    	, patient_id);
    url.addParam('object_class'    	, object_class);
    url.addParam('not_load_banque'	, not_load_banque);
    url.addParam('facture_id'		, facture_id);
    url.requestUpdate('load_facture');
  },
  modifCloture: function(form) {
    onSubmitFormAjax(form, {
      onComplete : function() {
        var url = new Url('facturation' , 'ajax_view_facture');
        url.addElement(form.facture_id);
        url.addParam('object_class'	, form.facture_class.value);
        url.requestUpdate('load_facture');
    }
    });
  },
  reloadReglement: function(facture_id, facture_class) {
    var url = new Url('facturation', 'ajax_refresh_reglement');
    url.addParam('facture_id'    , facture_id);
    url.addParam('facture_class' , facture_class);
    url.requestUpdate('reglements_facture');
  },
  cut: function(form) {
    onSubmitFormAjax(form, {
      onComplete : function() {
        var url = new Url('facturation', 'ajax_view_facture');
        url.addElement(form.facture_id);
        url.addParam('object_class'  , form.facture_class.value);
        url.requestUpdate("load_facture");
        Facture.modal.close();
      }
    });
  },
  edit: function(facture_id, facture_class) {
      var url = new Url('facturation', 'ajax_view_facture');
      url.addParam('facture_id'    , facture_id);
      url.addParam("object_class", facture_class);
      url.requestModal(1000);
  }
};