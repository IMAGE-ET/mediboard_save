// $Id$

function main() {
	try {
	  prepareForms();
	  initFCKEditor();
	  BrowserDetect.init();
	  ObjectInitialisation.hackIt();
	  SystemMessage.init();
	  WaitingMessage.init();
	  initPuces();
	  pageMain();
	  Main.init();
	}
	catch (e) {
		Console.debug(e);
	}	
}

//Event.onDOMReady(main);

/**
 * Main page initialisation scripts
 */
var Main = {
  scripts: [],
  initialized: false,
  
  /**
   * Add a script to be lanuched after onload notification
   * On the fly execution if already page already loaded
   */
  add: function(script) {
    if (this.initialized) {
      script();
    }
    else {
      this.scripts.push(script);
    }
  },
  
  /**
   * Call all Main functions
   */
  init: function() {
    this.scripts.each(function(e) { e() } );
    this.initialized = true;
  }
};

/**
 * References manipulation
 */
var References = {   
	/**
	 * Clean references involved in memory leaks
	 */
  clean: function(obj) {
	  var chi, cou, len, nam;
	  chi = obj.childNodes;
	  if(chi) {
	    len = chi.length;
	    for (cou = 0; cou < len; cou++) {
				this.clean(obj.childNodes[cou]);
	    }
	  }
	  
	  chi = obj.attributes;
	  if(chi) {
	    len = chi.length;
	    for(cou = 0; cou < len; cou++) {
	      nam = chi[cou].name;
	      if(typeof(obj[nam]) == 'function') {
	        obj[nam] = null;
	      }
	    }
	  }
  }
}

var WaitingMessage = {
	init: function() {
		window.onbeforeunload = function () {
		  if(FormObserver.checkChanges()) {
  		  WaitingMessage.show();
  		} else {
  		  return "Vous avez modifi� certaines informations sur cette page sans les sauvegarder.Si vous appuyez sur OK, ces donn�es seront perdues.";
  		}
    };

// Preload seem not to be necessary and is broken anyway 
//  (generates File not found 403 HTTP responses)
//		// Autoload loading image cuz the browser won't try on before unload
//    var eDiv = $(document.createElement("div"));
//	  eDiv.className = "loading";
//    var sStyle = eDiv.getStyle("background-image");
//    var sImg = sStyle.match(/url\((.*)\)/);
//    (new Image).src = sImg;
	},
	
  show: function() {
    var eMask = $('waitingMsgMask');
    var eText = $('waitingMsgText');
    var eDoc = document.documentElement;
    if (!eMask && !eText) {
      return;
    }
  
    // Display waiting text
    eText.show();
    Element.setOpacity(eText, 0.8);
    var posTop  = eDoc.scrollTop  + (eDoc.clientHeight - eText.offsetHeight)/2;
    var posLeft = eDoc.scrollLeft + (eDoc.clientWidth  - eText.offsetWidth )/2;
    eText.style.top  = posTop + "px";
    eText.style.left = posLeft + "px";
    
    // Display waiting mask
    Element.setOpacity(eMask, 0.2);
    eMask.show();
    eMask.style.top  = "0px";
    eMask.style.left = "0px";
    eMask.style.height = eDoc.scrollHeight + "px";
    eMask.style.width = eDoc.scrollWidth + "px";
  },
  
  cover: function(element) {
    // Problems under Explorer : don't want to waste time
    // - ugly rendering with no transparancy
    // - triggers errors when used in popup callbacks
    if (BrowserDetect.browser == "Explorer") {
      return;
    }
    
    var eDiv = $(document.createElement("div"));
    eDiv.absolutize();
    eDiv.style.backgroundColor = "#000";
    eDiv.style.border = "none";
    eDiv.setOpacity(0.1);
    Position.clone(element, eDiv);
    
    var descendant = $(element).firstDescendant();
    
    /** If the element is a TR, we add the div to the firstChild to avoid a bad page render (a div in a <table> or a <tr>)*/
    if (descendant) {
      if(descendant.tagName.toString().toLowerCase() == "tr") {
        descendant.appendChild(eDiv);
        return;
      }
    }

    $(element).appendChild(eDiv);
  }
}

function createDocument(oSelect, consultation_id) {
  if (modele_id = oSelect.value) {
    var url = new Url;
    url.setModuleAction("dPcompteRendu", "edit_compte_rendu");
    url.addParam("modele_id", modele_id);
    url.addParam("object_id", consultation_id);
    url.popup(700, 700, "Document");
  }
  
  oSelect.value = "";
}

function closeWindowByEscape(e) {
  var keycode;
  if (window.event) keycode = window.event.keyCode;
  else if (e) keycode = e.which;

  if(keycode == 27){
    window.close();
  }
}

var djConfig = { isDebug: false };

var AjaxResponse = {
  onDisconnected: function() {
    loginUrl = new Url;
    loginUrl.addParam("dialog", 1);
    loginUrl.pop(610, 300, "login");
  },
  
  onPerformances: Prototype.emptyFunction
}


