{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editConfig-treatment" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />
  <table class="form">
    {{mb_include module=system template=inc_config_str var=functionPratImport}}
    
    {{mb_include module=system template=inc_config_str var=medecinIndetermine}}
    
    {{mb_include module=system template=inc_config_bool var=medecinActif}}
    
    {{mb_include module=system template=inc_config_bool var=strictSejourMatch}}
    
    {{mb_include module=system template=inc_config_bool var=notifier_sortie_reelle}}
    
    {{mb_include module=system template=inc_config_bool var=notifier_entree_reelle}}
    
    {{mb_include module=system template=inc_config_bool var=trash_numdos_sejour_cancel}}
    
    {{mb_include module=system template=inc_config_enum var=code_transmitter_sender values=mb_id|finess}}
    
    {{mb_include module=system template=inc_config_enum var=code_receiver_sender values=dest|finess}}
   
    <tr>
      <td class="button" colspan="10">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>