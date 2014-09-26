/* $Id$ */

/**
 * @package Mediboard
 * @subpackage includes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * Global page load event
 */
document.observe('dom:loaded', function(){
  try {
    if (App.sessionLocked) {
      Session.lockScreen();
    }

    MbPerformance.timeStart("prepareForms");
      prepareForms();
    MbPerformance.timeEnd("prepareForms");

    MbPerformance.timeStart("initUI");
      SystemMessage.init();
      WaitingMessage.init();
      Note.refresh();
      Element.warnDuplicates();
      Event.initKeyboardEvents();
      ObjectTooltip.init();
      //App.initSessionLocker();
      $(document.documentElement).prepareTouchEvents();
    MbPerformance.timeEnd("initUI");

    MbPerformance.timeStart("main");
      Main.init();
    MbPerformance.timeEnd("main");

    var cookiejar = new CookieJar();
    
    // If the user is not connected
    if (!User.id) {
      cookiejar.put("uainfo", {
        screen: [screen.width, screen.height],
        pixelRatio: window.devicePixelRatio || null
      });
    }
    
    cookiejar.put("cookie-supported", 1);

    /*
    // If the user is not connected, we compute and write the score in the cookie
    if (!User.id) {
      UAInfo.getBenchScore(function(result){
        uainfo.performanceScore.result = result;
        cookiejar.put("uainfo", uainfo);
      });
    }*/
  }
  catch (e) {
    errorHandler(e.extMessage || e.message || e.description || e, e.fileName, e.lineNumber, e);
  }
});

/**
 * Extact advanced browser info
 *
 * @type {Object}
 */
var UAInfo = {
  string: "",

  osInfo: [
    {
      string: navigator.platform,
      subString: "Win",
      identity: "Windows"
    },
    {
      string: navigator.platform,
      subString: "Mac",
      identity: "Mac"
    },
    {
      string: navigator.userAgent,
      subString: "iPhone",
      identity: "iPhone/iPod"
    },
    {
      string: navigator.platform,
      subString: "Linux",
      identity: "Linux"
    }
  ],

  /**
   * Build the info string
   *
   * @return {String} The string with all the info
   */
  buildString: function(){
    if (UAInfo.string) return UAInfo.string;
    
    UAInfo.append("Navigateur", bowser.name+" "+bowser.version);
    
    if (Prototype.Browser.IE) {
      var ieVersion = IEVersion();
      UAInfo.append("Version", ieVersion.Version, 1);
      UAInfo.append("DocMode", ieVersion.DocMode, 1);
      UAInfo.append("Mode   ", ieVersion.BrowserMode, 1);
      UAInfo.string += "\n";
    }
    
    UAInfo.append("Cookies", ("cookieEnabled" in navigator ? (navigator.cookieEnabled ? "Oui" : "Non") : "Information non disponible"));

    UAInfo.append("Syst�me", UAInfo.getOS()+ " ("+navigator.platform+")");
    
    if (User.login) {
      UAInfo.append("Utilisateur", User.view+" ("+User.login+")");
      UAInfo.append("Fonction", User["function"].view);
      UAInfo.append("Etablissement", User.group.view);
    }
    else {
      UAInfo.append("Utilisateur", "Non connect�");
    }
    
    if (!navigator.plugins || navigator.plugins.length == 0) {
      UAInfo.append("Plugins", "Information non disponible");
    }
    else {
      UAInfo.string += "\n";
      UAInfo.append("Plugins", "");
      $A(navigator.plugins).each(function(plugin){
        if (plugin.name.match(/pdf|acrobat|java/i)) {
          UAInfo.append(plugin.name, plugin.version || plugin.description, 1);
        }
      });
    }
    
    //errorHandler(2, UAInfo.string.replace(new RegExp(String.fromCharCode(8226), "g"), "<br />"));
    
    return UAInfo.string;
  },

  /**
   * Get OS name: "Windows", "Linux", "Mac", "iPhone/iPod" or "Inconnu"
   *
   * @returns {string}
   */
  getOS: function() {
    var os = "Inconnu";

    for (var i = 0, l = UAInfo.osInfo.length; i < l; i++) {
      var detect = UAInfo.osInfo[i];
      if (detect.string.indexOf(detect.subString) !== false) {
        os = detect.identity;
        break;
      }
    }

    return os;
  },

  /**
   * Append a value to the string
   *
   * @param {String} label  The label to append
   * @param {String} value  The value
   * @param {String} indent Indentation size
   */
  append: function(label, value, indent) {
    var string = ((indent > 0) ? (new Array(indent*8).join(" ")) : "");
    UAInfo.string += string + String.fromCharCode(8226)+" "+label+" : \t"+value+"\n";
  },

  /**
   * Display the information window
   */
  show: function(){
    alert(UAInfo.buildString());
  },

  doBench: function() {
    var count = 2000000;

    var Foo = function(){};
    Foo.prototype.bar = function(){
      return "i";
    };

    var s = "";
    for (var i = 0; i < count; i++) {
      var a = new Foo();
      s += a.bar();
    }

    return s;
  },

  getBenchTime: function(){
    var now = performance.now();

    UAInfo.doBench();

    return (performance.now() - now);
  },

  getBenchScore: function(callback){
    var samples = 10;
    var results = [];

    for (var i = 0; i < samples; i++) {
      (function(){
        results.push(UAInfo.getBenchTime());

        if (results.length == samples) {
          callback(10000 / results.average());
        }
      }).delay(0.1);
    }
  }
};

document.observe("keydown", function(e){
  var key = Event.key(e);

  // Alt keys
  if (e.altKey) {
    switch (key) {
      case 80: // p
        Event.stop(e);
        MbPerformance.toggleProfiling();
        break;

    /**
     * Observe Alt+y to show general information about
     * the browser, its plugins and information about the user
     */
      case 89: // y
        Event.stop(e);
        UAInfo.show();
        break;
    }
  }
});

/**
 * Alerts the user about pending POST requests
 * when he tries to reload or quit the page
 */
window.onunload = function () {
  if (Url.activeRequests.post > 0) {
    alert($T("WaitingForAjaxRequestReturn"));
  }
};

/**
 * Handles the "loading" messages
 *
 * @type {Object}
 */
