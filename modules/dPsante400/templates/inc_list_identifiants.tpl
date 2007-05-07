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
    <th>Mise � jour</th>
    <th>ID Sant�400</th>
    <th>Etiquette</th>
  </tr>
  {{foreach from=$list_idSante400 item=_idSante400}}
  <tr {{if $_idSante400->_id == $idSante400->_id}}class="selected"{{/if}}>
    {{if !$dialog}}
    <td>{{$_idSante400->object_class}}</td>
    <td>{{$_idSante400->object_id}}</td>
    <td>
      {{assign var="object" value=$_idSante400->_ref_object}}
      {{if $object->_id}}
      <div onmouseover="ObjectTooltip.create(this, '{{$_idSante400->object_class}}', {{$_idSante400->object_id}})">
        {{$object->_view}}
      </div>
      {{else}}
      <div class="warning">Objet supprim�</div>
      {{/if}}
    </td>
    {{/if}}
    <td>
      <a href="?m=dPsante400&amp;{{$actionType}}={{$action}}&amp;dialog={{$dialog}}&amp;id_sante400_id={{$_idSante400->_id}}" >
        {{$_idSante400->last_update|date_format:"%d/%m/%Y � %Hh%M"}}
      </a>
    </td>
    <td>{{$_idSante400->id400}}</td>
    <td>{{$_idSante400->tag|replace:' ':'<br/>'}}</td>
  </tr>
  {{/foreach}}
</table>
