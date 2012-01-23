{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=hl7 script=mllp_server}}

<table class="tbl">
  <tr>
    <th class="narrow">Processus #id</th>
    <th class="narrow">Port</th>
    <th class="narrow">Accessible</th>
    <th class="narrow">Lanc�</th>
    <th class="narrow">{{tr}}Actions{{/tr}}</th>
    <th> Statistiques </th>
  </tr>
  {{foreach from=$processes key=process_id item=_process}}
    {{unique_id var=uid}}
    <tbody id="{{$uid}}">
      {{mb_include module=hl7 template=inc_server_mllp}}
    </tbody>
  {{/foreach}}
</table>