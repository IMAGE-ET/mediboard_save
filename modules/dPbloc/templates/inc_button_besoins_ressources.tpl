{{*
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage dPbloc
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

{{mb_default var=type value=operation_id}}
{{mb_default var=usage value=0}}
{{unique_id var=uniq_id}}

<script>
  // Dans le cas d'une intervention, il faut v�rifier
  // que pour chaque type de ressource exprim� par un besoin, une ressource
  // au moment de l'intervention est disponible.
  
  Main.add(function() {
    {{if $type == "operation_id"}}
      checkRessources("{{$object_id}}");
    {{/if}}
    window.besoins_non_stored = [];
  });

  editBesoins = function (object_id) {
    var url = new Url("bloc", "ajax_edit_besoins");
    url.addParam("type", "{{$type}}");
    url.addParam("object_id", object_id);
    url.addParam("usage", "{{$usage}}");
    url.requestModal(500, 380, {showReload: true});

    url.modalObject.observe("afterClose", function() {
      {{if !$object_id}}
        {{if $type == "operation_id"}}
          var form = getForm("editOp");
        {{elseif $type == "protocole_id"}}
          var form = getForm("editProtocole");
        {{/if}}
        if (form) {
          // Un protocole a pu �tre appliqu�, donc garder les besoins
          $V(form._types_ressources_ids, window.besoins_non_stored.join(","));
        }
      {{/if}}
      // Arpr�s fermeture de la modale, on r�actualise
      // la couleur de la bordure des boutons
      {{if $type == "operation_id"}}
        checkAllOps();
      {{/if}}
    });

  };
  
  synchronizeTypes = function(types) {
    window.besoins_non_stored = types.split(",");
  };

  checkAllOps = function() {
    $$(".bouton_materiel").each(function(button) {
      var object_id = button.get("object_id");
      checkRessources(object_id);
    });
  }

  checkRessources = function(object_id) {
    var url = new Url("bloc", "ajax_check_ressources");
    url.addParam("type", "{{$type}}");
    url.addParam("object_id", object_id);
    url.requestJSON(function(object) {
      // Ajout d'une bordure sur le bouton suivant l'�tat des besoins
      $$(".ressource_bouton_"+object_id).each(function(button) {
        button.setStyle({border: "2px solid #"+object.color});
        {{if $object_id}}
          button.down('span').update("("+object.count+")");
        {{/if}}
      });
    });
  }
</script>

<button type="button" class="search bouton_materiel ressource_bouton_{{$object_id}}" data-object_id="{{$object_id}}"
        onclick="editBesoins('{{$object_id}}');">Mat�riel <span></span></button>
