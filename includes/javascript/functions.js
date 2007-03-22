// $Id$

function main() {
  prepareForms();
  initFCKEditor();
  BrowserDetect.init();
  ObjectInitialisation.hackIt();
  SystemMessage.init();
  pageMain();
}

window.onbeforeunload= function () {
  //if(BrowserDetect.browser != "Explorer"){
    waitingMessage(true);
  //}
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
 
SystemMessage = {
  id : "systemMsg",

  effect : function (idElement, oOldValue, oNewValue) {
    new Effect.Appear(this.id);
    new Effect.Fade(this.id, { delay : 3 } );
    return oNewValue;
  },
  
  init : function () {
    var oElement = $(this.id);
    if (!oElement) {
      return;
    }
    
    if (!oElement.watch) {
      Element.show(oElement);
      return;
    }
    
    oElement.watch("innerHTML", this.effect);
  }
}

function initFCKEditor() {}
function pageMain() {}

/**
 * Javascript console
 */
var Console = {
  id : "console",
 
  trace: function(sContent, sClass) {
    Element.show(this.id);    
    new Insertion.Bottom(this.id, "<div class='" + (sClass || "label") + "'>" + sContent + "</div>");
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
          this.trace(">> array", "value");
        } else {        
          this.trace(">> object", "value");
        }
        break;

      case "function":
        this.trace(">> function " + oValue.getSignature(), "value");
        break;

      case "string":
        this.trace("'" + oValue + "'", "value");
        break;

      default:
        this.trace(oValue, "value");
    }
  },

  debug: function(oValue, sLabel) {
    sLabel = sLabel || "Trace";
    
    if (oValue instanceof Array) {
      this.trace(sLabel + ": array");
      oValue.each(this.traceValue.bind(this))
    } else if (oValue instanceof Object) {
      this.trace(sLabel + ": object");
      for (oKey in oValue) {
        this.trace(oKey + ": ", "key");
        this.traceValue(oValue[oKey]);
      }
    } else {
      this.trace(sLabel + ": " + typeof oValue);
       this.traceValue(oValue);
    }
  },
  
  debugElement: function(oElement, sLabel) {
    oElement = $(oElement);
    this.trace((sLabel || "Trace") + ": " + typeof oElement);

    for (kAttribute in oElement.attributes) {
      oAttribute = oElement.attributes[kAttribute];
      if (oAttribute) {
        if (oAttribute.nodeName) {
          this.trace("Attributes." + oAttribute.nodeName + ": ", "key");
          this.traceValue(oAttribute.nodeValue);
        }
      }
    }
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
 * Element.ClassNames class
 */

Class.extend(Element.ClassNames, {
  
  load: function (sCookieName) {
    var oCookie = new CJL_CookieUtil(sCookieName);
    if (sValue = oCookie.getSubValue(this.element.id)) {
      this.set(sValue);
    }
  },
  
  save: function (sCookieName) {
    var oCookie = new CJL_CookieUtil(sCookieName);
    oCookie.setSubValue(this.element.id, this.toString());
  }
});

/**
 * PairEffect Class
 */

var PairEffect = Class.create();

// PairEffect Methods
Class.extend(PairEffect, {

  // Constructor
  initialize: function(idTarget, oOptions) {
    var oDefaultOptions = {
      idTrigger: idTarget + "-trigger",
      sEffect: null, // could be null, "appear", "slide", "blind"
      bStartVisible: false, // Make it visible at start
      bStoreInCookie: true,
      sCookieName: "effect"
  };

    Object.extend(oDefaultOptions, oOptions);
    
    this.oOptions = oDefaultOptions;
    this.oTarget = $(idTarget);
    this.oTrigger = $(this.oOptions.idTrigger);

    Assert.that(this.oTarget, "Target element '%s' is undefined", idTarget);
    Assert.that(this.oTrigger, "Trigger element '%s' is undefined ", this.oOptions.idTrigger);
  
    // Initialize the effect
    Event.observe(this.oTrigger, "click", this.flip.bind(this));
  
    // Initialize classnames and adapt visibility
    var aCNs = Element.classNames(this.oTrigger);
    aCNs.add(this.oOptions.bStartVisible ? "triggerHide" : "triggerShow");
    if (this.oOptions.bStoreInCookie) {
      aCNs.load(this.oOptions.sCookieName);
    }
    Element[aCNs.include("triggerShow") ? "hide" : "show"](this.oTarget);   
  },
  
  // Flipper callback
  flip: function() {
    if (this.oOptions.sEffect && BrowserDetect.browser != "Explorer") {
      new Effect.toggle(this.oTarget, this.oOptions.sEffect);
    } else {
      Element.toggle(this.oTarget);
    }
  
    var aCNs = Element.classNames(this.oTrigger);
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

  // Initialize a whole group giving the className for all targets
  initGroup: function(sTargetsClass, oOptions) {
    var oDefaultOptions = {
      idStartVisible   : null, // Forces one element to start visible
      bStartAllVisible : false,
      sCookieName      : sTargetsClass
    }
    
    Object.extend(oDefaultOptions, oOptions);
    
    document.getElementsByClassName(sTargetsClass).each( 
      function(oElement) {
        oDefaultOptions.bStartVisible = oDefaultOptions.bStartAllVisible || (oElement.id == oDefaultOptions.idStartVisible);
        new PairEffect(oElement.id, oDefaultOptions);
      }
    );
  }
});

/**
 * ObjectTooltip Class
 *   Handle object tooltip creation, associated with a MbObject and a target HTML element
 */

var ObjectTooltip = Class.create();

Class.extend(ObjectTooltip, {

  // Constructor
  initialize: function(eTrigger, sClass, iObject, oOptions) {
    this.eTrigger = $(eTrigger);
    this.sClass = sClass;
    this.iObject = iObject;
    this.eDiv = null;
    this.eTarget = null;
    this.idTimeOut = null;

    this.oOptions = {
      mode: "view",
      duration: 200
    };
    
    Object.extend(this.oOptions, oOptions);
    this.mode = ObjectTooltip.modes[this.oOptions.mode];

    this.createDiv();
    this.addHandlers();
  },
  
	launchShow: function() {
  	if (!this.idTimeOut) {
      this.idTimeout = setTimeout(this.show.bind(this), this.oOptions.duration);
  	}
	},
	
  show: function() {
    if (!this.eTarget.innerHTML) {
      this.load();
  	}

    this.eDiv.show();
  },
  
  hide: function() {
  	clearTimeout(this.idTimeout);
    this.eDiv.hide();
  },
  
  load: function() {
    url = new Url;
    url.setModuleAction(this.mode.module, this.mode.action);
    url.addParam("object_class", this.sClass);
    url.addParam("object_id", this.iObject);
    url.requestUpdate(this.eTarget);
  },
  
  addHandlers: function() {
    if(this.oOptions.mode == "view") {
      Event.observe(this.eTrigger, "mouseout", this.hide.bind(this));
    }
    if(this.oOptions.mode == "notes") {
      Event.observe(this.eDiv, "click", this.hide.bind(this));
    }
  },
  
  createDiv: function() {
    this.eDiv  = Dom.cloneElemById("tooltipTpl",true);
    Element.classNames(this.eDiv).add(this.mode.sClass);
    Element.hide(this.eDiv);
    this.eTrigger.parentNode.insertBefore(this.eDiv, this.eTrigger.nextSibling);
    this.eTarget = document.getElementsByClassName("content", this.eDiv)[0];
  }  
   
  
} );

/**
 * ObjectTooltip utility fonctions
 *   Helpers for ObjectTooltip instanciations
 */

Object.extend(ObjectTooltip, {
  modes: {
    view: {
      module: "system",
      action: "httpreq_vw_object",
      sClass: "tooltip"
    },
    notes: {
      module: "system",
      action: "httpreq_vw_object_notes",
      sClass: "postit"
    }
  },
  create: function(eTrigger, sClass, iObject, oOptions) {
    if (!eTrigger.oTooltip) {
      eTrigger.oTooltip = new ObjectTooltip(eTrigger, sClass, iObject, oOptions);
    }

    eTrigger.oTooltip.launchShow();    
  }
} );



/**
 * Date utility functions
 * @todo: extend Date class
 */

function makeDateFromDATE(sDate) {
  // sDate must be: YYYY-MM-DD
  var aParts = sDate.split("-");
  Assert.that(aParts.length == 3, "'%s' is not a valid Date format", sDate);

  var year  = parseInt(aParts[0], 10);
  var month = parseInt(aParts[1], 10);
  var day   = parseInt(aParts[2], 10);
  
  return new Date(year, month - 1, day); // Js months are 0-11!!
}

function makeDateFromDATETIME(sDateTime) {
  // sDateTime must be: YYYY-MM-DD HH:MM:SS
  var aHalves = sDateTime.split(" ");
  Assert.that(aHalves.length == 2, "'%s' is not a valid DATETIME", sDateTime);

  var sDate = aHalves[0];
  var date = makeDateFromDATE(sDate);

  var sTime = aHalves[1];
  var aParts = sTime.split(":");
  Assert.that(aParts.length == 3, "'%s' is not a valid TIME", sTime);

  date.setHours  (parseInt(aParts[0], 10));
  date.setMinutes(parseInt(aParts[1], 10));
  date.setSeconds(parseInt(aParts[2], 10));
  
  return date;
}

function makeDateFromLocaleDate(sDate) {
  // sDate must be: dd/mm/yyyy
  var aParts = sDate.split("/");
  Assert.that(aParts.length == 3, "'%s' is not a valid display date", sDate);

  var year  = parseInt(aParts[2], 10);
  var month = parseInt(aParts[1], 10);
  var day   = parseInt(aParts[0], 10);
  
  return new Date(year, month - 1, day); // Js months are 0-11!!
}

function makeDATEFromDate(date) {
  var y = date.getFullYear();
  var m = date.getMonth()+1; // Js months are 0-11!!
  var d = date.getDate();
  
  return printf("%04d-%02d-%02d", y, m, d);
}

function makeLocaleDateFromDate(date) {
  var y = date.getFullYear();
  var m = date.getMonth()+1; // Js months are 0-11!!
  var d = date.getDate();
  
  return printf("%02d/%02d/%04d", d, m, y);
}


function makeLocaleDateTimeFromDate(date) {
  var h = date.getHours();
  var m = date.getMinutes();
  
  return makeLocaleDateFromDate(date) + printf(" %02d:%02d", h, m);
}


function makeDATETIMEFromDate(date, useSpace) {
  var h = date.getHours();
  var m = date.getMinutes();
  var s = date.getSeconds();
  
  if(useSpace)
    return makeDATEFromDate(date) + printf(" %02d:%02d:%02d", h, m, s);
  else
    return makeDATEFromDate(date) + printf("+%02d:%02d:%02d", h, m, s);
}

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
      date        : makeDateFromDATE(sInitDate),
      showsTime   : bTime,
      onUpdate    : function(calendar) { 
        if (calendar.dateClicked) {
          sDate = bTime ? makeDATETIMEFromDate(calendar.date) : makeDATEFromDate(calendar.date)
          window.location = sRedirectBase + sDate;
        }
      }
    } 
  );
}

