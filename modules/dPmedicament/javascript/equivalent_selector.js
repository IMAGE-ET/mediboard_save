/* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPmedicament
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

var EquivSelector = {
  sForm     : null,
  sView     : null,
  sSearch   : null,
  sInLivret : null,
  sLine     : null,
  oUrl      : null,
  selfClose : true,
  options : {
    width : 700,
    height: 400
  },
  
  pop: function() {
    this.oUrl = new Url("dPmedicament", "vw_equivalents");
    this.oUrl.addParam("line_id", this.sLine);
    this.oUrl.addParam("inLivret", this.sInLivret);
    this.oUrl.popup(this.options.width, this.options.height, "Equivalent Selector");
  },
  
  set: function(code, line) {},
  
  close: function() {
    // Peut �tre appel� sans contexte : ne pas utiliser this
    EquivSelector.oUrl.close();
  }
};
