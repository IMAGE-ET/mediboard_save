{{mb_include_script module="dPprescription" script="prescription"}}

{{if !$documents|@count && !$prescription_preadm->_id && !$prescription_sortie->_id}}
  <div class="small-info">
    Il n'y a aucun document pour cette consultation
  </div>
{{else}}
	{{if $prescription_preadm->_id}}
	<table class="form">
	  <tr>
	    <th class="title">Prescription de pre-admission</th>
	  </tr>
	  <tr>
	    <td style="text-align: center">
	      <button class="print" type="button" onclick="Prescription.printPrescription('{{$prescription_preadm->_id}}','0', '{{$prescription_preadm->object_id}}')">Imprimer</button>
	    </td>
	  </tr>
	</table>
	{{/if}}
	
	{{if $prescription_sortie->_id}}
	<table class="form">
	  <tr>
	    <th class="title">Prescription de sortie</th>
	  </tr>
	  <tr>
	   <td style="text-align: center">
	       <button class="print" type="button" onclick="Prescription.printPrescription('{{$prescription_sortie->_id}}','0', '{{$prescription_preadm->object_id}}')">Imprimer</button>
	   </td>
	  </tr>
	</table>
	{{/if}}
	{{if $documents|@count}}
  <form name="selectDocsFrm" action="?" method="get">
	  <input type="hidden" name="consultation_id" value="{{$consult->consultation_id}}" />
    <input type="hidden" name="m" value="{{$m}}" />
    <input type="hidden" name="dialog" value="1" />
    <input type="hidden" name="a" value="print_docs" />
	  <table class="main form">
	    <tr>
	      <th class="category" colspan="2">
	        Veuillez choisir le nombre de documents � imprimer
	      </th>
	    </tr>
	    {{foreach from=$documents item=curr_doc}}
	    <tr>
	      <th>
	        {{$curr_doc->nom}}
	      </th>
	      <td>
	        <input name="nbDoc[{{$curr_doc->compte_rendu_id}}]" type="text" size="2" value="1" />
	        <script type="text/javascript">
	          $(getForm("selectDocsFrm").elements['nbDoc[{{$curr_doc->compte_rendu_id}}]']).addSpinner({min:0});
	        </script>
	      </td>
	    </tr>
	    {{/foreach}}
	    <tr>
	      <td class="button" colspan="2">
	        <button type="submit" class="print">
	          {{tr}}Print{{/tr}}
	        </button>
	      </td>
	    </tr>
	  </table>
  </form>
  {{/if}}
{{/if}}