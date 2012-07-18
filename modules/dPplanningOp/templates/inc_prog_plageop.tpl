<script type="text/javascript">
  ObjectTooltip.modes.allergies = {  
    module: "patients",
    action: "ajax_vw_allergies",
    sClass: "tooltip"
  };
</script>

<table class="tbl">
  <col style="width: 25px;" />
  
  <tr>
    <th class="category" colspan="4">
      Programme du {{mb_value object=$plageop field=date}}
      <br />
      {{$plageop->debut|date_format:$conf.time}} -
      {{$plageop->fin|date_format:$conf.time}}
      &mdash; {{$plageop->_ref_salle->_view}}
      {{if $plageop->spec_id && !$plageop->unique_chir}}
        <br />
        <em>Plage multi-praticiens</em>
      {{/if}}
    </th>
  </tr>
  
  {{if $plageop->spec_id && !$plageop->unique_chir}}
    {{foreach from=$plageop->_ref_operations item=_operation name=operations_multiprat}}
      
      {{if $_operation->_id}}
        {{mb_include module=planningOp template=inc_prog_plageop_line operation=$_operation}}
        {{assign var=place_after_interv_id value=$_operation->_id}}
      {{else}}
        <tr>
          <td colspan="4">
            <hr />
            <button style="float: right;" class="tick" type="button" onclick="setClose('', '')">{{tr}}OK{{/tr}}</button>
            <button style="float: right;" class="cancel" type="button" onclick="window._close()">{{tr}}Cancel{{/tr}}</button>
            <input type="hidden" name="_place_after_interv_id" value="-1" />
            {{mb_label object=$_operation field=horaire_voulu}}
            {{mb_field object=$_operation field=_horaire_voulu form=plageSelectorFrm register=true}}
          </td>
        </tr>
      {{/if}}
      
      {{if !($_operation->rank || $_operation->horaire_voulu)}}
        {{assign var=is_placed value=false}}
      {{/if}}
      
    {{/foreach}}
    
  {{else}}
    {{assign var=rank_desired value="-1"}}
    <tr>
      <td colspan="4">
        <button style="float: right;" class="tick" type="button" onclick="setClose('', '')">{{tr}}OK{{/tr}}</button>
        <button style="float: right;" class="cancel" type="button" onclick="window._close()">{{tr}}Cancel{{/tr}}</button>
        <label class="insert" style="display: inline;">
          <input type="radio" name="_place_after_interv_id" value="0" checked="checked" />
          Sans pr�f�rence pour le placement
        </label>
      </td>
    </tr>
    
    {{assign var=place_after_interv_id value=-1}}
    {{assign var=is_placed value=true}}
    
    {{foreach from=$plageop->_ref_operations item=_operation name=operations}}
      {{if !$_operation->rank && $is_placed}}
        <tr>
          <td colspan="4" style="background: #ddd;">
            <label class="insert" title="Placer l'intervention de pr�f�rence ici">
              <input type="radio" name="_place_after_interv_id" value="{{$place_after_interv_id}}" /><div></div>
              {{mb_value object=$_operation field=_horaire_voulu}}
            </label>
          </td>
        </tr>
      {{else}}
        <tr>
          <td colspan="4" style="background: #ddd; padding: 1px;"></td>
        </tr>
      {{/if}}
      
      {{if $_operation->_id}}
        {{mb_include module=planningOp template=inc_prog_plageop_line operation=$_operation}}
        {{assign var=place_after_interv_id value=$_operation->_id}}
      {{/if}}
      
      {{if !($_operation->rank || $_operation->horaire_voulu)}}
        {{assign var=is_placed value=false}}
      {{/if}}
      
    {{/foreach}}
  {{/if}}
</table>