function regRedirectFlatCal(sInitDate, sRedirectBase, sContainerId, bTime) {
  if (sContainerId == null) sContainerId = "calendar-container";
  if (bTime == null) bTime = false;

  dInit = bTime ? makeDateFromDATETIME(sInitDate) : makeDateFromDATE(sInitDate);
  
  Calendar.setup( {
      date         : dInit,
      showsTime    : bTime,
      flat         : sContainerId,
      flatCallback : function(calendar) { 
        if (calendar.dateClicked) {
          sDate = bTime ? makeDATETIMEFromDate(calendar.date) : makeDATEFromDate(calendar.date)
          window.location = sRedirectBase + sDate;
        }
      }
    } 
  );
}


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
  aToken.removeByValue("");
  aToken.push(sValue);
  if(!multiple){
    aToken.removeDuplicates();
  }
  this.oElement.value = aToken.join("|");
  this.onComplete();
}

TokenField.prototype.remove = function(sValue) {
  if(this.oOptions.confirm && !confirm(this.oOptions.confirm)){
    return false;
  }
  var aToken = this.oElement.value.split("|");
  aToken.removeByValue("");
  aToken.removeByValue(sValue, true);
  this.oElement.value = aToken.join("|");
  this.onComplete();
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
  url.addParam("file_class", classe);
  url.addParam("file_object_id", id);
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
    this.url.popup(600, 500, "note");
  }
} )

// *******
var notWhitespace   = /\S/;
Dom = {
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
  }
}