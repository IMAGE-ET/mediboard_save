<script type="text/javascript">

{{if $type == "Med"}}
{{if !$line->_traitement && $line->_ref_prescription->object_id}}
{{if !$line->signee && !$line->fin}}
	var oForm = document.forms["editDates-Med-{{$line->_id}}"];
	$V(oForm.debut,'{{$line->debut}}');
	{{if $line->debut}}
	  var oDiv = $('editDates-Med-'+{{$line->_id}}+'_debut_da');
	  dDate = Date.fromDATE(oForm.debut.value);
	  oDiv.innerHTML = dDate.toLocaleDate();
	{{/if}}
	$V(oForm.duree,'{{$line->duree}}');
	$V(oForm.unite_duree,'{{$line->unite_duree}}');
{{/if}}
{{/if}}


// On grise le formulaire de signature de la ligne si aucune prise n'est cr��e
var oButton = $('signature_{{$line->_id}}'); 
if(oButton){
{{if $line->_count_prises_line}}
  oButton.disabled = false;
  oButton.setOpacity(1.0);
{{else}}
  oButton.disabled = true;
  oButton.setOpacity(0.3);
{{/if}}
}
{{/if}}

Main.add(function () {
{{foreach from=$line->_ref_prises item=prise}}
  prepareForm('addPrise-{{$prise->_id}}');
{{/foreach}}
});

</script>

{{assign var=line_id value=$line->_id}}

{{foreach from=$line->_ref_prises item=prise}}
  {{assign var=prise_id value=$prise->_id}}
  
  <form name="addPrise-{{$prise->_id}}" action="?" method="post" style="display: block;">
    <button style="float: right" type="button" class="remove notext" onclick="this.form.del.value = 1; testPharma({{$line_id}}); onSubmitPrise(this.form ,'{{$type}}'); ">Supprimer</button> 
  
  
	  <input type="hidden" name="dosql" value="do_prise_posologie_aed" />
	  <input type="hidden" name="del" value="0" />
	  <input type="hidden" name="m" value="dPprescription" />
	  <input type="hidden" name="prise_posologie_id" value="{{$prise->_id}}" />
	  <input type="hidden" name="object_id" value="{{$line_id}}" />
	  <input type="hidden" name="object_class" value="{{$line->_class_name}}" />

	  <!-- Formulaire de selection de la quantite -->
	  {{mb_field object=$prise field=quantite size="3" increment=1 min=1 form=addPrise-$prise_id onchange="testPharma($line_id); submitFormAjax(this.form, 'systemMsg');"}}	  
	  
	  {{if $line->_class_name == "CPrescriptionLineMedicament"}}
	  {{$prise->unite_prise}}
	  <!-- 
	  <select name="unite_prise" onchange="testPharma({{$line_id}}); submitFormAjax(this.form, 'systemMsg');">
		  {{foreach from=$line->_unites_prise item=_unite}}
		    <option value="{{$_unite}}" {{if $prise->unite_prise == $_unite}}selected="selected"{{/if}}>{{$_unite}}</option>
		  {{/foreach}}
		</select>
		 -->
	  {{/if}}
	  {{if $line->_class_name == "CPrescriptionLineElement"}}
		  {{$line->_unite_prise}}
		{{/if}}
		  
	  <!-- Cas d'un moment unitaire_id -->
	  {{if $prise->moment_unitaire_id}}
		  {{$prise->_ref_moment->_view}}
		  <!-- Selection du moment -->
{{* MASQUAGE DES CHANGEMENT DE MOMENTS UNITAIRE
		  <select name="moment_unitaire_id" style="width: 150px" onchange="testPharma({{$line_id}}); submitFormAjax(this.form, 'systemMsg');">      
		    <option value="">&mdash; S�lection du moment</option>
		    {{foreach from=$moments key=type_moment item=_moments}}
		     <optgroup label="{{$type_moment}}">
		     {{foreach from=$_moments item=moment}}
		     <option value="{{$moment->_id}}" {{if $prise->moment_unitaire_id == $moment->_id}}selected="selected"{{/if}}>{{$moment->_view}}</option>
		     {{/foreach}}
		     </optgroup>
		    {{/foreach}}
		  </select>
*}}
	  {{/if}}
	  
	  <!-- Cas des fois par -->
	  {{if $prise->nb_fois && $prise->unite_fois}}
      {{mb_value object=$prise field=nb_fois size=3 increment=1 min=1 form=addPrise-$prise_id onchange="testPharma($line_id); submitFormAjax(this.form, 'systemMsg')"}} fois par 
			{{mb_value object=$prise field=unite_fois onchange="testPharma($line_id); submitFormAjax(this.form, 'systemMsg')"}}
		{{/if}}
  
    <!-- Cas des tous les -->
    {{if $prise->nb_tous_les && $prise->unite_tous_les}}
      tous les
			{{mb_value object=$prise field=nb_tous_les size=3 increment=1 min=1 form=addPrise-$prise_id onchange="testPharma($line_id); submitFormAjax(this.form, 'systemMsg')"}}				   
			{{mb_value object=$prise field=unite_tous_les onchange="testPharma($line_id); submitFormAjax(this.form, 'systemMsg')"}}
		  (J+{{mb_value object=$prise field=decalage_prise size=1 increment=1 min=0 form=addPrise-$prise_id onchange="testPharma($line_id); submitFormAjax(this.form, 'systemMsg')"}})
		{{/if}}
	
  
  </form>
{{/foreach}}