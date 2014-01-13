{{*
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage soins
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
*}}

{{if $print}}
  {{mb_include style=mediboard template=open_printable}}
{{else}}

  {{if $select_view}}
    <form name="editPrefVueSejour" method="post">
      <input type="hidden" name="m" value="admin" />
      <input type="hidden" name="dosql" value="do_preference_aed" />
      <input type="hidden" name="user_id" value="{{$app->user_id}}" />
      <input type="hidden" name="pref[vue_sejours]" value="standard" />
      <input type="hidden" name="postRedirect" value="m=soins&tab=vw_idx_sejour" />
      <button type="submit" class="change notext">Vue par d�faut</button>
    </form>
  {{/if}}

  <form name="TypeHospi" method="get" action="?">
    <input type="hidden" name="m" value="soins" />

    {{if $select_view}}

      <input type="hidden" name="tab" value="vw_sejours" />
    {{else}}
      <input type="hidden" name="a" value="vw_sejours" />
    {{/if}}

    <input type="hidden" name="show_affectation" value="{{$show_affectation}}" />
    <input type="hidden" name="only_non_checked" value="{{$only_non_checked}}" />

    {{if $select_view}}
      <input type="hidden" name="select_view" value="{{$select_view}}" />
      <select name="service_id" style="width: 200px;" onchange="this.form.praticien_id.value = ''; this.form.function_id.value = ''; this.form.submit();">
        <option value="">&mdash; Service</option>
        {{foreach from=$services item=_service}}
          <option value="{{$_service->_id}}" {{if $_service->_id == $service_id}}selected{{/if}}>{{$_service->_view}}</option>
        {{/foreach}}
      </select>

      <select name="praticien_id" style="width: 200px;" onchange="this.form.service_id.value = ''; this.form.function_id.value = ''; this.form.submit();">
        <option value="">&mdash; Praticien</option>
        {{foreach from=$praticiens item=_praticien}}
          <option value="{{$_praticien->_id}}" {{if $_praticien->_id == $praticien_id}}selected{{/if}}>{{$_praticien->_view}}</option>
        {{/foreach}}
      </select>

      <select name="function_id" style="width: 200px;" onchange="this.form.praticien_id.value = ''; this.form.service_id.value = ''; this.form.submit();">
        <option value="">&mdash; Cabinet</option>
        {{foreach from=$functions item=_function}}
          <option value="{{$_function->_id}}" {{if $_function->_id == $function_id}}selected{{/if}}>{{$_function->_view}}</option>
        {{/foreach}}
      </select>
      <br />
    {{else}}
      <input type="hidden" name="service_id" value="{{$service_id}}" />
      <input type="hidden" name="praticien_id" value="{{$praticien->_id}}" />
      <input type="hidden" name="function_id" value="{{$function->_id}}" />
    {{/if}}

    {{mb_label class="CSejour" field="_type_admission"}}
    <label>
      <input type="radio" name="_type_admission" value="" {{if !$_sejour->_type_admission}}checked{{/if}} onclick="this.form.submit()" /> Tous types
    </label>
    {{assign var=specs value=$_sejour->_specs._type_admission}}
    {{foreach from=$specs->_list item=_type}}
      <label>
        <input type="radio" name="_type_admission" value="{{$_type}}" {{if $_sejour->_type_admission == $_type}}checked{{/if}} onclick="this.form.submit()" />
        {{tr}}CSejour._type_admission.{{$_type}}{{/tr}}
      </label>
    {{/foreach}}
  </form>
{{/if}}

<table class="main tbl">
  <thead>
  <tr>
    <th class="title" colspan="14" {{if $print}}onclick="window.print();"{{/if}}>
      {{if !$print}}
        <button type="button" class="print notext" style="float: right;" onclick="printSejours();">{{tr}}Print{{/tr}}</button>
      {{/if}}
      {{if $service->_id}}
        S�jours du service {{$service}}
      {{elseif $function->_id}}
        S�jours du cabinet {{$function}}
      {{elseif $praticien->_id}}
        S�jours  du praticien {{$praticien}}
      {{else}}
        Patients non plac�s
      {{/if}}
      ({{$sejours|@count}})

      {{if $print}}
        <span style="font-weight: normal;"> - {{$smarty.now|date_format:$conf.datetime}}</span>
      {{/if}}
    </th>
  </tr>
  </thead>

  {{if !$print}}
    <tr>
      {{if $service->_id || $function->_id || $praticien->_id || $show_affectation}}
        <th rowspan="2">{{mb_title class=CLit field=chambre_id}}</th>
      {{/if}}
      <th colspan="2" rowspan="2">{{mb_title class=CPatient field=nom}}<br />({{mb_title class=CPatient field=nom_jeune_fille}})</th>
      {{if "dPImeds"|module_active}}
        <th rowspan="2">Labo</th>
      {{/if}}
      <th colspan="5">Alertes</th>
      <th rowspan="2" class="narrow">{{mb_title class=CSejour field=entree}}</th>
      <th rowspan="2">{{mb_title class=CSejour field=libelle}}</th>
      <th rowspan="2">Prat.</th>
      <th rowspan="2">Projet de soin<br />Demandes particuli�res</th>
    </tr>
    <tr>
      <th><label title="Modification de prescriptions">Presc.</label></th>
      <th><label title="Prescriptions urgentes">Urg.</label></th>
      <th>Attentes</th>
      <th>Allergies</th>
      <th><label title="Ant�c�dents">Atcd</label></th>
    </tr>
  {{/if}}

  {{foreach from=$sejours item=sejour}}

    {{if $print}}
      {{mb_include module=soins template=inc_vw_print_sejour}}

    {{else}}
      <tr id="line_sejour_{{$sejour->_id}}">
        {{mb_include module=soins template=inc_vw_sejour}}
      </tr>
    {{/if}}

    {{foreachelse}}
    <tr>
      <td colspan="15" class="empty">
        {{tr}}CSejour.none{{/tr}}
      </td>
    </tr>
  {{/foreach}}
</table>

{{if $print}}
  {{mb_include style=mediboard template=close_printable}}
{{/if}}