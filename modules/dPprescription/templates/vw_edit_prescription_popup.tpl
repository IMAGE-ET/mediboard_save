<div id="prescription_sejour">

{{mb_include_script module="dPmedicament" script="medicament_selector"}}
{{mb_include_script module="dPmedicament" script="equivalent_selector"}}
{{mb_include_script module="dPprescription" script="element_selector"}}
{{mb_include_script module="dPprescription" script="prescription"}}

<script type="text/javascript">

// Visualisation du produit
function viewProduit(cip){
  var url = new Url;
  url.setModuleAction("dPmedicament", "vw_produit");
  url.addParam("CIP", cip);
  url.popup(900, 640, "Descriptif produit");
}

</script>

<table class="main">
  {{if $prescription->_id}}
  
  <!-- Cas d'une prescription de sejour -->
  {{if $prescription->object_class == "CSejour"}}
  {{assign var=sejour value=$prescription->_ref_object}}
  {{assign var=prescriptions value=$sejour->_ref_prescriptions}}
  {{assign var=prescription_pre_admission value=$prescriptions.pre_admission}}
  {{assign var=prescription_sejour value=$prescriptions.sejour}}
  
  <tr>
    <td>
      <table style="width: 100%;">
        <tr>
          
          <!-- Pre-admission -->
          <td id="pre_admission" class="step" style="border:{{if $prescription->type == 'pre_admission'}}2px ridge{{else}}1px dotted{{/if}} #55A">
          {{if $prescription_pre_admission->_id}}
	            <a href="#" onclick="Prescription.reloadPrescSejour('{{$prescription_pre_admission->_id}}','{{$prescription->object_id}}');"
	            {{if $prescription->type == "pre_admission"}}style="font-size: 170%"{{/if}}>
	              {{tr}}CPrescription.type.pre_admission{{/tr}}
	            </a>
            {{if $prescription->type == "pre_admission" && !$prescription_sejour->_id}}
              <form name="addPrescriptionSejour" method="post" action="?">
	              <input type="hidden" name="m" value="dPprescription" />
			          <input type="hidden" name="dosql" value="do_duplicate_prescription_aed" />
			          <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
			          <input type="hidden" name="type" value="sejour" />
			          <button type="button" 
			                  class="add" 
			                  onclick="submitFormAjax(this.form, 'systemMsg');">
			            S�jour
			          </button> 
		          </form>   
            {{/if}}
          {{/if}}
          </td>
          
          
          <!-- S�jour -->
          <td id="sejour" class="step" style="border: {{if $prescription->type == "sejour"}}2px ridge{{else}}1px dotted{{/if}} #55A">
	          {{if $prescription_sejour->_id}}
		          <a href="#" onclick="Prescription.reloadPrescSejour('{{$prescription_sejour->_id}}','{{$prescription->object_id}}');"
		          {{if $prescription->type == "sejour"}}style="font-size: 170%"{{/if}}>
		            {{tr}}CPrescription.type.sejour{{/tr}}
		          </a>
		      
		          {{if $prescription->type == "sejour"}}
			          <form name="addPrescriptionSejour" method="post" action="">
				          <input type="hidden" name="m" value="dPprescription" />
				          <input type="hidden" name="dosql" value="do_duplicate_prescription_aed" />
				          <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
				          <input type="hidden" name="type" value="sortie" />
						      <button type="button" 
						              class="add" 
						              onclick="submitFormAjax(this.form, 'systemMsg');">			       
						       Sortie
						      </button>
			          </form>
		          {{/if}}
	          {{/if}}
          </td>
         
          <td id="sortie" class="step" style="border: {{if $prescription->type == "sortie"}}2px ridge{{else}}1px dotted{{/if}} #55A">
          {{if $prescriptions.sortie|@count > 0}}
            <span {{if $prescription->type == "sortie"}}style="font-size: 170%"{{/if}}>
		          {{tr}}CPrescription.type.sortie{{/tr}}
		        </span>
            <form name="selSortie" method="get" action="">
		          <select name="selPrescriptionSortie" 
		                  onchange="if(this.value != ''){ 
		                              Prescription.reloadPrescSejour(this.value,'{{$prescription->object_id}}');
		                            } ">
		            <option value="">&mdash; Praticien</option>
		          {{foreach from=$prescriptions.sortie item=prescription_sortie}}
		            <option {{if $prescription_sortie->_id == $prescription->_id}}selected="selected"{{/if}}value="{{$prescription_sortie->_id}}">{{$prescription_sortie->_ref_praticien->_view}}</option>
		          {{/foreach}}
		          </select>
	         </form>
          {{/if}}
          </td>
        </tr>
      </table>
    </td>
  </tr>
  {{else}}
  <!-- Cas d'une prescription de consultation -->
  <tr>
    <th style="background-color: #EEF; border: 2px ridge #55A">
      Prescription Externe
    </th>
  </tr>
  {{/if}}
  <tr>
    <td class="greedyPane">
      {{assign var=httpreq value=0}}
      <div id="prescription">
        {{include file="inc_vw_prescription.tpl" mode_protocole=0}}
      </div>
    </td>
  </tr>
  {{else}}
   {{if $full_mode && $sejour_id}}
    <tr>
      <td>
			  <form action="?m=dPprescription" method="post" name="addPrescription" onsubmit="return checkForm(this);">
				  <input type="hidden" name="m" value="dPprescription" />
				  <input type="hidden" name="dosql" value="do_prescription_aed" />
				  <input type="hidden" name="prescription_id" value="" />
				  <input type="hidden" name="del" value="0" />
				  <input type="hidden" name="object_id" value="{{$sejour_id}}"/>
				  <input type="hidden" name="object_class" value="CSejour" />
				  <input type="hidden" name="callback" value="reloadPrescription" />				  
				  <input type="hidden" name="type" value="pre_admission" />
				  <button type=button class="submit" onclick="submitFormAjax(this.form, 'systemMsg');">Cr�er une prescription de sejour</button>
				</form>
		  </td>
		</tr>
   {{else}}
		  <tr>
		    <td>
		      <div class="big-info">
		        {{if $full_mode}}
		          Veuillez selectionner un s�jour ou une op�ration pour pouvoir cr�er une prescription
		        {{else}}
		          Veuillez choisir un contexte (s�jour ou consultation) pour la prescription
		        {{/if}}
		      </div>
		    </td>
		  </tr>
    {{/if}}
  {{/if}}
</table>

</div>