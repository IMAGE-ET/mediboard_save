{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if     $_sejour->type == 'ambu'}} {{assign var=background value="#faa"}}
{{elseif $_sejour->type == 'comp'}} {{assign var=background value="#fff"}}
{{elseif $_sejour->type == 'exte'}} {{assign var=background value="#afa"}}
{{elseif $_sejour->type == 'consult'}} {{assign var=background value="#cfdfff"}}
{{else}}
{{assign var=background value="#ccc"}}
{{/if}}

{{assign var="patient" value=$_sejour->_ref_patient}}

<td style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{if $canAdmissions->edit}}
  {{if $conf.dPplanningOp.COperation.verif_cote}}
  {{foreach from=$_sejour->_ref_operations item=curr_op}}
    {{if $curr_op->cote == "droit" || $curr_op->cote == "gauche"}}
      <form name="editCoteOp{{$curr_op->_id}}" action="?" method="post">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="dosql" value="do_planning_aed" />
        {{mb_key object=$curr_op}}
        {{mb_label object=$curr_op field="cote_admission"}} :
        {{mb_field emptyLabel="Choose" object=$curr_op field="cote_admission" onchange="submitCote(this.form);"}}
      </form>
      <br />
    {{/if}}
  {{/foreach}}
  {{/if}}
  <form name="editAdmFrm{{$_sejour->_id}}" action="?" method="post">
  <input type="hidden" name="m" value="dPplanningOp" />
  <input type="hidden" name="dosql" value="do_sejour_aed" />
  <input type="hidden" name="sejour_id" value="{{$_sejour->_id}}" />
  <input type="hidden" name="patient_id" value="{{$_sejour->patient_id}}" />
  {{if "web100T"|module_active}}
    {{mb_include module=web100T template=inc_button_iframe}}
  {{/if}}
  {{if !$_sejour->entree_reelle}}
    <input type="hidden" name="entree_reelle" value="now" />
    <button class="tick" type="button" onclick="{{if (($date_actuelle > $_sejour->entree_prevue) || ($date_demain < $_sejour->entree_prevue))}}confirmation(this.form);{{else}}submitAdmission(this.form);{{/if}};">
      {{tr}}CSejour-admit{{/tr}}
    </button>
    
  {{else}}
    <input type="hidden" name="entree_reelle" value="" />
    <button class="cancel" type="button" onclick="submitAdmission(this.form);">
      {{tr}}Cancel{{/tr}}
    </button>
    <br />
  
    {{if ($_sejour->entree_reelle < $date_min) || ($_sejour->entree_reelle > $date_max)}}
      {{$_sejour->entree_reelle|date_format:$conf.datetime}}
      <br>
    {{else}}
    {{$_sejour->entree_reelle|date_format:$conf.time}}
    {{/if}}
  {{/if}}
  </form>
  {{elseif $_sejour->entree_reelle}}
    {{if ($_sejour->entree_reelle < $date_min) || ($_sejour->entree_reelle > $date_max)}}
      {{$_sejour->entree_reelle|date_format:$conf.datetime}}
      <br>
    {{else}}
      {{$_sejour->entree_reelle|date_format:$conf.time}}
    {{/if}}
  {{else}}
    -
  {{/if}}
</td>

