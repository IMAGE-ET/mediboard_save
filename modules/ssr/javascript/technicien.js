/* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7795 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

Technicien = {
  edit: function(plateau_id, technicien_id) {
		new Url("ssr", "ajax_edit_technicien") .
		  addParam("technicien_id", technicien_id) .
			addParam("plateau_id", plateau_id) .
			requestUpdate("edit-techniciens");
  },
  	
  onSubmit: function(form) {
    return onSubmitFormAjax(form, { 
		  onComplete: Technicien.edit.curry($V(form.plateau_id), '0')
		} );
  },
	
	confirmTransfer: function(form, count) {
		var select = form._transfer_id;
		var option = select.options[select.selectedIndex];
		if (option.value == '') {
       Element.getLabel(select).addClassName('error');
			 return false;
		}

		return confirm($T('CTechnicien-_transfer_id-confirm', count, option.innerHTML));
	},
	
	updateTab: function(count) {
		var tab = $("tab-techniciens");
    tab.down("a").setClassName("empty", !count);
    tab.down("a small").update("("+count+")");
	}
};