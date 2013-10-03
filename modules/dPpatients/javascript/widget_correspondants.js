Correspondants = window.Correspondants || Class.create({
  initialize: function (patient_id, options) {
    this.module = "patients";
    this.action = "httpreq_widget_correspondants";
    
    this.patient_id = patient_id;
    this.options = Object.extend({
      container: null,
      popup: false
    }, options || {});
    
    this.widget_id = "correspondants-patient_id-"+this.patient_id;
    
    if ($(this.widget_id)) {
      Console.error("A widget with this id ("+this.widget_id+") already exists");
    }
    
    var widget = '<div id="'+this.widget_id+'"></div>';

    if (this.options.container) {
      $(this.options.container).insert(widget);
    }
    else {
      document.write(widget);
    }
    
    $(this.widget_id).widget = this;
    //if (!this.options.popup) {
      this.refresh();
    /*}
    else {
      var button = new Element('button', {className: 'new', type: 'button'}).update("Correspondants m�dicaux");
      var that = this;
      button.observe('click', function() {that.popup()});
      $(this.widget_id).update(button);
    }*/
  },
  
  /*popup: function() {
    var url = new Url(this.module, this.action);
    url.addParam("patient_id", this.patient_id);
    url.addParam("widget_id", this.widget_id);
    url.popup(600, 400, "Correspondants");  
  },*/
  
  refresh: function() {
    var url = new Url(this.module, this.action);
    url.addParam("patient_id", this.patient_id);
    url.addParam("widget_id", this.widget_id);
    url.requestUpdate(this.widget_id);
  }
});
