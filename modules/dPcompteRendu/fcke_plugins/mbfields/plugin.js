/* $Id: ckplugin.js $
 *
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision: $
 * @author S�bastien Fillonneau
 *
 */

CKEDITOR.plugins.add('mbfields',{
  requires: ['iframedialog'],
  init:function(editor){
  CKEDITOR.dialog.add('mbfields_dialog', function () {
    return {
    title : 'Ins�rer un champ',
    buttons: [
    {
       id: 'close_button',
       type: 'button',
       title: 'Fermer',
       label: "Fermer",
       onClick: function(e) { CKEDITOR.dialog.getCurrent().hide(); }
     }
  ],
    minWidth : 565,
    minHeight : 235,
    contents :
    [
      {
        id : 'iframe',
        label : 'Insertion de champs',
        expand : true,
        elements :
          [
            {
              type : 'iframe',
              src : 'modules/dPcompteRendu/fcke_plugins/mbfields/dialogs/fields.html',
              width : 565,
              height : 235
            }
          ]
     }
   ]
   };
   });
   
   editor.addCommand('mbfields', {exec: mbfields_onclick});
   editor.ui.addButton('mbfields', {label:'Champs', command:'mbfields',
     icon:'../../modules/dPcompteRendu/fcke_plugins/mbfields/images/mbfields.png' });
  }
});

function mbfields_onclick(editor) {
  CKEDITOR.instances.htmlarea.openDialog('mbfields_dialog');
}
