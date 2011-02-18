/* $Id$ */

/**
 * @package Mediboard
 * @subpackage includes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

var ElementChecker = {
  aProperties    : {},
  oElement       : null,
  oForm          : null,
  oLabel         : null,
  sLabel         : null,
  
  sTypeSpec      : null,
  oTargetElement : null,
  oCompare       : null,
  oErrors        : [],
  sValue         : null,

  prepare : function(oElement){
    this.oElement = oElement;
    
    var isArray  = (!oElement.options && (Object.isArray(oElement) || Object.isElement(oElement[0])));
    oElement = $(isArray ? oElement[0] : oElement);

    this.oForm = oElement.form;
    this.oProperties = oElement.getProperties();
    
    this.oLabel = Element.getLabel(oElement);
    this.sLabel = this.oLabel ? this.oLabel.innerHTML : oElement.name;
    
    if (this.oProperties.mask) {
      this.oProperties.mask = this.oProperties.mask.gsub('S', ' ').gsub('P', '|');
    }
    this.oErrors = [];
    this.sValue = (this.oProperties.mask ? 
                     this.oElement.getFormatted(this.oProperties.mask, this.oProperties.format) : 
                     $V(this.oElement));
                     
    Object.extend(this.check, this);
  },
  
  //---- Assertion functions, to check the number of arguments for each property type
  assertMultipleArgs: function(prop, multiplicity) {
    if (Object.isUndefined(this.oProperties[prop])) return false;
    Assert.that(this.oProperties[prop] !== true, '"'+prop+'" n�cessite '+multiplicity+((multiplicity != null) ? multiplicity : 'un ou plusieurs')+' argument(s)');
    this.oProperties[prop] = [this.oProperties[prop]].flatten();
    return this.oProperties[prop];
  },
  
  assertSingleArg: function(prop) {
    if (Object.isUndefined(this.oProperties[prop])) return false;
    Assert.that(((typeof this.oProperties[prop] != "boolean") && !Object.isArray(this.oProperties[prop])), '"'+prop+'" n�cessite un et un seul argument');
    
    var val = [this.oProperties[prop]].flatten();
    val = val.length > 1 ? val : val[0];
    this.oProperties[prop] = val;
    return this.oProperties[prop];
  },
  
  assertNoArg: function(prop) {
    if (Object.isUndefined(this.oProperties[prop])) return false;
    Assert.that(this.oProperties[prop] == true, '"'+prop+'" ne doit pas avoir d\'arguments');
    this.oProperties[prop] = true;
    return this.oProperties[prop];
  },
  //---------------------------------------------------------------------------------
  
  getCastFunction: function() {
  	if (this.oProperties["num"])   return function(value) { return parseInt(value, 10); }
		if (this.oProperties["float"]) return function(value) { return parseFloat(value, 10); }
		if (this.oProperties["date"])  return function(value) { return Date.fromDATE(value); }
		return Prototype.K;
  },
  
  castCompareValues: function(sTargetElement) {
    this.oTargetElement = this.oElement.form.elements[sTargetElement];
    if (!this.oTargetElement) {
      return printf("El�ment cible pour comparaison invalide ou inexistant (nom = %s)", sTargetElement);
		}
    
    var fCaster = this.getCastFunction();
  	this.oCompare = {
      source : this.sValue               ? fCaster(this.sValue) : null,
      target : this.oTargetElement.value ? fCaster(this.oTargetElement.value) : null
  	}
  	return null;
  },
  
  addError: function(prop, message) {
    if (!message) return true;
    if (!this.oErrors.find(function (e) {return e.type == prop})) {
      this.oErrors.push({type: prop, message: message});
    }
    return false;
  },

  getErrorMessage: function() {
    var msg = "";
    this.oErrors.each(function (error) {
      msg += "   - "+error.message+"\n";
    });
    return msg;
  },
  
  checkElement : function() {
    if (this.oProperties.notNull || (this.sValue && !this.oProperties.notNull)) {
      $H(this.oProperties).each(function (prop) {
        if (this.check[prop.key])
          this.addError(prop.key, this.check[prop.key]());
      }, this);
    }

    // Free DOM element references
    this.oElement = null;
    this.oForm = null;
    this.oLabel = null;
    this.oTargetElement = null;
    this.oCompare = null;
    
    return this.oErrors;
  }
};

Object.extend(ElementChecker, {
  check: {
    // toNumeric
    toNumeric: function () {
      this.sValue = new String(this.sValue).replace(/\s/g, '').replace(/,/, '.');
      
      if (isNaN(this.sValue))
        this.addError("toNumeric", "N'est pas dans un format num�rique valide");
    },
    
    // notNull
    notNull: function () {
      this.assertNoArg("notNull");
      if (this.sValue == "")
        this.addError("notNull", "Ne doit pas �tre vide");
    },
    
    // moreThan
    moreThan: function () {
      var sTargetElement = this.assertSingleArg("moreThan");
      this.addError("moreThan", this.castCompareValues(sTargetElement));
      
      if (this.oCompare && this.oCompare.source && this.oCompare.target && (this.oCompare.source <= this.oCompare.target))
        this.addError("moreThan", printf("'%s' n'est pas strictement sup�rieur � '%s'", this.sValue,  this.oTargetElement.value));
    },
    
    // moreEquals
    moreEquals: function () {
      var sTargetElement = this.assertSingleArg("moreEquals");
      this.addError("moreEquals", this.castCompareValues(sTargetElement));
      
      if (this.oCompare && this.oCompare.source && this.oCompare.target && (this.oCompare.source < this.oCompare.target))
        this.addError("moreEquals", printf("'%s' n'est pas sup�rieur ou �gal � '%s'", this.sValue,  this.oTargetElement.value));
    },
    
    // sameAs
    sameAs: function () {
      var sTargetElement = this.assertSingleArg("sameAs");
      this.addError("sameAs", this.castCompareValues(sTargetElement));
      
      if (this.oCompare && this.oCompare.source && this.oCompare.target && (this.oCompare.source != this.oCompare.target)) {
        var oTargetLabel = Element.getLabel(this.oTargetElement);
        var sTargetLabel = oTargetLabel ? oTargetLabel.innerHTML : this.oTargetElement.name;
        this.addError("sameAs", printf("Doit �tre identique � [%s]", sTargetLabel.strip()));
      }
    },
    
    // notContaining
    notContaining: function () {
      var sTargetElement = this.assertSingleArg("notContaining");
      this.addError("notContaining", this.castCompareValues(sTargetElement));

      if (this.oCompare && this.oCompare.source && this.oCompare.target && this.oCompare.source.match(this.oCompare.target)) {
        var oTargetLabel = Element.getLabel(this.oTargetElement);
        var sTargetLabel = oTargetLabel ? oTargetLabel.innerHTML : '"'+this.oCompare.target+'"';
        this.addError("notContaining", printf("Ne doit pas contenir [%s]", sTargetLabel.strip()));
      }
    },
    
    // notNear
    notNear: function () {
      var sTargetElement = this.assertSingleArg("notNear");
      this.addError("notNear", this.castCompareValues(sTargetElement));
      
      if (this.oCompare && this.oCompare.source && this.oCompare.target && levenshtein(this.oCompare.target, this.oCompare.source) < 3) {
        var oTargetLabel = Element.getLabel(this.oTargetElement);
        var sTargetLabel = oTargetLabel ? oTargetLabel.innerHTML : '"'+this.oCompare.target+'"';
        this.addError("notNear", printf("Ressemble trop � [%s]", sTargetLabel.strip()));
      }
    },
    
    // alphaAndNum
    alphaAndNum: function () {
      this.assertNoArg("alphaAndNum");
      if (!this.sValue.match(/[A-z]/) || !this.sValue.match(/\d+/))
        this.addError("alphaAndNum", "Doit contenir au moins une lettre et un chiffre");
    },
    
    // length
    length: function () { 
      this.assertSingleArg("length");
      var iLength = parseInt(this.oProperties["length"], 10);
      
      if (iLength < 1 || iLength > 255)
        console.error(printf("Sp�cification de longueur invalide (longueur = %s)", iLength));
  
      if (this.sValue.length != iLength)
        this.addError("length", printf("N'a pas la bonne longueur (longueur souhait�e : %s)", iLength));
    },
    
    // minLength
    minLength: function () { 
      this.assertSingleArg("minLength");
      var iLength = parseInt(this.oProperties["minLength"], 10);
      
      if (iLength < 1 || iLength > 255)
        console.error(printf("Sp�cification de longueur minimale invalide (longueur = %s)", iLength));
  
      if (this.sValue.length < iLength)
        this.addError("minLength", printf("N'atteint pas la bonne longueur (longueur souhait�e : %s)", iLength));
    },
    
    // maxLength
    maxLength: function () { 
      this.assertSingleArg("maxLength");
      var iLength = parseInt(this.oProperties["maxLength"], 10);
      
      if (iLength < 1 || iLength > 255)
        console.error(printf("Sp�cification de longueur maximale invalide (longueur = %s)", iLength));
  
      if (this.sValue.length > iLength)
        this.addError("maxLength", printf("D�passe la bonne longueur (longueur souhait�e : %s)", iLength));
    },
    
    // delimiter
    delimiter: function () { 
      this.assertSingleArg("delimiter");
      var sDelimiter = String.fromCharCode(parseInt(this.oProperties["maxLength"], 10));
      if (this.sValue.split(sDelimiter).indexOf("") != -1)
        this.addError("delimiter", printf("Contient des valeurs vides '%s'", this.sValue));
    },
    
    // canonical
    canonical: function(){
      this.assertNoArg("canonical");
      if (!this.sValue.match(/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/))
        this.addError("canonical", "Ne doit contenir que des chiffres et des lettres non-accentu�es (pas d'espaces)");
    },
    
    // pos
    pos: function () {
      this.assertNoArg("pos");
      this.toNumeric();
      
      if (this.sValue <= 0)
        this.addError("pos", "Doit �tre une valeur positive");
    },
    
    // min
    min: function () {
      this.assertSingleArg("min");
      this.toNumeric();
      
      var iMin = parseInt(this.oProperties["min"], 10);
      if (this.sValue < iMin)
        this.addError("min", printf("Doit avoir une valeur minimale de %s", iMin));
    },
    
    // max
    max: function () {
      this.assertSingleArg("max");
      this.toNumeric();
      
      var iMax = parseInt(this.oProperties["max"], 10);
      if (this.sValue > iMax)
        this.addError("max", printf("Doit avoir une valeur maximale de %s", iMax));
    },
    
    // ccam
    ccam: function() {
      this.assertNoArg("ccam");
      if (!this.sValue.match(/^[A-Z]{4}[0-9]{3}(-[0-9](-[0-9])?)?$/i))
        this.addError("ccam", "Code CCAM incorrect");
    },
    
    // cim10
    cim10: function () {
      this.assertNoArg("cim10");
      if (!this.sValue.match(/^[a-z][0-9x]{2,4}$/i))
        this.addError("cim10", "Code CIM incorrect, doit contenir une lettre, puis de 2 � 4 chiffres ou la lettre X");
    },
    
    // adeli
    adeli: function() {
      this.assertNoArg("adeli");
      if (!this.sValue.match(/^([0-9]){9}$/i))
        this.addError("adeli", "Code Adeli incorrect, doit contenir exactement 9 chiffres");
    },
    
    // insee
    insee: function () {
      this.assertNoArg("insee");
      if (this.sValue.match(/^([0-9]{7,8}[A-Z])$/i))
        return;
      
      var aMatches = this.sValue.match(/^([12478][0-9]{2}[0-9]{2}[0-9][0-9ab][0-9]{3}[0-9]{3})([0-9]{2})$/i);
      
      if (aMatches) {
        var nCode = parseInt(aMatches[1].replace(/2A/i, '19').replace(/2B/i, '18'), 10);
        var nCle  = parseInt(aMatches[2], 10);
        if (97 - (nCode % 97) != nCle)
          this.addError("insee", "Matricule incorrect, la cl� n'est pas valide");
        else return;
      }
  
      this.addError("insee", "Matricule incorrect");
    },
    
    // order number
    product_order: function () {
      this.assertNoArg("product_order");
      if (this.sValue.indexOf("%id") == -1)
        this.addError("produc_order", "Le num�ro de commande doit contenir %id");
    },
    
    // siret
    siret: function () {
      this.assertNoArg("siret");
      if (!luhn(this.sValue))
        this.addError("siret", "Code SIRET incorrect");
    },
    
    // rib
    rib: function () {
      this.assertNoArg("rib");
      // TODO: implement this
    },
    
    // list
    list: function() {
      var list = this.assertMultipleArgs("list");
			
			// If it is a "set"
			if (this.oProperties["set"]) {
				var values = this.sValue.split('|'),
				    intersect = list.intersect(values);
						
				if (intersect.length != values.length) {
	        this.addError("list", "Contient une valeur invalide possible");
				}
			}
			else {
				if (!this.sValue || (this.sValue && list.indexOf(this.sValue) == -1)) 
					this.addError("list", "N'est pas une valeur possible");
			}
    },
    
    ///////// Data types ////////////
    // ref
    ref: function() {
      this.notNull();
      this.pos();
    },
    
    // str
    str: function () {},
    
    // numchar
    numchar: function() {
      this.num();
    },
    
    // num
    num: function() {
      this.toNumeric();
    },
    
    // bool
    bool: function() {
      this.toNumeric();
      if(this.sValue != 0 && this.sValue != 1)
        this.addError("bool", "Ne peut �tre diff�rent de 0 ou 1");
    },
    
    // enum
    "enum": function() {
      if (!this.oProperties.list && !this.oProperties['class']) {
        console.error("Sp�cification 'list' ou 'class' manquante pour le champ " + this.sLabel);
        return;
      }
    },
    
    // set
    "set": function() {
      if (!this.oProperties.list) {
        console.error("Sp�cification 'list' manquante pour le champ " + this.sLabel);
        return;
      }
    },
    
    birthDate: function() {
      this.date();
      var values = this.sValue.match(/^(\d{4})-(\d{1,2})-(\d{1,2})$/);
      if (values) {
        if (parseInt(values[1]) < 1850) {
          this.addError("birthDate", "L'ann�e est inf�rieure � 1850");
        }
        if (this.sValue > new Date().toDATE()) {
          this.addError("birthDate", "La date de naissance ne doit pas �tre dans le futur");
        }
	      if (parseInt(values[3]) > 31 || parseInt(values[2]) > 12) {
	        var msg = printf("Le champ '%s' correspond � une date au format lunaire (jour '%s' et mois '%s')",
	          this.sLabel,
	          values[3],
	          values[2]
	        );
	         
	        // Attention, un seul printf() ne fonctionne pas
	        msg += ".\n\nVoulez vous n�anmoins sauvegarder ?";
	        
	        if (!confirm(msg)) {
	          this.addError("birthDate", "N'a pas un format de date correct");
	        }
	      }
	    }
    },
    
    // date
    date: function() {
    	if (["now", "current"].include(this.sValue)) {
    		return;
    	}
      
      if(this.sValue == "0000-00-00" && this.oProperties.notNull)
        this.addError("date", "N'est pas une date correcte");

      if (!this.sValue.match(/^\d{4}-\d{1,2}-\d{1,2}$/))
        this.addError("date", "N'a pas un format de date correct");
    },
    
    // time
    time: function() {
    	if (["now", "current"].include(this.sValue)) {
    		return;
    	}

      if(!this.sValue.match(/^\d{1,2}:\d{1,2}(:\d{1,2})?$/))
        this.addError("time", "N'a pas un format d'heure correct");
    },
    
    // dateTime
    dateTime: function() {
    	if (["now", "current"].include(this.sValue)) {
    		return;
    	}
    	
      if (!this.sValue.match(/^\d{4}-\d{1,2}-\d{1,2}[ \+]\d{1,2}:\d{1,2}(:\d{1,2})?$/))
        this.addError("dateTime", "N'a pas un format de date/heure correct");
    },
    
    // float
    'float': function() {
      this.toNumeric();
      
      if (parseFloat(this.sValue) != this.sValue)
        this.addError("float", "N'est pas une valeur d�cimale");
    },
    
    // currency
    currency: function() {
      this['float']();
    },
    
    // pct
    pct: function() {
      this.toNumeric();
			
      if (!this.sValue.match(/^\d+(\.\d+)?$/))
        this.addError("pct", "N'est pas une valeur d�cimale");
    },
    
    // text
    text: function() {
      this.str();
    },
  
    // html
    html: function() {
      this.str();
    },
    
    // url // (http|https|ftp)?(www\.)?([\w*])\.[a-zA-Z]{2,3}[/]?$
    url: function() {
      var regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
      if (!this.sValue.match(regexp))
         this.addError("url", "Le format de l'url n'est pas valide");
    },
    
    // mask
    mask: function() {
      this.str();
    },
    
    // email
    email: function() {
      if (!this.sValue.match(/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/))
        this.addError("email", "Le format de l'email n'est pas valide");
    },
    
    // code
    code: function() {
      if (!(this.oProperties.ccam || this.oProperties.cim10 || this.oProperties.adeli || this.oProperties.insee || 
            this.oProperties.product_order || this.oProperties.siret || this.oProperties.rib))
      this.addError("code", "Sp�cification de code invalide");
    },
    
    // password
    password: function() {
      this.str();
    },

    // regex avec ou sans modificateurs
    // par exemple : regex|^\s*[a-zA-Z][a-zA-Z0-9_]*\s*$|gi
    // On peut mettre des pipe dans la regex avec \x7C ou des espaces avec \x20
    regex: function(){
      this.assertMultipleArgs("regex");
      var re = new RegExp(this.oProperties.regex[0], this.oProperties.regex[1]);
      
      if (!this.sValue.match(re))
        this.addError("regex", "Ne respecte pas le format attendu"); // Inutile de mettre la regex dans le message
    }
  }
});

/***************/

