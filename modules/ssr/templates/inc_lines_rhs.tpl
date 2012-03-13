{{* $Id: vw_aed_rpu.tpl 7951 2010-02-01 10:44:08Z lryo $ *}}

{{*
  * @package Mediboard
  * @subpackage ssr
  * @version $Revision: 7951 $
  * @author SARL OpenXtrem
  * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
  *}}

{{mb_default var=read_only value=false}}
{{if $rhs->facture == 1}}
  {{assign var=read_only value=true}}
{{/if}}
  
{{assign var=days value="CRHS"|static:days}}
  
<table class="tbl">
  <tr>
    <th class="narrow"></th>
    <th colspan="2">{{mb_title object=$rhs_line field=code_activite_cdarr}}</th>
    <th>{{mb_title class=CActiviteCdARR field=libelle}}</th>
    
    {{foreach from=$days key=day item=litteral_day}}
    <th class="category narrow">{{mb_title object=$rhs_line field=qty_$litteral_day}}</th>
    {{/foreach}}
    
    <th class="narrow"></th>
  </tr>
  {{foreach from=$rhs->_ref_lines_by_executant key=executant_id item=_lines}}
    {{assign var=executant value=$rhs->_ref_executants.$executant_id}}
    <tr>
      <th class="text section" colspan="12" style="text-align: left;">
        {{mb_include module="mediusers" template="inc_vw_mediuser" mediuser=$executant}}
        &mdash;
        {{$executant->_ref_intervenant_cdarr}}
      </th>
    </tr>  
    {{foreach from=$_lines item=_line}}
    {{assign var=activite  value=$_line->_ref_activite_cdarr}}
    <tr>
      <td>
        {{if !$read_only && !$_line->auto}}
        <form name="del-line-{{$_line->_guid}}" action="?m={{$m}}" method="post" onsubmit="return CotationRHS.onSubmitLine(this);">
          <input type="hidden" name="m" value="ssr" />
          <input type="hidden" name="dosql" value="do_line_rhs_aed" />
          <input type="hidden" name="del" value="1" />
          {{mb_key object=$_line}}
          <input type="hidden" name="rhs_id" value="{{$rhs->_id}}" />
          <button class="notext trash" type="button" onclick="confirmDeletion(this.form, {
            typeName:'l\'activit�',
            objName:'{{$_line->_view|smarty:nodefaults|JSAttribute}}',
            ajax: 1})">
            {{tr}}Delete{{/tr}}
          </button>
        </form>
        {{/if}}
      </td>
      
      <td class="text">
        {{$activite}}
      </td>
      
      <td class="narrow">
        {{$activite->_ref_type_activite->code}}
      </td>
      
      <td class="text">
        {{$activite->libelle}}
      </td>
      
{{foreach from=$days key=day item=litteral_day}}
        {{mb_include template="inc_line_rhs"}}
      {{/foreach}}
      
      <td>
        {{mb_include module=system template=inc_object_history object=$_line}}
      </td>
      
    </tr>
    {{/foreach}}
    
  {{foreachelse}}
  <tr>
    <td colspan="10" class="empty">{{tr}}CRHS-back-lines.empty{{/tr}}</td>
  </tr>
  {{/foreach}}
</table>