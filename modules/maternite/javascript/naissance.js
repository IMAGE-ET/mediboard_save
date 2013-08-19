/**
 * $Id$
 *
 * @category Maternite
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

Naissance = {
  edit : function(naissance_id, operation_id, sejour_id, provisoire, callback) {
    var url = new Url('maternite', 'ajax_edit_naissance');
    url.addParam('naissance_id', naissance_id);
    if (operation_id) {
      url.addParam('operation_id', operation_id);
    }
    if (sejour_id) {
      url.addParam('sejour_id', sejour_id);
    }
    if (provisoire) {
      url.addParam('provisoire', provisoire);
    }
    if (callback) {
      url.addParam("callback", callback);
    }
    url.requestModal(700, 400);
  },
  
  reloadNaissances : function(operation_id) {
    if (!$('naissance_area')) return;
    var url = new Url('maternite', 'ajax_vw_naissances');
    url.addParam('operation_id', operation_id);
    url.requestUpdate('naissance_area');
  },
  
  confirmDeletion: function(form) {
    var options = {
      typeName:'la naissance', 
      ajax: 1
    }
	    
    var ajax = {
      onComplete: function() {
        Control.Modal.close();
      }
    }
	    
    confirmDeletion(form, options, ajax);    
  },
  printDossier: function(sejour_id) {
    var url = new Url("dPhospi", "httpreq_documents_sejour");
    url.addParam("sejour_id", sejour_id);
    url.addParam("only_sejour", 1);
    url.requestModal(700, 400);
  }
}
