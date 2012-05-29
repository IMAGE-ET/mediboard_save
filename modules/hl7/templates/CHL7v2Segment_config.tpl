{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editConfig-CHL7v2Segment" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />
  <table class="form">
    {{assign var=class value=CHL7v2Segment}}    
    {{mb_include module=system template=inc_config_bool var=ignore_unexpected_z_segment}}
    
    <tr>
      <th class="title" colspan="2">{{tr}}{{$class}}PV1{{/tr}}</th>
    </tr>
    
    {{mb_include module=system template=inc_config_str var=PV1_3_2}}
    {{mb_include module=system template=inc_config_str var=PV1_3_3}}
    
    <tr>
      <td class="button" colspan="10">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>