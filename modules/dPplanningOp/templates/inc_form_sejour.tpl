<!-- $Id: $ -->

<form name="editSejour" action="?m={{$m}}" method="post" onsubmit="if(checkDureeHospi()){return checkForm(this);}else{return false;}">

<input type="hidden" name="m" value="dPplanningOp" />
<input type="hidden" name="dosql" value="do_sejour_aed" />
<input type="hidden" name="del" value="0" />
{{if $mode_operation}}
  <input type="hidden" name="callback" value="submitFormOperation" />
{{/if}}
<input type="hidden" name="saisi_SHS" value="{{$sejour->saisi_SHS}}" />
<input type="hidden" name="modif_SHS" value="{{$sejour->modif_SHS}}" />
<input type="hidden" name="annule" value="{{$sejour->annule}}" />
{{if !$mode_operation}}
  <input type="hidden" name="sejour_id" value="{{$sejour->sejour_id}}" />
{{/if}}


<table class="form" onmousemove="checkSejoursToReload()">

<tr>
  <th class="category" colspan="4">
    {{if $mode_operation && $sejour->sejour_id}}
      {{if $canReadSante400}}
      <a style="float:right;" href="#" onclick="view_idsante400('CSejour',{{$sejour->sejour_id}})">
        <img src="images/icons/sante400.gif" alt="Sante400" title="Identifiant sante 400"/>
      </a>
      {{/if}}
      <a style="float:right;" href="#" onclick="view_log('CSejour',{{$sejour->sejour_id}})">
        <img src="images/icons/history.gif" alt="historique" />
      </a>
    {{/if}}
    Informations concernant le s�jour
  </th>
</tr>

{{if $sejour->annule == 1}}
<tr>
  <th class="category" colspan="4" style="background: #f00;">
  SEJOUR ANNULE
  </th>
</tr>
{{/if}}

{{if $mode_operation}}
<tr>
  <th>
    Sejours existants
  </th>
  <td colspan="3" id="selectSejours">
    <select name="sejour_id" onchange="reloadSejour(this.value)">
      <option value="" {{if !$sejour->sejour_id}} selected="selected" {{/if}}>
        &mdash; Cr�er un nouveau s�jour
      </option>
      {{foreach from=$sejours item=curr_sejour}}
      <option value="{{$curr_sejour->sejour_id}}" {{if $sejour->sejour_id == $curr_sejour->sejour_id}} selected="selected" {{/if}}>
        {{$curr_sejour->_view}}
      </option>
      {{/foreach}}
    </select>
  </td>
</tr>
{{/if}}

<tr>
  <th>
    <label for="group_id" title="Etablissement ou se d�roule le s�jour. Obligatoire">Etablissement</label>
  </th>
  <td colspan="3">
    <select title="{{$sejour->_props.group_id}}" name="group_id" onchange="removePlageOp(true);">
    {{foreach from=$etablissements item=curr_etab}}
      <option value="{{$curr_etab->group_id}}" {{if ($sejour->sejour_id && $sejour->group_id==$curr_etab->group_id) || (!$sejour->sejour_id && $g==$curr_etab->group_id)}} selected="selected"{{/if}}>{{$curr_etab->text}}</option>
    {{/foreach}}
    </select>
  </td>
</tr>

<tr>
  <th>
    <label for="praticien_id" title="Praticien responsable. Obligatoire">Praticien</label>
  </th>
  <td colspan="3">
    <select name="praticien_id" title="{{$sejour->_props.praticien_id}}">
      <option value="">&mdash; Choisir un praticien</option>
      {{foreach from=$listPraticiens item=curr_praticien}}
      <option class="mediuser" style="border-color: #{{$curr_praticien->_ref_function->color}};" value="{{$curr_praticien->user_id}}" {{if $praticien->user_id == $curr_praticien->user_id}} selected="selected" {{/if}}>
        {{$curr_praticien->_view}}
      </option>
      {{/foreach}}
    </select>
  </td>
</tr>

<tr>
  <th>
    <input type="hidden" name="patient_id" title="{{$sejour->_props.patient_id}}" ondblclick="popPat()" value="{{$patient->patient_id}}" />
    <label for="patient_id" title="Patient concern�. Obligatoire">Patient</label>
  </th>
  <td class="readonly">
  	<input type="text" name="_patient_view" size="30" value="{{$patient->_view}}" ondblclick="popPat()" readonly="readonly" />
  </td>
  <td colspan="2" class="button">
  	<button type="button" class="search" onclick="popPat()">Choisir un patient</button>
  </td>
