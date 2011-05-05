{{* $Id: $ *}}
 
{{*
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
  var destinataires = window.opener.destinataires;
  {{if $type == "doc"}}
    var CKEDITOR = window.opener.CKEDITOR;
  {{/if}} 
  Main.add(function() {
    var tabMail = $("tabMail");
    destinataires.each(function(item) {
      var disabled = '';
      if (item.email == null || item.email.indexOf("@") == -1) {
        item.email = '';
        disabled = "disabled = 'disabled'";
      }
      tabMail.insert(
        "<tr>\
           <td>\
             <input type='radio' name='destinataire' " + disabled + "/>\
           </td>\
           <td>" + item.tag + "\
           </td>\
           <td>" + item.nom + "\
           </td>\
           <td>" + item.email + "\
           </td>\
         </tr>");
    });
    tabMail.insert(
      "<tr>\
         <td>\
           <input type='radio' name='destinataire'/>\
         </td>\
         <td>\
         Autre\
         </td>\
         <td colspan='2'><input type='text' name='dest' onfocus='this.up().previous().previous().down().checked = \"true\"' value=''>\
         </td>\
       </tr>");
  });

  sendMail = function() {
    var destinataire = document.body.select("input:checked");
  
    // S'il n'y a pas de destinataire coch�s ou si ce nombre est diff�rent de 1
    if (destinataire.length != 1) {
      alert("Veuillez choisir un destinataire.");
      return;
    } else {
      // Sinon on peut pr�parer l'envoi du mail
      destinataire = destinataire[0].up().next();
      var nom = destinataire.next().innerHTML;
      var email = '';
      
      if (destinataire.innerHTML.indexOf("Autre") != -1) {
        nom = '';
        email = destinataire.next().down().value;
      }
      else {
        email = destinataire.next().next().innerHTML;
      }
      // Test d'int�grit� de l'adresse mail
      if (email.indexOf('@') == -1) {
        alert('L\'adresse n\'est pas valide');
        return;
      }
      
      var url = new window.opener.Url("dPcompteRendu", "ajax_send_mail");
      url.addParam("nom", nom);
      url.addParam("email", email);
      url.addParam("type", '{{$type}}');
      {{if $type == "doc"}}
        url.addParam("content", CKEDITOR.instances.htmlarea.getData());
      {{else}}
        url.addParam("file_id", window.opener.file_id);
      {{/if}}
      window.opener.document.body.down("#systemMsg").style.display="block";
      url.requestUpdate(window.opener.document.body.down("#systemMsg"), {method: 'post', getParameters: {m: 'dPcompteRendu', a: 'ajax_send_mail'}});
      window.close();
    }
  }    
</script>

<form name="formSendMail" method="get">
  <div style="height: 200px; overflow: auto;">
    <table style="width: 100%;" id="tabMail" class="tbl">
      <tr>
        <th>
        </th>
        <th>
          Qualit�
        </th>
        <th>
          Nom
        </th>
        <th>
          Adresse mail
        </th>
      </tr>
    </table>
  </div>
  <div style="padding-top: 20px; width: 100%; text-align: center;">
    <button class="cancel" type="button" onclick="window.close();">{{tr}}Cancel{{/tr}}</button>
    <button class="tick" type="button" onclick="sendMail();">{{tr}}CCompteRendu.send_mail{{/tr}}</button>
  </div>
</form>