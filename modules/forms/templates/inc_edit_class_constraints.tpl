<table class="main layout">

{{if true}}
  <tr>
    <td>
      <table class="main tbl">
        <tr>
          <td class="empty">En cours de développement</td>
        </tr>
      </table>
    </td>
  </tr>
  
{{else}}
  <tr>
    <td style="width: 20em; padding-right: 5px;">
      <button type="button" class="new" style="float: right;" onclick="ExConstraint.create({{$ex_class->_id}})">
        {{tr}}CExClassConstraint-title-create{{/tr}}
      </button>
      
      <table class="main tbl">
        <tr>
          <th>{{mb_title class=CExClassConstraint field=field}}</th>
          <th>{{mb_title class=CExClassConstraint field=operator}}</th>
          <th>{{mb_title class=CExClassConstraint field=value}}</th>
        </tr>
        {{foreach from=$ex_class->_ref_constraints item=_constraint}}
          <tr>
            <td>
              <a href="#1" onclick="ExConstraint.edit({{$_constraint->_id}})">
                <strong>
                  {{$_constraint}}
                </strong>
              </a>
            </td>
            <td style="text-align: center;">{{mb_value object=$_constraint field=operator}}</td>
            <td>
              {{if $_constraint->_ref_target_object->_id}}
                {{$_constraint->_ref_target_object}}
              {{else}}
                {{mb_value object=$_constraint field=value}}
              {{/if}}
            </td>
          </tr>
        {{foreachelse}}
          <tr>
            <td colspan="3" class="empty">{{tr}}CExClassConstraint.none{{/tr}}</td>
          </tr>
        {{/foreach}}
      </table>
    </td>
    <td id="exConstraintEditor">
      <!-- exConstraintEditor -->&nbsp;
    </td>
  </tr>
{{/if}}

</table>