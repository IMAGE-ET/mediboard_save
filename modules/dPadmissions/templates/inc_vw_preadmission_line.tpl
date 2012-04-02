{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var="patient" value=$curr_consult->_ref_patient}}
{{assign var="curr_adm" value=$curr_consult->_ref_consult_anesth->_ref_sejour}}
{{if !$curr_adm->_id}}
{{if $curr_consult->_next_sejour_and_operation.COperation->_id}}
{{assign var="curr_adm" value=$curr_consult->_next_sejour_and_operation.COperation->_ref_sejour}}
{{assign var="type_event" value="COperation"}}
{{else}}
{{assign var="curr_adm" value=$curr_consult->_next_sejour_and_operation.CSejour}}
{{assign var="type_event" value="CSejour"}}
{{/if}}
{{/if}}

<td class="text">
  {{if $curr_adm->_id && !$curr_adm->annule && $curr_consult->_ref_consult_anesth->_ref_sejour->_id}}
  {{foreach from=$curr_adm->_ref_operations item=curr_op}}
  <a class="action" style="float: right" title="Imprimer la DHE de l'intervention" href="#1" onclick="printDHE('operation_id', {{$curr_op->_id}}); return false;">
    <img src="images/icons/print.png" />
  </a>
  {{foreachelse}}
  <a class="action" style="float: right" title="Imprimer la DHE du s�jour" href="#1" onclick="printDHE('sejour_id', {{$curr_adm->_id}}); return false;">
    <img src="images/icons/print.png" />
  </a>
  {{/foreach}}
  {{/if}}
  <span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}');">
    {{$patient->_view}}
  </span>
  </a>
</td>
<td class="text">
  <div class="{{if $curr_consult->chrono == 64}}small-success{{else}}small-info{{/if}}" style="margin: 0px;">
  {{$curr_consult->heure|date_format:$conf.time}}
  -
  {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$curr_consult->_ref_plageconsult->_ref_chir}}
  </div>
</td>

{{if $curr_adm->_id}}

{{if $curr_adm->type == 'ambu'}} {{assign var=background value="#faa"}}
{{elseif $curr_adm->type == 'comp'}} {{assign var=background value="#fff"}}
{{elseif $curr_adm->type == 'exte'}} {{assign var=background value="#afa"}}
{{elseif $curr_adm->type == 'urg'}} {{assign var=background value="#ff6"}}
{{else}}
{{assign var=background value="#ccc"}}
{{/if}}

<td class="text" style="background: {{$background}}; {{if !$curr_adm->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$curr_adm->_ref_praticien}}
</td>
<td class="text" style="background: {{$background}}; {{if !$curr_adm->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{mb_include module=planningOp template=inc_vw_numdos nda=$curr_adm->_NDA _doss_id=$curr_adm->_id}}
  <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_adm->_guid}}');">
  le {{$curr_adm->_entree|date_format:$conf.date}}
  </span>
</td>
{{if !$curr_adm->annule && $curr_consult->_ref_consult_anesth->_ref_sejour->_id}}
<td class="text" style="background: {{$background}}; {{if !$curr_adm->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
{{if $canAdmissions->edit}}
  <form name="editChFrm{{$curr_adm->sejour_id}}" action="?" method="post">
  
  <input type="hidden" name="m" value="dPplanningOp" />
  <input type="hidden" name="dosql" value="do_sejour_aed" />
  <input type="hidden" name="sejour_id" value="{{$curr_adm->sejour_id}}" />
  <input type="hidden" name="patient_id" value="{{$curr_adm->patient_id}}" />
  {{if $curr_adm->chambre_seule}}
  <input type="hidden" name="chambre_seule" value="0" />
  <button class="change" type="button" style="color: #f22 !important" onclick="submitPreAdmission(this.form, 1);">
    Chambre simple
  </button>
  {{else}}
  <input type="hidden" name="chambre_seule" value="1" />
  <button class="change" type="button" onclick="submitPreAdmission(this.form, 1);">
    Chambre double
  </button>
  {{/if}}
  </form>
  
  <!-- Prestations -->
  {{if $prestations}}
  <form name="editPrestFrm{{$curr_adm->sejour_id}}" method="post">
    <input type="hidden" name="m" value="dPplanningOp" />
    <input type="hidden" name="dosql" value="do_sejour_aed" />
    <input type="hidden" name="sejour_id" value="{{$curr_adm->sejour_id}}" />
    <input type="hidden" name="patient_id" value="{{$curr_adm->patient_id}}" />
  <select name="prestation_id" onchange="submitFormAjax(this.form, 'systemMsg')">
  <option value="">&mdash; Prestation</option>
  {{foreach from=$prestations item="_prestation"}}
    <option value="{{$_prestation->_id}}" {{if $curr_adm->prestation_id==$_prestation->_id}} selected = selected {{/if}}>{{$_prestation->_view}}</option>
  {{/foreach}}
  </select>
  </form>
  {{/if}}
  {{else}}
  {{if $curr_adm->chambre_seule}}
    Chambre simple
  {{else}}
    Chambre double
  {{/if}}
  {{if $curr_adm->prestation_id && $prestations}}
  {{assign var=_prestation_id value=$curr_adm->prestation_id}}
  <br />
  Prest. {{$prestations.$_prestation_id->_view}}
  {{/if}}
  {{/if}}
  
  {{mb_include module=hospi template=inc_placement_sejour sejour=$curr_adm}}
