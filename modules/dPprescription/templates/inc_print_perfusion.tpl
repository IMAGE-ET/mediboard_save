{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<li>{{if !$praticien->_id}}({{$perf->_ref_praticien->_view}}){{/if}}
	<strong>{{$perf->_view}}
	{{if $perf->duree}}
	  pendant {{$perf->duree}} heures
	{{/if}}
	{{if $perf->_protocole}}
	  {{if $perf->decalage_interv != NULL}}
      � partir de I 
      {{if $perf->decalage_interv >= 0}}+{{/if}}
        {{mb_value object=$perf field=decalage_interv}}
         heures
     {{/if}}
	{{else}}
	  {{if $perf->date_debut}}
	    � partir du {{mb_value object=$perf field="date_debut"}}
	    {{if $perf->time_debut}}
	      � {{mb_value object=$perf field="time_debut"}}
	    {{/if}}
	  {{/if}}
	{{/if}}
	</strong>
	<ul>
	  {{foreach from=$perf->_ref_lines item=_line}}
	  <li>
	    {{$_line->_view}}
		  {{if $_line->_protocole}}
		    {{if $_line->date_debut}}, � partir du {{$_line->date_debut|date_format:"%d/%m/%Y"}} 
		      {{if $_line->time_debut}}
		        � {{$_line->time_debut}}
		      {{/if}}
		    {{/if}}
	    {{/if}}
	  </li>  
	  {{/foreach}}
	</ul>
</li>