/* $Id: plugin.js $
 *
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision: $
 * @author 
 *
 * Activate or deactivate autocapitalisation
 */

CKEDITOR.plugins.add('mbcap', {
  init: function(editor) {
    editor.addCommand('mbcap', {exec: mbcap_onclick});
    editor.ui.addButton('mbcap', {label:'Majuscule automatique en d�but de phrase', command:'mbcap',
         icon:'../../modules/dPcompteRendu/fcke_plugins/mbcap/images/icon.png' });
    editor.on("instanceReady", function() {
      // On regarde la pr�f�rence
      if (Preferences.auto_capitalize == "1") {
        mbcap_onclick(editor);
      }
    });
  }
});

function mbcap_onclick(editor) {
  var command = editor.getCommand('mbcap');

  command.setState(command.state == CKEDITOR.TRISTATE_ON ? CKEDITOR.TRISTATE_OFF : CKEDITOR.TRISTATE_ON);
}

function insertUpperCase(editor, event, keystroke) {
  // insert 'A' instead of 'a' (example)
  editor.insertText(String.fromCharCode(keystroke).toUpperCase());
  event.data.preventDefault();
}
