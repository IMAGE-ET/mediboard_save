<tr class="lit" id="lit{{$curr_lit->lit_id}}">
  <td>
    {{if $curr_lit->_overbooking}}
      <img src="images/icons/warning.png" alt="warning" title="Over-booking: {{$curr_lit->_overbooking}} collisions" />
    {{/if}}
    {{$curr_lit->nom}}
  </td>
  <td class="action">
    {{if $can->edit}}
      <input name="choixLit" type="radio" id="lit{{$curr_lit->lit_id}}" onclick="selectLit({{$curr_lit->lit_id}})" />
    {{/if}}
  </td>
</tr>
{{foreach from=$curr_lit->_ref_affectations item=curr_affectation}}
{{assign var="sejour" value=$curr_affectation->_ref_sejour}}
{{assign var="patient" value=$sejour->_ref_patient}}
{{assign var="aff_prev" value=$curr_affectation->_ref_prev}}
{{assign var="aff_next" value=$curr_affectation->_ref_next}}

<tbody class="hoverable">
<tr class="patient">
 
  {{if $curr_affectation->confirme}}
    <td class="text" style="background-image:url(images/icons/ray.gif); background-repeat:repeat;">
  {{else}}
    <td class="text">
  {{/if}}
  {{if $curr_affectation->sejour_id}}
    <a style="float: right;" href="index.php?m=dPpatients&amp;tab=vw_idx_patients&amp;patient_id={{$patient->patient_id}}"
              onmouseover="viewPrevTimeHospi(
                {{$curr_affectation->affectation_id}},
                {{$curr_affectation->_ref_sejour->praticien_id}},
                '{{$curr_affectation->_ref_sejour->_codes_ccam}}')"
              onmouseout="hidePrevTimeHospi(
                {{$curr_affectation->affectation_id}})">
      <img src="images/icons/edit.png" alt="edit" title="Editer le patient" />
    </a>
    <div id="tpsPrev{{$curr_affectation->affectation_id}}" class="tooltip" style="display: none; padding: 5px;">
    </div>
    
    {{if $sejour->_couvert_cmu}}
    <div style="float: right;"><strong>CMU</strong></div>
    {{/if}}
          
    {{if !$sejour->entree_reelle || ($aff_prev->affectation_id && $aff_prev->effectue == 0)}}
      <font style="color:#a33">
    {{else}}
      {{if $sejour->septique == 1}}
        <font style="color:#3a3">
      {{else}}
        <font>
     {{/if}}
    {{/if}} 
    {{if $sejour->type == "ambu"}}
      <img src="images/icons/X.png" alt="X" title="Sortant ce soir" />
    {{elseif $curr_affectation->sortie|date_format:"%Y-%m-%d" == $demain}}
      {{if $aff_next->affectation_id}}
        <img src="images/icons/OC.png" alt="OC" title="Sortant demain" />
      {{else}}
        <img src="images/icons/O.png" alt="O" title="Sortant demain" />
      {{/if}}
    {{elseif $curr_affectation->sortie|date_format:"%Y-%m-%d" == $date}}
      {{if $aff_next->affectation_id}}
        <img src="images/icons/OoC.png" alt="OoC" title="Sortant aujourd'hui" />
      {{else}}
        <img src="images/icons/Oo.png" alt="Oo" title="Sortant aujourd'hui" />
      {{/if}}
    {{/if}}
    {{if $sejour->type == "ambu"}}
      <em>{{$patient->_view}}</em>
    {{else}}
      <strong>{{$patient->_view}}</strong>
    {{/if}}
    {{if (!$sejour->entree_reelle) || ($aff_prev->affectation_id && $aff_prev->effectue == 0)}}
      {{$curr_affectation->entree|date_format:"%d/%m %Hh%M"}}
    {{/if}}
    </font>
  {{else}}
  <em>[LIT BLOQUE]</em>
  {{/if}}
  </td>
  <td class="action" style="background:#{{$sejour->_ref_praticien->_ref_function->color}}">
    {{$sejour->_ref_praticien->_shortview}}
  </td>
