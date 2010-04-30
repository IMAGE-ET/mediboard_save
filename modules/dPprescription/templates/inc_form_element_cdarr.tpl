{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editCdarr" action="" method="post">
 <input type="hidden" name="m" value="ssr" />
 <input type="hidden" name="dosql" value="do_element_prescription_to_cdarr_aed" />
 <input type="hidden" name="del" value="0" />
 <input type="hidden" name="element_prescription_id" value="{{$element_prescription->_id}}" />
 <input type="hidden" name="element_prescription_to_cdarr_id" value="{{$element_prescription_to_cdarr->_id}}" />
 
 <table class="form">
   <tr>
     {{if $element_prescription_to_cdarr->_id}}
       <th class="title text modify" colspan="2">
         {{mb_include module=system template=inc_object_idsante400 object=$element_prescription_to_cdarr}}
         {{mb_include module=system template=inc_object_history object=$element_prescription_to_cdarr}}
         Modification du code CdARR pour '{{$element_prescription->_view}}'
       </th>
     {{else}}
       <th class="title text" colspan="2">
         Ajout d'un code CdARR pour '{{$element_prescription->_view}}'
       </th>
     {{/if}}
    </tr>
    <tr>
      <th>{{mb_label object=$element_prescription_to_cdarr field="code"}}</th>
      <td>
        {{mb_field object=$element_prescription_to_cdarr field=code class="autocomplete"}}
        <div style="display:none;" class="autocomplete" id="code_auto_complete"></div>
      </td>
    </tr>
    <tr>
     <th>{{mb_label object=$element_prescription_to_cdarr field="commentaire"}}</th>
     <td>{{mb_field object=$element_prescription_to_cdarr field="commentaire"}}</td>
   </tr>
   
   <tr>
    <td class="button" colspan="2">
      <button type="submit" class="submit">{{tr}}Save{{/tr}}</button>
      <button class="trash" type="button" name="delete" onclick="confirmDeletion(this.form,{typeName:'le code',objName:'{{$element_prescription_to_cdarr->code|smarty:nodefaults|JSAttribute}}'})">
        {{tr}}Delete{{/tr}}
      </button>
    </td>
   </tr>
  </table>
</form>
