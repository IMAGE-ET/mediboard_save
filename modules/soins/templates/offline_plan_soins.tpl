{{*
 * $Id$
 *  
 * @category Soins
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<style>
  table tbody.line_print {
    page-break-inside: avoid;
  }
</style>

{{foreach from=$sejours item=sejour name=sejour}}
  {{mb_include module=soins template=inc_offline_plan_soins}}

  {{if !$smarty.foreach.sejour.last && $sejours|@count > 1}}
    <hr style="border: 0; page-break-after: always" />
  {{/if}}
{{foreachelse}}
  <h2>Pas de plan de soins � afficher.</h2>
{{/foreach}}
