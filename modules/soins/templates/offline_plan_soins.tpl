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

{{mb_include style=$style template=open_printable}}

<style>
  @media print {
    table {
      border-collapse: collapse !important;
    }

    table tbody {
      page-break-inside: avoid;
      display: table-row-group !important;
    }
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

{{mb_include style=$style template=close_printable}}