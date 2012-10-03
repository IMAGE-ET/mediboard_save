{{*
 * Tools detect collisions EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

<table class="tbl">
  <tr>
    <th></th>
    <th class="category">S�jour HL7</th>
    <th class="category">S�jour MB</th>
    <th class="category">Action</th>
  </tr>
  
  {{foreach from=$collisions item=_collision}}
    <tr>
      <td>{{mb_title class=CSejour field=entree_prevue}}</td>
    </tr>
  {{/foreach}}
</table>