</tr>

<tr>
  <th><label for="DP" title="Code CIM du diagnostic principal">Diagnostic principal (CIM)</label></th>
  <td><input type="text" name="DP" title="{{$sejour->_props.DP}}" size="10" value="{{$sejour->DP}}" /></td>
  <td colspan="2" class="button"><button type="button" class="search" onclick="popCode('cim10')">Choisir un code</button></td>
</tr>

<tr>
  <th class="category" colspan="4">Admission</th>
</tr>

<tr>
  <th>
  	<label for="_date_entree_prevue" title="Choisir une date d'entr�e">Entr�e pr�vue</label>
  </th>
  <td class="date">
    <div id="editSejour__date_entree_prevue_da">{{$sejour->_date_entree_prevue|date_format:"%d/%m/%Y"}}</div>
    <input type="hidden" name="_date_entree_prevue" title="date|notNull" value="{{$sejour->_date_entree_prevue}}" onchange="modifSejour(); updateSortiePrevue();"/>
    <img id="editSejour__date_entree_prevue_trigger" src="./images/icons/calendar.gif" alt="calendar"/>
  </td>
  <td colspan="2">
    �
    <select name="_hour_entree_prevue" onchange="updateHeureSortie()">
    {{foreach from=$hours|smarty:nodefaults item=hour}}
      <option value="{{$hour}}" {{if $sejour->_hour_entree_prevue == $hour || (!$sejour->sejour_id && $hour == "8")}} selected="selected" {{/if}}>{{$hour}}</option>
    {{/foreach}}
    </select> h
    <select name="_min_entree_prevue" onchange="updateHeureSortie()">
    {{foreach from=$mins|smarty:nodefaults item=min}}
      <option value="{{$min}}" {{if $sejour->_min_entree_prevue == $min}} selected="selected" {{/if}}>{{$min}}</option>
    {{/foreach}}
    </select> mn
  </td>
</tr>

<tr>
  <th>
    <label for="_duree_prevue" title="Choisir une dur�e pr�vue de s�jour">Dur�e du s�jour</label>
  </th>
  <td>
    <input type="text" name="_duree_prevue" title="num|min|0" value="{{if $sejour->sejour_id}}{{$sejour->_duree_prevue}}{{else}}0{{/if}}" size="4" onchange="updateSortiePrevue()" />
    jours
  </td>
  <td id="dureeEst" colspan="2">
  </td>
</tr>

<tr {{if $mode_operation}} style="display: none" {{/if}}>
  <th>
  	<label for="_date_sortie_prevue" title="Choisir une date de sortie">Sortie pr�vue</label>
  </th>
  <td class="date">
    <div id="editSejour__date_sortie_prevue_da">{{$sejour->_date_sortie_prevue|date_format:"%d/%m/%Y"}}</div>
    <input type="hidden" name="_date_sortie_prevue" title="date|moreEquals|_date_entree_prevue|notNull" value="{{$sejour->_date_sortie_prevue}}" onchange="updateDureePrevue(); modifSejour()" />
    <img id="editSejour__date_sortie_prevue_trigger" src="./images/icons/calendar.gif" alt="calendar"/>
  </td>
  <td colspan="2">
    � 
    <select name="_hour_sortie_prevue">
    {{foreach from=$hours|smarty:nodefaults item=hour}}
      <option value="{{$hour}}" {{if $sejour->_hour_sortie_prevue == $hour  || (!$sejour->sejour_id && $hour == "8")}} selected="selected" {{/if}}>{{$hour}}</option>
    {{/foreach}}
    </select> h
    <select name="_min_sortie_prevue">
    {{foreach from=$mins|smarty:nodefaults item=min}}
      <option value="{{$min}}" {{if $sejour->_min_sortie_prevue == $min}} selected="selected" {{/if}}>{{$min}}</option>
    {{/foreach}}
    </select> mn
  </td>
</tr>

{{if !$mode_operation}}
<tr>
  <th>Entr�e r�elle :</th>
  <td colspan="3">{{$sejour->entree_reelle|date_format:"%d/%m/%Y � %Hh%M"}}</td>
