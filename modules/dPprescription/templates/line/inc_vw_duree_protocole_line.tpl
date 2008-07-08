{{assign var=line_id value=$line->_id}}

{{if $typeDate != "mode_grille"}}
  {{assign var=onchange value="submitFormAjax(this.form, 'systemMsg');"}}
{{else}}
  {{assign var=onchange value=""}}
{{/if}}
<tr>
  <td></td>
  <td colspan="5">
    <form name="editDuree-{{$typeDate}}-{{$line->_id}}" action="?" method="post">
      <input type="hidden" name="m" value="dPprescription" />
      <input type="hidden" name="dosql" value="{{$dosql}}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="{{$line->_spec->key}}" value="{{$line->_id}}" />
      
      <!-- Dur�e -->
      {{if $typeDate != "anapath" && $typeDate != "imagerie" && $typeDate != "consult"}}
	      Dur�e de 
	      {{if $typeDate != "mode_grille"}}
	        {{mb_field object=$line field=duree increment=1 min=1 form=editDuree-$typeDate-$line_id size="3"
	                   onchange="submitFormAjax(this.form, 'systemMsg');"}}
	      {{else}}
	         {{mb_field object=$line field=duree increment=1 min=1 form=editDuree-$typeDate-$line_id size="3"}}
	      {{/if}}
				jour(s)
				<!-- D�calage -->
				� partir de 
			{{/if}}
			{{if $prescription->object_class == "CSejour"}}
			  {{if $typeDate != "mode_grille"}}
			    {{mb_field object=$line field=jour_decalage onchange="submitFormAjax(this.form, 'systemMsg');" defaultOption="&mdash Choix"}}
			  {{else}}
			    {{mb_field object=$line field=jour_decalage defaultOption="&mdash Choix"}}
			  {{/if}}
			{{else}}
			J
			{{/if}}
			{{if $typeDate != "mode_grille"}}
			  {{mb_field showPlus=1 object=$line field=decalage_line increment=1 form=editDuree-$typeDate-$line_id 
			           onchange="submitFormAjax(this.form, 'systemMsg');" size="3"}}
			 {{else}}
			   {{mb_field showPlus=1 object=$line field=decalage_line increment=1 form=editDuree-$typeDate-$line_id size="3"}}
			 {{/if}}
			           (Jours)
			� 
			{{if $typeDate != "mode_grille"}}
			  {{mb_field object=$line field=time_debut form=editDuree-$typeDate-$line_id onchange="submitFormAjax(this.form, 'systemMsg');"}}
			{{else}}
			  {{mb_field object=$line field=time_debut form=editDuree-$typeDate-$line_id}}
			{{/if}}
    </form>
    <script type="text/javascript">
      Main.add( function(){
       prepareForm(document.forms['editDuree-{{$typeDate}}-{{$line->_id}}']); 
      } );
    </script>
  </td>
</tr>