/**
 * System message effects
 */
var SystemMessage = {
  id: "systemMsg",
  autohide: null,
  effect: null,

  // Check message type (loading, notice, warning, error) from given div
  checkType: function() {
    this.autohide = $A($(this.id).childNodes).pluck("className").compact().last() == "message";
  },

  // show/hide the div
  doEffect : function (delay, forceFade) {
    var oDiv = $(this.id);
    
    // Cancel current effect
    if (this.effect) {
      this.effect.cancel();
      this.effect = null;
    }
      
    // Ensure visible        
    oDiv.show();
    oDiv.setOpacity(1);
    
    // Only hide on type 'message'
    this.checkType();
    if (!this.autohide && !forceFade) {
      return;
    }
    
    // Program fading
    this.effect = new Effect.Fade(this.id, { delay : delay || 5} );
  },
  
  init : function () {
    var oDiv = $(this.id);
    Assert.that(oDiv, "No system message div");
    
    // Hide on onclick
    Event.observe(oDiv, 'click', function(event) {
      SystemMessage.doEffect(0.1, true);
    } );
        
    // Hide empty message immediately
    if (!oDiv.innerHTML.strip()) {
      SystemMessage.doEffect(0.1, true);
      return;
    }
    
    SystemMessage.doEffect();
  }
}

function initFCKEditor() {}
function pageMain() {}

/**
 * Javascript console
 */
var Console = {
  id: "console",

  hide: function() {
    Element.hide($(this.id));
  },
  
  trace: function(sContent, sClass, nIndent) {
    sClass = sClass || "label";
    
    if(Preferences.INFOSYSTEM == 1) {
      Element.show(this.id);
    }
    var eDiv = document.createElement("div");
    eDiv.className = sClass;
    eDiv.innerHTML = sContent.toString().escapeHTML();

    if (nIndent) {
      Element.setStyle(eDiv, { marginLeft: nIndent + "em" } );
    }

    var eParent = $(this.id);
    eParent.appendChild(eDiv);
    eParent.scrollTop = eParent.scrollHeight;
  },
  
  traceValue: function(oValue) {
    if (oValue === null) {
      this.trace("null", "value");
      return;
    }
    
    switch (typeof oValue) {
      case "undefined": 
        this.trace("undefined", "value");
        break;
      
      case "object":
        if (oValue instanceof Array) {
          this.trace(">> [Array]", "value");
        } else {        
          this.trace(">> " + oValue, "value");
        }
        break;

      case "function":
        this.trace(">> Function" + oValue.getSignature(), "value");
        break;

      case "string":
        this.trace("'" + oValue + "'", "value");
        break;

      default:
        this.trace(oValue, "value");
    }
  },
  
  debug: function(oValue, sLabel, oOptions) {
    if (Preferences.INFOSYSTEM != "1") {
      return;
    }
  
    sLabel = sLabel || "Value";

    var oDefault = {
      level: 1,
      current: 0
    }
      
    Object.extend(oDefault, oOptions);
  
    if (oDefault.current > oDefault.level) {
      return;
    }
            
    try {
      this.trace(sLabel + ": ", "key", oDefault.current);
      
      if (oValue === null) {
        this.trace("null", "value");
        return;
      }
      
      switch (typeof oValue) {
        case "undefined": 
          this.trace("undefined", "value");
          break;
        
        case "object":
          oDefault.current++;
          if (oValue instanceof Array) {
            this.trace("[Array]", "value");
            oValue.each(function(value) { 
              Console.debug(value, "", oDefault);
            } );
          } else {
            this.trace(oValue, "value");
            $H(oValue).each(function(pair) {
              Console.debug(pair.value, pair.key, oDefault);
              
            } );
          }
          break;
  
        case "function":
          this.trace("[Function] : " + oValue.getSignature(), "value");
          break;
  
        case "string":
          this.trace("'" + oValue + "'", "value");
          break;
  
        default:
          this.trace(oValue, "value");
      }

    }
    catch(e) {
      this.trace("[couldn't get value]", "error");
    }
  },
  
  debugElement: function(oElement, sLabel, oOptions) {
    sLabel = sLabel || "Element";
    
    var oDefault = {
      level: 1,
      current: 0
    }
      
    Object.extend(oDefault, oOptions);
    
    oElement = $(oElement);
    
    var oNoRecursion = { 
      level: oDefault.current, 
      current: oDefault.current
    };
    
    this.debug(oElement, sLabel, oNoRecursion);

    oDefault.current++;

    if (oDefault.current > oDefault.level) {
      return;
    }
            
    oNoRecursion = { 
      level: oDefault.current, 
      current: oDefault.current
    };

    // Text nodes don't have tagName
    if (oElement.tagName) {
      this.debug(oElement.tagName.toLowerCase(), "tagName",  oNoRecursion);
    }
    
    if (oElement instanceof Text) {
      this.debug(oElement.textContent, "textContent", oNoRecursion);
    }
    
    $A(oElement.attributes).each( function(oAttribute) {
      Console.debug(oAttribute.nodeValue, "Attributes." + oAttribute.nodeName, oDefault);
    } );

    $A(oElement.childNodes).each( function(oElement) {
      Console.debugElement(oElement, "Element", oDefault)
    } );
  },
  
  error: function (sMsg) {
    this.trace("Error: " + sMsg, "error");
  },
  
  start: function() {
    this.dStart = new Date;
  },
  
  stop: function() {
    var dStop = new Date;
    this.debug(dStop - this.dStart, "Duration in milliseconds");
    this.dStart = null;
  }
  
}