</td>

<td style="background: {{$background}}; {{if !$curr_adm->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{if $canAdmissions->edit}}
  <form name="editSaisFrm{{$curr_adm->_id}}" action="?" method="post">

  <input type="hidden" name="m" value="dPplanningOp" />
  <input type="hidden" name="dosql" value="do_sejour_aed" />
  <input type="hidden" name="sejour_id" value="{{$curr_adm->_id}}" />
	<input type="hidden" name="patient_id" value="{{$curr_adm->patient_id}}" />
  {{if !$curr_adm->saisi_SHS}}
  <input type="hidden" name="saisi_SHS" value="1" />
  <button class="tick" type="button" onclick="submitPreAdmission(this.form);">
    {{tr}}CSejour-saisi_SHS{{/tr}}
  </button>
  {{else}}
  <input type="hidden" name="saisi_SHS" value="0" />
  <button class="cancel" type="button" onclick="submitPreAdmission(this.form);">
    {{tr}}Cancel{{/tr}}
  </button>
  {{/if}}
  {{if ($curr_adm->modif_SHS == 1) && ($conf.dPplanningOp.CSejour.modif_SHS == 1)}}
    <img src="images/icons/warning.png" title="Le dossier a �t� modifi�, il faut le pr�parer" />
  {{/if}}
  </form>
  {{else}}
  {{mb_value object=$curr_adm field="saisi_SHS"}}
  {{/if}}
</td>

<td style="background: {{$background}}; {{if !$curr_adm->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{if $curr_adm->_couvert_cmu}}
    <img src="images/icons/tick.png" title="Droits CMU en cours" />
  {{else}}
    -
  {{/if}}
</td>

<td style="background: {{$background}}; {{if !$curr_adm->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{foreach from=$curr_adm->_ref_operations item=curr_op}}
  {{if $curr_op->depassement}}
  <!-- Pas de possibilit� d'imprimer les d�passements pour l'instant -->
  <!-- <a href="#" onclick="printDepassement({{$curr_adm->sejour_id}})"></a> -->
  {{mb_value object=$curr_op field="depassement"}}
  <br />
  {{/if}}
  {{foreachelse}}
  -
  {{/foreach}}
</td>
{{elseif $curr_adm->annule}}
<td colspan="4" class="cancelled">
  Annul�
</td>
{{else}}
<td colspan="4" class="button" style="background: {{$background}}; {{if !$curr_adm->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{if $type_event == "COperation"}}
  <a class="action" style="float: right" title="Imprimer la DHE de l'intervention" href="#1" onclick="printDHE('operation_id', {{$curr_consult->_next_sejour_and_operation.COperation->_id}}); return false;">
    <img src="images/icons/print.png" />
  </a>
  Intervention non associ� � la consultation
  {{if $canAdmissions->edit}}
  <br />
  <form name="addOpFrm-{{$curr_consult->_id}}" action="?m={{$m}}" method="post">
  <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="m" value="dPcabinet" />
  {{mb_key object=$curr_consult->_ref_consult_anesth}}
  <input type="hidden" name="operation_id" value="{{$curr_consult->_next_sejour_and_operation.COperation->_id}}" />
  <button type="submit" class="tick">
    Associer l'intervention
  </button>
  </form>
  {{/if}}
  {{else}}
  <a class="action" style="float: right" title="Imprimer la DHE du s�jour" href="#1" onclick="printDHE('sejour_id', {{$curr_adm->_id}}); return false;">
    <img src="images/icons/print.png" />
  </a>
  S�jour non associ� � la consultation
  {{if $canAdmissions->edit}}
  <br />
  <form name="addOpFrm-{{$curr_consult->_id}}" action="?m={{$m}}" method="post">
  <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="m" value="dPcabinet" />
  {{mb_key object=$curr_consult->_ref_consult_anesth}}
  <input type="hidden" name="sejour_id" value="{{$curr_adm->_id}}" />
  <button type="submit" class="tick">
    Associer le s�jour
  </button>
  </form>
  {{/if}}
  {{/if}}
</td>
{{/if}}
{{else}}
<td colspan="6" class="button">
  DHE non trouv�e
  {{if $canPlanningOp->edit}}
  :
  <a href="?m=dPplanningOp&amp;tab=vw_edit_planning&amp;pat_id={{$curr_consult->patient_id}}&amp;operation_id=0&amp;sejour_id=0" class="button new">
    Cr�er une demande d'hospitalisation
  </a>
  {{/if}}
</td>
{{/if}}