{{*
 * Details Exchange Tabular EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

{{if $exchange instanceof CExchangeHL7v2}}
  {{mb_include template=inc_exchange_er7_details}}
{{elseif $exchange instanceof CEchangeHprim21 || $exchange instanceof CExchangeHprimSante}}
  {{mb_include template=inc_exchange_hpr_details}}
{{/if}}
      