/**
 * Assert utility object
 */ 
 
var Assert = {

  that: function (bPredicate, sMsg) {
    if (!bPredicate) {
      var aArgs = $A(arguments);
      aArgs.shift();
      sMsg = printf.apply(null, aArgs);
      Console.error(sMsg);
    }
  }
};

/**
 * PairEffect Class
 */

var PairEffect = Class.create();

// PairEffect Methods
Class.extend(PairEffect, {

  // Constructor
  initialize: function(idTarget, oOptions) {
  	
    var oDefaultOptions = {
      idTarget       : idTarget,
      idTrigger      : idTarget + "-trigger",
      sEffect        : null, // could be null, "appear", "slide", "blind"
      bStartVisible  : false, // Make it visible at start
      bStoreInCookie : true,
      sCookieName    : "effects"
    };

    Object.extend(oDefaultOptions, oOptions);
    
    this.oOptions = oDefaultOptions;
    var oTarget   = $(this.oOptions.idTarget);
    var oTrigger  = $(this.oOptions.idTrigger);

    Assert.that(oTarget, "Target element '%s' is undefined", idTarget);
    Assert.that(oTrigger, "Trigger element '%s' is undefined ", this.oOptions.idTrigger);
  
    // Initialize the effect
    Event.observe(oTrigger, "click", this.flip.bind(this));
  
    // Initialize classnames and adapt visibility
    var aCNs = Element.classNames(oTrigger);
    aCNs.add(this.oOptions.bStartVisible ? "triggerHide" : "triggerShow");
    if (this.oOptions.bStoreInCookie) {
      aCNs.load(this.oOptions.sCookieName);
    }
    Element[aCNs.include("triggerShow") ? "hide" : "show"](oTarget);   
  },
  
  // Flipper callback
  flip: function() {
    var oTarget = $(this.oOptions.idTarget);
    var oTrigger = $(this.oOptions.idTrigger);
    if (this.oOptions.sEffect && BrowserDetect.browser != "Explorer") {
      new Effect.toggle(oTarget, this.oOptions.sEffect);
    } else {
      Element.toggle(oTarget);
    }
  
    var aCNs = Element.classNames(oTrigger);
    aCNs.flip("triggerShow", "triggerHide");
    
    if (this.oOptions.bStoreInCookie) {
      aCNs.save(this.oOptions.sCookieName);
    }
  }
} );

/**
 * PairEffect utiliy function
 */

Object.extend(PairEffect, {
	declaredEffects : {},

  // Initialize a whole group giving the className for all targets
  initGroup: function(sTargetsClass, oOptions) {
    var oDefaultOptions = {
      idStartVisible   : null, // Forces one element to start visible
      bStartAllVisible : false,
      sCookieName      : sTargetsClass
    }
    
    Object.extend(oDefaultOptions, oOptions);
    
    $A(document.getElementsByClassName(sTargetsClass)).each( 
      function(oElement) {
        oDefaultOptions.bStartVisible = oDefaultOptions.bStartAllVisible || (oElement.id == oDefaultOptions.idStartVisible);
        new PairEffect(oElement.id, oDefaultOptions);
      }
    );
  }
});



/**
 * TogglePairEffect Class
 */

var TogglePairEffect = Class.create();

// TogglePairEffect Methods
Class.extend(TogglePairEffect, {

  // Constructor
  initialize: function(idTarget1, idTarget2, oOptions) {
  	
    var oDefaultOptions = {
    	idFirstVisible : 1,
      idTarget1      : idTarget1,
      idTarget2      : idTarget2,
      idTrigger1     : idTarget1 + "-trigger",
      idTrigger2     : idTarget2 + "-trigger"
    };

    Object.extend(oDefaultOptions, oOptions);
    
    this.oOptions = oDefaultOptions;
    var oTarget1  = $(this.oOptions.idTarget1);
    var oTarget2  = $(this.oOptions.idTarget2);
    var oTrigger1 = $(this.oOptions.idTrigger1);
    var oTrigger2 = $(this.oOptions.idTrigger2);

    Assert.that(oTarget1, "Target1 element '%s' is undefined", idTarget1);
    Assert.that(oTarget2, "Target2 element '%s' is undefined", idTarget2);
    Assert.that(oTrigger1, "Trigger1 element '%s' is undefined ", this.oOptions.idTrigger1);
    Assert.that(oTrigger2, "Trigger2 element '%s' is undefined ", this.oOptions.idTrigger2);
  
    // Initialize the effect
    var fShow = this.show.bind(this);
    Event.observe(oTrigger1, "click", function() { fShow(2); } );
    Event.observe(oTrigger2, "click", function() { fShow(1); } );
  	
  	this.show(this.oOptions.idFirstVisible);
  },
  
  show: function(iWhich) {
    var oTarget1  = $(this.oOptions.idTarget1);
    var oTarget2  = $(this.oOptions.idTarget2);
    var oTrigger1 = $(this.oOptions.idTrigger1);
    var oTrigger2 = $(this.oOptions.idTrigger2);
		oTarget1[1 == iWhich ? "show" : "hide"]();
		oTarget2[2 == iWhich ? "show" : "hide"]();
		oTrigger1[1 == iWhich ? "show" : "hide"]();
		oTrigger2[2 == iWhich ? "show" : "hide"]();
  }
  
} );

