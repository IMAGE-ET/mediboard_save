{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />

  <table class="form">
    <tr>
      <th colspan="2" class="title">Activation des messageries</th>
    </tr>
  {{mb_include module=system template=inc_config_bool var=enable_internal}}
  {{mb_include module=system template=inc_config_bool var=enable_external}}
    <tr>
      <th colspan="2" class="title">Mises � jour planifi�es</th>
    </tr>
  {{mb_include module=system template=inc_config_str numeric=true var=CronJob_nbMail}}
  {{mb_include module=system template=inc_config_str numeric=true var=CronJob_schedule}}
  {{mb_include module=system template=inc_config_str numeric=true var=CronJob_olderThan}}
    <tr>
      <td class="button" colspan="2">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>
