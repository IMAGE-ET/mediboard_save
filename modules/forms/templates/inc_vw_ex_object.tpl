{{mb_default var=hide_empty_groups value=false}}
{{mb_default var=print value=false}}
 
{{foreach from=$ex_object->_ref_ex_class->_ref_groups item=_ex_group}}
  {{assign var=go value=true}}
  
  {{if $hide_empty_groups}}
    {{assign var=any value=false}}
    
    {{foreach from=$_ex_group->_ref_fields item=_ex_field}}
      {{assign var=field_name value=$_ex_field->name}}
      
      {{if $ex_object->$field_name !== null}}
        {{assign var=any value=true}}
      {{/if}}
    {{/foreach}}
    
    {{assign var=go value=$any}}
  {{/if}}
  
  {{if $go}}
    <h4 style="margin: 0.5em; border-bottom: 1px solid #666;">{{$_ex_group}}</h4>
    
    <ul>
    {{assign var=any value=false}}
    
    {{foreach from=$_ex_group->_ref_fields item=_ex_field}}
      {{assign var=field_name value=$_ex_field->name}}

      {{if !$_ex_field->hidden && $ex_object->$field_name !== null}}
        {{assign var=any value=true}}
        <li>
          <span style="color: #666;">
            {{if $print}}
              {{tr}}{{$ex_object->_class}}-{{$field_name}}{{/tr}}
            {{else}}
              {{mb_label object=$ex_object field=$field_name}}
            {{/if}}
          </span> : {{mb_value object=$ex_object field=$field_name}}
        </li>
      {{/if}}
    {{/foreach}}
    {{if !$any}}
      <li class="empty">Aucune valeur</li>
    {{/if}}
    </ul>
    <br />
  {{/if}}
{{/foreach}}