var WaitingMessage = {
  /**
   * Init function, which puts observers on observed forms to prevent
   * the user from quiting the page if he didn't submit forms
   */
  init: function() {
    window.onbeforeunload = function () {
      if(FormObserver.checkChanges()) {
        WaitingMessage.show();
      }
      else {
        if (FormObserver.onChanged) {
          FormObserver.onChanged();
        }
        return $T("FormObserver-msg-confirm");
      }
    };
  },

  /**
   * Shows the waiting message between pages
   */
  show: function() {
    var doc  = document.documentElement,
        mask = $('waitingMsgMask'),
        text = $('waitingMsgText');
        
    if (!mask && !text) return;
  
    // Display waiting text
    var vpd = document.viewport.getDimensions(),
        etd = text.getDimensions();
        
    text.setStyle({
      top: (vpd.height - etd.height)/2 + "px",
      left: (vpd.width - etd.width)/2 + "px",
      opacity: 0.8
    }).show();
    
    // Display waiting mask
    mask.setStyle({
      top: 0,
      left: 0,
      height: doc.clientHeight + "px",
      width: doc.clientWidth + "px",
      opacity: 0.3,
      position: "fixed"
    }).show();
  },

  /**
   * Cover an element while it's refreshing via an Ajax request
   *
   * @param {HTMLElement} element The element to cover
   */
  cover: function(element) {
    element = $(element);
    
    var coverContainer = new Element("div", {style: "border:none;background:none;padding:0;margin:0;position:relative;"}).addClassName("cover-container").hide(),
        cover = new Element("div").addClassName("ajax-loading");

    coverContainer.insert(cover);
    
    /** If the element is a TR, we add the div to the firstChild to avoid a bad page render (a div in a <table> or a <tr>)*/
    var receiver = element;
    
    if (/^tbody$/i.test(receiver.tagName)) {
      receiver = receiver.down();
    }
    
    if (/^tr$/i.test(receiver.tagName)) {
      receiver = receiver.down();
    }

    receiver.insert({top: coverContainer});
    
    cover.setStyle({
      opacity: 0.3,
      position: 'absolute',
      left: -parseInt(receiver.getStyle("padding-left"))+"px"
    }).show().clonePosition(element, {setLeft: false, setTop: false});
    
    var isTopAligned = receiver.getStyle("vertical-align") === "top";
    if (isTopAligned) {
      cover.style.top = -parseInt(receiver.getStyle("padding-top"))+"px";
    }
    
    var isLeftAligned = /left|start/i.test(receiver.getStyle("text-align") || "left");
    if (isLeftAligned) {
      cover.style.left = -parseInt(receiver.getStyle("padding-left"))+"px";
    }

    coverContainer.show();

    if (!isTopAligned || !isLeftAligned) {
      var offsetCover = coverContainer.cumulativeOffset();
      var offsetContainer = receiver.cumulativeOffset();
      
      if (!isTopAligned) {
        coverContainer.style.top = (offsetContainer.top - offsetCover.top)+"px";
      }
  
      if (!isLeftAligned) {
        coverContainer.style.left = (offsetContainer.left - offsetCover.left)+"px";
      }
    }
  }
};

/**
 * Handles different status changes of Ajax requests
 *
 * @type {Object}
 */
var AjaxResponse = {
  /**
   * Happens when the user was disconnected before the Ajax request
   */
  onDisconnected: function() {
    if (window.children['login'] && window.children['login'].closed) {
      window.children['login'] = null;
    }

    if (!window.children['login']) {
      var url = new Url;
      url.addParam("dialog", 1);
      url.addParam("login_info", 1);
      url.pop(650, 400, "login");
    }
  },

  /**
   * Happens for each successful Ajax request
   *
   * @param {Object} getData     The GET data
   * @param {Object} performance The performance data
   */
  onLoaded: function(getData, performance) {
    try {
      if (Prototype.Browser.IE) {
        return;
      }
      
      // If Firebug or Chrome console
      if (!("_mediboard" in window.console)) {
        console.log(getData, " ", performance, getData.m + " - " + getData.a);
      }
    } catch (e) {}
  }
};


/**
 * System message effects
 */
var SystemMessage = {
  id: "systemMsg",
  effect: null,

  // Check message type (loading, notice, warning, error) from given div
  autohidable: function() {
    return $(this.id).select(".error, .warning, .loading").length == 0;
  },
  
  notify: function(text, append) {
    $(this.id)[append ? "insert" : "update"](text);
    this.doEffect();
  },

  // show/hide the div
  doEffect : function (delay, forceFade) {
    // Cancel current effect
    if (this.effect) {
      this.effect.cancel();
      this.effect = null;
    }
    
    var element = $(this.id);
    delay = delay || 5;
    
    if (element.empty()) {
      element.hide();
      return;
    }
    
    // Ensure visible        
    element.show().setOpacity(1);
    
    // Only hide on type 'message'
    if (!forceFade && (!this.autohidable() || Preferences.INFOSYSTEM == 1)) {
      return;
    }
    
    // Program fading
    if (window.Effect) {
      this.effect = new Effect.Fade(this.id, { delay: delay } );
    }
    else {
      element.hide.delay(delay);
    }
  },
  
  init : function () {
    var element = $(this.id);
    Assert.that(element, "No system message div");
    
    // Hide on onclick
    element.observe('click', function(event) {
      SystemMessage.doEffect(0.1, true);
    });
        
    // Hide empty message immediately
    if (element.empty()) {
      element.hide();
      return;
    }
    
    SystemMessage.doEffect();
  }
};

/**
 * PairEffect Class
 */
