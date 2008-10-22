
<table class="form">
  <tr>
    <th class="title" colspan="4">
      Dossier médical du 
      {{tr}}{{$object->object_class}}{{/tr}}
    </th>
  </tr>
  <tr>
    <th class="title">Antécédent(s)</th>
    {{if is_array($object->_ref_traitements)}}
    <th class="title">Traitement(s)</th>
    {{/if}}
    <th class="title">Diagnostic(s)</th>
  </tr>
  
  <tr>
    <td class="text">
      {{foreach from=$object->_ref_antecedents key=curr_type item=list_antecedent}}
      {{if $list_antecedent|@count}}
      <strong>
        {{tr}}CAntecedent.type.{{$curr_type}}{{/tr}}
      </strong>
      <ul>
        {{foreach from=$list_antecedent item=curr_antecedent}}
        <li>
          {{mb_value object=$curr_antecedent field="date"}}
          {{mb_value object=$curr_antecedent field="rques"}}
        </li>
        {{/foreach}}
      </ul>
      {{/if}}
      {{foreachelse}}
        <i>Pas d'antécédents</i>
      {{/foreach}}
    </td>
    
    {{if is_array($object->_ref_traitements)}}
    <td class="text">
      {{if $object->_ref_traitements|@count}}<ul>{{/if}}
      {{foreach from=$object->_ref_traitements item=curr_traitement}}
        <li>
          {{if $curr_traitement->fin}}
            Du {{mb_value object=$curr_traitement field="debut"}}
            au {{mb_value object=$curr_traitement field="fin"}} :
          {{elseif $curr_traitement->debut}}
            Depuis le {{mb_value object=$curr_traitement field="debut"}} :
          {{/if}}
          {{mb_value object=$curr_traitement field="traitement"}}
        </li>
      {{foreachelse}}
        <i>Pas de traitements</i>
      {{/foreach}}
      {{if $object->_ref_traitements|@count}}</ul>{{/if}}
    </td>
    {{/if}}
    
    <td class="text">
      {{if $object->_ext_codes_cim|@count}}<ul>{{/if}}
      {{foreach from=$object->_ext_codes_cim item=curr_code}}
        <li>
          <strong>{{$curr_code->code}}:</strong> {{$curr_code->libelle}}
        </li>
      {{foreachelse}}
        <i>Pas de diagnostics</i>
      {{/foreach}}
      {{if $object->_ext_codes_cim|@count}}</ul>{{/if}}
    </td>
  </tr>
</table>
