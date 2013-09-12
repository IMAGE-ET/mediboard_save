/* $Id: plugin.js $
 *
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision: $
 * @author 
 *
 * Display or hide a header in the editor
 */

CKEDITOR.plugins.add('mbheader',{
  requires: ['dialog'],
  init: function(editor) {
    editor.addCommand('mbheader', {exec: mbheader_onclick});
    editor.ui.addButton('mbheader', {label: 'Ent�te', command: 'mbheader',
      	 icon:'../../modules/dPcompteRendu/fcke_plugins/mbheader/images/icon.gif' });
    editor.on("instanceReady", function() {
      // On regarde la pr�sence d'un ent�te dans la source une fois que l'�diteur est pr�t.
      // S'il y a un ent�te, alors on coche le bouton, sinon on le d�sactive.
      if (window.parent.document.getElementById('htmlarea').innerHTML.indexOf("header") != -1) {
        editor.getCommand('mbheader').setState(CKEDITOR.TRISTATE_ON);
        return;
      }
      editor.getCommand('mbheader').setState(CKEDITOR.TRISTATE_DISABLED);
    });
  }
});

function mbheader_onclick(editor) {
  var oHeader = editor.document.getById("header");
  if (!oHeader) return;
  if (oHeader.$.style.display == "block" || oHeader.$.style.display == "") {
    oHeader.setStyle("display", "none");
    editor.getCommand('mbheader').setState(CKEDITOR.TRISTATE_OFF);
    return;
  }
  oHeader.setStyle("display",  "block");
  editor.getCommand('mbheader').setState(CKEDITOR.TRISTATE_ON);
}
