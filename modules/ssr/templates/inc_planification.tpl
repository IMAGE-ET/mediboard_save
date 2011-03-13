{{* $Id: vw_aed_rpu.tpl 7951 2010-02-01 10:44:08Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 7951 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=ssr script=planning}}
{{mb_script module=ssr script=planification}}

{{if $bilan->_id && !$bilan->planification}} 
<div class="small-info">
  {{tr}}CBilanSSR-msg-planification-off{{/tr}}
  <br />
  {{tr}}CBilanSSR-msg-planification-cf{{/tr}}
</div>
{{else}}

<script type="text/javascript">

Planification.onCompleteShowWeek = function() {
  Planification.refreshSejour();
  PlanningTechnicien.show();
  PlanningEquipement.show();
//  Planification.refreshActivites();
}

onKeyDelete = function(e) {
  if (Event.key(e) == Event.KEY_DELETE) {
    $("editSelectedEvent_delete").onclick();
  }
}

Main.add(function(){
  Planification.showWeek();
  var planning = $("planning");
  var vp = document.viewport.getDimensions();
  var top = planning.cumulativeOffset().top;
  planning.setStyle({
    height: (vp.height-top-20)+"px"
  });
  document.observe("keydown", onKeyDelete);
});

printPlanningSejour = function(){
  var url = new Url("ssr", "print_planning_sejour");
  url.addParam("sejour_id", "{{$sejour->_id}}");
  url.popup("700","700","Planning du patient");
}
</script>

<div id="week-changer"></div>

<table class="main" id="planning" style="table-layout: fixed;">
  <col style="width: 50%;" />
  
	<tr style="height: 50%;">
		<td style="height: 50%;">
			<div style="position: relative; height: 100%;">
			  <button type="button" style="position: absolute; top: 0px; right: 0px;" class="print notext" onclick="printPlanningSejour();">{{tr}}Print{{/tr}}</button>
				<div id="planning-sejour" style="height: 100%;"></div>
			</div>
		</td>
    <td id="activites-sejour"></td>
	</tr>
	
  <tr style="height: 50%;">
  	<td style="height: 50%;">
			<div style="position: relative; height: 100%;">
			  <button type="button" style="position: absolute; top: 0px; right: 0px;" class="change notext" onclick="PlanningTechnicien.toggle();"></button>
				<div id="planning-technicien" style="height: 100%;"></div>
		  </div>
		</td>
    <td style="height: 50%;">
      <!-- it's better to have the same dom here -->
      <div style="position: relative; height: 100%;">
        <div id="planning-equipement" style="height: 100%;"></div>
      </div>
    </td>
  </tr>
</table>

{{/if}}