</tr>

<tr>
  <th>Sortie r�elle :</th>
  <td colspan="3">{{$sejour->sortie_reelle|date_format:"%d/%m/%Y � %Hh%M"}}</td>
</tr>
{{/if}}

<tr>
  <th><label for="type" title="Type d'admission">{{tr}}Type d'admission{{/tr}}</label></th>
  <td colspan="3">
    {{if $sejour->sejour_id}}
    {{assign var="checked" value=$sejour->type}}
    {{else}}
    {{assign var="checked" value="ambu"}}
    {{/if}}
    {{html_options name="type" options=$sejour->_enumsTrans.type title=$sejour->_props.type selected=$checked}}
  </td>
</tr>

{{if !$mode_operation}}
<tr>
  <th>
    <label for="modalite_libre" title="modalite d'admission">{{tr}}Modalit� d'admission{{/tr}}</label>
  </th>
  <td colspan="3">
    {{if $sejour->sejour_id}}
    {{assign var="checked" value=$sejour->modalite}}
    {{else}}
    {{assign var="checked" value="libre"}}
    {{/if}}
    {{html_radios name="modalite" options=$sejour->_enumsTrans.modalite separator="" title=$sejour->_props.modalite checked=$checked}}
  </td>
</tr>

<tr>
  <th><label for="ATNC_1">ATNC</label></th>
  <td>
    <input name="ATNC" value="1" type="radio" {{if $sejour->ATNC == "1"}} checked="checked" {{/if}}/>
    <label for="ATNC_1">Oui</label>
    <input name="ATNC" value="0" type="radio" {{if !$sejour->ATNC || $sejour->ATNC == "0"}} checked="checked" {{/if}}/>
    <label for="ATNC_0">Non</label>
  </td>
  <th><label for="hormone_croissance_1">Traitement hormonal</label></th>
  <td colspan="2">
    <input name="hormone_croissance" value="1" type="radio" {{if $sejour->hormone_croissance == "1"}} checked="checked" {{/if}}/>
    <label for="hormone_croissance_1">Oui</label>
    <input name="hormone_croissance" value="0" type="radio" {{if !$sejour->hormone_croissance || $sejour->hormone_croissance == "0"}} checked="checked" {{/if}}/>
    <label for="hormone_croissance_0">Non</label>
  </td>  
</tr>
{{/if}}

<tr>
  <th>
    <label for="chambre_seule_1" title="Patient � placer dans une chambre particuli�re">Chambre particuli�re</label>
  </th>
  <td {{if $mode_operation}}colspan="3"{{/if}}>
    <input name="chambre_seule" value="1" type="radio" {{if $sejour->chambre_seule}} checked="checked" {{/if}} onchange="modifSejour()" />
    <label for="chambre_seule_1">Oui</label>
    <input name="chambre_seule" value="0" type="radio" {{if !$sejour->chambre_seule || !$sejour->sejour_id}} checked="checked" {{/if}} onchange="modifSejour()" />
    <label for="chambre_seule_0">Non</label>
  </td>
  {{if !$mode_operation}}
  <th>
    <label for="lit_accompagnant_1" title="Un lit pour un accompagnant est-il � pr�voir">Lit accompagnant</label>
  </th>
  <td>
    <input name="lit_accompagnant" value="1" type="radio" {{if $sejour->lit_accompagnant}} checked="checked" {{/if}} />
    <label for="lit_accompagnant_1">Oui</label>
    <input name="lit_accompagnant" value="0" type="radio" {{if !$sejour->lit_accompagnant || !$sejour->sejour_id}} checked="checked" {{/if}} />
    <label for="lit_accompagnant_0">Non</label>
  </td>
  {{/if}}
</tr>

{{if !$mode_operation}}
<tr>
  <th>
    <label for="repas_sans_sel_1" title="Repas sans sel">Repas sans sel</label>
  </th>
  <td>
    <input name="repas_sans_sel" value="1" type="radio" {{if $sejour->repas_sans_sel}} checked="checked" {{/if}} />
    <label for="repas_sans_sel_1">Oui</label>
    <input name="repas_sans_sel" value="0" type="radio" {{if !$sejour->repas_sans_sel || !$sejour->sejour_id}} checked="checked" {{/if}} />
    <label for="repas_sans_sel_0">Non</label>
  </td>
  <th>
    <label for="isolement_1" title="Le patient doit-il �tre isol�">Isolement</label>
  </th>
  <td>
    <input name="isolement" value="1" type="radio" {{if $sejour->isolement}} checked="checked" {{/if}} />
    <label for="isolement_1">Oui</label>
    <input name="isolement" value="0" type="radio" {{if !$sejour->isolement || !$sejour->sejour_id}} checked="checked" {{/if}} />
    <label for="isolement_0">Non</label>
  </td>