var PairEffect = Class.create({
  initialize: function(idTarget, oOptions) {
    this.oOptions = Object.extend({
      idTarget       : idTarget,
      idTrigger      : idTarget + "-trigger",
      sEffect        : null, // could be null, "appear", "slide", "blind"
      bStartVisible  : false, // Make it visible at start
      bStoreInCookie : true,
      sCookieName    : "effects",
      duration       : 0.3
    }, oOptions);
    
    var oTarget   = $(this.oOptions.idTarget);
    var oTrigger  = $(this.oOptions.idTrigger);

    Assert.that(oTarget, "Target element '%s' is undefined", idTarget);
    Assert.that(oTrigger, "Trigger element '%s' is undefined ", this.oOptions.idTrigger);
  
    // Initialize the effect
    oTrigger.observe("click", this.flip.bind(this));
  
    // Initialize classnames and adapt visibility
    var aCNs = Element.classNames(oTrigger);
    aCNs.add(this.oOptions.bStartVisible ? "triggerHide" : "triggerShow");
    if (this.oOptions.bStoreInCookie) {
      aCNs.load(this.oOptions.sCookieName);
    }
    oTarget.setVisible(!aCNs.include("triggerShow"));   
  },
  
  // Flipper callback
  flip: function() {
    var oTarget = $(this.oOptions.idTarget);
    var oTrigger = $(this.oOptions.idTrigger);
    if (this.oOptions.sEffect && !Prototype.Browser.IE) {
      new Effect.toggle(oTarget, this.oOptions.sEffect, this.oOptions);
    } else {
      oTarget.toggle();
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
    oOptions = Object.extend({
      idStartVisible   : null, // Forces one element to start visible
      bStartAllVisible : false,
      sCookieName      : sTargetsClass
    }, oOptions);
    
    $$('.'+sTargetsClass).each(function(oElement) {
      oOptions.bStartVisible = oOptions.bStartAllVisible || (oElement.id == oOptions.idStartVisible);
      new PairEffect(oElement.id, oOptions);
    });
  }
});


/**
 * TogglePairEffect Class
 */
var TogglePairEffect = Class.create({
  initialize: function(idTarget1, idTarget2, oOptions) {
    this.oOptions = Object.extend({
      idFirstVisible : 1,
      idTarget1      : idTarget1,
      idTarget2      : idTarget2,
      idTrigger1     : idTarget1 + "-trigger",
      idTrigger2     : idTarget2 + "-trigger"
    }, oOptions);
    
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
    oTrigger1.observe("click", function() { fShow(2); } );
    oTrigger2.observe("click", function() { fShow(1); } );
    
    this.show(this.oOptions.idFirstVisible);
  },
  
  show: function(iWhich) {
    $(this.oOptions.idTarget1).setVisible(1 == iWhich);
    $(this.oOptions.idTarget2).setVisible(2 == iWhich);
    $(this.oOptions.idTrigger1).setVisible(1 == iWhich);
    $(this.oOptions.idTrigger2).setVisible(2 == iWhich);
  }
});

/**
 * PairEffect utiliy function
 */

Object.extend(TogglePairEffect, {
  declaredEffects : {},

  // Initialize a whole group giving the className for all targets
  initGroup: function(sTargetsClass, oOptions) {
    oOptions = Object.extend({
      idStartVisible   : null, // Forces one element to start visible
      bStartAllVisible : false,
      sCookieName      : sTargetsClass
    }, oOptions);
    
    $$('.'+sTargetsClass).each(function(oElement) {
      oOptions.bStartVisible = oOptions.bStartAllVisible || (oElement.id == oOptions.idStartVisible);
      new PairEffect(oElement.id, oOptions);
    });
  }
});

/**
 * View port manipulation object
 *   Handle view ported objects
 */
var ViewPort = {
  /**
   *
   * @param {HTMLElement|string} element DOM element
   * @param {float}              pct     Percentage of available height
   * @constructor
   */
  SetAvlHeight: function (element, pct) {
    element = $(element);
    if (!element) return;
  
    // Position Top de la div, hauteur de la fenetre,
    // puis calcul de la taille de la div
    var pos = element.cumulativeOffset().top;
    var winHeight, modal;
    if (modal = element.up('.modal')) {
      winHeight = modal.getDimensions().height;
      pos = pos - modal.cumulativeOffset().top;
    }
    else {
      winHeight = window.getInnerDimensions().height;
    }

    element.style.overflowY = "auto";
    element.style.overflowX = "hidden";
    element.style.height = ((winHeight - pos) * pct - 10) + "px";
  },

  /**
   *
   * @param {HTMLElement|string} element DOM element
   * @param {float}              pct     Percentage of available width
   * @constructor
   */
  SetAvlWidth : function(element, pct) {
    element = $(element);
    if (!element) return;

    // Position Top de la div, hauteur de la fenetre,
    // puis calcul de la taille de la div
    var pos = element.cumulativeOffset().left;
    var winWidth, modal;
    if (modal = element.up('.modal')) {
      winWidth = modal.getDimensions().width;
      pos = pos - modal.cumulativeOffset().left;
    }
    else {
      winWidth = window.getInnerDimensions().width;
    }

    element.style.overflowX = "auto";
    element.style.width = ((winWidth - pos) * pct - 10) + "px";
  },

  SetAvlSize : function(element, pct) {
    element = $(element);
    if (!element) return;

    // Position Top de la div, hauteur de la fenetre,
    // puis calcul de la taille de la div
    var size = element.cumulativeOffset();
    var top = size.top;
    var left = size.left;
    var winSize, winHeight, winWidth, modal;
    if (modal = element.up('.modal')) {
      winSize = modal.getDimensions()
      winHeight = winSize.height;
      winWidth = winSize.width;
      var modOffset = modal.cumulativeOffset()
      top = top - modOffset.top;
      left = left - modOffset.left;
    }
    else {
      winSize = window.getInnerDimensions()
      winHeight = winSize.height;
      winWidth = winSize.width;
    }

    element.style.overflowY = "auto";
    element.style.overflowX = "auto";
    element.style.height = ((winHeight - top) * pct - 30) + "px";
    element.style.width = ((winWidth - left) * pct - 10) + "px";
  },

  SetFrameHeight: function(element, options){
    options = Object.extend({
      marginBottom : 15
    }, options);
    
    // Calcul de la position top de la frame
    var fYFramePos = Position.cumulativeOffset(element)[1];  
    
    // hauteur de la fenetre
    var fNavHeight = window.getInnerDimensions().height;
    
    // Calcul de la hauteur de la div
    var fFrameHeight = fNavHeight - fYFramePos;
    
    element.setAttribute("height", fFrameHeight - options.marginBottom);
  },

  getScrollOffset: function() {
    return {
      top:  document.documentElement.scrollTop  || document.body.scrollTop,
      left: document.documentElement.scrollLeft || document.body.scrollLeft
    };
  }
};

/** Token field used to manage multiple enumerations easily.
 *  @param element The element used to get piped values : token1|token2|token3
 *  @param options Accepts the following keys : onChange, confirm, props, separator
 */
var TokenField = Class.create({
  initialize: function(element, options) {
    this.element = $(element);
    
    this.options = Object.extend({
      onChange : Prototype.emptyFunction,
      confirm  : null,
      props    : null,
      separator: "|"
    }, options);
  },
  onComplete: function(value) {
    if(this.options.onChange != null)
      this.options.onChange(value);
    return true;
  },
  add: function(value, multiple) {
    if (!value) return false;

    if(this.options.props) {
      ElementChecker.prepare(new Element('input', {value: value, className: this.options.props}));
      ElementChecker.checkElement();
      if(ElementChecker.oErrors.length) {
        alert(ElementChecker.getErrorMessage());
        return false;
      }
    }
    
    var aToken = this.getValues();
    aToken.push(value);
    if(!multiple) aToken = aToken.uniq();
    
    this.setValues(aToken);
    return true;
  },
  remove: function(value, unique) {
    if(!value || (this.options.confirm && !confirm(this.options.confirm))) {
      return false;
    }

    var values = this.getValues();
    if (unique) {
      values.splice(values.lastIndexOf(value), 1);
    }
    else {
      values = values.without(value);
    }
    this.setValues(values);

    return true;
  },
  contains: function(value) {
   return (this.getValues().indexOf(value) != -1);
  },
  toggle: function(value, force, multiple) {
    if (!Object.isUndefined(force)) {
      return this[force?"add":"remove"](value, multiple);
    }
    return this[this.contains(value)?"remove":"add"](value);
  },
  getValues: function(asString) {
    if (asString) {
      return this.element.value;
    }
    return this.element.value.split(this.options.separator).without("");
  },
  setValues: function(values) {
    if (Object.isArray(values)) {
      values = values.join(this.options.separator);
    }
    this.onComplete(this.element.value = values);
    return values;
  }
});

function guid_log(guid) {
  var parts = guid.split("-");
  var url = new Url("system", "view_history");
  url.addParam("object_class", parts[0]);
  url.addParam("object_id", parts[1]);
  url.addParam("user_id", "");
  url.addParam("type", "");
  url.popup(600, 500, "history");
}

function guid_ids(guid) {
  var parts = guid.split("-");
  var url = new Url("dPsante400", "view_identifiants");
  url.addParam("object_class", parts[0]);
  url.addParam("object_id", parts[1]);
  url.addParam("dialog", 1);
  url.requestModal(750, 400);
}

function uploadFile(object_guid, file_category_id, _rename, named){
  var url = new Url("files", "upload_file");
  url.addParam("object_guid", object_guid);
  url.addParam("file_category_id", file_category_id);
  url.addParam("_rename", _rename);
  url.addParam("named", named);
  url.requestModal(700, 300);
}

function popChgPwd() {
  new Url("admin", "chpwd").requestModal(400);
}

var Note = {
  init: function() {
    this.url = new Url("system", "edit_note");
  },
  
  create: function (object_guid) {
  this.init();
    this.url.addParam("object_guid", object_guid);
    this.modal();
  },

  edit: function(note_id) {
  this.init();
    this.url.addParam("note_id", note_id);
    this.modal();
  },

  modal: function () {
    this.url.requestModal(500);
  },
  
  submit: function (form) {
    return onSubmitFormAjax(form, { onComplete: function() { 
      Note.refresh(true); 
      Note.close();
    }});
  },
  
  close: function() {
    this.url.modalObject.close();
  },
  
  refresh: function(force, object_guid) {
    var selector = "div.noteDiv";
    
    if (force) {
      object_guid = object_guid || Note.url.oParams['object_guid'];
    }
    
    // Specific guid if forced, non initialized otherwise
    selector += force ? ("."+object_guid) : ":not(.initialized)";
    
    $$(selector).each(function(element) {
      element.addClassName("initialized");
      var url = new Url("system", "httpreq_get_notes_image");
      url.addParam("object_guid", element.className.split(" ")[1]);
      url.requestUpdate(element);
    });
  }
};

// *******
var Dom = {
  writeElem : function(elem, elemReplace){
    elem = $(elem);
    while (elem.firstChild) {
      elem.removeChild(elem.firstChild);
    }
    if(elemReplace){
      elem.appendChild(elemReplace);
    }
  },
  
  cloneElemById : function(id, withChildNodes){
    return $(id).clone(withChildNodes);
  },
  
  createTd : function(sClassname, sColspan){
    return new Element('td', {
      className: sClassname,
      colspan: sColspan
    });
  },
  
  createTh : function(sClassname, sColspan){
    return new Element('th', {
      className: sClassname,
      colspan: sColspan
    });
  },
  
  createImg : function(sSrc){
    return new Element('img', {
      src: sSrc
    });
  },
  
  createInput : function(sType, sName, sValue){
    return new Element('input', {
      type: sType,
      name: sName,
      value: sValue
    });
  },
  
  createSelect : function(sName){
    return new Element('select', {
      name: sName
    });
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
        if((childNode.nodeType == Node.TEXT_NODE) && (!/\S/.test(childNode.nodeValue))){
          node.removeChild(node.childNodes[i]);
          i--;
        }else if (Object.isElement(childNode)) {
          Dom.cleanWhitespace(childNode);
        } 
      }
    }
  }
};

