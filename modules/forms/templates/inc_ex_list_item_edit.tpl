<script type="text/javascript">
editListItem = function(line) {
  var form = line.up('form');
  $V(form.ex_list_item_id, line.get('id'));
  $V(form.elements.name, line.get('name'));
  $V(form.elements.code, line.get('code'));
  var button = form.down('button');
  button.removeClassName('add').addClassName('save');
  form.down('button.cancel').setVisibility(true);
}

cancelEditListItem = function(form) {
  $V(form.ex_list_item_id, "");
  $V(form.elements.name, "");
  $V(form.elements.code, "");
  var button = form.down('button');
  button.removeClassName('save').addClassName('add');
  form.down('button.cancel').setVisibility(false);
}
  
Main.add(function(){
  var form = getForm("edit-{{$context->_guid}}");
  if (!form || !form.elements.coded) return;
  
  $A(form.elements.coded).each(function(e){
    e.observe("click", function(){
      getForm('CExListItem-create').select("table .code").invoke('setVisible', e.value == 1);
    });
  });
});
</script>

{{assign var=coded value=false}}

{{if $context instanceof CExList && $context->coded == 1}}
  {{assign var=coded value=true}}
{{/if}}

{{assign var=owner_field value=$context->getBackRefField()}}

<form name="CExListItem-create" method="post" action="?" 
      onsubmit="return onSubmitFormAjax(this, {onComplete: {{if $context instanceof CExClassField}} ExField.edit.curry('{{$context->_id}}') {{else}} MbObject.edit.curry('{{$context->_guid}}') {{/if}} })">
      	
  {{mb_class class=CExListItem}}
  <input type="hidden" name="ex_list_item_id" value="" class="ref" />
	<input type="hidden" name="{{$owner_field}}" value="{{$context->_id}}" />
  <input type="text" name="__default" value="{{$spec_value}}" />
  
  <table class="main tbl">
    <tr>
      <th class="narrow"></th>
      <th class="narrow code" {{if !$coded}}style="display: none"{{/if}}>
        {{mb_title class=CExListItem field=code}}
      </th>
      <th {{if $context instanceof CExClassField}}colspan="2"{{/if}}>
        {{mb_title class=CExListItem field=name}}
      </th>
      <th class="narrow"></th>
      <th class="narrow">
      	Coch� par<br />d�faut
      </th>
    </tr>
    
    <tr>
      <td>
        <button class="add notext" style="margin: -1px;">{{tr}}Add{{/tr}}</button>
      </td>
      <td class="code" {{if !$coded}}style="display: none"{{/if}}>{{mb_field class=CExListItem field=code size=6}}</td>
      <td {{if $context instanceof CExClassField}}colspan="2"{{/if}}>
        {{mb_field class=CExListItem field=name style="width: 99%;"}}
			</td>
			<td>
        <button class="cancel notext" type="button" onclick="cancelEditListItem(this.form)" style="margin: -1px; visibility: hidden;">
          {{tr}}Cancel{{/tr}}
        </button>
      </td>
			<td style="text-align: center;" title="Aucune valeur par d�faut">
			  {{mb_include module=forms template=inc_ex_list_default_value value=""}}
			</td>
    </tr>
    
    {{foreach from=$context->_back.list_items item=_item}}
      <tr data-id="{{$_item->_id}}" data-name="{{$_item->name}}" data-code="{{$_item->code}}">
        <td>
          <button class="edit notext" type="button" style="margin: -1px;" onclick="editListItem($(this).up('tr'))">
            {{tr}}Edit{{/tr}}
          </button>
        </td>
        <td class="code" {{if !$coded}}style="display: none"{{/if}}>{{mb_value object=$_item field=code}}</td>
        <td>{{mb_value object=$_item field=name}}</td>
        
        {{if $context instanceof CExClassField}}
          {{if $triggerables|@count}}
            <td>
              <select class="triggered-data-select" onchange="updateTriggerData(this)">
                <option value=""> &mdash; </option>
                {{foreach from=$triggerables item=_triggerable}}
                  {{assign var=_trigger_value value="`$_triggerable->_id`-`$_item->_id`"}}
                  <option value="{{$_trigger_value}}" {{if $context->_triggered_data == $_trigger_value}}selected="selected"{{/if}}>
                    {{$_triggerable->name}}
                  </option>
                {{/foreach}}
              </select>
            </td>
          {{else}}
            <td class="empty">Aucun formulaire � d�clencher</td>
          {{/if}}
        {{/if}}
        
				<td></td>
        <td style="text-align: center;">
          {{mb_include module=forms template=inc_ex_list_default_value value=$_item->_id}}
        </td>
      </tr>
    {{foreachelse}}
      <tr>
        <td class="empty" colspan="5">{{tr}}CExListItem.none{{/tr}}</td>
      </tr>
    {{/foreach}}
  </table>
</form>