</tr>

<tr>
  <th>
    <label for="repas_diabete_1" title="Repas diab�tique">Repas diab�tique</label>
  </th>
  <td>
    <input name="repas_diabete" value="1" type="radio" {{if $sejour->repas_diabete}} checked="checked" {{/if}} />
    <label for="repas_diabete_1">Oui</label>
    <input name="repas_diabete" value="0" type="radio" {{if !$sejour->repas_diabete || !$sejour->sejour_id}} checked="checked" {{/if}} />
    <label for="repas_diabete_0">Non</label>
  </td>
  <th>
    <label for="television_1" title="La chambre doit-elle avoir la t�l�vision">T�l�vision</label>
  </th>
  <td>
    <input name="television" value="1" type="radio" {{if $sejour->television}} checked="checked" {{/if}} />
    <label for="television_1">Oui</label>
    <input name="television" value="0" type="radio" {{if !$sejour->television || !$sejour->sejour_id}} checked="checked" {{/if}} />
    <label for="television_0">Non</label>
  </td>
</tr>

<tr>
  <th>
    <label for="repas_sans_residu_1" title="Repas sans r�sidu">Repas sans r�sidu</label>
  </th>
  <td>
    <input name="repas_sans_residu" value="1" type="radio" {{if $sejour->repas_sans_residu}} checked="checked" {{/if}} />
    <label for="repas_sans_residu_1">Oui</label>
    <input name="repas_sans_residu" value="0" type="radio" {{if !$sejour->repas_sans_residu || !$sejour->sejour_id}} checked="checked" {{/if}} />
    <label for="repas_sans_residu_0">Non</label>
  </td>
  <td colspan="2"></td>
</tr>

<tr>
  <th>
    <label for="venue_SHS" title="Code Administratif SHS">Code de venue SHS</label>
  </th>
  <td colspan="3">
    <input type="text" size="8" maxlength="8" name="venue_SHS" title="{{$sejour->_props.venue_SHS}}" value="{{$sejour->venue_SHS}}" />
  </td>
</tr>
{{/if}}

<tr>
  <td class="text">
    <label for="convalescence" title="Convalescence">Convalescence</label>
  </td>
  <td class="text" colspan="3">
    <label for="rques" title="Remarques">Remarques sur le s�jour</label>
  </td>
</tr>

<tr>
  <td>
    <textarea name="convalescence" title="{{$sejour->_props.convalescence}}" rows="3">{{$sejour->convalescence}}</textarea>
  </td>
  <td colspan="3">
    <textarea name="rques" title="{{$sejour->_props.rques}}" rows="3">{{$sejour->rques}}</textarea>
  </td>
</tr>


{{if !$mode_operation}}
<tr>
  <td class="button" colspan="4">
  {{if $sejour->sejour_id}}
    <button class="modify" type="submit">Modifier</button>
    <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le {{$sejour->_view|smarty:nodefaults|JSAttribute}}'});">
      Supprimer
    </button>
    {{if $sejour->annule == "0"}}
      {{assign var="annule_text" value="Annuler"}}
      {{assign var="annule_class" value="cancel"}}
    {{/if}}
    {{if $sejour->annule == "1"}}
      {{assign var="annule_text" value="R�tablir"}}
      {{assign var="annule_class" value="change"}}
    {{/if}}
    <button class="{{$annule_class}}" type="button" onclick="cancelSejour();">
      {{$annule_text}}
    </button>
  {{else}}
    <button class="submit" type="submit">Cr�er</button>
  {{/if}}
  </td>
</tr>
{{/if}}

</table>

</form>

<script type="text/javascript">
// Explicit form preparation for Ajax loading
prepareForm(document.editSejour);
regFieldCalendar("editSejour", "_date_entree_prevue");
regFieldCalendar("editSejour", "_date_sortie_prevue");
removePlageOp(false);
</script>
