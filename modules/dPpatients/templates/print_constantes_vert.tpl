{{unique_id var=uniq_ditto}}
{{assign var="one_date" value="1"}}
{{assign var=cste_grid value=$constantes_medicales_grid}}
{{assign var=list_constantes value="CConstantesMedicales"|static:list_constantes}}

<table class="tbl constantes" style="width: 40%;">
  <tr>
    <th></th>
    {{foreach from=$cste_grid.grid item=_constante key=_datetime}}
      <th style="text-align: center; vertical-align: top; font-size: 0.9em;" class="text">
        {{$_datetime|substr:0:18|date_format:$conf.datetime}}
        
        {{if $_constante.comment}}
          <hr />
          {{$_constante.comment}}
        {{/if}}
      </th>
    {{/foreach}}
  </tr>
  
  {{foreach from=$cste_grid.names item=_cste_name}}
    <tr>
      <th style="text-align: right;">
        {{if array_key_exists("cumul_for", $list_constantes.$_cste_name)}}
          Cumul {{tr}}CConstantesMedicales-{{$list_constantes.$_cste_name.cumul_for}}-court{{/tr}}
        {{else}}
          {{tr}}CConstantesMedicales-{{$_cste_name}}-court{{/tr}}
        {{/if}}
          
        {{if $list_constantes.$_cste_name.unit}} ({{$list_constantes.$_cste_name.unit}}){{/if}}
      </th>
      
      {{foreach from=$cste_grid.grid item=_constante key=_datetime}}
        {{assign var=_value value=null}}
        
        {{if array_key_exists($_cste_name,$_constante.values)}}
          {{assign var=_value value=$_constante.values.$_cste_name}}
        {{/if}}
        
        {{if is_array($_value)}}
          {{if $_value.value === null}}
            <td {{if $_value.span_com > 0}} colspan="{{$_value.span_com}}" {{/if}} style="border-left: 1px solid #999;"></td>
          {{else}}
            <td style="text-align: center; border-top: 2px solid {{if $_value.pair == "odd"}} #36c {{else}} #3c9 {{/if}}; border-left: 1px solid #999;" 
                {{if $_value.span > 0}} colspan="{{$_value.span}}" {{/if}}>
              <strong>{{$_value.value}}</strong> <br />
              <small>{{$_value.day}}</small>
            </td>
          {{/if}}
        {{elseif $_value != "__empty__"}}
          <td style="text-align: center;">
            {{$_value}}
          </td>
        {{/if}}
      {{/foreach}}
     </tr>
  {{/foreach}}
</table>
