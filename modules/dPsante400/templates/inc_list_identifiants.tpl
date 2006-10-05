<table class="tbl">
  {{if $dialog}}
  <tr>
    <th colspan="4" class="title">
      {{if $target}}
      Identifiants 400 pour '{{$target->_view}}'
      {{else}}
      Identifiants 400
      {{/if}}
    </th>
  </tr>
  {{/if}}
  
  <tr>
    {{if !$dialog}}
    <th>classe</th>
    <th>ID Mediboard</th>
    <th>Objet</th>
    {{/if}}
    <th>Derni�re mise � jour</th>
    <th>ID Sant�400</th>
    <th>Etiquette</th>
  </tr>
  {{foreach from=$list_idSante400 item=curr_idSante400}}
  <tr>
    {{if !$dialog}}
    <td>{{$curr_idSante400->object_class}}</td>
    <td>{{$curr_idSante400->object_id}}</td>
    <td>
      {{assign var="object" value=$curr_idSante400->_ref_object}}
      {{if $object->_id}}
      {{$object->_view}}
      {{else}}
      <div class="warning">Objet supprim�</div>
      {{/if}}
    </td>
    {{/if}}
    <td>
      <a href="?m=dPsante400&amp;{{$actionType}}={{$action}}&amp;dialog={{$dialog}}&amp;id_sante400_id={{$curr_idSante400->_id}}" >
        {{$curr_idSante400->last_update|date_format:"%d/%m/%Y � %Hh%M (%A)"}}
      </a>
    </td>
    <td>{{$curr_idSante400->id400}}</td>
    <td>{{$curr_idSante400->tag}}</td>
  </tr>
  {{/foreach}}
</table>