</tr>
{{if !$curr_affectation->sejour_id}}
 
  <tr class="dates">   
    <td class="text">
    {{if $can->edit}}
    <form name="rmvAffectation{{$curr_affectation->affectation_id}}" action="?m={{$m}}" method="post">
    <input type="hidden" name="dosql" value="do_affectation_aed" />
    <input type="hidden" name="del" value="1" />
    <input type="hidden" name="affectation_id" value="{{$curr_affectation->affectation_id}}" />
    </form>
    <a style="float: right;" href="#" onclick="confirmDeletion(document.rmvAffectation{{$curr_affectation->affectation_id}},{typeName:'l\'affectation',objName:'{{$patient->_view|smarty:nodefaults|JSAttribute}}'})">
      <img src="images/icons/trash.png" alt="trash" title="Supprimer l'affectation" />
    </a>
    {{/if}}
    <em>Du</em>:
    {{$curr_affectation->entree|date_format:"%A %d %B %H:%M"}}
    ({{$curr_affectation->_entree_relative}} jours)
  </td>
  <td class="action">
    {{if $can->edit}}
    <form name="entreeAffectation{{$curr_affectation->affectation_id}}" action="?m={{$m}}" method="post">
    <input type="hidden" name="dosql" value="do_affectation_aed" />
    <input type="hidden" name="affectation_id" value="{{$curr_affectation->affectation_id}}" />
    <input type="hidden" name="entree" value="{{$curr_affectation->entree}}" />
    </form>
    <a>
      <img id="entreeAffectation{{$curr_affectation->affectation_id}}__trigger_entree" src="images/icons/planning.png" alt="Planning" title="Modifier la date de d�but" />
    </a>
    {{/if}}
  </td>
  </tr>
  <tr class="dates">
  <td class="text">
    <em>Au</em>:
    {{$curr_affectation->sortie|date_format:"%A %d %B %H:%M"}}
    ({{$curr_affectation->_sortie_relative}} jours)
  </td>
  <td class="action">
    {{if $can->edit}}
    <form name="sortieAffectation{{$curr_affectation->affectation_id}}" action="?m={{$m}}" method="post">
    <input type="hidden" name="dosql" value="do_affectation_aed" />
    <input type="hidden" name="affectation_id" value="{{$curr_affectation->affectation_id}}" />
    <input type="hidden" name="sortie" value="{{$curr_affectation->sortie}}" />
    </form>
    <a>
      <img id="sortieAffectation{{$curr_affectation->affectation_id}}__trigger_sortie" src="images/icons/planning.png" alt="Planning" title="Modifier la date de fin" />
    </a>
    {{/if}}
  </td>
  </tr>

  {{if $curr_affectation->rques}}
    <tr class="dates">
      <td class="text highlight" colspan="2">
        <em>Remarques:</em> {{$curr_affectation->rques|nl2br}}
      </td>
    </tr>
  {{/if}}
{{else}}

<tr class="dates">
  {{if $aff_prev->affectation_id}}
  <td class="text">
    <em>D�plac�</em> (chambre: {{$aff_prev->_ref_lit->_ref_chambre->nom}}):
    {{$curr_affectation->entree|date_format:"%A %d %B %H:%M"}}
    ({{$curr_affectation->_entree_relative}} jours)
  </td>
  <td class="action">
    {{if $can->edit}}
    <form name="rmvAffectation{{$curr_affectation->affectation_id}}" action="?m={{$m}}" method="post">

    <input type="hidden" name="dosql" value="do_affectation_aed" />
    <input type="hidden" name="del" value="1" />
    <input type="hidden" name="affectation_id" value="{{$curr_affectation->affectation_id}}" />

    </form>
    
    <a style="float: right;" href="#" onclick="confirmDeletion(document.rmvAffectation{{$curr_affectation->affectation_id}},{typeName:'l\'affectation',objName:'{{$patient->_view|addslashes}}'})">
      <img src="images/icons/trash.png" alt="trash" title="Supprimer l'affectation" />
    </a>
    {{/if}}
  </td>
  {{else}}
  <td class="text">
    {{if $can->edit}}
    <form name="rmvAffectation{{$curr_affectation->affectation_id}}" action="?m={{$m}}" method="post">

    <input type="hidden" name="dosql" value="do_affectation_aed" />
    <input type="hidden" name="del" value="1" />
    <input type="hidden" name="affectation_id" value="{{$curr_affectation->affectation_id}}" />

    </form>
    <a style="float: right;" href="#" onclick="confirmDeletion(document.rmvAffectation{{$curr_affectation->affectation_id}},{typeName:'l\'affectation',objName:'{{$patient->_view|addslashes}}'})">
      <img src="images/icons/trash.png" alt="trash" title="Supprimer l'affectation" />
    </a>
    {{/if}}
    <em>Entr�e</em>:
    {{$curr_affectation->entree|date_format:"%A %d %B %H:%M"}}
    ({{$curr_affectation->_entree_relative}} jours)
  </td>
  <td class="action">
    {{if $can->edit}}
    <form name="entreeAffectation{{$curr_affectation->affectation_id}}" action="?m={{$m}}" method="post">

    <input type="hidden" name="dosql" value="do_affectation_aed" />
    <input type="hidden" name="affectation_id" value="{{$curr_affectation->affectation_id}}" />
    <input type="hidden" name="entree" value="{{$curr_affectation->entree}}" />

    </form>
    
    <a>
      <img id="entreeAffectation{{$curr_affectation->affectation_id}}__trigger_entree" src="images/icons/planning.png" alt="Planning" title="Modifier la date d'entr�e" />
    </a>
    {{/if}}
  </td>
  {{/if}}