/**
 * Levenstein function
 *
 * @param {String} str1 String 1
 * @param {String} str2 String 2
 *
 * @return {Number} The Levenstein distance between str1 and str2
 */
function levenshtein(str1, str2) {
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
  /**
   * Store the state of a Control Tab in a cookie
   *
   * @param {String} id  Id of the Control Tab UL
   * @param {String} tab Active tab
   */
  storeTab: function (id, tab) {
    new CookieJar().setValue("TabState", id, tab);
  },

  /**
   * Get the state of a Control Tab from the cookie
   *
   * @param {String} id  Id of the Control Tab UL
   *
   * @return {String} The active tab
   */
  loadTab: function (id) {
    return new CookieJar().getValue("TabState", id);
  },

  /**
   * Create a Control Tab
   *
   * @param {String}  id            ID of the UL
   * @param {Boolean} storeInCookie Store the state in a cookie
   * @param {Object=} options       Options
   *
   * @return {Control.Tabs,Boolean}
   */
  create: function (id, storeInCookie, options) {
    if (!$(id)) {
      return false;
    }

    var tab = new Control.Tabs(id, options);

    if (storeInCookie) {
      var oldAfterChange = tab.options.afterChange;

      tab.options.afterChange = function (tab, tabName) {
        if (oldAfterChange && Object.isFunction(oldAfterChange)) {
          oldAfterChange(tab, tabName);
        }

        Control.Tabs.storeTab(id, tab.id);
      };

      var tabName = Control.Tabs.loadTab(id);
      if (tabName) {
        tab.setActiveTab(tabName);
      }
    }

    return tab;
  },
  
  activateTab: function(tabName) {
    Control.Tabs.findByTabId(tabName).setActiveTab(tabName);
  },
  
  getTabAnchor: function(tabName) {
    // Find anchor
    var anchors = $$('a[href=#'+tabName+']');
    if (anchors.length != 1) {
      Console.error('Anchor not found or found multiple for tab: '+tabName);
      return;
    }

    return anchors[0];
  },

  /**
   * Redefine this method as it doesn't work if the tab was redefined
   */
  findByTabId: function(id){
    return Control.Tabs.instances.find(function(tab){
      return tab.links.find(function(link){
        if (link.key != id) {
          return false;
        }

        return link.descendantOf(document.body);
      });
    });
  },
    
  setTabCount: function(tabName, count, total) {
    var anchor = this.getTabAnchor(tabName);
    
    //anchor.writeAttribute("data-count", count);
    
    // Find count span
    var small = anchor.down('small') || anchor.insert({
      bottom: " <small></small>" // keep the space
    }).down('small');
    
    if (!small) {
      Console.error('Small count span not found for tab: '+tabName);
      return;
    }
    
    // Manage relative count
    count += ''; // String cast
    if (count.charAt(0) == "+" || count.charAt(0) == "-") {
      count = parseInt(small.innerHTML.replace(/(\(|\))*/,"")) + parseInt(count);
    }
    
    // Set empty class
    anchor.setClassName('empty', count < 1);
    
     // Set count label
    var label = count + (total ? ' / ' + total : '');
    small.update('('+label+')');
  }
});

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
  },
  print: function(){
    this.toPrint().print();
  },
  toPrint: function(){
    var container = DOM.div({});
    
    this.links.each(function(link){
      // header
      var h = DOM.h2({});
      h.update(link.innerHTML);
      h.select('button').invoke('remove');
      container.insert(h);
      
      // content
      var content = $(link.getAttribute("href").substr(1)).clone(true);
      content.show();
      container.insert(content);
    }, this);
    
    return container;
  }
});

window.getInnerDimensions = function() {
  var doc = document.documentElement;

  return {
    width:  doc.clientWidth,
    height: doc.clientHeight
  };
};

