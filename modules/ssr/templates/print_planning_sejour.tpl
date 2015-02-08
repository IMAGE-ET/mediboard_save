{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=ssr script=planning}}
{{mb_script module=ssr script=planification}}

<script type="text/javascript">
Main.add(function(){
  Planification.refreshSejour("{{$sejour->_id}}", false, 600, true, true);
});

</script>

<h3>
	Kin� r�f�rent : 
  {{$sejour->_ref_bilan_ssr->_ref_technicien->_ref_kine}}
</h3>

<table class="tbl">
	<tr>
		<th class="title" colspan="2">Intervenants</th>
	</tr>
	{{foreach from=$intervenants item=_intervenant_by_elt key=element_id}}
	  {{assign var=element value=$elements.$element_id}}
		<tr>
	  	<th>{{$element->_view}}</th>
		 <td>
		{{foreach from=$_intervenant_by_elt item=_intervenant name="intervenants"}}
		   {{$_intervenant->_view}}
		   {{if !$smarty.foreach.intervenants.last}},{{/if}}
		{{/foreach}}
		</td>
	 </tr> 
	{{/foreach}}
</table>

<div id="planning-sejour"></div>

<div class="planning-signatures">
	<fieldset style="float: left;">
	  <legend>Patient</legend>
		Je, soussign�(e) {{$sejour->_ref_patient}}, 
		<br />d�clare avoir pris connaissance 
		<br />et accepter les activit�s de r��ducation.
	</fieldset>
	
	<fieldset style="float: right;">
	  <legend>Praticien</legend>
		M�decin r�f�rent :
		<br/>{{$sejour->_ref_praticien}}
	</fieldset>
</div>