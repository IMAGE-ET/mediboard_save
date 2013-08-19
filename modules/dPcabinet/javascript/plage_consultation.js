/* $Id: plage_consultation.js $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author SARL OpenXtrem
* @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
*/

PlageConsultation  = {
  status_images : ["images/icons/status_red.png", "images/icons/status_orange.png", "images/icons/status_green.png"],
  modal: null,
  
  edit: function(plageconsult_id, callback) {
    var url = new Url('cabinet', 'edit_plage_consultation');
    url.addParam('plageconsult_id', plageconsult_id);
    url.requestModal(800);
    this.modal = url.modalObject;
    if (callback) {
      url.modalObject.observe("afterClose", callback);
    }
  },
  
  onSubmit: function(form) {
    return onSubmitFormAjax(form, function() {
      PlageConsultation.refreshList();
      PlageConsultation.modal.close();
    });
  },
  
  checkForm: function(form) {
    if (!checkForm(form)) {
      return false;
    }

    if (form.nbaffected.value!= 0 && form.nbaffected.value != "") {
      if (form.debut.value > form._firstconsult_time.value || form.fin.value < form._lastconsult_time.value){
        if (!(confirm("Certaines consultations se trouvent en dehors de la plage de consultation.\n\nVoulez-vous appliquer les modifications ?"))){
          return false;
        }
      }  
    }

    //pour le compte de = chir sel
    if ($V(form.chir_id) == $V(form.pour_compte_id)) {
      alert("Vous ne pouvez pas cr�er une plage pour le compte de vous-m�me");
      return false;
    }

    // remplacement de soit m�me
    if ($V(form.chir_id) == $V(form.remplacant_id)) {
      alert("Vous ne pouvez pas vous remplacer vous-m�me");
      return false;
    }
          
    return true;
  },
  
  resfreshImageStatus : function(element){
    if (!element.get('id')) {
      return;
    }
  
    element.title = "";
    element.src   = "style/mediboard/images/icons/loading.gif";
    
    url.addParam("source_guid", element.get('guid'));
    url.requestJSON(function(status) {
      element.src = PlageConsultation.status_images[status.reachable];
      });
  }
};