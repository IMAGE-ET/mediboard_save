// $Id: $

var ExamDialog = {
  sForm            : null,
  sConsultId       : null,
  options : {
    width : 900,
    height: 600
  },

  // Ouverture de la popup en fonction du type d'examen
  pop: function(type_exam) {
    var oForm = document[this.sForm];     
    var url = new Url();
    url.setModuleAction("dPcabinet", type_exam);
    url.addParam("consultation_id", oForm[this.sConsultId].value);
    url.popup(this.options.width, this.options.height, type_exam);
  }, 
  reload: function(consultation_id){
    var url = new Url();
    url.setModuleAction("dPcabinet", "httpreq_vw_examens_comp");
    url.addParam("consultation_id", consultation_id);
    url.requestUpdate("examDialog-"+consultation_id, { waitingText: null } );
  },
  register: function(object_id){
    document.write('<div id=examDialog-'+object_id+'></div>');
    Main.add( function() {
      ExamDialog.reload(object_id);
    } );
  },
  remove: function(oButton, object_id){
    oOptions = {
      typeName: 'l\'examen',
      objName: oButton.form._view.value,
      ajax: 1,
      target: 'systemMsg'
    }
    oAjaxOptions = {
      onComplete: function() { ExamDialog.reload(object_id) } 
    }
    confirmDeletion(oButton.form, oOptions, oAjaxOptions);
  },
  
}