/**
 * PairEffect utiliy function
 */

Object.extend(TogglePairEffect, {
	declaredEffects : {},

  // Initialize a whole group giving the className for all targets
  initGroup: function(sTargetsClass, oOptions) {
    var oDefaultOptions = {
      idStartVisible   : null, // Forces one element to start visible
      bStartAllVisible : false,
      sCookieName      : sTargetsClass
    }
    
    Object.extend(oDefaultOptions, oOptions);
    
    $A(document.getElementsByClassName(sTargetsClass)).each( 
      function(oElement) {
        oDefaultOptions.bStartVisible = oDefaultOptions.bStartAllVisible || (oElement.id == oDefaultOptions.idStartVisible);
        new PairEffect(oElement.id, oDefaultOptions);
      }
    );
  }
});

/**
 * View port manipulation object
 *   Handle view ported objects
 */

var ViewPort = {
  SetAvlHeight: function (sDivId, iPct) {
    var oDiv = $(sDivId);
    if (!oDiv) {
      return;
    }
    var fYDivPos   = 0;
    var fNavHeight = 0;
    var fDivHeight = 0;
  
    // Position Top de la div, hauteur de la fenetre,
    // puis calcul de la taille de la div
    fYDivPos   = Position.cumulativeOffset(oDiv)[1];
    fNavHeight = window.getInnerDimensions().y;
    fDivHeight = fNavHeight - fYDivPos;
    oDiv.style.overflow = "auto";
    oDiv.style.height = (fDivHeight * iPct - 10) +"px";
  },
  
  SetFrameHeight: function(oFrame, oOptions){
	  var oDefaultOptions = {
	    iBottomMargin : 15
	  }
	    
	  var fYFramePos        = 0;
	  var fNavHeight        = 0;
	  var fFrameHeight      = 0;
	  var fFrameHeightFinal = 0;
	  
	  // Calcul de la position top de la frame
	  fYFramePos = Position.cumulativeOffset(oFrame)[1];  
	  
	  // hauteur de la fenetre
	  fNavHeight = window.getInnerDimensions().y;
	  
	  // Calcul de la hauteur de la div
	  fFrameHeight = fNavHeight - fYFramePos;
	
	  // Ajustement de la hauteur
	  fFrameHeightFinal = fFrameHeight - oDefaultOptions.iBottomMargin
	  
	  oFrame.setAttribute("height", fFrameHeightFinal);
  }
}


/**
 * ObjectTooltip Class
 *   Handle object tooltip creation, associated with a MbObject and a target HTML element
 */

var ObjectTooltip = Class.create();

