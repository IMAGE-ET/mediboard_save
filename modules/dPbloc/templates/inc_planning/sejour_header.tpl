{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<!-- Sejour -->
<th>Hospi</th>
<th>Entr�e</th>
<th>Chambre</th>
{{if $prestation->_id}}
  <th>{{$prestation}}</th>
{{/if}}
{{if $_show_comment_sejour}}
  <th>Rques</th>
{{/if}}