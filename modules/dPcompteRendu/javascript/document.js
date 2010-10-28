var Document = {
	/**
	 * @param ... A DECRIRE
	 */
  create: function(modele_id, object_id, target_id, target_class, switch_mode, form) {
    if (!modele_id) return;
    
    var url = new Url("dPcompteRendu", "edit_compte_rendu");
    url.addParam("modele_id", modele_id);
    url.addParam("object_id", object_id);
 
    if (target_id){
      url.addParam("target_id", target_id);
    }
    
    if(target_class){
      url.addParam("target_class", target_class);
    }
    
    if(switch_mode){
      url.addParam("switch_mode", switch_mode);
      /*form.select('.liste').each(function(select) {
        var v=   $V(select);
        if (!v || !v.length) return;
        var s = select.name.substring(0, select.name.length-2);
        url.addArrayParam(s, v);
      });*/
    }
    url.popup(950, 700, "Document");
  },
  
  createFromFast: function(modele_id, object_id, target_id, target_class, switch_mode) {
    
  },
  
  createPack: function(pack_id, object_id, target_id, target_class) {
    if (!pack_id) return;
    
    var url = new Url("dPcompteRendu", "edit_compte_rendu");
    url.addParam("pack_id", pack_id);
    url.addParam("object_id", object_id);
 
    if (target_id){
      url.addParam("target_id", target_id);
    }
    
    if(target_class){
      url.addParam("target_class", target_class);
    }
    
    url.popup(700, 700, "Document");
  },
  
  fastMode: function(object_class, modele_id, object_id, target_id, target_class, unique_id) {
    if (!modele_id) return;
    
    var fast = $('fast-'+unique_id);
    
    if (fast) {
			if (fast.select(".freetext").length || fast.innerHTML.indexOf("[Liste")) {
        var modaleWindow = modal(fast);
        modaleWindow.position();
      }
      var url = new Url("dPcompteRendu", "edit_compte_rendu");
      url.addParam("modele_id"   , modele_id);
      url.addParam("object_id"   , object_id);
      url.addParam('object_class', object_class);
			url.addParam('unique_id'   , unique_id);
      url.requestUpdate(fast, {onComplete: function(){
      }});
    }
  },
  
  edit: function(compte_rendu_id){
    var url = new Url("dPcompteRendu", "edit_compte_rendu");
    url.addParam("compte_rendu_id", compte_rendu_id);
    url.popup(900, 700, "Document");  
  },
  
  del: function(form, doc_view) {
  	var oConfirmOptions = {	
  		typeName: 'le document',
  		objName: doc_view,
  		ajax: 1,
  		target: 'systemMsg'
  	};
  	
  	var oAjaxOptions = {
  		onComplete: function () { 
  			Document.refreshList($V(form.object_class), $V(form.object_id)); 
  		}
  	};
  	
		confirmDeletion(form, oConfirmOptions, oAjaxOptions);
  },
  
  refreshList: function(object_class, object_id) {
    var selector = printf("div.documents-%s-%s", object_class, object_id);
  	$$(selector).each(Document.refresh);
  },
    
  /**
   * Mode normal|collapse Defaults to normal
   */
  register: function(object_id, object_class, praticien_id, container, mode, options) {   
    if (!object_id || !object_class) return;
    
  	options = Object.extend({
  	  mode: "normal",
  	  categories: "hide"
  	}, options);
  	
  	mode = mode || "normal";
    
  	var element = $(container);
  	
  	if (!element) {
      console.warn(container+" doesn't exist");
      return;
  	}
  	
    var div = new Element("div", {style: "min-width:260px;"+((mode != "hide") ? "min-height:50px;" : "")});
    div.className = printf("documents-%s-%s praticien-%s mode-%s", object_class, object_id, praticien_id, mode);
    $(element).insert(div);
    Document.refresh(div);
  },
  
  refresh: function(container, oOptions) {
    var matches = container.className.match(/documents-(\w+)-(\d+) praticien-(\d*) mode-(\w+)/);

    if (!matches) {
      console.warn(printf("'%s' is not a valid document container", container.className));
      return;
    }
    
    oOptions = Object.extend({
      object_class: matches[1],
      object_id   : matches[2],
      praticien_id: matches[3],
      mode        : matches[4]
    }, oOptions);
    
    var url = new Url("dPcompteRendu", "httpreq_widget_documents");
    url.addParam("object_class", oOptions.object_class);
    url.addParam("object_id"   , oOptions.object_id);
    url.addParam("praticien_id", oOptions.praticien_id);
    url.addParam("mode"        , oOptions.mode);
    url.requestUpdate(container);
  }
};

var DocumentPack = {
  create : function (pack_id, operation_id) {
	  var url = new Url("dPcompteRendu", "edit_compte_rendu");
	  url.addParam("pack_id", pack_id);
	  url.addParam("object_id", operation_id);
	  url.popup(700, 700, "Document");
	}
};
