<form name="editFrm" action="?" method="post" onsubmit="return onSubmitFormAjax(this, Control.Modal.close);">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="dosql" value="do_idsante400_aed" />
  <input type="hidden" name="del" value="0" />
  {{mb_key object=$idSante400}}
  <input type="hidden" name="callback" value="reloadId400" />
  
  <table class="form">
    <tr>
      <th class="title text {{if $idSante400->_id}}modify{{/if}}" colspan="2">
      {{if $idSante400->_id}}
        {{mb_include module=system template=inc_object_history object=$idSante400}}
        {{tr}}CIdSante400-title-modify{{/tr}} &lsquo;{{$idSante400->_view}}&rsquo;
      {{else}}
        {{tr}}CIdSante400-title-create{{/tr}}
      {{/if}}
      </th>
    </tr>
    <tr>
      <th>{{mb_label object=$filter field="object_class"}}</th>
      <td>
        {{if $dialog && $target}}
        <input type="hidden" name="object_class" class="{{$filter->_props.object_class}}" value="{{$filter->object_class}}" />
        {{tr}}{{$filter->object_class}}{{/tr}}
        {{else}}
        <select name="object_class" class="{{$idSante400->_props.object_class}}">
          <option value="">&mdash; Choisir une classe</option>
          {{foreach from=$listClasses item=curr_class}}
          <option value="{{$curr_class}}" {{if $curr_class == $idSante400->object_class}}selected="selected"{{/if}}>
            {{$curr_class}}
          </option>
          {{/foreach}}
        </select>
        {{/if}}
      </td>
    </tr>
  
    <tr>
      <th>{{mb_label object=$filter field="object_id"}}</th>
      <td>
      {{if $dialog && $target}}
      <input type="hidden" name="object_id" class="{{$filter->_props.object_id}}" value="{{$filter->object_id}}" />
        {{$target->_view}}
      {{else}}
        {{mb_field object=$idSante400 field=object_id}}
        <button class="search" type="button" onclick="ObjectSelector.initEdit()">Chercher</button>
        <script type="text/javascript">
          ObjectSelector.initEdit = function(){
            this.sForm     = "editFrm";
            this.sId       = "object_id";
            this.sClass    = "object_class";
            this.onlyclass = "false";
            this.pop();
          }
        </script>
        {{if $idSante400->_id}}
        <br />
        {{$idSante400->_ref_object->_view}}
        {{/if}}
        {{/if}}
      </td>
    </tr>
  
    <tr>
      <th>{{mb_label object=$idSante400 field="id400" }}</th>
      <td>{{mb_field object=$idSante400 field="id400" canNull="false"}}</td>
    </tr>
  
    <tr>
      <th>{{mb_label object=$idSante400 field="tag"}}</th>
      <td>{{mb_field object=$idSante400 field="tag" size="40"}}</td>
    </tr>
  
    <tr>
      <th>{{mb_label object=$idSante400 field="last_update"}}</th>
      <td>{{mb_field object=$idSante400 field="last_update" form="editFrm" canNull="false" register=true}} </td>
    </tr>
  
    <tr>
      <td class="button" colspan="2">
      {{if $idSante400->_id}}
        <button type="submit" class="modify">Valider</button>
        <button class="trash" type="button" onclick="confirmDeletion(this.form, {
          typeName: 'l\'identifiant',
          objName: '{{$idSante400->_view|smarty:nodefaults|JSAttribute}}',
        }, Control.Modal.close )">
          Supprimer
        </button>
      {{else}}
        <button type="submit" class="submit">Cr�er</button>
      {{/if}}
      </td>
    </tr>
  </table>

</form>