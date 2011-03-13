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
  PlanningTechnicien.show("{{$kine_id}}", false, null, 800, false, true, true);
});

</script>

<div id="planning-technicien"></div>
