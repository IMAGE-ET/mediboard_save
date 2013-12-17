{{*
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage dPcabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

{{assign var=dossiers_anesth value=$consult->_refs_dossiers_anesth}}

{{mb_default var=onlycreate value=false}}

<script type="text/javascript">
  createDossier = function(consult_id, duplicate) {
    var form = getForm("manageDossierAnesth");
    $V(form.del, 0);
    form.consultation_id.writeAttribute("disabled", null);
    $V(form.consultation_id, consult_id);
    if (duplicate == 1) {
      $V(form.dosql, "do_duplicate_dossier_anesth_aed");
    }
    form.submit();
  };

  delDossier = function(dossier_anesth_id) {
    var form = getForm("manageDossierAnesth");
    form.consultation_id.writeAttribute("disabled", "disabled");
    $V(form.consultation_anesth_id, dossier_anesth_id);
    confirmDeletion(form, {
      typeName: 'le dossier d\'anesth�sie'
    });
  };
  reloadDossierAnesth = function(dossier_anesth_id) {
    var url = new Url("cabinet", Preferences.new_consultation == "1" ? "vw_consultation" : "edit_consultation", "tab");
    url.addParam("selConsult", "{{$consult->_id}}");
    url.addParam("dossier_anesth_id", dossier_anesth_id);
    url.redirect();
  }
</script>

<!-- Formulaire de cr�ation / suppression de dossier d'anesth�sie -->
<form name="manageDossierAnesth" action="?" method="post">
  <input type="hidden" name="m" value="cabinet" />
  <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="consultation_anesth_id" />
  <input type="hidden" name="consultation_id" disabled/>
  {{if $app->user_prefs.new_consultation}}
    <input type="hidden" name="postRedirect" value="m=cabinet&tab=vw_consultation&selConsult={{$consult->_id}}" />
  {{else}}
    <input type="hidden" name="postRedirect" value="m=cabinet&tab=edit_consultation&selConsult={{$consult->_id}}" />
  {{/if}}
  {{if $dossiers_anesth|@count}}
    <select name="_consult_anesth_id" style="width: 20em;" onchange="reloadDossierAnesth(this.value)">
      {{foreach from=$dossiers_anesth item=_dossier}}
        {{assign var=_op value=$_dossier->_ref_operation}}
        <option value="{{$_dossier->_id}}" {{if $consult_anesth->_id == $_dossier->_id}}selected{{/if}}>
          Dossier d'anesth�sie du {{$_dossier->_ref_consultation->_ref_plageconsult->date|date_format:$conf.date}}
          {{if $_op->_id}}(Intervention du {{$_op->_datetime|date_format:$conf.date}}){{/if}}
        </option>
      {{/foreach}}
    </select>
    <button type="button" class="trash notext" title="Supprimer le dossier d'anesth�sie"
            onclick="delDossier($V(this.form._consult_anesth_id))">{{tr}}Delete{{/tr}}</button>
  {{/if}}
  <button type="button" class="add {{if !$onlycreate}}notext{{/if}}" onclick="createDossier('{{$consult->_id}}')">Ajouter un dossier d'anesth�sie</button>
  {{if $dossiers_anesth|@count}}
    <button type="button" class="add" title="Dupliquer le dossier courant" onclick="createDossier('{{$consult->_id}}', 1)">Dupliquer</button>
  {{/if}}
</form>