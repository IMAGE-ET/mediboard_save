Seance = {
  jsonSejours: {},
  checked: 0,
  selectPatient: function(form) {
    var url = new Url('ssr', 'ajax_patients_seance_collective');
    url.addFormData(form);
    url.requestModal('90%', '90%');
  },

  checkCountSejours: function() {
    var checked   = 0;
    var count     = 0;
    getForm('select_sejour_collectif').select('input[class=sejour_collectif]').each(function(e){
      count++;
      if ($V(e)) { checked ++; }
    });
    var element = $('select_sejour_collectif_check_all_sejours');
    element.checked = '';
    element.style.opacity = '1';
    if (checked > 0) {
      element.checked = 'checked';
      $V(element, 1);
      if (checked < count) {
        element.style.opacity = '0.5';
      }
    }
    Seance.checked = checked;
  },

  selectSejours: function(valeur){
    getForm('select_sejour_collectif').select('input[class=sejour_collectif]').each(function(e){
      $V(e, valeur);
    });
  },

  addSejour: function(){
    $V(getForm("editEvenementSSR")._sejours_guids, Object.toJSON(Seance.jsonSejours));
    var button_seance = $('seance_collective_add_patient');
    button_seance.className = "info";
    button_seance.innerHTML = Seance.checked+" patient(s) sélectionné(s)";
    Control.Modal.close();
  }
};