/** DOM element creator for Prototype by Fabien M�nager
 *  Inspired from Michael Geary 
 *  http://mg.to/2006/02/27/easy-dom-creation-for-jquery-and-prototype
 *
 *  @property a
 *  @property br
 *  @property div
 *  @property fieldset
 *  @property form
 *  @property h1
 *  @property h2
 *  @property h3
 *  @property iframe
 *  @property img
 *  @property input
 *  @property ul
 **/
var DOM = {
  defineTag: function (tag) {
    DOM[tag] = function () {
      return DOM.createNode(tag, arguments);
    };
  },
  
  createNode: function (tag, args) {
    var e, i, j, arg, length = args.length;
    try {
      e = new Element(tag, args[0]);
      for (i = 1; i < length; i++) {
        arg = args[i];
        if (arg == null) continue;
        if (!Object.isArray(arg)) e.insert(arg);
        else {
          for (j = 0; j < arg.length; j++) e.insert(arg[j]);
        }
      }
    }
    catch (ex) {
      console.error('Cannot create <' + tag + '> element:\n' + Object.inspect(args) + '\n' + ex.message);
      e = null;
    }
    return e;
  },
  
  tags: [
    'a', 'applet', 'big', 'br', 'button', 'canvas', 'div', 'fieldset', 'form',
    'h1', 'h2', 'h3', 'h4', 'h5', 'hr', 'iframe', 'img', 'input', 'ins', 'label',
    'legend', 'li', 'ol', 'optgroup', 'option', 'p', 'param', 'pre', 'script',
    'select', 'small', 'span', 'strong', 'table', 'tbody', 'td', 'textarea',
    'tfoot', 'th', 'thead', 'tr', 'tt', 'ul'
  ]
};

DOM.tags.each(DOM.defineTag);

// To let the tooltips on top
Control.Window.baseZIndex = 800;

if (document.documentMode >= 8) {
  Control.Overlay.getIeStyles = function() {
    return {
      position: 'fixed',
      top: 0,
      left: 0,
      zIndex: Control.Window.baseZIndex - 1
    };
  };
}

// Replacements for the javascript alert() and confirm()
var Modal = {
  alert: function(message, options) {
    options = Object.extend({
      className: 'modal alert big-warning',
      okLabel: 'OK',
      onValidate: Prototype.emptyFunction,
      closeOnClick: null,
      iframeshim: false
    }, options);
    
    // Display element
    if (Object.isElement(message)) {
      message.show();
    }

    var html = DOM.div(null, 
      DOM.div( { style: "min-height: 3em;"}, message),
      DOM.div( { style: "text-align: center; margin-left: -3em;" },
        DOM.button( {className : "tick", type: "button"}, options.okLabel)
      )
    );

    var m = Control.Modal.open(html.innerHTML, options);
    m.container.down('button.tick').observe('click', (function(){this.close(); options.onValidate();}).bind(m));
  },
  
  confirm: function(message, options) {
    options = Object.extend( {
      className: 'modal confirm big-info',
      yesLabel: 'Oui',
      noLabel: 'Non',
      onOK: Prototype.emptyFunction,
      onKO: Prototype.emptyFunction,
      onValidate: Prototype.emptyFunction,
      closeOnClick: null,
      iframeshim: false
    }, options);
    
    // Display element  
    if (Object.isElement(message)) {
      message.show();
    }

    var html = DOM.div(null, 
      DOM.div( { style: "min-height: 3em;"}, message),
      DOM.div( { style: "text-align: center; margin-left: -3em;" },
       DOM.button( {className : "tick"  , type: "button"}, options.yesLabel), 
       DOM.button( {className : "cancel", type: "button"}, options.noLabel )
      )
    );
    
    var m = Control.Modal.open(html.innerHTML, options);
    
    var okButton = m.container.down('button.tick');
    var koButton = m.container.down('button.cancel');
    
    var closeModal = function(){
      document.stopObserving('keyup', escapeClose);
      m.close();
    };
    
    var okCallback = function(){ 
      closeModal(); 
      options.onValidate(true); 
      options.onOK();
    };
    okButton.observe('click', okCallback);
    
    var koCallback = function(){ 
      closeModal(); 
      options.onValidate(false); 
      options.onKO(); 
    };
    koButton.observe('click', koCallback);
    
    var escapeClose = function(e){
      if (Event.key(e) == Event.KEY_ESC) {
        e.stop();
        koCallback();
      }
    };
    
    okButton.tryFocus();
    document.observe('keydown', escapeClose);
  },
  
  open: function(container, options) {
    options = Object.extend({
      className: 'modal popup',
      title: null,
      showClose: false,
      fireLoaded: true,
      onClose: null,
      align: null
    }, options);

    var containerId = container;
    container = $(container);

    if (!container) {
      throw new Error("Unknown modal element: '"+containerId+"'");
    }

    var offset = 10;
    var wrapper;
    var containerStyle = container.style;

    // When the modal size is put on the element itself (should be avoided)
    if (!options.width && containerStyle.width && !/%/.test(containerStyle.width)) {
      options.width = containerStyle.width;
    }
    if (!options.height && containerStyle.height && !/%/.test(containerStyle.height)) {
      options.height = containerStyle.height;
    }
    containerStyle.width = null;
    containerStyle.height = null;

    var style = Modal.prepareDimensions({
      width:  options.width,
      height: options.height
    });

    // Do not pass dimensions to Control.Modal.open
    delete options.height;
    delete options.width;

    container.show();

    // reset defaults
    container.setStyle({
      position: "static",
      overflow: "visible",
      top: "auto",
      left: "auto"
    });

    // Wrap the content if not already done (modal windows launched more than once)
    if (!container._alreadyWrapped) {
      var content = DOM.div({className: "content"});

      if (options.align) {
        style.textAlign = options.align;
      }

      content.setStyle(style);

      wrapper = DOM.div({className: "modal-wrapper"}, content);
      container.insert({after: wrapper});
      content.update(container);

      // Decoration preparing
      var titleElement = DOM.div({className: "title"},
        DOM.span({className: "left"}, options.title),
        DOM.span({className: "right"})
      );

      if (options.showClose) {
        var closeButton = DOM.button({type: "button", className: "close notext" }, $T('Close'));
        titleElement.down(".right").insert({bottom: closeButton});
        closeButton.observe("click", Control.Modal.close);
      }

      content.insert({before: titleElement});

      // Display small shadows if the user has to scroll
      content.observe("scroll", Modal.updateScrollStatus.curry(content, wrapper, offset));

      container._alreadyWrapped = true;
    }
    else {
      wrapper = container.up(".modal-wrapper");
    }

    wrapper.show();

    var modal = Modal.openRaw(wrapper, options);
    container = modal.container;

    if (!container._loadedEventAttached) {
      container.observe("modal:loaded", (function(){
        var container    = this.container;
        var titleElement = container.down(".title");
        var content      = container.down(".content");
        var style        = container.style;

        style.paddingTop = titleElement.getHeight()+"px";

        if (!style.width || !style.height) {
          var contentDim   = content.getDimensions();
          var containerDim = container.getDimensions();

          // Useless, makes BIG modal windows which should remain small
          /*if (!style.width && contentDim.width  > containerDim.width - 5) {
            container.addClassName("full-width");
          }*/

          if (!style.height && contentDim.height > containerDim.height - 5) {
            container.addClassName("full-height");
          }
        }

        this.position();

        Modal.updateScrollStatus(content, container, offset);
      }).bindAsEventListener(modal));
    }

    container._loadedEventAttached = true;

    if (options.fireLoaded) {
      container.fire("modal:loaded");
    }

    if (options.onClose) {
      modal.observe("afterClose", options.onClose.bindAsEventListener(modal));
    }

    return modal;
  },

  openRaw: function(container, options) {
    options = Object.extend({
      className: 'modal',
      closeOnClick: null,
      overlayOpacity: 0.5,
      iframeshim: false
    }, options);

    return Control.Modal.open(container, options);
  },

  updateScrollStatus: function(element, modalContainer, offset){
    // Do NOT use toggleClassName to prevent reflows !!
    if (!Prototype.Browser.IPad) {
      if (element.scrollTop > offset) {
        modalContainer.addClassName("scroll-top");
      }
      else {
        modalContainer.removeClassName("scroll-top");
      }
    }

    if (element.scrollHeight - element.scrollTop - element.clientHeight > offset) {
      modalContainer.addClassName("scroll-bottom");
    }
    else {
      modalContainer.removeClassName("scroll-bottom");
    }
  },

  prepareDimensions: function(dimensions) {
    var viewportDimensions = document.viewport.getDimensions();

    var newDimensions = {};

    if (dimensions.width) {
      if (/^-/.test(dimensions.width)) {
        dimensions.width = viewportDimensions.width + parseInt(dimensions.width, 10) * 2;
      }

      newDimensions.width = String.getCSSLength(dimensions.width);
    }

    if (dimensions.height) {
      if (/^-/.test(dimensions.height)) {
        dimensions.height = viewportDimensions.height + parseInt(dimensions.height, 10) * 2;
      }

      newDimensions.height = String.getCSSLength(dimensions.height);
    }

    return newDimensions;
  }
};

