{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module="dPmedicament" script="medicament_selector"}}
{{mb_script module="dPmedicament" script="equivalent_selector"}}
{{mb_script module="dPprescription" script="element_selector"}}
{{mb_script module="dPprescription" script="prescription"}}
{{mb_script module="dPprescription" script="protocole"}}
{{mb_script module="dPcabinet" script="file"}}

<script type="text/javascript">

Main.add( function(){
  // Refesh de la liste des protocoles
  Protocole.refreshList('{{$protocole_id}}');
  {{if $protocole_id}}
  Prescription.reload('{{$protocole_id}}', '', '', '1');
  {{/if}}
} );

</script>

<table class="main">
	<tr>
    <!-- Affichage de la liste des protocoles pour le praticien selectionn� -->
    <td class="halfPane" style="width: 23em;" id="list_protocoles">
	    <form name="selPrat" action="?" method="get">
	      <input type="hidden" name="tab" value="vw_edit_protocole" />
        <input type="hidden" name="m" value="dPprescription" />
        <select name="praticien_id" onchange="this.form.function_id.value=''; this.form.group_id.value=''; Protocole.refreshListProt();" style="width: 23em;">
          <option value="">&mdash; Praticien</option>
	        {{foreach from=$praticiens item=praticien}}
	        <option class="mediuser" 
	                style="border-color: #{{$praticien->_ref_function->color}};" 
	                value="{{$praticien->_id}}"
	                {{if $praticien->_id == $praticien_id}}selected="selected"{{/if}}>{{$praticien->_view}}
	        </option>
	        {{/foreach}}
	      </select>
	      <select name="function_id" onchange="this.form.praticien_id.value=''; this.form.group_id.value=''; Protocole.refreshListProt();" style="width: 23em;">
          <option value="">&mdash; Cabinet</option>
          {{foreach from=$functions item=_function}}
          <option class="mediuser" style="border-color: #{{$_function->color}}" value="{{$_function->_id}}" {{if $function_id == $_function->_id}}selected=selected{{/if}} title="{{$_function->_view}}">{{$_function->_view|spancate:40}}</option>
          {{/foreach}}
        </select>
        <select name="group_id" onchange="this.form.function_id.value=''; this.form.praticien_id.value=''; Protocole.refreshListProt();" style="width: 23em;">
          <option value="">&mdash; Etablissement</option>
          {{foreach from=$groups item=_group}}
          <option value="{{$_group->_id}}" {{if $group_id == $_group->_id}}selected=selected{{/if}}>{{$_group->_view}}</option>
          {{/foreach}}
        </select>
				<br />
	      <button type="button" class="submit" onclick="this.form.submit();">
	        Cr�er un protocole
	      </button>
        {{if $can->admin}}
          <button type="button" class="new" type="button" onclick="Protocole.importProtocole('selPrat');">
          {{tr}}CPrescription.import_protocole{{/tr}}
          </button>
        {{/if}}
	    </form>
	    <div id="protocoles"></div>
		 </td>
    <!-- Affichage du protocole s�lectionn�-->
    <td>
      <div id="vw_protocole">
        {{if !$protocole_id}}
		    {{include file="inc_vw_prescription.tpl" httpreq=1 mode_protocole=1 prescription=$protocole category="medicament"}}
		    {{/if}}
		  </div>  
    </td>
  </tr>
</table>