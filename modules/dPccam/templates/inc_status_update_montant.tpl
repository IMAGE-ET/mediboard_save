{{*
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage ccam
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
*}}

{{if $current >= $total}}
  <script type="text/javascript">
    Main.add(function() {
      stopUpdateMontant();
    });
  </script>

  <div class="small-info">Mise � jour des montants termin�e : {{$total}} actes trait�s</div>
{{else}}
  <div class="small-info">Nombre d'actes � traiter : {{$total}}</div>
  <div class="small-info">Nombre d'actes trait�s : {{$current}}</div>
{{/if}}