window._close = function() {
  window.close();
};

// We replace the window.close method for iframes (modal windows)
if (window.parent && window.parent != window && window.parent.Mediboard) {
  window.launcher = window.parent;
  
  window.oldClose = window.close;
  window.close = window._close = function(){
    try {
      var modal = window.launcher.Control.Modal.stack.last();
      modal.close();
      modal.destroy();
    } catch (e) {
      window.oldClose();
    }
  }
}
else {
  window.launcher = window.opener;
}
// TODO To be removed
window.modal = Modal.open;

window.scrollToTop = function(){
  window.scrollTopSave = document.documentElement.scrollTop || document.body.scrollTop;
  
  document.documentElement.scrollTop = 0;
  document.body.scrollTop = 0;
};

window.scrollReset = function(){
  if (!window.scrollTopSave) {
    return;
  }
  
  document.documentElement.scrollTop = window.scrollTopSave;
  document.body.scrollTop = window.scrollTopSave;
};

// Multiple mocals
Object.extend(Control.Modal,{
  stack: [],
  close: function() {
    if (!Control.Modal.stack.length) return;
    Control.Modal.stack.last().close();
  },
  
  position: function() {
    if (!Control.Modal.stack.length) return;
    Control.Modal.stack.last().position();
  },
  
  Observers: {
    beforeOpen: function(){
      /// FUCK APPLETS AND FIREFOX
      if (!Prototype.Browser.Gecko || !$$("applet").length) {
        document.body.style.overflow = "hidden"; // Removes the body's scrollbar
      }
      
      if(!this.overlayFinishedOpening && Control.Modal.stack.length == 0){
        Control.Overlay.observeOnce('afterShow',function(){
          this.overlayFinishedOpening = true;
          this.open();
        }.bind(this));
        Control.Overlay.show(this.options.overlayOpacity,this.options.fade ? this.options.fadeDuration : false);
        throw $break;
      }/*else
      Control.Window.windows.without(this).invoke('close');*/
    },
    
    afterOpen: function(){
      if (Control.Modal.stack.length == 0) {
        // Forcer le scroll to top a cause des datepicker qui se positionnent mal
        // (prescription, planning de la semaine du bloc, etc)
        window.scrollToTop();
      }
      
      //Control.Modal.current = this;
      var overlay = Control.Overlay.container;
      Control.Modal.stack.push(this);
      overlay.style.zIndex = this.container.style.zIndex - 1;
      
      // move the overlay before the modal element (zIndex trick)
      this.container.insert({before: overlay});
      overlay.insert({after: Control.Overlay.iFrameShim.element});
      
      this.container.style.position = "fixed";
      
      Event.stopObserving(window, 'scroll', this.positionHandler);
      Event.stopObserving(window, 'resize', this.outOfBoundsPositionHandler);
      
      Control.Overlay.positionIFrameShim();
      
      /* Gestion du didacticiel dans le cas o� il est pr�sent dans la page*/
      if (window.Didacticiel && Didacticiel.main_didacticiel.state){
        Didacticiel.main_didacticiel.isModalWindow = true;
      }
    },
    
    afterClose: function(){
      Control.Modal.stack.pop().close();
      var overlay = Control.Overlay.container;
      
      if (Control.Modal.stack.length == 0) {
        var body = $(document.body);
        
        window.scrollReset();
        
        /// FUCK APPLETS AND FIREFOX
        if (!Prototype.Browser.Gecko || !$$("applet").length) {
          body.style.overflow = "auto"; // Put back the body's scrollbar
        }
        
        // put it back at the end of body
        body.insert(overlay);
        body.insert(Control.Overlay.iFrameShim.element);
        
        Control.Overlay.hide(this.options.fade ? this.options.fadeDuration : false);
      }
      else {
        var lastModal = Control.Modal.stack.last().container;
        overlay.style.zIndex = lastModal.style.zIndex - 2;
        
        // move the overlay before the modal element (zIndex trick)
        lastModal.insert({before: overlay}); 
        overlay.insert({after: Control.Overlay.iFrameShim.element});
        
        Control.Overlay.positionIFrameShim();
      }
      
      //Control.Modal.current = false;
      this.overlayFinishedOpening = false;
      
      /* Didacticiel management*/
      if (window.Didacticiel && Didacticiel.main_didacticiel.state) {
        Didacticiel.main_didacticiel.positionTour();
        if (Didacticiel.main_didacticiel.nbiframe > 0) {
          Didacticiel.main_didacticiel.closeAfterModal();
        }
      }
    }
  }
});

