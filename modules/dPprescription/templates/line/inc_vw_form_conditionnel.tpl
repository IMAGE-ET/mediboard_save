{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $prescription->type != "externe"}}
	{{if $line->_can_view_form_conditionnel}}
	  <input name="conditionnel" type="checkbox" {{if $line->conditionnel}}checked="checked"{{/if}} onchange="submitConditionnel('{{$line->_class}}','{{$line->_id}}',this.checked)"  />
	  {{mb_label object=$line field="conditionnel"}}
	{{elseif !$line->_protocole}}
	  {{mb_label object=$line field="conditionnel"}}:
	  {{if $line->conditionnel}}Oui{{else}}Non{{/if}} 
	{{/if}}
{{/if}}