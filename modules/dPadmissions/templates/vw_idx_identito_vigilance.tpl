{{* $Id: vw_idx_consult.tpl 6961 2009-09-28 17:19:13Z rhum1 $ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision: 6961 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module=dPurgences script=identito_vigilance}}

<script type="text/javascript">
onMergeComplete = function() {
  IdentitoVigilance.start(0, 80);
}

Main.add(function () {
	IdentitoVigilance.date = "{{$date}}";
  IdentitoVigilance.start(2, 5);

  var tabs = Control.Tabs.create('tab_admissions_identito_vigilance', false);
});
</script>

<ul id="tab_admissions_identito_vigilance" class="control_tabs">
  <li><a href="#identito_vigilance" class="empty">Identito-vigilance <small>(&ndash;)</small></a></li>
  <li style="width: 20em; text-align: center">
    <script type="text/javascript">
    Main.add(function() {
      Calendar.regField(getForm("changeDate").date, null, { noView: true } );
    } );
    </script>
    <strong><big>{{$date|date_format:$dPconfig.longdate}}</big></strong>
    
    <form action="?" name="changeDate" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="tab" value="{{$tab}}" />
      <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
    </form>
  </li>
</ul>
<hr class="control_tabs" />

<div id="identito_vigilance" style="display: none; margin: 0 5px;">
  <div class="small-info">{{tr}}msg-common-loading-soon{{/tr}}</div>
</div>