Class.extend(ObjectTooltip, {

  // Constructor
  initialize: function(eTrigger, oOptions) {
    eTrigger = $(eTrigger);
    eTrigger.identify();
    this.sTrigger = eTrigger.id;
    this.sDiv = null;
    this.sTarget = null;
    this.idTimeOut = null;

    this.oOptions = {
      mode: "objectView",
      popup: false,
      duration: 400,
      params: {}
    };
    
    Object.extend(this.oOptions, oOptions);
    this.mode = ObjectTooltip.modes[this.oOptions.mode];

    if (!this.oOptions.popup) {
      this.createDiv();
      this.addHandlers();
    }
  },
  
  launchShow: function() {
    if (!this.idTimeOut) {
      this.idTimeout = setTimeout(this.show.bind(this), this.oOptions.duration);
    }
  },
  
  show: function() {
    var eDiv    = $(this.sDiv);
    var eTarget = $(this.sTarget);
    if (this.oOptions.popup || !eTarget.innerHTML) {
      this.load();
    }
    if (!this.oOptions.popup) {
      eDiv.show();
    }
  },
  
  hide: function() {
    var eDiv = $(this.sDiv);
    clearTimeout(this.idTimeout);
    eDiv.hide();
  },
  
  stopShow: function() {
    clearTimeout(this.idTimeout);
  },
  
  load: function() {
    var eTarget = $(this.sTarget);
    var url = new Url;
    url.setModuleAction(this.mode.module, this.mode.action);
    $H(this.oOptions.params).each( function(pair) { url.addParam(pair.key,pair.value); } );
    if(!this.oOptions.popup) {
      url.requestUpdate(eTarget);
      return;
    }
    
    if(this.oOptions.popup) {
      url.popup(this.mode.width, this.mode.height, this.oOptions.mode);
      return;
    }
  },
  
  addHandlers: function() {
    var eDiv     = $(this.sDiv);
    var eTrigger = $(this.sTrigger);
    if(this.oOptions.mode == "objectView" || this.oOptions.mode == "translate" || this.oOptions.mode == "objectViewHistory") {
      Event.observe(eTrigger, "mouseout", this.hide.bind(this));
    }
    if(this.oOptions.mode == "objectNotes") {
      Event.observe(eTrigger, "mouseout", this.stopShow.bind(this));
      Event.observe(eDiv, "click", this.hide.bind(this));
    }
  },
  
  createDiv: function() {
    var eTrigger = $(this.sTrigger);  
    var eDiv  = Dom.cloneElemById("tooltipTpl",true);
    eDiv.identify();
    Element.classNames(eDiv).add(this.mode.sClass);
    Element.hide(eDiv);
    eDiv.removeAttribute("_extended");
    this.sDiv = eDiv.id;
    eTrigger.parentNode.insertBefore(eDiv, eTrigger.nextSibling);
    var eTarget = document.getElementsByClassName("content", eDiv)[0];
    eTarget.removeAttribute("_extended");
    eTarget.identify();
    this.sTarget = eTarget.id;
  }  
   
  
} );

/**
 * ObjectTooltip utility fonctions
 *   Helpers for ObjectTooltip instanciations
 */

Object.extend(ObjectTooltip, {
  modes: {
    objectCompleteView: {
      module: "system",
      action: "httpreq_vw_complete_object",
      sClass: "tooltip",
      width: 600,
      height: 500
    },
    objectViewHistory: {
      module: "system",
      action: "httpreq_vw_object_history",
      sClass: "tooltip",
      width: 300,
      height: 150
    },
    objectView: {
      module: "system",
      action: "httpreq_vw_object",
      sClass: "tooltip",
      width: 300,
      height: 250
    },
    objectNotes: {
      module: "system",
      action: "httpreq_vw_object_notes",
      sClass: "postit"
    },
    translate: {
      module: "system",
      action: "httpreq_vw_translation",
      sClass: "tooltip"
    }
  },
  create: function(eTrigger, oOptions) {
    if (!eTrigger.oTooltip) {
      eTrigger.oTooltip = new ObjectTooltip(eTrigger, oOptions);
    }

    eTrigger.oTooltip.launchShow();    
  }
} );


function initNotes(){
  $$("div.noteDiv").each(function(pair) {
    var sClassDiv = pair.className;
    var aClass    = sClassDiv.split(" ");
    var aInfos    = aClass[1].split("-");

    url = new Url;
    url.setModuleAction("system", "httpreq_get_notes_image");
    url.addParam("object_class" , aInfos[0]);
    url.addParam("object_id"    , aInfos[1]);
    url.requestUpdate(pair, { waitingText : null });
      
  });
}


function initSante400(){
  $$("div.idsante400").each(function(element) {
    var sIdDiv = element.id;
    var aInfos = sIdDiv.split("-");
  
    url = new Url;
    url.setModuleAction("system", "httpreq_vw_object_idsante400");
    url.addParam("object_class" , aInfos[0]);
    url.addParam("object_id"    , aInfos[1]);
    url.requestUpdate(element, { waitingText : null });
  });
}

function initPuces() {
  initNotes();
  initSante400();
}

function reloadNotes(){
  initNotes(); 
}


