{{*
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage reservation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

<form name="editCommentaire" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: Control.Modal.close})">
  <input type="hidden" name="m" value="reservation" />
  <input type="hidden" name="dosql" value="do_commentaire_planning_aed" />
  <input type="hidden" name="del" value="0" />
  {{if $callback}}
    <input type="hidden" name="callback" value="{{$callback}}" />
  {{/if}}
  {{mb_key object=$commentaire}}
  {{mb_field object=$commentaire field=salle_id hidden=true}}
  
  <table class="form">
    {{mb_include module=system template=inc_form_table_header object=$commentaire duplicate=$clone}}
    
    <tr>
      <th>
        {{mb_label object=$commentaire field=debut}}
      </th>
      <td>
        {{mb_field object=$commentaire field=debut form=editCommentaire register=true}}
      </td>
    </tr>
    
    <tr>
      <th>
        {{mb_label object=$commentaire field=fin}}
      </th>
      <td>
        {{mb_field object=$commentaire field=fin form=editCommentaire register=true}}
      </td>
    </tr>
    
    <tr>
      <th>
        {{mb_label object=$commentaire field=color}}
      </th>
      <td>
        {{mb_field object=$commentaire field="color" form=editCommentaire}}
      </td>
    </tr>
    
    <tr>
      <th>
        {{mb_label object=$commentaire field=libelle}}
      </th>
      <td>
        {{mb_field object=$commentaire field=libelle form="editCommentaire"}}
      </td>
    </tr>
    
    <tr>
      <th>
        {{mb_label object=$commentaire field=commentaire}}
      </th>
      <td>
        {{mb_field object=$commentaire field=commentaire form="editCommentaire"}}

      </td>
    </tr>
    
    <tr>
      <td colspan="2" class="button">
        {{if !$commentaire->_id}}
          <button type="submit" class="save">{{tr}}Create{{/tr}}</button>
        {{else}}
          <button type="button" class="save" onclick="this.form.onsubmit()">{{tr}}Save{{/tr}}</button>
          <button type="button" class="trash"
            onclick="confirmDeletion(this.form, {
              typeName: 'le commentaire',
              objName: '{{$commentaire->libelle}}',
              ajax: true
              });Control.Modal.close();">{{tr}}Delete{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>
