{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage forms
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
toggleListCustom = function(form) {
  var concept_type = $V(form._concept_type);
	
  if (concept_type) {
	  var enableList = (concept_type == "concept");
	  
	  var input = form.concept_id_autocomplete_view;
	  var select = form._spec_type;
	  
	  if (input) {
	    input.up(".dropdown").down(".dropdown-trigger").setVisibility(enableList);
	    input.disabled = input.readOnly = !enableList;
	  }
	  
	  if (enableList) {
	    //$V(select, "none");
	  }
	  else {
	    $V(input, "");
	    $V(form.concept_id, "");
	  }
	  
	  select.disabled = select.readOnly = !!$V(form.ex_class_field_id) || enableList;
	}
  
  ExFieldSpec.edit(form);
}

Main.add(function(){
  var form = getForm("editField");
  var field_id = $V(form.ex_class_field_id);

  toggleListCustom.defer(form);
  form.elements._locale.select();

  if (field_id)
    ExFormula.edit(field_id);

  new Control.Tabs("ExClassField-param", {
    afterChange: function(newContainer){
      ExFormula.toggleInsertButtons(newContainer.id == "fieldFormulaEditor");
    }
  });
});

updateInternalName = function(e){
  var form = e.form;
	var str = ExField.slug($V(e));
	$V(form.elements.name, str);
}
</script>

<form name="editField" method="post" action="?" data-object_guid="{{$ex_field->_guid}}" onsubmit="return onSubmitFormAjax(this, {onComplete: ExClass.edit.curry({{$ex_class->_id}})})">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="dosql" value="do_ex_class_field_aed" />
  <input type="hidden" name="del" value="0" />
  
  <input type="hidden" name="_triggered_data" value="{{$ex_field->_triggered_data}}" />
	
  {{mb_key object=$ex_field}}
  {{mb_field object=$ex_field field=ex_group_id hidden=true}}
	
	{{mb_field object=$ex_field field=name hidden=true}}
  
  <table class="form">
  	
    {{assign var=object value=$ex_field}}
		<tr>
		  {{if $object->_id}}
		  <th class="title modify text" colspan="4">
		    {{mb_include module=system template=inc_object_notes}}
		    {{mb_include module=system template=inc_object_idsante400}}
		    {{mb_include module=system template=inc_object_history}}
		    {{tr}}{{$object->_class_name}}-title-modify{{/tr}} 
		    '{{$object}}'
		  </th>
		  {{else}}
		  <th class="title text" colspan="4">
		    {{tr}}{{$object->_class_name}}-title-create{{/tr}} 
		  </th>
		  {{/if}}
		</tr>
    
    <tr>
      <th style="width: 8em;">{{mb_label object=$ex_field field=_locale}}</th>
      <td>
      	{{if $ex_field->_id}}
          {{mb_field object=$ex_field field=_locale size=30}}
				{{else}}
      	  {{mb_field object=$ex_field field=_locale onkeyup="updateInternalName(this)" size=30}}
				{{/if}}
			</td>
      
      <th>{{mb_label object=$ex_field field=_locale_court}}</th>
      <td>{{mb_field object=$ex_field field=_locale_court tabIndex="3" size=30}}</td>
    </tr>
		
    <tr>
      <th>{{mb_label object=$ex_field field=_locale_desc}}</th>
      <td>{{mb_field object=$ex_field field=_locale_desc tabIndex="2" size=30}}</td>
      
      <th><label for="ex_group_id">Groupe</label></th>
      <td>
        <select name="ex_group_id">
          {{foreach from=$ex_class->_ref_groups item=_group}}
            <option value="{{$_group->_id}}" {{if $_group->_id == $ex_field->ex_group_id}}selected="selected"{{/if}}>{{$_group}}</option>
          {{/foreach}}
        </select>
      </td>
		</tr>
		
		<tr>
			
			{{if $ex_field->_id}}
			
			  <th>
			  	{{if $ex_field->concept_id}}
					  Concept [liste/type]
					{{else}}
			  	  Type
					{{/if}}
				</th>
				<td colspan="3">
					<strong>
						{{if $ex_field->concept_id}}
		          {{mb_value object=$ex_field field=concept_id}}
		          {{mb_field object=$ex_field field=concept_id hidden=true}}
						{{else}}
						  {{tr}}CMbFieldSpec.type.{{$spec_type}}{{/tr}}
						{{/if}}
					</strong>
				</td>
				
			{{else}}
			
	      <th>
	        <label>
	          Type personnalisé
            <input type="radio" {{if !$ex_field->concept_id}} checked="checked" {{/if}} 
                   onclick="toggleListCustom(this.form)" name="_concept_type" value="custom" />
	        </label>
	      </th>
	      
	      <td>
          <select  name="_spec_type" onchange="ExFieldSpec.edit(this.form)">
            {{foreach from="CMbFieldSpecFact"|static:classes item=_class key=_key}}
              <option value="{{$_key}}" {{if $_key == $spec_type && !$ex_field->concept_id}}selected="selected"{{/if}}>
              	{{tr}}CMbFieldSpec.type.{{$_key}}{{/tr}}
							</option>
            {{/foreach}}
          </select>
	      </td>
	      
	      <th>
	        <label>
	          {{tr}}CExClassField-concept_id{{/tr}}
						
            <input type="radio" {{if $ex_field->concept_id}} checked="checked" {{/if}}
						       onclick="toggleListCustom(this.form)" name="_concept_type" value="concept" />
	        </label>
	      </th>
				
	      <td>
          {{mb_field object=$ex_field field=concept_id form="editField" autocomplete="true,1,50,false,true" 
                     onchange="ExFieldSpec.edit(this.form)"}}
	      </td>
				
			{{/if}}
			
    </tr>
		
		<tr>
			<th></th>
      <td colspan="3">
        {{if $ex_field->_id}}
          <button type="submit" class="modify">{{tr}}Save{{/tr}}</button>
          <button type="button" class="trash" onclick="confirmDeletion(this.form,{ajax:true,typeName:'le champ ',objName:'{{$ex_field->_view|smarty:nodefaults|JSAttribute}}'})">
            {{tr}}Delete{{/tr}}
          </button>
        {{else}}
          <button type="submit" class="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
		
		<tr {{if $app->user_prefs.INFOSYSTEM == 0}}style="display: none"{{/if}}>
			<th></th>
			<td colspan="3">
				{{mb_field object=$ex_field field=prop readonly="readonly" size=50}}
			</td>
    </tr>
		
  </table>
</form>

<ul class="control_tabs" id="ExClassField-param">
  <li><a href="#fieldSpecEditor">Propriétés</a></li>
  <li><a href="#fieldFormulaEditor" {{if !$ex_field->formula}} class="empty" {{/if}} style="background-image: url(style/mediboard/images/buttons/formula.png); background-repeat: no-repeat; background-position: 2px 2px; padding-left: 18px;">Formule / concaténation</a></li>
</ul>
<hr class="control_tabs" />

<div id="fieldSpecEditor" style="white-space: normal; display: none;"></div>

<div id="fieldFormulaEditor" style="display: none;"></div>
