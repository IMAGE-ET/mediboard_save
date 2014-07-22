{{*
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage system
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
*}}
  
{{if $is_last}}
  <input type="text" class="{{$_prop.string}}" 
         name="c[{{$_feature}}]" size="50"
         value="{{$value|smarty:nodefaults|purify|JSAttribute|stripslashes}}" {{* needed to handle ", ', \ etc well *}}
    {{if $is_inherited}} disabled {{/if}} />
{{else}}
  {{$value|smarty:nodefaults|purify}}
{{/if}}