<td colspan="2" class="text" style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{if $canPlanningOp->read}}
  <a class="action" style="float: right" title="Modifier le s�jour" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$_sejour->_id}}">
    <img src="images/icons/planning.png" />
  </a>
    {{foreach from=$_sejour->_ref_operations item=_op}}
    <a class="action" style="float: right" title="Imprimer la DHE de l'intervention" href="#printDHE" onclick="printDHE('operation_id', {{$_op->_id}}); return false;">
      <img src="images/icons/print.png" />
    </a>
    {{foreachelse}}
    <a class="action" style="float: right" title="Imprimer la DHE du s�jour" href="#printDHE" onclick="printDHE('sejour_id', {{$_sejour->_id}}); return false;">
      <img src="images/icons/print.png" />
    </a>
    {{/foreach}}

    {{if $conf.dPadmissions.show_deficience}}
      <span style="float: right;">
        {{mb_include module=patients template=inc_vw_antecedents type=deficience}}
      </span>
    {{/if}}
  {{/if}}
  
  {{if $patient->_ref_IPP}}
  <script type="text/javascript">
    PatHprimSelector.init{{$patient->_id}} = function(){
      this.sForm      = "editIPP{{$patient->_id}}";
      this.sId        = "id400";
      this.sPatNom    = "{{$patient->nom}}";
      this.sPatPrenom = "{{$patient->prenom}}";
      this.pop();
    };
  </script>
  <form name="editIPP{{$patient->_id}}" action="?m={{$m}}" method="post">
    <input type="hidden" name="dosql" value="do_idsante400_aed" />
    <input type="hidden" name="m" value="dPsante400" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="ajax" value="1" />
    <input type="hidden" name="id_sante400_id" value="{{$patient->_ref_IPP->_id}}" />
    <input type="hidden" class="notNull" name="id400" value="{{$patient->_ref_IPP->id400}}" />
    <input type="hidden" class="notNull" name="tag" value="{{$patient->_ref_IPP->tag}}" />
    <input type="hidden" class="notNull" name="object_id" value="{{$patient->_id}}" />
    <input type="hidden" class="notNull" name="object_class" value="CPatient" />
    <input type="hidden" name="last_update" value="{{$patient->_ref_IPP->last_update}}" />
  </form>
  
  <script type="text/javascript">
    SejourHprimSelector.init{{$_sejour->_id}} = function(){
      this.sForm      = "editNumdos{{$_sejour->_id}}";
      this.sId        = "id400";
      this.sIPPForm   = "editIPP{{$patient->_id}}";
      this.sIPPId     = "id400";
      this.sIPP       = document.forms.editIPP{{$patient->_id}}.id400.value;
      this.sPatNom    = "{{$patient->nom}}";
      this.sPatPrenom = "{{$patient->prenom}}";
      this.pop();
    };
  </script>
  {{if $_sejour->_ref_NDA}}
  <form name="editNumdos{{$_sejour->_id}}" action="?m={{$m}}" method="post" onsubmit="return ExtRefManager.submitNumdosForm({{$_sejour->_id}})">
    <input type="hidden" name="dosql" value="do_idsante400_aed" />
    <input type="hidden" name="m" value="dPsante400" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="ajax" value="1" />
    <input type="hidden" name="id_sante400_id" value="{{$_sejour->_ref_NDA->_id}}" />
    <input type="hidden" class="notNull" name="id400" value="{{$_sejour->_ref_NDA->id400}}" size="8" />
    <input type="hidden" class="notNull" name="tag" value="{{$_sejour->_ref_NDA->tag}}" />
    <input type="hidden" class="notNull" name="object_id" value="{{$_sejour->_id}}" />
    <input type="hidden" class="notNull" name="object_class" value="CSejour" />
    <input type="hidden" class="notNull" name="sejour_id" value="{{$_sejour->_id}}" />
    <input type="hidden" name="last_update" value="{{$_sejour->_ref_NDA->last_update}}" />
    {{if @$modules.hprim21}}
      <button type="button" class="edit notext" onclick="setExternalIds(this.form)">Edit external Ids</button>
    {{/if}}
  </form>
  {{/if}}
  {{/if}}
  {{mb_include module=planningOp template=inc_vw_numdos nda=$_sejour->_NDA _doss_id=$_sejour->_id}}
  <span class="CPatient-view" onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}');">
    {{$patient}}
  </span>
</td>

<td class="text" style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_sejour->_ref_praticien}}
</td>

<td style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}');">
    {{$_sejour->entree_prevue|date_format:$conf.time}} 
    <br />
		{{$_sejour->type|upper|truncate:1:"":true}}
    {{$_sejour->_ref_operations|@count}} Int.
  </span>
</td>

<td class="text" style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{if !($_sejour->type == 'exte') && !($_sejour->type == 'consult') && $_sejour->annule != 1}}
    {{if $canAdmissions->edit}}
      <form name="editChFrm{{$_sejour->sejour_id}}" action="?" method="post">
      <input type="hidden" name="m" value="dPplanningOp" />
      <input type="hidden" name="dosql" value="do_sejour_aed" />
      <input type="hidden" name="sejour_id" value="{{$_sejour->sejour_id}}" />
      <input type="hidden" name="patient_id" value="{{$_sejour->patient_id}}" />
      {{if $_sejour->chambre_seule}}
        <input type="hidden" name="chambre_seule" value="0" />
        <button class="change" type="button" style="color: #f22 !important" onclick="submitAdmission(this.form, 1);">
          Chambre simple
        </button>
      {{else}}
        <input type="hidden" name="chambre_seule" value="1" />
        <button class="change" type="button" onclick="submitAdmission(this.form, 1);">
          Chambre double
        </button>
      {{/if}}
      </form>
    
      <!-- Prestations -->
      {{if $prestations}}
      <form name="editPrestFrm{{$_sejour->sejour_id}}" method="post">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="dosql" value="do_sejour_aed" />
        <input type="hidden" name="sejour_id" value="{{$_sejour->sejour_id}}" />
        <input type="hidden" name="patient_id" value="{{$_sejour->patient_id}}" />
        <select name="prestation_id" onchange="submitFormAjax(this.form, 'systemMsg')">
        <option value="">&mdash; Prestation</option>
        {{foreach from=$prestations item="_prestation"}}
          <option value="{{$_prestation->_id}}" {{if $_sejour->prestation_id==$_prestation->_id}} selected = selected {{/if}}>{{$_prestation->_view}}</option>
        {{/foreach}}
        </select>
      </form>
      {{/if}}
    {{else}}
      {{if $_sejour->chambre_seule}}
        Simple
      {{else}}
        Double
      {{/if}}
      {{if $_sejour->prestation_id && $prestations}}
        {{assign var=_prestation_id value=$_sejour->prestation_id}}
        <br />
        Prest. {{$prestations.$_prestation_id}}
      {{/if}}
    {{/if}}
    {{mb_include module=hospi template=inc_placement_sejour sejour=$_sejour}}
  {{/if}}  
