<script type="text/javascript">

preselectType = function(contexte, oForm){
  if(!oForm){
    var oForm = document.addProtocolePresc; 
  }
  {{if $contexteType}}
	  contexteType = {{$contexteType|@json}};
	  _contexteType = contexteType[contexte];
	  if(contexte == "CConsultation"){
	    oForm.type.value = "externe";
	  } else {
	    oForm.type.value = "pre_admission";
	  }
	  $A(oForm.type).each( function(input) {
	    input.disabled = !_contexteType.include(input.value);
	  });
  {{/if}}
}

Main.add(function () {
  // Preparation du formulaire de creation de protocole
  if(document.addProtocolePresc){
    prepareForm(document.addProtocolePresc);  
    preselectType("CConsultation");
  }
});

</script>

<!-- Formulaire de creation du protocole -->
{{if !$prescription->_id && $mode_protocole && !$mode_pharma}}
  {{if $function_id || $praticien_id || $group_id}}
		<form action="?m=dPprescription" method="post" name="addProtocolePresc" onsubmit="return Protocole.add();">	
		   <input type="hidden" name="m" value="dPprescription" />
		   <input type="hidden" name="dosql" value="do_prescription_aed" />
		   <input type="hidden" name="prescription_id" value="" />
		   <input type="hidden" name="del" value="0" />
		   <input type="hidden" name="object_class" value=""/>
		   <input type="hidden" name="object_id" value=""/>
		   <input type="hidden" name="praticien_id" value="" />
		   <input type="hidden" name="function_id" value="" />
		   <input type="hidden" name="group_id" value="" />
		   <input type="hidden" name="callback" value="Prescription.reloadAddProt" />
		   <table class="form">
		     <tr>
		       <th class="category" colspan="2">
		         Cr�ation d'un protocole
		       </th>
		    </tr>
		    <tr>
		      <th>{{mb_label object=$protocole field="libelle"}}</th>
		      <td>{{mb_field object=$protocole field="libelle" class="notNull"}}</td>
		    </tr>
		    <tr>
		      <th>{{mb_label object=$protocole field="object_class"}}</th>
					<td>{{mb_field object=$protocole field="object_class" onchange="preselectType(this.value)"}}</td>
			  </tr>
			  <tr>
			    <th>{{mb_label object=$protocole field="type"}}</th>
			    <td>
			      <select name="type">
			        <option value="pre_admission">Pr�-admission</option>
			        <option value="sejour">S�jour</option>
			        <option value="sortie">Sortie</option>
			        <option value="externe">Externe</option>
			      </select>  
			    </td>
			  </tr>
			  <tr>
			   <td colspan="2" style="text-align: center">
					  <button type="button" onclick="this.form.onsubmit();" class="new">Cr�er un protocole</button>
			   </td>  
			  </tr>
		  </table>
		</form>
  {{else}}
    <div class="big-info">
      Veuillez s�lectionner un praticien, un cabinet ou un �tablissement pour cr�er un protocole.
    </div>
  {{/if}}
{{/if}}


{{if !$prescription->_id && $mode_pharma}}
  <div class="big-info">
    Veuillez s�lectionner un s�jour pour pouvoir acc�der � sa prescription.
  </div>
{{/if}}


{{if $prescription->_id || $mode_pack}}
{{if !$mode_pack}}
  <!-- Affichage de l'entete de la prescription -->
  {{include file="../../dPprescription/templates/inc_header_prescription.tpl"}}	
{{/if}}
	<!-- Affichage des elements de la prescription -->
	<div id="produits_elements">
	  {{include file="../../dPprescription/templates/inc_vw_produits_elements.tpl"}}  
	</div>
{{/if}}