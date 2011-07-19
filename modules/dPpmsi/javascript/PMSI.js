/**
 * JS function PMSI
 *  
 * @category dPpmsi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */


PMSI = {
  exportActes : function(object_id, object_class, oOptions, confirmCloture, module){
	if ((confirmCloture == 1) && !confirm("L'envoi des actes cloturera d�finitivement le codage de cette intervention pour le chirurgien et l'anesth�siste." +
        "\nConfirmez-vous l'envoi en facturation ?")) {
      return;
	} 
	
	var oDefaultOptions = {
  	  onlySentFiles : false
    };
  
    Object.extend(oDefaultOptions, oOptions);
  
    var url = new Url("dPpmsi", "export_actes_pmsi");
    url.addParam("object_id", object_id);
    url.addParam("object_class", object_class);
    url.addParam("sent_files", oDefaultOptions.onlySentFiles ? 1 : 0);
  
    var oRequestOptions = {
      waitingText: oDefaultOptions.onlySentFiles ? 
  	    "Chargement des fichers envoy�s" : 
        "Export des actes..."
    };
    if (confirmCloture == 1) {
      oRequestOptions.onComplete = function() {
        PMSI.reloadActes(object_id, module);
      }
    }

    url.requestUpdate("export_" + object_class + "_" + object_id, oRequestOptions); 
  },
  
  deverouilleDossier : function(object_id, object_class, confirmCloture, module) {
    var url = new Url("dPpmsi", "ajax_refresh_export_actes_pmsi");
	url.addParam("object_id", object_id);
	url.addParam("object_class", object_class);
	
	var oRequestOptions = {
			waitingText: "D�v�rouillage du dossier..."
    };
	if (confirmCloture == 1) {
      oRequestOptions.onComplete = function() {
        PMSI.reloadActes(object_id, module);
      }
    }
	
	url.requestUpdate("export_" + object_class + "_" + object_id, oRequestOptions);     
  },
  
  reloadActes : function(operation_id, module) {
    var url = new Url("dPsalleOp", "ajax_refresh_actes");
	url.addParam("operation_id", operation_id);
	url.addParam("module", module);
	url.requestUpdate("codage_actes");
  }
};