</tr>
 
<tr class="dates">
  {{if $aff_next->affectation_id}}
  <td class="text" colspan="2">
    <em>D�plac�</em> (chambre: {{$aff_next->_ref_lit->_ref_chambre->nom}}):
    {{$curr_affectation->sortie|date_format:"%A %d %B %H:%M"}}
    ({{$curr_affectation->_sortie_relative}} jours)
  </td>
  {{else}}
  <td class="text">
    {{if $can->edit}}
    <form name="splitAffectation{{$curr_affectation->affectation_id}}" action="?m={{$m}}" method="post">

    <input type="hidden" name="dosql" value="do_affectation_split" />
    <input type="hidden" name="affectation_id" value="{{$curr_affectation->affectation_id}}" />
    <input type="hidden" name="sejour_id" value="{{$curr_affectation->sejour_id}}" />
    <input type="hidden" name="entree" value="{{$curr_affectation->entree}}" />
    <input type="hidden" name="sortie" value="{{$curr_affectation->sortie}}" />
    <input type="hidden" name="no_synchro" value="1" />
    <input type="hidden" name="_new_lit_id" value="" />
    <input type="hidden" name="_date_split" value="{{$curr_affectation->sortie}}" />

    </form>
    
    <a style="float: right;">
<img id="splitAffectation{{$curr_affectation->affectation_id}}__trigger_split" src="images/icons/move.gif" alt="Move" title="D�placer un patient" />
    </a>
    {{/if}}

    <em>Sortie</em>:
    {{$curr_affectation->sortie|date_format:"%A %d %B %H:%M"}}
    ({{$curr_affectation->_sortie_relative}} jours)
  </td>
  <td class="action">
    {{if $can->edit}}
    <form name="sortieAffectation{{$curr_affectation->affectation_id}}" action="?m={{$m}}" method="post">

    <input type="hidden" name="dosql" value="do_affectation_aed" />
    <input type="hidden" name="affectation_id" value="{{$curr_affectation->affectation_id}}" />
    <input type="hidden" name="sortie" value="{{$curr_affectation->sortie}}" />

    </form>
    
    <a>
<img id="sortieAffectation{{$curr_affectation->affectation_id}}__trigger_sortie" src="images/icons/planning.png" alt="Planning" title="Modifier la date de sortie" />
    </a>
    {{/if}}
  </td>
  {{/if}}
</tr>
<tr class="dates">
  <td colspan="2"><em>Age</em>: {{$patient->_age}} ans</td>
</tr>
<tr class="dates">
  <td class="text" colspan="2"><em>Dr. {{$sejour->_ref_praticien->_view}}</em></td>
</tr>

   {{if $curr_affectation->_ref_sejour->prestation_id}}
     <tr class="dates">
       <td colspan="2">
      <strong>Prestation:</strong> {{$curr_affectation->_ref_sejour->_ref_prestation->_view}}
     </td>
     </tr>
    {{/if}}
   
