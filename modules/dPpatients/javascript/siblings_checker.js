/**
 * Check for siblings or too different text
 */
SiblingsChecker = {
  form: null,
  // Mutex
  running : false,
  // Submit
  submit: 0,

  // Send Ajax request
  request: function(oForm) {
    if (this.running) {
      return;
    }

    this.running = true;
    this.form = oForm;

    var url = new Url("patients", "ajax_get_siblings");
    url.addElement(oForm.patient_id);
    url.addElement(oForm.nom);
    url.addElement(oForm.nom_jeune_fille);
    url.addElement(oForm.prenom);
    url.addElement(oForm.prenom_2);
    url.addElement(oForm.prenom_3);
    url.addElement(oForm.prenom_4);
    url.addParam("submit", this.submit);
    if (oForm.naissance) {
      url.addParam("naissance", $(oForm.naissance).getFormatted("99/99/9999", "$3-$2-$1"));
    }

    if (this.submit) {
      url.addParam("json_result", "1");
      url.requestJSON((function (data) {
        if (data) {
          url.addParam("json_result", "0");
          url.requestModal(290, 200);
        }
        else {
          this.running = false;
          if (this.form.modal.value == 1) {
            onSubmitFormAjax(this.form, function() {window.parent.Control.Modal.close()});
          }
          else {
            this.form.submit();
          }
        }
      }).bind(this));
    }
    else {
      url.requestUpdate("doublon-patient", {
        waitingText: ""
      });
    }
  },

  confirmCreate : function() {
    if (this.form.modal.value == 1) {
      Control.Modal.close();
      onSubmitFormAjax(this.form, function() {window.parent.Control.Modal.close()});
    }
    else {
      this.form.submit();
    }
  }
};

