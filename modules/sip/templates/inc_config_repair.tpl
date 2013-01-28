{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage sip
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editConfigRepair" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />
  <table class="form">
    {{assign var="mod" value="sip"}}
    <tr>
      <th class="title" colspan="10">{{tr}}config-{{$mod}}-repair{{/tr}}</th>
    </tr>
    {{mb_include module=system template=inc_config_str var=repair_segment}}
    
    {{mb_include module=system template=inc_config_str var=repair_date_min}}
    
    {{mb_include module=system template=inc_config_str var=repair_date_max}}
		
		{{mb_include module=system template=inc_config_bool var=verify_repair}}
              
    <tr>
      <td class="button" colspan="10">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>

<table class="tbl">
	<tr>
	  <th class="title" colspan="100">{{tr}}sip-repair-classes{{/tr}}</th>
	</tr>
	
	<tr>
	  <td>
	    {{tr}}sip-repair-class{{/tr}} '{{tr}}CSejour{{/tr}}'
	  </td>
	  <td>
	    <button type="button" class="new" onclick="SIP.repair('start', 'sejour')">
	      {{tr}}Start{{/tr}}      
	    </button>
	    <button type="button" class="change" onclick="SIP.repair('retry', 'sejour')">
	      {{tr}}Retry{{/tr}}      
	    </button>
	    <button type="button" class="tick" onclick="SIP.repair('continue', 'sejour')">
	      {{tr}}Continue{{/tr}}      
	    </button>
	  </td>
	  <td id="repair"></td>
	</tr>
</table>

{{if $conf.sip.verify_repair}}
<div class="small-info">Vous �tes en mode v�rification des r�parations � effectuer.</div>
{{/if}}