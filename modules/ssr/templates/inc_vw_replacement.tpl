{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

Main.add(function(){
  var oForm = getForm("editReplacement");
  if($V(oForm.replacer_id)){
    refreshReplacerPlanning($V(oForm.replacer_id));
  }
});

onSubmitReplacement = function(form, sejour_id, conge_id, type) {
  return onSubmitFormAjax(form, { onComplete: function() { 
    refreshReplacement(sejour_id, conge_id, type);
    refreshlistSejour(sejour_id, type);
    if (type == 'kine'       ) refreshlistSejour('','reeducateur');
    if (type == 'reeducateur') refreshlistSejour('','kine'       );
    if (type == 'reeducateur') $('replacement-reeducateur').update(''); 
  } } ); 
}

</script>

<table class="tbl">
  <tr>
    <th class="title text" colspan="3">
      R��ducateurs de la fonction 
      {{mb_include module=mediusers template=inc_vw_function function=$user->_ref_function}}
      <br />
      {{if $sejour_id}} 
        pour {{$sejour->_ref_patient}}
      {{else}}
        Plusieurs s�jours         
      {{/if}}
    </th>
  </tr> 
  <tr>
    <th>{{mb_title class=CEvenementSSR field=sejour_id    }}</th>
    <th>{{mb_title class=CEvenementSSR field=therapeute_id}}</th>
    <th>Evts SSR</th>
  </tr>
  
  {{foreach from=$evenements_counts key=_sejour_id item=_counts_by_sejour}}
  <tbody class="hoverable">
    
  {{foreach from=$_counts_by_sejour key=therapeute_id item=_count name=therapeutes}}
    {{assign var=_sejour value=$all_sejours.$_sejour_id}}
    <tr {{if array_key_exists($_sejour->_id, $sejours)}} class="selected" {{/if}} >
      {{if $smarty.foreach.therapeutes.first}} 
      <td rowspan="{{$_counts_by_sejour|@count}}" class="text">
        {{if !$sejour_id}}
          {{$_sejour->_ref_patient}}
          <br />
        {{/if}}
        <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}')">
          {{mb_include module=system template=inc_interval_date from=$_sejour->entree to=$_sejour->sortie}}
        </span>
      </td>
      {{/if}}
      {{assign var=technicien value=$_sejour->_ref_bilan_ssr->_ref_technicien}}
      <td>
        {{if $technicien->kine_id == $therapeute_id}}
        <strong>
          {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$therapeutes.$therapeute_id}} /
          {{mb_value object=$technicien field=plateau_id}}
          </strong>
        {{else}}
          {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$therapeutes.$therapeute_id}}
        {{/if}}
        </td>
      <td style="text-align: center;">{{$_count}}</td>
    </tr>
  {{/foreach}}

  {{foreachelse}}
  <tr>
    <td colspan="3" class="empty">{{tr}}None{{/tr}}</td>
  </tr>
  {{/foreach}}
  </tbody>
</table>

<form name="editReplacement" action="?" method="post" onsubmit="return onSubmitReplacement(this, '{{$sejour->_id}}','{{$conge->_id}}','{{$type}}');">
        
  <input type="hidden" name="m" value="ssr" />
  {{if $type == "kine"}}
    <input type="hidden" name="dosql" value="do_replacement_aed" />
    {{mb_key object=$replacement}}
  {{else}}
    <input type="hidden" name="dosql" value="do_transfert_ssr_multi_aed" />
  {{/if}}
  <input type="hidden" name="del" value="0" />
  
  {{if $sejour_id}} 
    {{mb_field object=$replacement field=sejour_id hidden=1}}
  {{else}}
    <input type="hidden" name="sejour_ids" value="{{$sejours|@array_keys|@implode:'-'}}" />
  {{/if}}
  
  {{* Prop definition cannot be ref due to pseudo plage *}}
  {{mb_field object=$replacement field=conge_id hidden=1 prop=""}}

  <table class="form">
    <tr>
      {{if $type == "kine"}}
        {{if $replacement->_id}}
          <th class="text title modify" colspan="2">
            {{mb_include module=system object=$replacement template=inc_object_idsante400}}
            {{mb_include module=system object=$replacement template=inc_object_history   }}
            {{mb_include module=system object=$replacement template=inc_object_notes     }}
             Modification du remplacement du s�jour<br />'{{$sejour}}'
          </th>
        {{else}}
         <th class="text title" colspan="2">Cr�ation d'un remplacement</th>
        {{/if}}
      {{else}}
        <th class="title" colspan="2">
          Transfert des '{{$transfer_count}}' �venement(s)
        </th>
      {{/if}}
    </tr>
    
    <tr>
      <td colspan="2">
        <table class="tbl">
          <tr>
            </td>
            {{foreach from=$transfer_counts key=_day item=_count}}
            <th>{{$_day|date_format:"%a"}}<br />{{$_day|date_format:"%d"}}</th>
            {{/foreach}}
          </tr>
          <tr>
            {{foreach from=$transfer_counts key=_day item=_count}}
            <td style="text-align: center;">{{$_count|ternary:$_count:"-"}}</td>
            {{/foreach}}
          </tr>
        </table>
      </td>
    </tr>
    
    {{if $type == "kine"}}
    <tr>
      <th>{{mb_label object=$replacement field=replacer_id}}</th>
      <td>
        {{if $replacement->_id}}
          {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$replacement->_ref_replacer}}
        {{else}}
          <select name="replacer_id" onchange="refreshReplacerPlanning(this.value);">
            <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
            {{mb_include module=mediusers template=inc_options_mediuser list=$users disabled=$conge->user_id}}
          </select>
        {{/if}}
      </td>      
    </tr>
    <tr>
      <td colspan="2" class="button">
        {{if $replacement->_id}}
          <button class="trash" type="button" onclick="confirmDeletion(this.form, {
            typeName: 'le remplacement ',
            objName: '{{$replacement->_view|smarty:nodefaults|JSAttribute}}',
            ajax: 1 } )">
            {{tr}}Delete{{/tr}}
          </button>
        {{else}}
          <button type="submit" class="submit">{{tr}}Save{{/tr}}</button>
        {{/if}}
      </td>  
    </tr>
    {{/if}}
    
    {{if $type == "reeducateur"}}
    <tr>
      <td colspan="2" class="button">
        <select name="replacer_id" onchange="refreshReplacerPlanning(this.value);">
          <option value="">&mdash; Utilisateur</option>
          {{foreach from=$users item=_user}}
            <option value="{{$_user->_id}}" class="mediuser" style="border-color: #{{$_user->_ref_function->color}};">{{$_user->_view}}</option>
          {{/foreach}}
        </select>
      </td>     
    </tr>
    <tr>
      <td colspan="2" class="button">
         <button type="submit" class="submit">Transf�rer les �venements</button>
      </td>
    </tr>
    {{/if}}
    
    
  </table>
</form>
<div id="replacer-planning">
  
</div>
