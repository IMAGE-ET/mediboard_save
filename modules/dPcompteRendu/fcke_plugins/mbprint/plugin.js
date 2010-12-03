/* $Id: ckplugin.js $
 *
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision: $
 * @author Sébastien Fillonneau
 *
 */

CKEDITOR.plugins.add('mbprint',{
  requires: ['iframedialog'],
  init:function(editor){ 
   editor.addCommand('mbprint', {exec: mbprint_onclick});
   editor.ui.addButton('mbprint', {label:'Imprimer (ancienne version)', command:'mbprint',
   	 icon:'../../modules/dPcompteRendu/fcke_plugins/mbprint/images/mbprint.gif' });
  }
});

function mbprint_onclick(editor) {
  var printDoc = function() {
    if ( CKEDITOR.env.gecko )
      editor.window.$.print();
    else
      editor.document.$.execCommand("Print");
  };
  if (window.parent.same_print == 1) {
    var content = editor.getData();
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

