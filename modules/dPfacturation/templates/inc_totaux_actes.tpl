{{assign var=type_aff value=1}}
{{if @$modules.tarmed->_can->read && $conf.tarmed.CCodeTarmed.use_cotation_tarmed == "1"}}
  {{assign var=type_aff value=0}}
{{/if}}
<div id="obsolete-totals" style="background-color: #888; display: none">
  <div class="big-warning">
    <p>Les totaux sont obsol�tes suite � la saisie de r�glements</p>
    <a class="button change" onclick="location.reload()">{{tr}}Refresh{{/tr}} {{tr}}Now{{/tr}}</a>
  </div>
</div>

<table id="totals" class="tbl" style="text-align: center;">
  {{foreach from=$reglement->_specs.emetteur->_list item=emetteur}}
    {{if $emetteur == "patient" || $type_aff}}
    <tr>
      <th class="title" colspan="9">R�glements {{tr}}CReglement.emetteur.{{$emetteur}}{{/tr}}</th>
    </tr>

    <tr>
      <th>{{mb_label object=$reglement field=mode}}</th>
      <th>{{tr}}Total{{/tr}}</th>
      {{foreach from=$reglement->_specs.mode->_list item=_mode}}
        <th>{{tr}}CReglement.mode.{{$_mode}}{{/tr}}</th>
      {{/foreach}}
      {{if isset($recapReglement.total.nb_impayes_patient|smarty:nodefaults)}}
        <th>Impay�</th>
      {{/if}}
    </tr>

    <tr>
      <th class="category">Nb r�glements</th>
      {{assign var=nb_reglement_name value="nb_reglement_$emetteur"}}
      <td>{{$recapReglement.total.$nb_reglement_name}}</td>
      {{foreach from=$reglement->_specs.mode->_list item=_mode}}
      <td>{{$recapReglement.$_mode.$nb_reglement_name}}</td>
      {{/foreach}}
      {{assign var=impaye_name value="nb_impayes_$emetteur"}}
      {{if isset($recapReglement.total.$impaye_name|smarty:nodefaults)}}
        <td>{{$recapReglement.total.$impaye_name}}</td>
      {{/if}}
    </tr>

    <tr>
      <th class="category">Total r�glements</th>
      {{assign var=du_name value="du_$emetteur"}}
      <td>{{$recapReglement.total.$du_name|currency}}</td>
      {{foreach from=$reglement->_specs.mode->_list item=_mode}}
      <td>{{$recapReglement.$_mode.$du_name|currency}}</td>
      {{/foreach}}
      {{assign var=reste_name value="reste_$emetteur"}}
      {{if isset($recapReglement.total.nb_impayes_patient|smarty:nodefaults)}}
        <td>{{$recapReglement.total.$reste_name|currency}}</td>
      {{/if}}
    </tr>
    {{/if}}
  {{/foreach}}
   
  <tr>
    <th class="title" colspan="9">R�capitulatif des s�jours concern�s</th>
  </tr>
  <tr>
    <th>Nb de {{tr}}CSejour{{/tr}}</th>
    <td colspan="{{if $type_aff}}4{{else}}8{{/if}}">{{$recapReglement.total.nb_sejours}}</td>
  </tr>
  
  {{if $type_aff}}
    <tr>
      <th>
        {{tr}}Total{{/tr}}
        {{mb_label class=CConsultation field=secteur1}}
      </th>
      <td colspan="4">{{$recapReglement.total.secteur1|currency}}</td>
      <th colspan="4">{{mb_label class=CConsultation field=_somme}}</th>
    </tr>
    <tr>
      <th>
        {{tr}}Total{{/tr}}
        {{mb_label class=CConsultation field=secteur2}}
      </th>
      <td colspan="4">{{$recapReglement.total.secteur2|currency}}</td>
      <td colspan="4" class="button">
        {{$recapReglement.total.secteur1+$recapReglement.total.secteur2|currency}}
      </td>
    </tr>
    <tr>
      <th>Total r�gl� patient</th>
      <td colspan="4">{{$recapReglement.total.du_patient|currency}}</td>
      <th colspan="4">Total r�gl�</th>
    </tr>
    <tr>
      <th>Total r�gl� tiers</th>
      <td colspan="4">{{$recapReglement.total.du_tiers|currency}}</td>
      <td colspan="4" class="button">
        {{$recapReglement.total.du_patient+$recapReglement.total.du_tiers|currency}}
      </td>
    </tr>
    <tr>
      <th>Total non r�gl� patient</th>
      <td colspan="4">{{$recapReglement.total.reste_patient|currency}}</td>
      <th colspan="4">Total non r�gl�</th>
    </tr>
    <tr>
      <th>Total non r�gl� tiers</th>
      <td colspan="4">{{$recapReglement.total.reste_tiers|currency}}</td>
      <td colspan="4" class="button">
        {{$recapReglement.total.reste_patient+$recapReglement.total.reste_tiers|currency}}
      </td>
    </tr>
  {{else}}
    <tr>
      <th>{{mb_label class=CConsultation field=_somme}}</th>
      {{assign var=total_du value=$recapReglement.total.secteur1+$recapReglement.total.secteur2}}
      <td colspan="8">{{$total_du|currency}}</td>
    </tr>
    <tr>
      <th>Total r�gl�</th>
      {{assign var=regle value=$recapReglement.total.du_patient+$recapReglement.total.du_tiers}}
      <td colspan="8">{{$regle|currency}}</td>
    </tr>
    {{if isset($recapReglement.total.nb_impayes_patient|smarty:nodefaults)}}
      <tr>
        <th>Total non r�gl�</th>
        <td colspan="8">{{$total_du-$regle|currency}}</td>
      </tr>
    {{/if}}
  {{/if}}
</table>