<tr class="dates">
  <td class="text" colspan="2">
    {{foreach from=$sejour->_ref_operations item=curr_operation}}
      {{if $curr_operation->libelle}}
      <em>[{{$curr_operation->libelle}}]</em>
      <br />
      {{/if}}
      {{foreach from=$curr_operation->_ext_codes_ccam item=curr_code}}
      <em>{{$curr_code->code}}</em> : {{$curr_code->libelleLong}}<br />
      {{/foreach}}
    {{/foreach}}
  </td>
</tr>
<tr class="dates">
  <td class="text" colspan="2">
    <form name="SeptieSejour{{$sejour->sejour_id}}" action="?m=dPhospi" method="post">

    <input type="hidden" name="m" value="dPplanningOp" />
    <input type="hidden" name="otherm" value="dPhospi" />
    <input type="hidden" name="dosql" value="do_sejour_aed" />
    <input type="hidden" name="sejour_id" value="{{$sejour->sejour_id}}" />
    
    <em>Pathologie</em>:
    {{$sejour->pathologie}}
    -
    {{if $can->edit}}
    <input type="radio" name="septique" value="0" {{if $sejour->septique == 0}} checked="checked" {{/if}} onclick="this.form.submit()" />
    <label for="septique_0" title="S�jour propre">Propre</label>
    <input type="radio" name="septique" value="1" {{if $sejour->septique == 1}} checked="checked" {{/if}} onclick="this.form.submit()" />
    <label for="septique_1" title="S�jour septique">Septique</label>
    {{else}}
{{if $sejour->septique == 0}}
Propre
{{else}}
Septique
{{/if}}
    {{/if}}
    </form>

  </td>
</tr>
{{if $sejour->rques != ""}}
<tr class="dates">
  <td class="text highlight" colspan="2">
    <em>S�jour</em>: {{$sejour->rques|nl2br}}
  </td>
</tr>
{{/if}}
{{foreach from=$sejour->_ref_operations item=curr_operation}}
{{if $curr_operation->rques != ""}}
<tr class="dates">
  <td class="text highlight" colspan="2">
    <em>Intervention</em>: {{$curr_operation->rques|nl2br}}
  </td>
</tr>
{{/if}}
{{/foreach}}
{{if $patient->rques != ""}}
<tr class="dates">
  <td class="text highlight" colspan="2">
    <em>Patient</em>: {{$patient->rques|nl2br}}
  </td>
</tr>
{{/if}}
<tr class="dates">
  <td class="text" colspan="2">
    {{if $can->edit}}
    <form name="editChFrm{{$sejour->sejour_id}}" action="index.php" method="post">
    <input type="hidden" name="m" value="{{$m}}" />
    <input type="hidden" name="dosql" value="do_edit_chambre" />
    <input type="hidden" name="id" value="{{$sejour->sejour_id}}" />
    {{if $sejour->chambre_seule}}
    <input type="hidden" name="value" value="0" />
    <button class="change" type="submit" style="color: #f22;">
      chambre simple
    </button>
    {{else}}
    <input type="hidden" name="value" value="1" />
    <button class="change" type="submit">
      chambre double
    </button>
    {{/if}}
    </form>
    {{/if}}
  </td>
</tr>

{{/if}}

{{foreachelse}}
<tr class="litdispo"><td colspan="2">Lit disponible</td></tr>
<tr class="litdispo">
  <td class="text" colspan="2">
  depuis:
  {{if $curr_lit->_ref_last_dispo && $curr_lit->_ref_last_dispo->affectation_id}}
  {{$curr_lit->_ref_last_dispo->sortie|date_format:"%A %d %B %H:%M"}} 
  ({{$curr_lit->_ref_last_dispo->_sortie_relative}} jours)
  {{else}}
  Toujours
  {{/if}}
  </td>
</tr>
<tr class="litdispo">
  <td class="text" colspan="2">
  jusque: 
  {{if $curr_lit->_ref_next_dispo && $curr_lit->_ref_next_dispo->affectation_id}}
  {{$curr_lit->_ref_next_dispo->entree|date_format:"%A %d %B %H:%M"}}
  ({{$curr_lit->_ref_next_dispo->_entree_relative}} jours)
  {{else}}
  Toujours
  {{/if}}
  </td>
</tr>
</tbody>
{{/foreach}}