</td>

<td style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{if $canAdmissions->edit}}
    <form name="editSaisFrm{{$_sejour->_id}}" action="?" method="post">
      <input type="hidden" name="m" value="dPplanningOp" />
      <input type="hidden" name="dosql" value="do_sejour_aed" />
      <input type="hidden" name="sejour_id" value="{{$_sejour->_id}}" />
    	<input type="hidden" name="patient_id" value="{{$_sejour->patient_id}}" />
      
      {{if !$_sejour->entree_preparee}}
        <input type="hidden" name="entree_preparee" value="1" />
        <button class="tick" type="button" onclick="submitAdmission(this.form, 1);">
          {{tr}}CSejour-entree_preparee{{/tr}}
        </button>
      {{else}}
        <input type="hidden" name="entree_preparee" value="0" />
        <button class="cancel" type="button" onclick="submitAdmission(this.form, 1);">
          {{tr}}Cancel{{/tr}}
        </button>
      {{/if}}
      
      {{if ($_sejour->entree_modifiee == 1) && ($conf.dPplanningOp.CSejour.entree_modifiee == 1)}}
        <img src="images/icons/warning.png" title="Le dossier a �t� modifi�, il faut le pr�parer" />
      {{/if}}
    </form>
  {{else}}
    {{mb_value object=$_sejour field="entree_preparee"}}
  {{/if}}
</td>

<td style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{foreach from=$_sejour->_ref_operations item=_op}}
  {{if $_op->_ref_consult_anesth->_id}}
  <div class="{{if $_op->_ref_consult_anesth->_ref_consultation->chrono == 64}}small-success{{else}}small-info{{/if}}" style="margin: 0px;">
    <span onmouseover="ObjectTooltip.createEx(this, '{{$_op->_ref_consult_anesth->_guid}}');">
    {{$_op->_ref_consult_anesth->_date_consult|date_format:$conf.date}}
    </span>
  </div>
  {{/if}}
  {{/foreach}}
</td>

<td style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}" class="button">
  {{if $_sejour->_couvert_cmu}}
  <div><strong>CMU</strong></div>
  {{/if}}
  {{if $_sejour->_couvert_ald}}
  <div><strong {{if $_sejour->ald}}style="color: red;"{{/if}}>ALD</strong></div>
  {{/if}}
</td>

{{if $conf.dPadmissions.show_dh}}
  <td style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
    {{foreach from=$_sejour->_ref_operations item=_op}}
    {{if $_op->_ref_actes_ccam|@count}}
    <span style="color: #484;">
    {{foreach from=$_op->_ref_actes_ccam item=_acte}}
      {{if $_acte->montant_depassement}}
        {{if $_acte->code_activite == 1}}
        Chir :
        {{elseif $_acte->code_activite == 4}}
        Anesth :
        {{else}}
        Activit� {{$_acte->code_activite}} :
        {{/if}}
        {{mb_value object=$_acte field=montant_depassement}}
        <br />
      {{/if}}
    {{/foreach}}
    </span>
    {{/if}}
    {{if $_op->depassement}}
    <!-- Pas de possibilit� d'imprimer les d�passements pour l'instant -->
    <!-- <a href="#" onclick="printDepassement({{$_sejour->sejour_id}})"></a> -->
    Pr�vu : {{mb_value object=$_op field="depassement"}}
    <br />
    {{/if}}
    {{foreachelse}}
    -
    {{/foreach}}
  </td>
{{/if}}