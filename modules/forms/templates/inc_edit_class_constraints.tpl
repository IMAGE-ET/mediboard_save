
<script>
  Control.Tabs.setTabCount("fields-constraints", {{$ex_class->_ref_constraints|@count}});
</script>

<table class="main layout" id="exClassConstraintList">
  <tr>
    <td style="width: 30em; padding-right: 5px;">
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
          <tr data-constraint_id="{{$_constraint->_id}}">
            <td class="text">
              <a href="#1" onclick="ExConstraint.edit({{$_constraint->_id}})">
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
    </td>
    <td id="exConstraintEditor">
      <div class="small-info">
      	Les contraintes permettent de d�finir dans quelles conditions les formulaires seront pr�sent�s � l'utilisateur.<br />
			  Le formulaire sera pr�sent� s'il n'y a <strong>aucune contrainte</strong>, ou si <strong>au moins une des contraintes</strong> est satisfaite.
      </div>
    </td>
  </tr>
</table>