/* $Id: */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision:$
* @author SARL Openxtrem
*/

CKEDITOR.plugins.add('mbprinting',{
  requires: ['iframedialog'],
  init:function(editor){ 
   editor.addCommand('mbprinting', {exec: mbprinting_onclick});
   editor.ui.addButton('mbprinting', {label:'Imprimer par le serveur', command:'mbprinting',
     icon:'../../modules/dPcompteRendu/fcke_plugins/mbprinting/images/mbprinting.png' });
  }
});

function mbprinting_onclick(editor) {
  if (window.parent.nb_printers == 0) {
    if (window.parent.pdf_thumbnails == 1) {
      editor.execCommand("mbprintPDF");
    }
    else {
      alert("Aucune imprimante configur�e");
    }
    return;
  }
  window.parent.Thumb.print = 1;
  window.parent.openModalPrinters();
  window.parent.submitCompteRendu();
}

