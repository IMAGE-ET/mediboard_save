/* $Id: ckplugin.js $
 *
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision: $
 * @author S�bastien Fillonneau
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
  editor.getCommand('mbprint').setState(CKEDITOR.TRISTATE_DISABLED);
  var printDoc = function() {
    if ( CKEDITOR.env.gecko )
      editor.window.$.print();
    else
      editor.document.$.execCommand("Print");
  };
  // Mise � jour de la date d'impression
  $V(getForm("editFrm").date_print, "now");
  if (window.parent.same_print == 1) {
    var content = editor.getData();
    var form = getForm("download-pdf-form");
    form.elements.content.value = encodeURIComponent(content);
    form.onsubmit();
  }
  else {
    submitCompteRendu(printDoc);
  }
}

