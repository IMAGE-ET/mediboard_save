{{*
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Maternite
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

<script>
  Main.add(function() {
    window.save_num_naissance = "{{$naissance->num_naissance}}";
    var form = getForm("newNaissance");
    var url = new Url("system", "ajax_seek_autocomplete");
    url.addParam("object_class", "CMediusers");
    url.addParam('show_view', true);
    url.addParam("input_field", "_prat_autocomplete");
    url.autoComplete(form.elements._prat_autocomplete, null, {
      minChars: 2,
      method: "get",
      select: "view",
      dropdown: true,
      afterUpdateElement: function(field,selected) {
        $V(field.form['praticien_id'], selected.getAttribute('id').split('-')[2]);
      },
      callback:  function(input, queryString) {
        if (form._only_pediatres.checked) {
          queryString += "&ljoin[spec_cpam]=spec_cpam.spec_cpam_id  = users_mediboard.spec_cpam_id";
          queryString += "&where[spec_cpam.text]=PEDIATRE";
        }
        return queryString;
      }
    });
  });

  toggleNumNaissance = function(fausse_couche) {
    $V(fausse_couche.form.num_naissance, $V(fausse_couche) == "inf_15" ? "" : window.save_num_naissance);
  }
</script>

<form name="newNaissance" method="post" action="?"
  onsubmit="return onSubmitFormAjax(this, function() { Control.Modal.close(); Naissance.reloadNaissances('{{$operation_id}}'); })">
  <input type="hidden" name="m" value="maternite" />
  <input type="hidden" name="del" value="0" />
  {{mb_field object=$naissance field=sejour_maman_id hidden=true}}
  {{mb_field object=$naissance field=operation_id hidden=true}}
  <input type="hidden" name="praticien_id" value="{{$sejour->praticien_id}}" />
  
  {{if $provisoire}}
    <input type="hidden" name="dosql" value="do_dossier_provisoire_aed" />
  {{else}}
    <input type="hidden" name="dosql" value="do_create_naissance_aed" />
  {{/if}}
  {{if $callback}}
    <input type="hidden" name="callback" value="{{$callback}}" />
  {{/if}}
  
  {{if $naissance}}
    {{mb_key object=$naissance}}
  {{/if}}
 
  {{if $parturiente}} 
    {{mb_key object=$parturiente}}
  {{/if}}

  {{if $constantes}} 
    {{mb_key object=$constantes}}
  {{/if}}
  
  <table class="form">
    <tr>
      <th class="category" colspan="4">
        Informations sur la naissance
      </th>
    </tr>
    <tr>
      <th>
        {{mb_label object=$patient field="sexe"}}
      </th>
      <td>
        {{mb_field object=$patient field="sexe" emptyLabel="CPatient.sexe."}}
      </td>
      <th>
        {{mb_label object=$naissance field="num_naissance"}}
      </th>
      <td>
        {{mb_field object=$naissance field="num_naissance" size="2" increment="true" form="newNaissance" step="1"}}
      </td>
    </tr>
    <tr>
      <th>
        {{mb_label object=$patient field="naissance"}}
      </th>
      <td>
        {{mb_field object=$patient field="naissance" form="newNaissance" register="true"}}
      </td>
      <th>
        {{mb_label object=$naissance field="fausse_couche"}}
      </th>
      <td>
        {{mb_field object=$naissance field="fausse_couche" emptyLabel="Choose" onchange="toggleNumNaissance(this)"}}
      </td>
    </tr>
    <tr>
      <th>
        {{mb_label object=$patient field="nom"}}
      </th>
      <td>
        {{mb_field object=$patient field="nom"}}
      </td>
      <th>
        {{mb_label object=$naissance field="by_caesarean"}}
      </th>
      <td>
        {{mb_field object=$naissance field="by_caesarean"}}
      </td>
    </tr>
    
    {{if !$provisoire}}
      <tr>
        <th>
          {{mb_label object=$patient field="prenom"}}
        </th>
        <td>
          {{mb_field object=$patient field="prenom"}}
        </td>
        <td colspan="2"></td>
      </tr>
      <tr>
        <th>
         {{mb_label object=$naissance field="hors_etab"}}
        </th>
        <td>
          {{mb_field object=$naissance field="hors_etab"}}
        </td>
        <td colspan="2"></td>
      </tr>
      {{if $naissance->_id}}
        <tr>
          <th>
            {{mb_label object=$naissance field="date_time"}}
          </th>
          <td>
            {{mb_field object=$naissance field="date_time" form="newNaissance" register="true"}}
          </td>
          <td colspan="2"></td>
        </tr>
      {{else}}
        <tr>
          <th>
           {{mb_label object=$naissance field="_heure"}}
          </th>
          <td>
            {{mb_field object=$naissance field="_heure" form="newNaissance" register="true"}}
          </td>
          <td colspan="2"></td>
        </tr>
      {{/if}}
    {{/if}}
    <tr>
      <th>
       {{mb_label object=$naissance field="rang"}}
      </th>
      <td>
        {{mb_field object=$naissance field="rang" size="2" increment="true" form="newNaissance" step="1"}}
      </td>
      <td colspan="2"></td>
    </tr>
    <tr>
      <th>
        {{mb_label object=$naissance field="rques"}}
      </th>
      <td colspan="3">
        {{mb_field object=$naissance field="rques" form="newNaissance"}}
      </td>
    </tr>
    <tr>
      <th>
        {{mb_label object=$sejour field=praticien_id}}
      </th>
      <td>
        <input type="text" name="_prat_autocomplete" value="{{$sejour->_ref_praticien}}" />
        <label>
          <input type="checkbox" name="_only_pediatres" checked="checked" />
          {{tr}}CNaissance-only_pediatres{{/tr}}
        </label>
      </td>
      <td colspan="2"></td>
    </tr>
    {{if !$provisoire}}
      <tr>
        <th class="category" colspan="4">{{tr}}CConstantesMedicales{{/tr}}</th>
      </tr>
      <tr>
        <th>
         {{mb_label object=$constantes field=poids}}
        </th>
        <td>
          {{mb_field object=$constantes field=poids size="3"}} {{$list_constantes.poids.unit}}
        </td>
        <td colspan="2"></td>
      </tr>
      <tr>
        <th>
         {{mb_label object=$constantes field=taille}}
        </th>
        <td>
          {{mb_field object=$constantes field=taille size="3"}} {{$list_constantes.taille.unit}}
        </td>
        <td colspan="2"></td>
      </tr>
      <tr>
        <th>
         {{mb_label object=$constantes field=perimetre_cranien}}
        </th>
        <td>
          {{mb_field object=$constantes field=perimetre_cranien size="3"}} {{$list_constantes.perimetre_cranien.unit}}
        </td>
        <td colspan="2"></td>
      </tr>
    {{/if}}
    <tr>
      <td class="button" colspan="4">
        {{if $naissance->_id}}
          <button type="submit" class="submit">{{tr}}Modify{{/tr}}</button>
        {{else}}
          <button type="submit" class="submit singleclick">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>