Object.extend(Calendar, {
	// This function is bound to date specification
	dateStatus: function(date) {
	  var sDate = date.toDATE();
	  var aStyles = [];
	
	  if (this.limit.start && this.limit.start > sDate) {
	    aStyles.push("disabled");
	  }
	
	  if (this.limit.stop && this.limit.stop < sDate) {
	    aStyles.push("disabled");
	  }
	
		if (this.current.start || this.current.stop) {
	    aStyles.push("current");
		}
		
	  if (this.current.start && this.current.start > sDate) {
	    aStyles = aStyles.without("current");
	  }
	
	  if (this.current.stop && this.current.stop < sDate) {
	    aStyles = aStyles.without("current");
	  }
	  
	  if (this.spots.include(sDate)) {
	    aStyles.push("spot");
	  }
	  
	  aStyles.uniq();
	  return aStyles.join(" ");
  },

	prepareDates: function(dates) {
	  dates.current.start = Calendar.prepareDate(dates.current.start);
	  dates.current.stop  = Calendar.prepareDate(dates.current.stop);
	  dates.limit.start = Calendar.prepareDate(dates.limit.start);
	  dates.limit.stop  = Calendar.prepareDate(dates.limit.stop);
	  dates.spots = dates.spots.map(Calendar.prepareDate);
	},
	
  prepareDate: function(datetime) {
    if (!datetime) {
      return null;
    }
    
    return Date.isDATETIME(datetime) ? Date.fromDATETIME(datetime).toDATE() : datetime;
  },
  
  regField: function(sFormName, sFieldName, bTime, userDates) {
		if (!Object.isArray(userDates.spots)) {
			userDates.spots = Object.values(userDates.spots);
		}

	  var dates = {
		  current: {
		    start: null,
		    stop: null
		  },
		  limit: {
		    start: null,
		    stop: null
		  },
		  spots: []
		};
				
		Object.extend(dates, userDates);

		Calendar.prepareDates(dates);
		

		// Test element existence
		var sInputId = sFormName + "_" + sFieldName;
	  if (!$(sInputId)) {
	    return;
	  }
	
	  var cal = Calendar.setup( {
	      inputField  : sInputId,
	      displayArea : sInputId + "_da",
	      ifFormat    : "%Y-%m-%d" + (bTime ? " %H:%M:%S" : ""),
	      daFormat    : "%d/%m/%Y" + (bTime ? " %H:%M" : ""),
	      button      : sInputId + "_trigger",
	      showsTime   : bTime,
	      dateStatusFunc: Calendar.dateStatus.bind(dates)
	    } 
	  );
	}
} );

function regFieldCalendar(sFormName, sFieldName, bTime) {
  
  if (bTime == null) bTime = false;
  
  var sInputId = sFormName + "_" + sFieldName;
  
  if (!document.getElementById(sInputId)) {
    return;
  }

  Calendar.setup( {
      inputField  : sInputId,
      displayArea : sInputId + "_da",
      ifFormat    : "%Y-%m-%d" + (bTime ? " %H:%M:%S" : ""),
      daFormat    : "%d/%m/%Y" + (bTime ? " %H:%M" : ""),
      button      : sInputId + "_trigger",
      showsTime   : bTime
    } 
  );
}

function regRedirectPopupCal(sInitDate, sRedirectBase, sContainerId, bTime) {
  if (sContainerId == null) sContainerId = "changeDate";
  if (bTime == null) bTime = false;
  
  Calendar.setup( {
      button      : sContainerId,
      date        : Date.fromDATE(sInitDate),
      showsTime   : bTime,
      onUpdate    : function(calendar) { 
        if (calendar.dateClicked) {
          sDate = bTime ? calendar.date.toDATETIME() : calendar.date.toDATE();
          window.location = sRedirectBase + sDate;
        }
      }
    } 
  );
}

function regRedirectFlatCal(sInitDate, sRedirectBase, sContainerId, bTime) {
  if (sContainerId == null) sContainerId = "calendar-container";
  if (bTime == null) bTime = false;

  dInit = bTime ? Date.fromDATETIME(sInitDate) : Date.fromDATE(sInitDate);
  
  Calendar.setup( {
      date         : dInit,
      showsTime    : bTime,
      flat         : sContainerId,
      flatCallback : function(calendar) { 
        if (calendar.dateClicked) {
          sDate = bTime ? calendar.date.toDATETIME() : calendar.date.toDATE();
          window.location = sRedirectBase + sDate;
        }
      }
    } 
  );
}

/**
 * Durations expressed in milliseconds
 */
var Duration = {
  // Exact durations
  second: 1000,
  minute: 60 * 1000,
  hour: 60 * 60 * 1000,
  day: 24 * 60 * 60 * 1000,
  week: 7 * 24 * 60 * 60 * 1000,
  
  // Approximative durations
  month: 30 * 24 * 60 * 60 * 1000,
  year: 365 * 24 * 60 * 60 * 1000
}

Object.extend(Date, { 
	isDATETIME: function(sDateTime) {
	  return sDateTime.match(/\d\d\d\d-\d\d-\d\d \d\d:\d\d:\d\d/);
	},
	
  fromDATE: function(sDate) {
	  // sDate must be: YYYY-MM-DD
	  var aParts = sDate.split("-");
	  Assert.that(aParts.length == 3, "'%s' is not a valid DATE", sDate);
	
	  var year  = parseInt(aParts[0], 10);
	  var month = parseInt(aParts[1], 10);
	  var day   = parseInt(aParts[2], 10);
	  
	  return new Date(year, month - 1, day); // Js months are 0-11!!
	},

  fromDATETIME : function(sDateTime) {
	  // sDateTime must be: YYYY-MM-DD HH:MM:SS
	  var aHalves = sDateTime.split(" ");
	  Assert.that(aHalves.length == 2, "'%s' is not a valid DATETIME", sDateTime);
	
	  var sDate = aHalves[0];
	  var date = Date.fromDATE(sDate);
	
	  var sTime = aHalves[1];
	  var aParts = sTime.split(":");
	  Assert.that(aParts.length == 3, "'%s' is not a valid TIME", sTime);
	
	  date.setHours  (parseInt(aParts[0], 10));
	  date.setMinutes(parseInt(aParts[1], 10));
	  date.setSeconds(parseInt(aParts[2], 10));
	  
	  return date;
	},

  fromLocaleDate : function(sDate) {
	  // sDate must be: dd/mm/yyyy
	  var aParts = sDate.split("/");
	  Assert.that(aParts.length == 3, "'%s' is not a valid display date", sDate);
	
	  var year  = parseInt(aParts[2], 10);
	  var month = parseInt(aParts[1], 10);
	  var day   = parseInt(aParts[0], 10);
	  
	  return new Date(year, month - 1, day); // Js months are 0-11!!
	},

  fromLocaleDateTime : null
  
  
} );

