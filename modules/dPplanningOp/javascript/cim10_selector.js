// $Id: $

var CIM10Selector = {
  sForm     : null,
  sView     : null,
  sChir     : null,
  sCode     : null,
  oUrl      : null,
  
	prepared : {
	  code : null
	},
	
  options : {
    width : 800,
    height: 500
  },

  pop: function() {
    var oForm = document[this.sForm];
    this.oUrl = new Url();
    this.oUrl.setModuleAction("dPplanningOp", "code_selector");
    
    this.oUrl.addParam("chir", oForm[this.sChir].value);
    this.oUrl.addParam("type", "cim10");
    
    this.oUrl.popup(this.options.width, this.options.height, "CIM10 Selector");
  },
  
  // Code finder
  find: function(){
    var oForm = document[this.sForm];
    this.oUrl = new Url();
    this.oUrl.setModuleAction("dPcim10", "code_finder");
    this.oUrl.addParam("code", oForm[this.sCode].value);
    this.oUrl.popup(this.options.width, this.options.height, "CIM")
  },
  
  set: function(code) {
    this.prepared.code = code;
    window.setTimeout(window.CIM10Selector.doSet, 1);
  },
  
  doSet: function(){
    var oForm = document[CIM10Selector.sForm];
    Form.Element.setValue(oForm[CIM10Selector.sView], CIM10Selector.prepared.code);
  }
}
