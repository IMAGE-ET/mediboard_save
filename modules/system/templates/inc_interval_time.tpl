{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $from != $to}}
  De {{$from|date_format:$conf.time}}
  �  {{$to|date_format:$conf.time}}
{{else}}
  �  {{$to|date_format:$conf.time}}
{{/if}}
