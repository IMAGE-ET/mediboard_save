{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module=ssr script=planification}}
{{mb_include_script module="ssr" script="planning"}}
<script type="text/javascript">

onCompleteShowWeek = function(){
  refreshlistSejour('','kine');
  refreshlistSejour('','reeducateur');
  $('replacement-kine').update('');
  $('replacement-reeducateur').update(''); 
}

refreshlistSejour = function(selected_sejour_id, type){
  var url = new Url("ssr", "ajax_vw_list_sejours");
	url.addParam("type", type);
	url.requestUpdate("sejours-"+type, { 
	  onComplete: function(){
		  $('replacement-'+type+'-'+selected_sejour_id).addUniqueClassName('selected');
		}
	});
}

refreshReplacement = function(sejour_id, conge_id, type){
  var url = new Url("ssr", "ajax_vw_replacement");
  url.addParam("sejour_id", sejour_id);
  url.addParam("conge_id", conge_id);
	url.addParam("type", type);
  url.requestUpdate("replacement-"+type);
}

refreshReplacerPlanning = function(replacer_id){
  var url = new Url("ssr", "ajax_planning_technicien");
	url.addParam("kine_id", replacer_id);
	url.requestUpdate("replacer-planning");
}

Main.add(function(){
  Planification.showWeek();
	onCompleteShowWeek();
	
	Control.Tabs.create('tabs-replacement', true);
});

</script>

<table class="main">
	<tr>
    <td id="week-changer" colspan="2"></td>
  </tr>
	<tr>
	  <td colspan="2">
	  	<div id="toto">
	  	  <ul id="tabs-replacement" class="control_tabs">
				  <li>
				    <a href="#kines">Remplacement des r�f�rents</a>
				  </li>
				  <li>
				    <a href="#reeducateurs">Transfert des r��ducateurs</a>
				  </li>
			  </ul>
			   <hr class="control_tabs" />
      </div>
	  </td>
	</tr>
	<tr id="kines">
		<td class="halfPane" id="sejours-kine"></td>
		<td id="replacement-kine"></td>
	</tr>
  <tr id="reeducateurs">
    <td class="halfPane" id="sejours-reeducateur"></td>
    <td id="replacement-reeducateur"></td>
  </tr>
</table>