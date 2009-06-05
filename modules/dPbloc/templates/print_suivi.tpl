{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="main">
  <tr>
    <td class="button not-printable" colspan="0">
      {{foreach from=$bloc->_ref_salles item=_salle}}
        <label><input type="checkbox" onclick="Effect.toggle('salle-{{$_salle->_id}}', 'appear');" checked="checked" /> {{$_salle->nom}}</label>
      {{/foreach}}
    </td>
  </tr>
  <tr>
    {{foreach from=$bloc->_ref_salles item=_salle}}
	    <td id="salle-{{$_salle->_id}}">
	      <table class="form">
	        <tr>
	          <th class="category">{{$_salle->nom}}</th>
	        </tr>
	      </table>
	      {{assign var="salle" value=$_salle}}     
	      {{include file="./inc_planning/print_suivi_plages.tpl"}}
	    </td>
    {{foreachelse}}
      <td>{{tr}}CSalle.none{{/tr}}</td>
    {{/foreach}}
  </tr>
</table>