function checkForm(oForm) {
  oForm = $(oForm);
  
  var oElementFirstFailed = null;
  var oFormErrors = [];
  
  // For each element in the form
  oForm.getElementsEx().each(function (oElement) {
    if (!oElement || oElement.disabled) return;
    
    var isArray = (!oElement.options && (Object.isArray(oElement) || Object.isElement(oElement[0])));
    var oFirstElement = isArray ? oElement[0] : oElement;

    if (!oFirstElement.className ||
        oFirstElement.getAttribute("readonly") ||
        oFirstElement.hasClassName("nocheck")) return;
    
    // Element checker preparing and error checking
    ElementChecker.prepare(oElement);
    var sMsgFailed = ElementChecker.sLabel || printf("%s (val:'%s', spec:'%s')", oFirstElement.name, $V(oElement), oFirstElement.className);
    var oLabel     = ElementChecker.oLabel;
    var oErrors    = ElementChecker.checkElement(); // will reset all ElementChecker's properties
    
    // If errors, we append them to the error object
    if (oErrors.length) {
      oFormErrors.push({
        title: sMsgFailed,
        element: oFirstElement.name, 
        errors: oErrors
      });
      if (!oElementFirstFailed && (oFirstElement.type != "hidden") && !oFirstElement.readonly && !oFirstElement.disabled) oElementFirstFailed = oFirstElement;
      if (oLabel) oLabel.addClassName('error');
    }
    else {
      if (oLabel) oLabel.removeClassName('error');
    }
  });
  
  // Check for form-level errors (xor)
  var xorFields, 
      re = /xor(?:\|(\S+))+/g;
      
  while (xorFields = re.exec(oForm.className)) {
    xorFields = xorFields[1].split("|");
    
    var n = 0, 
        xorFieldsInForm = 0,
        listLabels = [];
        
    xorFields.each(function(xorField){
      var element = $(oForm.elements[xorField]);
      if (!element) return;
      xorFieldsInForm++;
      var label = Element.getLabel(element);
      listLabels.push(label ? label.innerHTML : xorField);
      if ($V(element)) n++;
    });
    if (n != 1 && xorFieldsInForm > 0) {
      oFormErrors.push({
        title: "Vous devez choisir une et une seule valeur parmi",
        element: "Formulaire", 
        errors: listLabels
      });
    }
  }
  
  if (oFormErrors.length) {
    var sMsg = "Merci de remplir/corriger les champs suivants : \n";
    oFormErrors.each(function (formError) {
      var oElement = oForm.elements[formError.element];
      
      sMsg += "  "+String.fromCharCode(8226)+" "+formError.title.strip()+":\n";
      formError.errors.each(function (error) {
        sMsg += "     - " + (error.message || error).strip() + "\n";
      });
    });
    alert(sMsg);
    
    if (oElementFirstFailed && oElementFirstFailed.type != "hidden") {
      oElementFirstFailed.select();
    }
    return false;
  }
  FormObserver.changes = 0;
  return true;
}

/** Validation d'un element de formulaire. 
  * Est utile pour la validation lors de la saisie du formulaire.
  */
function checkFormElement(oElement) {
	ElementChecker.prepare(oElement);
	
	// Recuperation de l'element HTML qui accueillera le message.
	var oMsg = $(oElement.id+'_message');
	if (oMsg && ElementChecker.oProperties.password) {
    ElementChecker.checkElement();
		if (ElementChecker.oErrors.length) {
			oMsg.innerHTML = 'S�curit� trop faible : <br />'+ElementChecker.getErrorMessage().gsub("\n", "<br />");
			oMsg.style.backgroundColor = '#FF7A7A';
		} 
		else {
			oMsg.innerHTML = 'S�curit� correcte';
			oMsg.style.backgroundColor = '#33FF66';
		}
	}
	if (oElement.value == '') {
		oMsg.innerHTML = '';
		oMsg.style.background = 'none';
	}
	return true;
}
