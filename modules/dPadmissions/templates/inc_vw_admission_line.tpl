{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $curr_adm->annule == 1}} {{assign var=background value="#f00"}}
{{elseif $curr_adm->type == 'ambu'}} {{assign var=background value="#faa"}}
{{elseif $curr_adm->type == 'comp'}} {{assign var=background value="#fff"}}
{{elseif $curr_adm->type == 'exte'}} {{assign var=background value="#afa"}}
{{elseif $curr_adm->type == 'consult'}} {{assign var=background value="#cfdfff"}}
{{else}}
{{assign var=background value="#ccc"}}
{{/if}}

{{assign var="patient" value=$curr_adm->_ref_patient}}

<td colspan="2" style="background: {{$background}}; {{if !$curr_adm->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{if $canPlanningOp->read}}
  <a class="action" style="float: right" title="Modifier le s�jour" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$curr_adm->_id}}">
    <img src="images/icons/planning.png" />
  </a>
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
    SejourHprimSelector.init{{$curr_adm->_id}} = function(){
      this.sForm      = "editNumdos{{$curr_adm->_id}}";
      this.sId        = "id400";
      this.sIPPForm   = "editIPP{{$patient->_id}}";
      this.sIPPId     = "id400";
      this.sIPP       = document.forms.editIPP{{$patient->_id}}.id400.value;
      this.sPatNom    = "{{$patient->nom}}";
      this.sPatPrenom = "{{$patient->prenom}}";
      this.pop();
    };
  </script>
  {{if $curr_adm->_ref_numdos}}
  <form name="editNumdos{{$curr_adm->_id}}" action="?m={{$m}}" method="post" onsubmit="return ExtRefManager.submitNumdosForm({{$curr_adm->_id}})">
    <input type="hidden" name="dosql" value="do_idsante400_aed" />
    <input type="hidden" name="m" value="dPsante400" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="ajax" value="1" />
    <input type="hidden" name="id_sante400_id" value="{{$curr_adm->_ref_numdos->_id}}" />
    <input type="hidden" class="notNull" name="id400" value="{{$curr_adm->_ref_numdos->id400}}" size="8" />
    <input type="hidden" class="notNull" name="tag" value="{{$curr_adm->_ref_numdos->tag}}" />
    <input type="hidden" class="notNull" name="object_id" value="{{$curr_adm->_id}}" />
    <input type="hidden" class="notNull" name="object_class" value="CSejour" />
    <input type="hidden" class="notNull" name="sejour_id" value="{{$curr_adm->_id}}" />
    <input type="hidden" name="last_update" value="{{$curr_adm->_ref_numdos->last_update}}" />
    {{if @$modules.hprim21}}
      <button type="button" class="edit notext" onclick="setExternalIds(this.form)">Edit external Ids</button>
    {{/if}}
  </form>
  {{/if}}
  {{/if}}
  {{mb_include module=dPplanningOp template=inc_vw_numdos num_dossier=$curr_adm->_num_dossier}}
  <a class="action" style="margin-right: 18px;" name="adm{{$curr_adm->sejour_id}}" href="#1" onclick="printAdmission({{$curr_adm->sejour_id}})">
    <span class="CPatient-view" onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}');">
      {{$patient}}
    </span>
  </a>
</td>

<td style="background: {{$background}}; {{if !$curr_adm->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  <a href="#1" onclick="printAdmission({{$curr_adm->sejour_id}})">
    {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$curr_adm->_ref_praticien}}
  </a>
</td>

<td style="background: {{$background}}; {{if !$curr_adm->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  <a href="#1" onclick="printAdmission({{$curr_adm->sejour_id}})">
    <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_adm->_guid}}');">
    {{$curr_adm->entree_prevue|date_format:$dPconfig.time}} 
    <br />
		{{$curr_adm->type|upper|truncate:1:"":true}}
    {{$curr_adm->_ref_operations|@count}} Int.
    </span>
  </a>
</td>

<td class="text" style="background: {{$background}}; {{if !$curr_adm->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{if !($curr_adm->type == 'exte') && !($curr_adm->type == 'consult') && $curr_adm->annule != 1}}
    {{if $canAdmissions->edit}}
      <form name="editChFrm{{$curr_adm->sejour_id}}" action="?" method="post">
      <input type="hidden" name="m" value="dPplanningOp" />
      <input type="hidden" name="dosql" value="do_sejour_aed" />
      <input type="hidden" name="sejour_id" value="{{$curr_adm->sejour_id}}" />
      <input type="hidden" name="patient_id" value="{{$curr_adm->patient_id}}" />
      {{if $curr_adm->chambre_seule}}
        <input type="hidden" name="chambre_seule" value="0" />
        <button class="change" type="button" style="color: #f22" onclick="submitAdmission(this.form, 1);">
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
        Simple
      {{else}}
        Double
      {{/if}}
      {{if $curr_adm->prestation_id && $prestations}}
        {{assign var=_prestation_id value=$curr_adm->prestation_id}}
        <br />
        Prest. {{$prestations.$_prestation_id->_view}}
      {{/if}}
    {{/if}}
    <br />
    {{assign var=affectation value=$curr_adm->_ref_first_affectation}}
    {{if $affectation->affectation_id}}
      {{$affectation->_ref_lit->_view}}
    {{else}}
      Non plac�
    {{/if}}
  {{/if}}  
