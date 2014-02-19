

<table class="main tbl">
  <tr>
    <th class="title" colspan="3">
      <button type="button" class="new" style="float: right;" onclick="ExConstraint.create({{$ex_class_event->_id}})">
        {{tr}}CExClassConstraint-title-create{{/tr}}
      </button>
      {{tr}}CExClassEvent-back-constraints{{/tr}}
    </th>
  </tr>
  <tr>
    <th>{{mb_title class=CExClassConstraint field=field}}</th>
    <th>{{mb_title class=CExClassConstraint field=operator}}</th>
    <th>{{mb_title class=CExClassConstraint field=value}}</th>
  </tr>
  {{foreach from=$constraints item=_constraint}}
    <tr data-constraint_id="{{$_constraint->_id}}">
      <td class="text">
        <a href="#1" onclick="ExConstraint.edit({{$_constraint->_id}}); return false;">
          {{$_constraint}}
        </a>
      </td>
      <td style="text-align: center;">{{mb_value object=$_constraint field=operator}}</td>
      <td class="text">
        {{if !$_constraint->_ref_target_object}}
          <div class="small-error">
            L'objet cible n'existe plus
          </div>
        {{else}}
          {{if $_constraint->_ref_target_object->_id}}
            {{if $_constraint->_ref_target_object instanceof CMediusers}}
              {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_constraint->_ref_target_object}}
            {{else}}
              <span onmouseover="ObjectTooltip.createEx(this, '{{$_constraint->_ref_target_object->_guid}}');">
                {{$_constraint->_ref_target_object}}
              </span>
            {{/if}}
          {{elseif $_constraint->operator == "in"}}
            {{assign var=_values value=$_constraint->getInValues()}}
            {{foreach from=$_values item=_value name=_in}}
              <span style="background: #ddd; padding: 0 2px; white-space: nowrap;">{{$_value}}</span>{{if !$smarty.foreach._in.last}},{{/if}}
            {{/foreach}}
          {{else}}
            {{mb_value object=$_constraint field=value}}
          {{/if}}
        {{/if}}
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="3" class="empty">{{tr}}CExClassConstraint.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>

<div class="small-info">
  Les contraintes permettent de d�finir dans quelles conditions les formulaires seront pr�sent�s � l'utilisateur.<br />
  Le formulaire sera pr�sent� s'il n'y a <strong>aucune contrainte</strong>, ou si <strong>au moins une des contraintes</strong> est satisfaite.
</div>