Class.extend(Date, {
  toDATE: function() {
	  var y = this.getFullYear();
	  var m = this.getMonth()+1; // Js months are 0-11!!
	  var d = this.getDate();
	  
	  return printf("%04d-%02d-%02d", y, m, d);
	},
  
  toDATETIME: function(useSpace) {
	  var h = this.getHours();
	  var m = this.getMinutes();
	  var s = this.getSeconds();
	  
	  if(useSpace)
	    return this.toDATE() + printf(" %02d:%02d:%02d", h, m, s);
	  else
	    return this.toDATE() + printf("+%02d:%02d:%02d", h, m, s);
	},
  
  toLocaleDate: function() {
	  var y = this.getFullYear();
	  var m = this.getMonth()+1; // Js months are 0-11!!
	  var d = this.getDate();
	  
	  return printf("%02d/%02d/%04d", d, m, y);
	},
  
  toLocaleDateTime: function () {
	  var h = this.getHours();
	  var m = this.getMinutes();
	  
	  return this.toLocaleDate() + printf(" %02d:%02d", h, m);
	},
  
  addDays: function(iDays) {
    this.setDate(this.getDate() + iDays);
  }
} );

function TokenField(oElement, oOptions){
  this.oElement = oElement;
  
  var oDefaultOptions = {
    onChange: function(){},
    confirm : null,
    sProps  : null
  };
  Object.extend(oDefaultOptions, oOptions);
  this.oOptions = oDefaultOptions;
}

TokenField.prototype.onComplete = function(){
  if (this.oOptions.onChange != null)
    this.oOptions.onChange();
  return true;
}

TokenField.prototype.add = function(sValue,multiple) {
  if(!sValue){
    return false;
  }
  if(this.oOptions.sProps){
    oCode = new Object();
    oCode.value = sValue;
    oCode.className = this.oOptions.sProps;
    ElementChecker.prepare(oCode);
    if(sAlert = ElementChecker.checkElement()) {
      alert(sAlert);
      return false;
    }
  }
  var aToken = this.oElement.value.split("|");
  aToken = aToken.without("");
  aToken.push(sValue);
  if(!multiple){
    aToken.uniq();
  }
  this.oElement.value = aToken.join("|");
  this.onComplete();
  return true;
}

TokenField.prototype.remove = function(sValue) {
  if(this.oOptions.confirm && !confirm(this.oOptions.confirm)){
    return false;
  }
  var aToken = this.oElement.value.split("|");
  aToken = aToken.without("");
  
  var iPos = aToken.indexOf(sValue);
  if (iPos != -1) {
    aToken.splice(iPos, 1);
  }
  
  this.oElement.value = aToken.join("|");
  this.onComplete();	
  return true;
}

function view_log(classe, id) {
  url = new Url();
  url.setModuleAction("system", "view_history");
  url.addParam("object_class", classe);
  url.addParam("object_id", id);
  url.addParam("user_id", "");
  url.addParam("type", "");
  url.popup(600, 500, "history");
}

function view_idsante400(classe, id) {
  url = new Url();
  url.setModuleAction("dPsante400", "view_identifiants");
  url.addParam("object_class", classe);
  url.addParam("object_id", id);
  url.popup(750, 400, "sante400");
}

function uploadFile(classe, id, categorie_id){
  url = new Url();
  url.setModuleAction("dPfiles", "upload_file");
  url.addParam("object_class", classe);
  url.addParam("object_id", id);
  url.addParam("file_category_id", categorie_id);
  url.popup(600, 200, "uploadfile");
}

var Note = Class.create();
Class.extend(Note,  {
  initialize: function() {
    this.url = new Url();
    this.url.setModuleAction("system", "edit_note");
  },
  create: function (classe, object_id) {
    this.url.addParam("object_class", classe);
    this.url.addParam("object_id", object_id);
    this.popup();
  },
  edit: function(note_id) {
    this.url.addParam("note_id", note_id);
    this.popup();
  },
  popup: function () {
    this.url.popup(600, 300, "note");
  }
} )