</td>

{{if $curr_adm->annule == 1}}
<td colspan="5" class="cancelled" {{if !$curr_adm->facturable}}style="background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;"{{/if}}>
  <strong>ANNULE</strong>
</td>
{{else}}
<td style="background: {{$background}}; {{if !$curr_adm->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{if $canAdmissions->edit}}
  {{if $dPconfig.dPplanningOp.COperation.verif_cote}}
  {{foreach from=$curr_adm->_ref_operations item=curr_op}}
    {{if $curr_op->cote == "droit" || $curr_op->cote == "gauche"}}
      <form name="editCoteOp{{$curr_op->_id}}" action="?" method="post">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="dosql" value="do_planning_aed" />
        {{mb_key object=$curr_op}}
        {{mb_label object=$curr_op field="cote_admission"}} :
        {{mb_field defaultOption="&mdash; choisir" object=$curr_op field="cote_admission" onchange="submitCote(this.form);"}}
      </form>
      <br />
    {{/if}}
  {{/foreach}}
  {{/if}}
  <form name="editAdmFrm{{$curr_adm->_id}}" action="?" method="post">
  <input type="hidden" name="m" value="dPplanningOp" />
  <input type="hidden" name="dosql" value="do_sejour_aed" />
  <input type="hidden" name="sejour_id" value="{{$curr_adm->_id}}" />
  <input type="hidden" name="patient_id" value="{{$curr_adm->patient_id}}" />

  {{if !$curr_adm->entree_reelle}}
	  <input type="hidden" name="entree_reelle" value="now" />
	  <button class="tick" type="button" onclick="{{if (($date_actuelle > $curr_adm->entree_prevue) || ($date_demain < $curr_adm->entree_prevue))}}confirmation(this.form);{{else}}submitAdmission(this.form);{{/if}};">
	    Admettre
	  </button>
	  
  {{else}}
	  <input type="hidden" name="entree_reelle" value="" />
	  <button class="cancel" type="button" onclick="submitAdmission(this.form);">
	    {{tr}}Cancel{{/tr}}
	  </button>
	  <br />
  
 	  {{if ($curr_adm->entree_reelle < $date_min) || ($curr_adm->entree_reelle > $date_max)}}
	    {{$curr_adm->entree_reelle|date_format:$dPconfig.datetime}}
	    <br>
	  {{else}}
	  {{$curr_adm->entree_reelle|date_format:$dPconfig.time}}
	  {{/if}}
  {{/if}}
  </form>
  {{elseif $curr_adm->entree_reelle}}
    {{if ($curr_adm->entree_reelle < $date_min) || ($curr_adm->entree_reelle > $date_max)}}
	    {{$curr_adm->entree_reelle|date_format:$dPconfig.datetime}}
	    <br>
	  {{else}}
	    {{$curr_adm->entree_reelle|date_format:$dPconfig.time}}
	  {{/if}}
  {{else}}
    -
  {{/if}}
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
        <button class="tick" type="button" onclick="submitAdmission(this.form, 1);">
          {{tr}}CSejour-saisi_SHS{{/tr}}
        </button>
      {{else}}
        <input type="hidden" name="saisi_SHS" value="0" />
        <button class="cancel" type="button" onclick="submitAdmission(this.form, 1);">
          {{tr}}Cancel{{/tr}}
        </button>
      {{/if}}
      
      {{if ($curr_adm->modif_SHS == 1) && ($dPconfig.dPplanningOp.CSejour.modif_SHS == 1)}}
        <img src="images/icons/warning.png" title="Le dossier a �t� modifi�, il faut le pr�parer" />
      {{/if}}
    </form>
  {{else}}
    {{mb_value object=$curr_adm field="saisi_SHS"}}
  {{/if}}
</td>

<td style="background: {{$background}}; {{if !$curr_adm->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{foreach from=$curr_adm->_ref_operations item=curr_op}}
  {{if $curr_op->_ref_consult_anesth->_id}}
  <div class="{{if $curr_op->_ref_consult_anesth->_ref_consultation->chrono == 64}}small-success{{else}}small-info{{/if}}" style="margin: 0px;">
    <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_op->_ref_consult_anesth->_guid}}');">
    {{$curr_op->_ref_consult_anesth->_date_consult|date_format:$dPconfig.date}}
    </span>
  </div>
  {{/if}}
  {{/foreach}}
</td>

<td style="background: {{$background}}; {{if !$curr_adm->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}" class="button">
  {{if $curr_adm->_couvert_cmu}}
    <img src="images/icons/tick.png" title="Droits CMU en cours" />
  {{else}}
    -
  {{/if}}
</td>

{{if $dPconfig.dPadmissions.show_dh}}
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
{{/if}}
{{/if}}