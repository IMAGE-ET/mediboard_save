{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=active_list_type value=null}}
{{assign var=types value="CDailyCheckList"|static:types}}

{{foreach from=$operation_check_lists item=_check_list key=_type}}
  {{if $_check_list->_readonly && !$active_list_type}}
    {{assign var=active_list_type value=$types.$_type}}
  {{/if}}
{{/foreach}}

<script type="text/javascript">
var checkListTypes = ["normal", "endoscopie", "endoscopie-bronchique", "radio"];

function showCheckListType(element, type) {
  checkListTypes.each(function(t){
    if (t != type)
      element.select('tr.'+t).invoke("hide");
  });
  
  element.select('tr.'+type).invoke("show");
}

Main.add(function(){
  showCheckListType($("checkList-container"), "{{$active_list_type}}" || "normal");
});
</script>

<table class="main form" id="checkList-container">
  <col style="width: 33%" />
  <col style="width: 33%" />
  <col style="width: 33%" />
  
  <tr>
    <th colspan="10" class="title">

      <button class="down" onclick="$('check-lists').toggle(); $(this).toggleClassName('down').toggleClassName('up')">
        Check list S�curit� du Patient
      </button>
      
      <select onchange="showCheckListType($(this).up('table'), $V(this))" style="max-width: 18em;">
        <option value="normal" {{if $active_list_type == "normal"}} selected="selected" {{/if}}>Au bloc op�ratoire (v. 2011-01)</option>
        <option value="endoscopie" {{if $active_list_type == "endoscopie"}} selected="selected" {{/if}}>En endoscopie digestive (v. 2010-01)</option>
        <option value="endoscopie-bronchique" {{if $active_list_type == "endoscopie-bronchique"}} selected="selected" {{/if}}>En endoscopie bronchique (v. 2011-01)</option>
        <option value="radio" {{if $active_list_type == "radio"}} selected="selected" {{/if}}>En radiologie interv. (v. 2011-01)</option>
      </select>
      
      <img height="20" src="images/pictures/logo-has-small.png" />
      
      <button class="print" onclick="(new Url('dPsalleOp', 'print_check_list_operation')).addParam('operation_id', {{$selOp->_id}}).popup(800, 600, 'check_list')">{{tr}}Print{{/tr}}</button>
      
      {{mb_include module=forms template=inc_widget_ex_class_register object=$selOp event=checklist cssStyle="display: inline-block; font-size: 0.8em;"}}
    </th>
  </tr>
  
  <tr class="normal" style="display: none;">
    <td class="button" id="preanesth-title">
      <h3 style="margin: 2px;">
        <img src="images/icons/{{$operation_check_lists.preanesth->_readonly|ternary:"tick":"cross"}}.png" />
        Avant induction anesth�sique
      </h3>
      <small>Temps de pause avant anesth�sie</small>
    </td>
    <td class="button" id="preop-title">
      <h3 style="margin: 2px;">
        <img src="images/icons/{{$operation_check_lists.preop->_readonly|ternary:"tick":"cross"}}.png" />
        Avant intervention chirurgicale
      </h3>
      <small>Temps de pause avant incision</small>
    </td>
    <td class="button" id="postop-title">
      <h3 style="margin: 2px;">
        <img src="images/icons/{{$operation_check_lists.postop->_readonly|ternary:"tick":"cross"}}.png" />
        Apr�s intervention
      </h3>
      <small>Pause avant sortie de salle d'intervention</small>
    </td>
  </tr>
    
  <tr class="endoscopie" style="display: none;">
    <td class="button" id="preendoscopie-title" colspan="2">
      <h3 style="margin: 2px;">
        <img src="images/icons/{{$operation_check_lists.preendoscopie->_readonly|ternary:"tick":"cross"}}.png" />
        Avant l'endoscopie digestive
      </h3>
      <small>Avec ou sans anesth�sie</small>
    </td>
    <td class="button" id="postendoscopie-title">
      <h3 style="margin: 2px;">
        <img src="images/icons/{{$operation_check_lists.postendoscopie->_readonly|ternary:"tick":"cross"}}.png" />
        Apr�s l'endoscopie digestive
      </h3>
    </td>
  </tr>
    
  <tr class="endoscopie-bronchique" style="display: none;">
    <td class="button" id="preendoscopie_bronchique-title" colspan="2">
      <h3 style="margin: 2px;">
        <img src="images/icons/{{$operation_check_lists.preendoscopie_bronchique->_readonly|ternary:"tick":"cross"}}.png" />
        Avant l'endoscopie bronchique
      </h3>
      <small>Avec ou sans anesth�sie</small>
    </td>
    <td class="button" id="postendoscopie_bronchique-title">
      <h3 style="margin: 2px;">
        <img src="images/icons/{{$operation_check_lists.postendoscopie_bronchique->_readonly|ternary:"tick":"cross"}}.png" />
        Apr�s l'endoscopie bronchique
      </h3>
    </td>
  </tr>
  
  <tr class="radio" style="display: none;">
    <td class="button" id="preanesth_radio-title">
      <h3 style="margin: 2px;">
        <img src="images/icons/{{$operation_check_lists.preanesth_radio->_readonly|ternary:"tick":"cross"}}.png" />
        Avant anesth�sie ou s�dation
      </h3>
    </td>
    <td class="button" id="preop_radio-title">
      <h3 style="margin: 2px;">
        <img src="images/icons/{{$operation_check_lists.preop_radio->_readonly|ternary:"tick":"cross"}}.png" />
        Avant intervention
      </h3>
    </td>
    <td class="button" id="postop_radio-title">
      <h3 style="margin: 2px;">
        <img src="images/icons/{{$operation_check_lists.postop_radio->_readonly|ternary:"tick":"cross"}}.png" />
        Apr�s intervention
      </h3>
    </td>
  </tr>
  
  <tbody id="check-lists" style="display: none;">
    <tr class="normal" style="display: none;">
      <td style="padding:1px;">
        <div id="preanesth">
        {{assign var=check_list value=$operation_check_lists.preanesth}}
        {{mb_include module=dPsalleOp template=inc_edit_check_list 
             check_item_categories=$operation_check_item_categories.preanesth
             personnel=$listValidateurs}}
        </div>
      </td>
      <td style="padding:1px;">
        <div id="preop">
        {{assign var=check_list value=$operation_check_lists.preop}}
        {{mb_include module=dPsalleOp template=inc_edit_check_list 
             check_item_categories=$operation_check_item_categories.preop
             personnel=$listValidateurs}}
        </div>
      </td>
      <td style="padding:1px;">
        <div id="postop">
        {{assign var=check_list value=$operation_check_lists.postop}}
        {{mb_include module=dPsalleOp template=inc_edit_check_list 
             check_item_categories=$operation_check_item_categories.postop
             personnel=$listValidateurs}}
        </div>
      </td>
    </tr>
    
    <tr class="endoscopie" style="display: none;">
      <td style="padding:0;" colspan="2">
        <div id="preendoscopie">
        {{assign var=check_list value=$operation_check_lists.preendoscopie}}
        {{mb_include module=dPsalleOp template=inc_edit_check_list 
             check_item_categories=$operation_check_item_categories.preendoscopie
             personnel=$listValidateurs}}
        </div>
      </td>
      <td style="padding:0;">
        <div id="postendoscopie">
        {{assign var=check_list value=$operation_check_lists.postendoscopie}}
        {{mb_include module=dPsalleOp template=inc_edit_check_list 
             check_item_categories=$operation_check_item_categories.postendoscopie
             personnel=$listValidateurs}}
        </div>
      </td>
    </tr>
    
    <tr class="endoscopie-bronchique" style="display: none;">
      <td style="padding:0;" colspan="2">
        <div id="preendoscopie_bronchique">
        {{assign var=check_list value=$operation_check_lists.preendoscopie_bronchique}}
        {{mb_include module=dPsalleOp template=inc_edit_check_list 
             check_item_categories=$operation_check_item_categories.preendoscopie_bronchique
             personnel=$listValidateurs}}
        </div>
      </td>
      <td style="padding:0;">
        <div id="postendoscopie_bronchique">
        {{assign var=check_list value=$operation_check_lists.postendoscopie_bronchique}}
        {{mb_include module=dPsalleOp template=inc_edit_check_list 
             check_item_categories=$operation_check_item_categories.postendoscopie_bronchique
             personnel=$listValidateurs}}
        </div>
      </td>
    </tr>
    
    <tr class="radio" style="display: none;">
      <td style="padding:1px;">
        <div id="preanesth_radio">
        {{assign var=check_list value=$operation_check_lists.preanesth_radio}}
        {{mb_include module=dPsalleOp template=inc_edit_check_list 
             check_item_categories=$operation_check_item_categories.preanesth_radio
             personnel=$listValidateurs}}
        </div>
      </td>
      <td style="padding:1px;">
        <div id="preop_radio">
        {{assign var=check_list value=$operation_check_lists.preop_radio}}
        {{mb_include module=dPsalleOp template=inc_edit_check_list 
             check_item_categories=$operation_check_item_categories.preop_radio
             personnel=$listValidateurs}}
        </div>
      </td>
      <td style="padding:1px;">
        <div id="postop_radio">
        {{assign var=check_list value=$operation_check_lists.postop_radio}}
        {{mb_include module=dPsalleOp template=inc_edit_check_list 
             check_item_categories=$operation_check_item_categories.postop_radio
             personnel=$listValidateurs}}
        </div>
      </td>
    </tr>
    
    <tr>
      <td colspan="3" class="button text">
        <hr />
        Le r�le du coordonnateur check-list sous la responsabilit� du(es) chirurgien(s) et anesth�siste(s) responsables 
        de l'intervention est de ne cocher les items de la check list  que (1) si la v�rification a bien �t� effectu�e,  
        (2) si elle a �t� faite oralement en pr�sence des membres de l'�quipe concern�e et (3) si les non conformit�s (marqu�es d'un *) 
        ont fait l'objet d'une concertation en �quipe et d'une d�cision qui doit le cas �ch�ant �tre rapport�e dans l'encart sp�cifique.
      </td>
    </tr>
  </tbody>
</table>
