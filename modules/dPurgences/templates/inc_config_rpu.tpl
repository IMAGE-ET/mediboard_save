{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script>
  Main.add(function() {
    getForm("editConfig-RPU")["dPurgences[sibling_hours]"].addSpinner({min:0, max:24});
  });
</script>

<form name="editConfig-RPU" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="dosql" value="do_configure" />
  <table class="form">    
  
    {{mb_include module=system template=inc_config_enum var=old_rpu values="0|1"}}
    {{mb_include module=system template=inc_config_bool var=allow_change_patient}}
    {{mb_include module=system template=inc_config_enum var=sortie_prevue values="sameday|h24"}}
    {{mb_include module=system template=inc_config_bool var=only_prat_responsable}}
    {{mb_include module=system template=inc_config_bool var=gerer_reconvoc}}
    {{mb_include module=system template=inc_config_bool var=gerer_hospi}}
    {{mb_include module=system template=inc_config_bool var=gerer_circonstance}}
    {{mb_include module=system template=inc_config_str var=sibling_hours size="2" suffix="h"}}
    {{mb_include module=system template=inc_config_bool var=pec_change_prat}}
    {{mb_include module=system template=inc_config_bool var=pec_after_sortie}}
    {{mb_include module=system template=inc_config_bool var=create_sejour_hospit}}
    {{mb_include module=system template=inc_config_bool var=valid_cotation_sortie_reelle}}
    {{mb_include module=system template=inc_config_bool var=use_blocage_lit}}
    {{mb_include module=system template=inc_config_bool var=motif_rpu_view}}
    {{mb_include module=system template=inc_config_bool var=create_affectation}}

    <tr>
      <td class="button" colspan="2">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>
