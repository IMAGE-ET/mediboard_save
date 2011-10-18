{{mb_default var=offline value=0}}
{{unique_id var=uniq_ditto}}
{{assign var=cste_grid value=$constantes_medicales_grid}}

<table class="tbl constantes" style="width: 100%;">
  {{if $offline && isset($sejour|smarty:nodefaults)}}
    <thead>
      <tr>
        <th class="title"></th>
        <th class="title" colspan="{{$cste_grid.names|@count}}">
          {{$sejour->_view}}
          {{mb_include module=dPplanningOp template=inc_vw_numdos nda=$sejour->_NDA}}
        </th>
      </tr>
    </thead>
  {{/if}}

  {{if $cste_grid.grid|@count}}
    {{assign var=list_constantes value="CConstantesMedicales"|static:list_constantes}}
    <tr>
      <th class="narrow"></th>
      {{foreach from=$cste_grid.names item=_cste_name}}
        <th class="narrow" style="vertical-align: bottom;">
          {{vertical}}{{tr}}CConstantesMedicales-{{$_cste_name}}-court{{/tr}}{{if $list_constantes.$_cste_name.unit}} ({{$list_constantes.$_cste_name.unit}}){{/if}}{{/vertical}}
        </th>
      {{/foreach}}
    </tr>
    
    {{foreach from=$cste_grid.grid item=_constante key=_datetime}}
      {{assign var=_datetime value=$_datetime|substr:0:18}}
      
      <tr class="comment-line">
        <th {{if $_constante.comment}} rowspan="2" {{/if}}>
          {{mb_ditto name="datetime$uniq_ditto" value=$_datetime|date_format:$conf.datetime}}
        </th>
        {{foreach from=$cste_grid.names item=_cste_name}}
          {{assign var=_value value=null}}
          
          {{if array_key_exists($_cste_name,$_constante.values)}}
            {{assign var=_value value=$_constante.values.$_cste_name}}
          {{/if}}
            
          {{if is_array($_value)}}
            <td style="text-align: center; font-size: 0.9em; border-left: 2px solid {{if $_value.pair == "odd"}} #36c {{else}} #3c9 {{/if}}; border-top: 1px solid #999;" 
                {{if $_value.span_com > 0}} rowspan="{{$_value.span_com}}" {{/if}}>
              <strong>{{$_value.value}}</strong> <br />
              {{$_value.day}}
            </td>
          {{elseif $_value != "__empty__"}}
            <td style="text-align: center; font-size: 0.9em;" >
              {{$_value}}
            </td>
          {{else}}
            <!--<td></td>-->
          {{/if}}
        {{/foreach}}
      </tr>
      {{if $_constante.comment}}
        <tr>
          <td colspan="{{$cste_grid.names|@count}}">
            {{tr}}CConstantesMedicales-comment-court{{/tr}}:
            {{$_constante.comment}}
          </td>
        </tr>
      {{/if}}
    {{/foreach}}
  
  {{else}}
  
    <tr>
      <td></td>
      <td class="empty">{{tr}}CConstantesMedicales.none{{/tr}}</td>
    </tr>
    
  {{/if}}
</table>
