{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Calendar.regField(getForm("changeDateSorties").date, null, {noView: true});
</script>

<table class="tbl" id="sortie">
  <tr>
    <th class="title" colspan="10">
      <a href="?m=dPadmissions&tab=vw_idx_sortie&date={{$hier}}" style="display: inline"><<<</a>
      {{$date|date_format:$conf.longdate}}
      <form name="changeDateSorties" action="?" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="vw_idx_sortie" />
        <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
      </form>
      <a href="?m=dPadmissions&tab=vw_idx_sortie&date={{$demain}}" style="display: inline">>>></a>
      
      <br />
      
      <em style="float: left; font-weight: normal;">
      {{$sejours|@count}}
      {{if $selSortis == "n"}}sorties non effectu�es
      {{else}}sorties ce jour
      {{/if}}
      </em>
  
      <select style="float: right" name="filterFunction" style="width: 16em;" onchange="reloadSorties($V(getForm('selType')._type_admission), this.value);">
        <option value=""> &mdash; Toutes les fonctions</option>
        {{foreach from=$functions item=_function}}
          <option value="{{$_function->_id}}" {{if $_function->_id == $filterFunction}}selected="selected"{{/if}} class="mediuser" style="border-color: #{{$_function->color}};">{{$_function}}</option>
        {{/foreach}}
      </select>
      
      {{if $type == "ambu"}}
      <button class="print" type="button" onclick="printAmbu()">Impression ambu</button>
      {{/if}}
    </th>
  </tr>
  
  {{assign var=url value="?m=$m&tab=vw_idx_sortie&selSortis=$selSortis"}}
  <tr>
    <th>Effectuer la sortie</th>
    <th>
      {{mb_colonne class="CSejour" field="patient_id" order_col=$order_col order_way=$order_way url="$url"}}
    </th>
    <th class="narrow">
      <input type="text" size="3" onkeyup="Admissions.filter(this, 'sortie')" id="filter-patient-name" />
    </th>
    <th>
      {{mb_colonne class="CSejour" field="praticien_id" order_col=$order_col order_way=$order_way url="?$url"}}
    </th>
    <th>
      {{mb_colonne class="CSejour" field="sortie_prevue" order_col=$order_col order_way=$order_way url="$url"}}
    </th>
    <th>Chambre</th>
  </tr>
  
  {{foreach from=$sejours item=_sejour}}
  {{if $_sejour->type == 'ambu'}} {{assign var=background value="#faa"}}
  {{elseif $_sejour->type == 'comp'}} {{assign var=background value="#fff"}}
  {{elseif $_sejour->type == 'exte'}} {{assign var=background value="#afa"}}
  {{elseif $_sejour->type == 'consult'}} {{assign var=background value="#cfdfff"}}
  {{else}}
  {{assign var=background value="#ccc"}}
  {{/if}}
  <tr>
    <td class="text" style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
      {{if $canAdmissions->edit}}
      <form name="editFrm{{$_sejour->_id}}" action="?m={{$m}}" method="post">
      <input type="hidden" name="m" value="dPplanningOp" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_sejour_aed" />
      <input type="hidden" name="sejour_id" value="{{$_sejour->_id}}" />
      <input type="hidden" name="type" value="{{$_sejour->type}}" />
      
      {{if $_sejour->sortie_reelle}}
      <input type="hidden" name="mode_sortie" value="{{$_sejour->mode_sortie}}" />
      <input type="hidden" name="etablissement_transfert_id" value="{{$_sejour->etablissement_transfert_id}}" />
      <input type="hidden" name="_modifier_sortie" value="0" />
      <button class="cancel" type="button" onclick="submitSortie(this.form)">
        Annuler la sortie
      </button>
      <br />
      {{if ($_sejour->sortie_reelle < $date_min) || ($_sejour->sortie_reelle > $date_max)}}
        {{$_sejour->sortie_reelle|date_format:$conf.datetime}}
      {{else}}
        {{$_sejour->sortie_reelle|date_format:$conf.time}}
      {{/if}}
      - {{tr}}CSejour.mode_sortie.{{$_sejour->mode_sortie}}{{/tr}}
      {{if $_sejour->etablissement_transfert_id}}
        - {{$_sejour->_ref_etabExterne->_view}}
      {{/if}}
      {{else}}
      <input type="hidden" name="_modifier_sortie" value="1" />
      <input type="hidden" name="entree_reelle" value="{{$_sejour->entree_reelle}}" />
      <button class="tick" type="button" onclick="confirmation('{{$date_actuelle}}', '{{$date_demain}}', '{{$_sejour->sortie_prevue}}', '{{$_sejour->entree_reelle}}', this.form);">
        Effectuer la sortie
      </button>
      <br />  
      {{mb_field object=$_sejour field="mode_sortie" onchange="this.form._modifier_sortie.value = '0'; submitSortie(this.form);"}}
      <br />
      <div id="listEtabExterne-editFrm{{$_sejour->_id}}" style="display: inline;"></div>
      <script type="text/javascript">
        loadTransfert(document.editFrm{{$_sejour->_id}});
      </script>
      {{/if}}
      </form>
      {{elseif $_sejour->sortie_reelle}}
      {{if ($_sejour->sortie_reelle < $date_min) || ($_sejour->sortie_reelle > $date_max)}}
        {{$_sejour->sortie_reelle|date_format:$conf.datetime}}
      {{else}}
        {{$_sejour->sortie_reelle|date_format:$conf.time}}
      {{/if}}
      {{if $_sejour->mode_sortie}}
      <br />
      {{tr}}CSejour.mode_sortie.{{$_sejour->mode_sortie}}{{/tr}}
      {{/if}}
      {{if $_sejour->etablissement_transfert_id}}
        <br />{{$_sejour->_ref_etabExterne->_view}}
      {{/if}}
      {{else}}
      -
      {{/if}}
    </td>
    
    <td class="text CPatient-view" colspan="2" style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
      {{if $canPatients->edit}}
      <a class="action" style="float: right"  title="Modifier le dossier administratif" href="?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id={{$_sejour->_ref_patient->patient_id}}">
        <img src="images/icons/edit.png" title="{{tr}}Edit{{/tr}}" />
     </a>
     {{/if}}
     {{if $canPlanningOp->read}}
     <a class="action" style="float: right"  title="Modifier le s�jour" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$_sejour->_id}}">
       <img src="images/icons/planning.png" title="{{tr}}Edit{{/tr}}" />
     </a>
      {{/if}}
      {{mb_include module=dPplanningOp template=inc_vw_numdos num_dossier=$_sejour->_num_dossier}}
      <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_ref_patient->_guid}}');">
        {{$_sejour->_ref_patient->_view}}
      </span>
    </td>
    <td class="text" style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
      {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_sejour->_ref_praticien}}
    </td>
    <td style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
      <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}');">
        {{if ($_sejour->sortie_prevue < $date_min) || ($_sejour->sortie_prevue > $date_max)}}
          {{$_sejour->sortie_prevue|date_format:$conf.datetime}}
        {{else}}
          {{$_sejour->sortie_prevue|date_format:$conf.time}}
        {{/if}}
      </span>
      {{if $_sejour->_ref_last_affectation->confirme}}
        <img src="images/icons/tick.png" title="Sortie confirm�e par le praticien" />
      {{/if}}
        
    </td>
    <td class="text" style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
      {{if !($_sejour->type == 'exte') && !($_sejour->type == 'consult') && $_sejour->annule != 1}}
        {{foreach from=$_sejour->_ref_affectations item=_aff}}
          {{if $_aff->effectue}}
            <div style="display: inline;" class="effectue">{{$_aff->_ref_lit->_view}}</div>
          {{else}}
            {{$_aff->_ref_lit->_view}}
          {{/if}}
          <br />
        {{/foreach}}
        
        {{if !$_sejour->_ref_affectations|@count}}
          Non plac�
        {{/if}}
       {{/if}}  
    </td>
  </tr>
  {{/foreach}}
</table>