// *******
var notWhitespace   = /\S/;
Dom = {
  createMessage : function (sMsg, sClassName) {
    var eDiv = document.createElement("div");
    eDiv.className = sClassName;
    eDiv.innerHTML = sMsg;
    return eDiv;
  },
  
  writeElem : function(elem_replace_id,elemReplace){
    elem = $(elem_replace_id);
    while (elem.firstChild) {
      elem.removeChild(elem.firstChild);
    }
    if(elemReplace){
      elem.appendChild(elemReplace);
    }
  },
  
  cloneElemById : function(id,withChildNodes){
    var elem = $(id).cloneNode(withChildNodes);
    elem.removeAttribute("id");
    return elem;
  },
  
  createTd : function(sClassname, sColspan){
    var oTd = document.createElement("td");
    if(sClassname){
      oTd.className = sClassname;
    }
    if(sColspan){
      oTd.setAttribute("colspan" , sColspan); 
    }
    return oTd;
  },
  
  createTh : function(sClassname, sColspan){
    var oTh = document.createElement("th");
    if(sClassname){
      oTh.className = sClassname;
    }
    if(sColspan){
      oTh.setAttribute("colspan" , sColspan); 
    }
    return oTh;
  },
  
  createImg : function(sSrc){
    var oImg = document.createElement("img");
    oImg.setAttribute("src", sSrc);
    return oImg;
  },
  
  createInput : function(sType, sName, sValue){
    var oInput = document.createElement("input");
    oInput.setAttribute("type"  , sType);
    oInput.setAttribute("name"  , sName);
    oInput.setAttribute("value" , sValue);
    return oInput;
  },
  
  createSelect : function(sName){
    var oSelect = document.createElement("select");
    oSelect.setAttribute("name"  , sName);
    return oSelect;
  },
  
  createOptSelect : function(sValue, sName, selected, oInsertInto){
    var oOpt = document.createElement("option");
    oOpt.setAttribute("value" , sValue);
    if(selected && selected == true){
      oOpt.setAttribute("selected" , "selected");
    }
    oOpt.innerHTML = sName;
    if(!oInsertInto){
      return oOpt;
    }
    oInsertInto.appendChild(oOpt);
  },
  
  cleanWhitespace : function(node){
    if(node.hasChildNodes()){
      for(var i=0; i< node.childNodes.length; i++){
        var childNode = node.childNodes[i];
        if((childNode.nodeType == Node.TEXT_NODE) && (!notWhitespace.test(childNode.nodeValue))){
          node.removeChild(node.childNodes[i]);
          i--;
        }else if (childNode.nodeType == 1) {
          Dom.cleanWhitespace(childNode);
        } 
      }
    }
  },
  
  build: function(json, root) {
    
    for(key in json) {
    }
    
  }
}

/** Levenstein function **/

function levenshtein( str1, str2 ) {
    // http://kevin.vanzonneveld.net
    // +   original by: Carlos R. L. Rodrigues
    // *     example 1: levenshtein('Kevin van Zonneveld', 'Kevin van Sommeveld');
    // *     returns 1: 3
 
    var s, l = (s = str1.split("")).length, t = (str2 = str2.split("")).length, i, j, m, n;
    if(!(l || t)) return Math.max(l, t);
    for(var a = [], i = l + 1; i; a[--i] = [i]);
    for(i = t + 1; a[0][--i] = i;);
    for(i = -1, m = s.length; ++i < m;){
        for(j = -1, n = str2.length; ++j < n;){
            a[(i *= 1) + 1][(j *= 1) + 1] = Math.min(a[i][j + 1] + 1, a[i + 1][j] + 1, a[i][j] + (s[i] != str2[j]));
        }
    }
    return a[l][t];
}

function luhn (code) {
  var code_length = code.length;
  var sum = 0;
  var parity = code_length % 2;
  
  for (var i = code_length - 1; i >= 0; i--) {
    var digit = code.charAt(i);
    
    
    if (i % 2 == parity) {
      digit *= 2;
      
      if (digit > 9) {
        digit -= 9;
      }
    }
    
    sum += parseInt(digit);
  }
  
  return ((sum % 10) == 0);
}


/* Control tabs creation. It saves selected tab into a cookie name TabState */
Object.extend (Control.Tabs, {
  storeTab: function (tabName, tab) {
    new CookieJar().setValue("TabState", tabName, tab);
  },
  loadTab: function (tabName) {
    return new CookieJar().getValue("TabState", tabName);
  },
  create: function (name, storeInCookie) {
    var tab = new Control.Tabs(name);
    if (storeInCookie) {
      tab.options.afterChange = function (tab, tabName) {
        Control.Tabs.storeTab(name, tab.id);
      }
      tab.setActiveTab(Control.Tabs.loadTab(name));
    }
    return tab;
  }
} );


Class.extend (Control.Tabs, {
  changeTabAndFocus: function(iIntexTab, oField) {
    this.setActiveTab(iIntexTab);
    if (oField) {
      oField.focus();
    } else {
      var oForm = $$('form')[0];
      if (oForm) {
        oForm.focusFirstElement();
      }
    }
  }
} );