Class.extend(Control.Modal, {
  restore: function() {
    this.container.removeClassName("modal");
    this.container.setStyle({ position: null});
    Control.Overlay.container.hide(); 
    this.iFrameShim.hide();
    this.isOpen = false;
    this.notify('afterClose');
  },
  print: function() {
    var e = this.container.clone(true);
    e.style.cssText = null;
    e.removeClassName("modal");
    e.print();
  },
  position: function(){
    var container = this.container;

    // Reset container position so that it take all the room it needs
    container.setStyle({
      top: null,
      left: null
    });

    var contDims = container.getDimensions();
    var vpDims = document.viewport.getDimensions();
    
    var top  = Math.max(0, (vpDims.height - contDims.height) / 2);
    var left = Math.max(0, (vpDims.width  - contDims.width)  / 2);

    container.setStyle({
      top:  Math.round(top)  + "px",
      left: Math.round(left) + "px"
    });
  },
  // Redefine this method to ...
  bringToFront: function(){
    // ... do nothing!
  }
});

var Session = {
  window: null,
  isLocked: false,
  lock: function(){
    Session.lockScreen();

    var url = new Url;
    url.addParam("lock", true);
    url.requestUpdate("systemMsg", {
      method: "post",
      getParameters: {m: 'admin', a: 'ajax_unlock_session'}
    });
  },
  lockScreen: function(){
    this.isLocked = true;

    var container = $('sessionLock');
    this.window = Modal.open(container, {
      width: 350,
      height: 150,
      title: $T("Session locked") + " - " + User.view
    });

    container.down('form').reset();
    container.down('input[type=text], input[type=password]').focus();

    $('main').hide();
  },
  request: function(form){
    var url = new Url;
    url.addElement(form.password);
    url.requestUpdate(form.down('.login-message'), {
      method: "post",
      getParameters: {m: 'admin', a: 'ajax_unlock_session'}
    });
    return false;
  },
  unlock: function(){
    this.window.close();
    $('main').show();
    return false;
  },
  close: function(){
    document.location.href = '?logout=-1';
  }
};

/*
function dataUri2File(data, filename, replace) {
  data = new String(data).replace(/=/g, '%3D').replace(/\//g, '%2F').replace(/\+/g, '%2B');
  
  new Ajax.Request('?m=system&a=datauri_to_file&suppressHeaders=1', {
    method: 'post',
    postBody: 'filename='+filename+'&replace='+replace+'&data='+data
  });
}
*/

