// $Id: $

var IconeSelector = {
  sForm   : null,
  sView   : null,
  options : {
    width : 400,
    height: 150
  },

  pop: function() {
    var url = new Url("dPcabinet", "icone_selector");
    url.popup(this.options.width, this.options.height, "Icone");
  },

  set: function(view) {
    var oForm = getForm(this.sForm);
    
    // Champs text qui contient le nom de l'icone
    $V(oForm[this.sView], view);
    
    // Affichage de l'icone
    $('iconeBackground').src = "./modules/dPcabinet/images/categories/"+view;
  }
};