/* $Id: fckplugin.js $
 *
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision: $
 * @author S�bastien Fillonneau
 *
 */

// Define the commande name
var sMbPrintName = "mbPrint";

//Defines command class
var FCKMbPrintCommand = function() {
  this.Name = "mbPrint";
}

FCKMbPrintCommand.prototype.Execute = function() {
  var printDoc = function() {   
    FCKeditorAPI.__Instances._source.ToolbarSet.Items[0].Disable();
    FCK.EditorWindow.focus();
    FCK.EditorWindow.print();
    setTimeout(function(){FCKeditorAPI.__Instances._source.ToolbarSet.Items[0].Enable() }, 5000);
  };
  if (window.parent.same_print == 1) {
    var content = FCKeditorAPI.Instances._source.GetHTML();
    var form = window.parent.document.forms["download-pdf-form"];
    form.elements.content.value = encodeURIComponent(content);
    form.onsubmit();
  }
  else {
    if (window.parent.Preferences.saveOnPrint == 2 || confirm("Souhaitez-vous enregistrer ce document ?")) {
      window.parent.submitCompteRendu(printDoc);
    }
    else {
      printDoc();
    }
  }
}

FCKMbPrintCommand.prototype.GetState = function() {
  return FCK_TRISTATE_OFF ;
}

//Registers command object
var oCommand = new FCKMbPrintCommand();
FCKCommands.RegisterCommand("mbPrint", oCommand);

//Defines toolbar item class
var FCKToolbarMbPrint = function() {
  this.Command = FCKCommands.GetCommand("mbPrint");
  this.commandName = "mbPrint";
}

//***********************************


// Inherit from FCKToolbarButton.
FCKToolbarMbPrint.prototype = new FCKToolbarButton("mbPrint", FCKLang.mbPrint) ;

FCKToolbarMbPrint.prototype.GetLabel = function() {
  return "mbPrint" ;
}

var oMbPrintItem = new FCKToolbarMbPrint ;

oMbPrintItem.IconPath = sMbPluginsPath + 'mbprint/images/mbprint.gif';

// Registers toolbar item object
FCKToolbarItems.RegisterItem("mbPrint", oMbPrintItem) ;