var UserSwitch = {
  window: null,
  popup: function(){
    var container = $('userSwitch');
    this.window = Modal.open(container, {
      width: 350,
      height: 200,
      showClose: true,
      title: $T("User switch"),
      onClose: UserSwitch.cancel
    });

    container.down('form').reset();
    container.down('input[type=text], input[type=password]').focus();
  },
  reload: function(){
    this.window.close();
    location.reload();
    location.href = location.href.match(/^(.*)#/)[1]; // When Mediboard is inside a showModalDialog window (url without what's after #)
  },
  login: function(form){
    if (!checkForm(form)) return false;
    var url = new Url;
    url.addElement(form.username);
    url.addElement(form.password);
    url.requestUpdate(form.down('.login-message'), {
      method: "post",
      getParameters: {
        m: 'admin',
        a: 'ajax_login_as',
        is_locked: Session.isLocked
      }
    });
    return false;
  },
  cancel: function(){
    if (Session.isLocked) {
      Session.lockScreen();
    }
  }
};

Element.addMethods({
  highlight: function(element, term, className) {
    function innerHighlight(element, term, className) {
      className = className || 'highlight';
      term = (term || '').toUpperCase();
      
      var skip = 0;
      if ($(element).nodeType == 3) {
        var pos = element.data.toUpperCase().indexOf(term);
        if (pos >= 0) {
          var middlebit = element.splitText(pos),
              endbit = middlebit.splitText(term.length),
              middleclone = middlebit.cloneNode(true),
              spannode = document.createElement('span');
              
          spannode.className = 'highlight';
          spannode.appendChild(middleclone);
          middlebit.parentNode.replaceChild(spannode, middlebit);
          skip = 1;
        }
      }
      else if (element.nodeType == 1 && element.childNodes && !/(script|style|textarea|select)/i.test(element.tagName)) {
        for (var i = 0; i < element.childNodes.length; ++i)
          i += innerHighlight(element.childNodes[i], term, className);
      }
      return skip;
    }
    innerHighlight(element, term, className);
    return element;
  },
  removeHighlight: function(element, term, className) {
    className = className || 'highlight';
    $(element).select("span."+className).each(function(e) {
      e.parentNode.replaceChild(e.firstChild, e);
    });
    return element;
  },
  getSelection: function(element) {
    var doc, win, selection, range;
    if ((doc = element.ownerDocument) && (win = doc.defaultView) &&
        win.getSelection && doc.createRange && 
        (selection = window.getSelection()) && 
        selection.removeAllRanges) {
        range = doc.createRange();
        range.selectNode(element);
        selection.removeAllRanges();
        selection.addRange(range);
    }
    else if (document.body && document.body.createTextRange && 
            (range = document.body.createTextRange())) {
        range.moveToElementText(element);
        range.select();
    }
  },
  print: function(element){
    var iframe = $("printFrame");
    
    if (iframe) iframe.remove();

    // FIXME use Element.getTempIframe
    $(document.documentElement).insert(DOM.iframe({
      id:    "printFrame",
      name:  "printFrame", 
      src:   "about:blank",
      style: "position:absolute;width:1px;height:1px;",
      frameborder: 0
    }));
    
    iframe = $("printFrame");
    
    var win = iframe.contentWindow;
    var doc = win.document;
    var bodyContent = "";
    var parentHead = $$("head")[0];
  
    if (Prototype.Browser.IE) { // argh
      parentHead.select("link, style").each(function(e){
        // Si c'est une feuille de style
        if (e.styleSheet) {
          var css = e.styleSheet.cssText;
          
          // Si elle a un href (feuille de style externe)
          if (e.href) {
            var matchHref = e.href.match(/(.*\/)[^\/]+$/);
            var pattern = /@import\s*(?:url\s*\(\s*)?["']?([^"'\)]+)\)?["']?/g;
            var i = 50, match;
            
            if (matchHref) {
              // on regarde tous ses @import pour les importer "� la main"
              while(i-- && (match = pattern.exec(css))) {
                bodyContent += "<" + "link type=\"text/css\" rel=\"stylesheet\" href=\"" + matchHref[1] + match[1] + "\" />";
              }
            }
          }
          
          bodyContent += "<"+"style type='text/css'>"+css+"<"+"/style>";
        }
      });
    }
    
    bodyContent += "<"+"style type='text/css'>";
    $$("style").each(function(elt) {
      bodyContent += elt.innerHTML;
    });
    bodyContent += "<"+"/style>";

    var htmlClass = $$("html")[0].className;
    var meta = Prototype.Browser.IE ? "<meta http-equiv='X-UA-Compatible' content='IE="+document.documentMode+"' />" : "";
    
    doc.open();
    doc.write("<"+"html class='"+htmlClass+"'><"+"head>"+meta+"<"+"/head><"+"body>"+bodyContent+"<"+"/body><"+"/html>");

    // !! Don't use PrototypeJS functions here, this is an iframe
    var head = doc.head || doc.getElementsByTagName('head')[0];
    var body = doc.body || doc.getElementsByTagName('body')[0];
    
    var elements;
    
    if (Object.isElement(element))
      elements = [element];
    else 
      elements = element;
    
    elements.each(function(e){
      if (Object.isFunction(e.toPrint)) {
        e = e.toPrint();
      }
      var clone = $(e).clone(true);
      clone.select('script').invoke('remove');
      
      if (Prototype.Browser.IE && document.documentMode && document.documentMode == 9) {
        body.insertAdjacentHTML("beforeEnd", clone.outerHTML.replace(/>\s+<(t[dh])/gi, "><$1"));
      }
      else {
        body.appendChild(clone); // Fx, IE8 and others
      }
    });
    
    if (Prototype.Browser.IE) { // argh
      parentHead.select("script").each(function(e){
        bodyContent += e.outerHTML;
      });
    
      doc.close();
      doc.execCommand('print', false, null);
    }
    else {
      if (Prototype.Browser.Gecko) {
        parentHead.select("link").each(function(e) {
          if (/css/.test(e.getAttribute("href"))) {
            var css = "";
            $A(e.sheet.cssRules).each(function(rule) {
              css += rule.cssText;
            });
            head.innerHTML += "<"+"style type='text/css'>"+css+"<"+"/style>";
          }
        });
      }
      else {
        head.innerHTML = parentHead.innerHTML;
      }
      
      win.focus();
      win.print();
    }
  }
});

/**
 * Print current page or iframe
 */
App.print = function(){
  if (Prototype.Browser.IE && document.documentMode == 9 && window.parent) {
    document.execCommand('print', false, null);
  }
  else {
    window.focus();
    window.print();
  }
};

/**
 * Defer window closing
 */
App.deferClose = function(){
  (function(){
    window.close();
  }).defer();
};

/**
 * Show application release information
 */
App.showReleaseInfo = function(){
  var url = new Url("system", "ajax_show_release_info");
  url.requestModal();
};

/**
 * Initializes application locking when no movement
 */
App.initSessionLocker = (function(){
  var duration = App.sessionLifetime;

  var timer;

  function setTimer() {
    timer = window.setTimeout(function(){
      Session.lock();
    }, duration * 1000);
  }

  function resetTimer(){
    window.clearTimeout(timer);
    setTimer();
  }

  return function(){
    setTimer();

    document.observe("mousemove", resetTimer);
    document.observe("keydown", resetTimer);
    document.observe("touchstart", resetTimer);
  };
})();

/**
 * Adds column highlighting to a table
 * @param {Element} table The table
 * @param {String} className The CSS class to give to the highlighted cells
 */
Element.addMethods("table", {
  gridHighlight: function(table, className) {
    className = className || "hover";
    
    var rows = $(table).select("tr");
    
    rows.each(function(row){
      row.select('th,td').each(function(cell, i){
        cell.observe("mouseover", function(e){
          $(table).select("th."+className+",td."+className).invoke("removeClassName", className);
          rows.each(function(_row){
            _row.childElements()[i].addClassName(className);
          });
        });
      });
    });
  }
});

/**
 * Creates a temporary iframe element
 * @param {String} id The ID to give to the iframe
 */
Element.getTempIframe = function(id) {
  var iframe = DOM.iframe({
    src:   "about:blank",
    style: "position:absolute;width:0px;height:0px;",
    frameborder: 0
  });
  
  if (id) {
    Element.writeAttribute(iframe, 'id', id);
  }
    
  Element.writeAttribute(iframe, 'name', iframe.identify());
  
  $(document.documentElement).insert(iframe);
  
  return iframe;
};

var BarcodeParser = {
  inputWatcher: Class.create({
    initialize: function(input, options) {
      this.input = $(input);
      if (!this.input) return;
      
      this.options = Object.extend({
        size: null,
        field: "scc_prod",
        onRead: null,
        onAfterRead: function(parsed){}
      }, options);
      
      this.options.onRead = this.options.onRead ||
        function(parsed) {
          var field = this.options.field;
          var alert = (!parsed.comp[field] && field != "ref") || (field == "ref" && (parsed.comp.lot || parsed.comp.scc || parsed.comp.per));
          var message = input.next(".barcode-message");
          
          if (!message) {
            message = DOM.span({style: "display: none;", className: "barcode-message warning"}, "Code possiblement invalide");
            input.up().insert({bottom: message});
          }
          message.setVisible(alert);
          
          if (parsed.comp[this.options.field]) {
            $V(input, parsed.comp[this.options.field]);
          }
          
          input.select();
        }.bind(this);
      
      input.maxLength = 50;
      input.addClassName("barcode");
      input.observe("keypress", function(e){
        var charCode = Event.key(e);
        var input = Event.element(e);
        
        if (charCode == Event.KEY_RETURN) {
          if (!this.options.size || ($V(input).length != this.options.size)) {
            Event.stop(e);
          }
          
          var url = new Url("dPstock", "httpreq_parse_barcode");
          url.addParam("barcode", $V(input));
          url.requestJSON(function(parsed){
            this.options.onRead(parsed);
            this.options.onAfterRead(parsed);
          }.bind(this));
        }
      }.bindAsEventListener(this));
    }
  })
};

/**
 * iFrame shim, modified:
 *  * this.element.src from "javascript:void(0);" to "about:blank" for IE10
 *  * implicit global "prop" in setBounds
 *  * Code formatting
 *
 * Must be included AFTER livepipe.js
 *
 * @type {IframeShim}
 */
var IframeShim = Class.create({
  initialize: function() {
    this.element = new Element('iframe',{
      style: 'position:absolute;filter:progid:DXImageTransform.Microsoft.Alpha(opacity=0);display:none',
      src: 'about:blank',
      frameborder: 0
    });
    $(document.body).insert(this.element);
  },
  hide: function() {
    this.element.hide();
    return this;
  },
  show: function() {
    this.element.show();
    return this;
  },
  positionUnder: function(element) {
    var element = $(element);
    var offset = element.cumulativeOffset();
    var dimensions = element.getDimensions();
    this.element.setStyle({
      left: offset[0] + 'px',
      top: offset[1] + 'px',
      width: dimensions.width + 'px',
      height: dimensions.height + 'px',
      zIndex: element.getStyle('zIndex') - 1
    }).show();
    return this;
  },
  setBounds: function(bounds) {
    for (var prop in bounds) {
      bounds[prop] += 'px';
    }
    this.element.setStyle(bounds);
    return this;
  },
  destroy: function() {
    if (this.element) {
      this.element.remove();
    }
    return this;
  }
});

isEmpty = function(obj) {
  var hasOwnProperty = Object.prototype.hasOwnProperty;
  // null and undefined are "empty"
  if (obj == null) return true;

  // Assume if it has a length property with a non-zero value
  // that that property is correct.
  if (obj.length > 0)    return false;
  if (obj.length === 0)  return true;

  // Otherwise, does it have any properties of its own?
  // Note that this doesn't handle
  // toString and valueOf enumeration bugs in IE < 9
  for (var key in obj) {
    if (hasOwnProperty.call(obj, key)) return false;
  }

  return true;
};