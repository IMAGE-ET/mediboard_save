{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

syncDateSubmit = function(oForm, curr_line_id, fieldName, type, object_class, cat_id) {
  syncDate(oForm, curr_line_id, fieldName, type, object_class, cat_id);
  if(!curr_line_id){
    return;
  } 
  
  if(!checkForm(oForm)){
    return;
  }
  submitFormAjax(oForm, 'systemMsg');
}

</script>

{{assign var=line_id value=$line->_id}}
{{assign var=_object_class value=$line->_class_name}}

{{if $line->_class_name == "CPrescriptionLineElement" && $typeDate != "mode_grille"}}
  {{assign var=category_id value=$category->_id}}
  {{assign var=chapitre value=$line->_ref_element_prescription->_ref_category_prescription->chapitre}}
{{else}}
  {{assign var=category_id value=""}}
  {{assign var=chapitre value=""}}
{{/if}}

{{if $typeDate != "mode_grille"}}
  {{assign var=onchange value="submitFormAjax(this.form, 'systemMsg');"}}
{{else}}
  {{assign var=onchange value=""}}
{{/if}}

{{if $prescription->object_id}}
<form name="editDates-{{$typeDate}}-{{$line->_id}}" action="?" method="post">
   <input type="hidden" name="m" value="dPprescription" />
   <input type="hidden" name="dosql" value="{{$dosql}}" />
   <input type="hidden" name="del" value="0" />
   <input type="hidden" name="{{$line->_spec->key}}" value="{{$line->_id}}" />
  
   <table class="main layout">
	   {{if !$line->fin}}
	   <tr>  
	     {{if $line->_can_modify_dates || $typeDate == "mode_grille"}}
	     <td style="{{if $typeDate == 'mode_grille'}}width: 190px;{{/if}}">
			   <strong>
				 {{if $chapitre == "consult" || $chapitre == "anapath" || $chapitre == "imagerie"}}
           Date
         {{else}}
           {{mb_label object=$line field=debut}}
         {{/if}}
				 </strong>
	       {{if $prescription->type != "externe" && $typeDate == "mode_grille"}}
	           <select name="debut_date" onchange="getForm('editDates-{{$typeDate}}-{{$line->_id}}').debut_da.value = new String;
				 				                                  this.form.debut.value = '';
	           																		 if(this.value == 'other') {
						 				          					           $('date_mb_field-{{$typeDate}}-{{$line_id}}').show();
						 				          					  			 } else { 
						 				          					  			   this.form.debut.value = this.value;
						 				          				             $('date_mb_field-{{$typeDate}}-{{$line_id}}').hide();
						 				          				           }">
						 	 <option value="other">Autre date</option>
						   <optgroup label="S�jour">
						     <option value="{{$prescription->_ref_object->_entree|iso_date}}">Entr�e: {{$prescription->_ref_object->_entree|date_format:"%d/%m/%Y"}}</option>
						     <option value="{{$prescription->_ref_object->_sortie|iso_date}}">Sortie: {{$prescription->_ref_object->_sortie|date_format:"%d/%m/%Y"}}</option>
						   </optgroup>
						   <optgroup label="Intervention">
							   {{foreach from=$prescription->_ref_object->_dates_operations item=_date_operation}}
							   <option value="{{$_date_operation}}">{{$_date_operation|date_format:"%d/%m/%Y"}}</option>
							   {{/foreach}}
							 </optgroup>					   
						 </select>
						 <div id="date_mb_field-{{$typeDate}}-{{$line_id}}" style="border:none;">
						   {{if $typeDate != "mode_grille" && ($chapitre == "consult" || $chapitre == "anapath" || $chapitre == "imagerie")}}
						     {{mb_field object=$line field=debut canNull=false form=editDates-$typeDate-$line_id onchange="submitFormAjax(this.form, 'systemMsg');"}}
						   {{else}}
						     {{mb_field object=$line field=debut canNull=false form=editDates-$typeDate-$line_id onchange="syncDateSubmit(this.form, '$line_id', this.name, '$typeDate','$_object_class','$category_id');"}}
			         {{/if}}
			         {{mb_field object=$line field=time_debut form=editDates-$typeDate-$line_id onchange="$onchange"}}
			       </div>	       
	       {{else}}
	         {{if $typeDate != "mode_grille" && ($chapitre == "consult" || $chapitre == "anapath" || $chapitre == "imagerie")}}
		         {{mb_field object=$line field=debut form=editDates-$typeDate-$line_id onchange="submitFormAjax(this.form, 'systemMsg');"}}  
	         {{else}}
	           {{mb_field object=$line field=debut form=editDates-$typeDate-$line_id onchange="syncDateSubmit(this.form, '$line_id', this.name, '$typeDate','$_object_class','$category_id');"}}  
	         {{/if}}
	         {{mb_field object=$line field=time_debut form=editDates-$typeDate-$line_id onchange="$onchange"}}    
			   {{/if}} 
	     </td>
	     {{else}}
	     <td>
	     	 <strong>
	       {{if $chapitre == "consult" || $chapitre == "anapath" || $chapitre == "imagerie"}}
           Date
         {{else}}
           {{mb_label object=$line field=debut}}
         {{/if}}
				 </strong>
	       {{if $line->debut}}
	         {{$line->debut|date_format:"%d/%m/%Y"}}
	         {{if $prescription->type == "sejour"}}
	           {{mb_field object=$line field=time_debut form=editDates-$typeDate-$line_id onchange="$onchange"}} 
	         {{/if}}
	       {{else}}
	        -
	       {{/if}}				   
	     </td>
	     {{/if}}
	
	     {{if $chapitre != "consult" && $chapitre != "anapath" && $chapitre != "imagerie"}}
	     <td>
	      	<strong>{{mb_label object=$line field=duree}}</strong>
	       {{if $line->_can_modify_dates || $typeDate == "mode_grille"}}
			     {{mb_field object=$line field=duree increment=1 min=1 form=editDates-$typeDate-$line_id onchange="syncDateSubmit(this.form, '$line_id', this.name, '$typeDate','$_object_class','$category_id');" size="1" }}
			     {{mb_field object=$line style="width: 70px;" field=unite_duree onchange="syncDateSubmit(this.form, '$line_id', this.name, '$typeDate','$_object_class','$category_id');"}}
			   {{else}}
			     {{if $line->duree}}
			       {{$line->duree}}
			     {{else}}
			       {{if $line->_ref_prescription->type == "sejour" && !$line->duree}} 
	              {{if $line instanceof CPrescriptionLineMedicament}}
	                {{mb_value object=$line field=_duree}}
                {{else}}
	                1
	              {{/if}}
	           {{else}}
	             - 
	           {{/if}}
			     {{/if}}
			     {{if $line->unite_duree}}
			       {{tr}}CPrescriptionLineMedicament.unite_duree.{{$line->unite_duree}}{{/tr}}	      
			     {{/if}}
			   {{/if}}
	     </td>

	     {{if $line->_can_modify_dates || $typeDate == "mode_grille"}}
	     <td>
	     	 <script type="text/javascript">
	     	 	 Main.add(function(){
						 {{if $line->_ref_prescription->type == "sejour" && !$line->_fin}} 
							 var oForm = getForm("editDates-{{$typeDate}}-{{$line->_id}}");
							 {{if $line instanceof CPrescriptionLineMedicament}}
							   $V(oForm._fin_da, "Fin du s�jour");
							 {{elseif $line instanceof CPrescriptionLineElement}}
							   $V(oForm._fin_da, "Date de d�but");
		           {{/if}}
		     	   {{/if}}
					 });
				 </script>

	     	 <strong>{{mb_label object=$line field=_fin}}</strong>
	       {{mb_field object=$line field=_fin form=editDates-$typeDate-$line_id onchange="syncDateSubmit(this.form, '$line_id', this.name, '$typeDate','$_object_class','$category_id');"}}
	       {{mb_field object=$line field=time_fin form=editDates-$typeDate-$line_id onchange="syncDateSubmit(this.form, '$line_id', this.name, '$typeDate','$_object_class','$category_id');"}}
	     </td>
	     {{else}}
	     <td>
	     	 <strong>{{mb_label object=$line field=_fin}}</strong>
	       {{if $line->_fin}}
	         {{$line->_fin|date_format:"%d/%m/%Y"}}
	       {{else}}
				   {{if $line->_ref_prescription->type == "sejour" && !$line->_fin}} 
					    {{if $line instanceof CPrescriptionLineMedicament}}
							  Fin du s�jour
							{{else}}
							  {{mb_value object=$line field=debut}}
							{{/if}}
					 {{else}}
					   - 
					 {{/if}}
	       {{/if}}				   
	     </td>
	    </tr>
	   {{/if}}
	  </tr>
	  {{/if}}
	  {{/if}}
	  {{if $line->fin}}
	  <tr>  
	   {{if $line->_can_modify_dates}}
	     <td>
	     	 <strong>Fin</strong>
	       {{mb_field object=$line field=fin canNull=false form=editDates-$typeDate-$line_id onchange="submitFormAjax(this.form, 'systemMsg');"}}
	       {{mb_field object=$line field=time_fin form=editDates-$typeDate-$line_id onchange="submitFormAjax(this.form, 'systemMsg');"}}
	     </td>
	     {{else}}
	     <td>
	       {{if $line->fin}}
	         {{$line->fin|date_format:"%d/%m/%Y"}}
	       {{else}}
	        -
	       {{/if}}				   
	     </td>
	     {{/if}}
	   {{/if}}
  </table>
 </form> 
 <div id="info_date_{{$line->_id}}" class="small-info" style="display: none;">
   Cette ligne de prescription est li�e � l'intervention, elle est seulement appliqu�e le jour de l'intervention, le {{$line->debut|date_format:"%d/%m/%Y"}}
 </div>
  {{else}}
     <!-- Selection d'une date dans le cas des protocoles -->
     {{include file="../../dPprescription/templates/line/inc_vw_duree_protocole_line.tpl"}}
	{{/if}}
