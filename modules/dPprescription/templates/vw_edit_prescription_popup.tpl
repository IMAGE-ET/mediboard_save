{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $dialog}}
  <div id="prescription_sejour">
{{/if}}

{{mb_script module="dPmedicament" script="medicament_selector"}}
{{mb_script module="dPmedicament" script="equivalent_selector"}}
{{mb_script module="dPprescription" script="element_selector"}}
{{mb_script module="dPprescription" script="prescription"}}
{{mb_script module="dPprescription" script="protocole"}}

<script type="text/javascript">
			          
var oFormProtocole = getForm("applyProtocoleFirst");

if (oFormProtocole) {
  var url = new Url("dPprescription", "httpreq_vw_select_protocole");
  var autocompleter = url.autoComplete(oFormProtocole.libelle_protocole, "protocole_auto_complete", {
    dropdown: true,
    minChars: 2,
    valueElement: oFormProtocole.elements.pack_protocole_id,
    updateElement: function(selectedElement) {
      var node = $(selectedElement).down('.view');
      $V(oFormProtocole.libelle_protocole, (node.innerHTML).replace("&lt;", "<").replace("&gt;",">"));
      $V(oFormProtocole.protocole_id, selectedElement.get("id"));
      if (autocompleter.options.afterUpdateElement)
        autocompleter.options.afterUpdateElement(autocompleter.element, selectedElement);
    },
    callback: 
        function(input, queryString){
          {{if !$is_praticien}}
            return (queryString + "&prescription_id={{$prescription->_id}}&praticien_id="+$('choix_prat').value+"&type="+$("type_prescription").value);
          {{else}}
            return (queryString + "&prescription_id={{$prescription->_id}}&praticien_id={{$current_user->_id}}&type="+$("type_prescription").value);
        	{{/if}}
        }
  } );
}

addProtocole = function(prescription_id) {
  $V(oFormProtocole.prescription_id, prescription_id);
  if ($V(oFormProtocole.pack_protocole_id) == "") {
	  reloadPrescription(prescription_id);
  }
  else {
	  $V(oFormProtocole.praticien_id, $('choix_prat').value);
	  onSubmitFormAjax(oFormProtocole);
  }
}

emptyProtocole = function() {
	$V(oFormProtocole.libelle_protocole, String.fromCharCode(8212)+ " Aucun protocole");
	$V(oFormProtocole.pack_protocole_id, '');
	$V(getForm("addPrescription").type, $("type_prescription").value);
}

// Si la prescription est ouverte en popup, comme dans la dhe,
// la fonction reloadPrescription est � d�finir.
if (!window.reloadPrescription) {
  reloadPrescription = function(prescription_id) {
    try {
      Prescription.reloadPrescSejour(prescription_id, '','', '0', null, null, null,'', null, false);
    }
    catch(e){ }
  }
}

selectLines = function(prescription_id, protocole_id) {
  var oForm = getForm("applyProtocoleFirst");
  
  // Ouverture de la modale pour choisir les lignes
  var url = new Url("dPprescription", "ajax_select_lines");
  url.addParam("prescription_id", prescription_id);
  url.addParam("protocole_id", protocole_id);
  url.addParam("pratSel_id", $V(oForm.pratSel_id));
  url.addParam("praticien_id", $V(oForm.praticien_id));
  url.requestModal(700, 300, {showClose: false, showReload: false});

}

prescriptions_ids = {{$multiple_prescription|@json}};

Main.add(function () {
  // Si la modale n'existe pas, on la cr�e
  if(!$('modalPrescriptionLine')){
    $(document.body).insert(
      DOM.div({id: "modalPrescriptionLine"}).setStyle({
        width: '90%',
        diplay: 'none'
    }))
  }
});
</script>

{{if $multiple_prescription|@count > 1}}
  <div class="big-error">
    {{tr}}CPrescription.merge_prescription_message{{/tr}}
    <br/>
    {{if $admin_prescription}}
    <button class="hslip" onclick="Prescription.mergePrescriptions(prescriptions_ids)">Fusionner les prescriptions</button>
    {{else}}
      Veuillez contacter un praticien ou un administrateur pour effectuer cette fusion.
    {{/if}}
  </div>
{{else}}
  <table class="main">
    {{if $prescription->_id}}
    
    <!-- Cas d'une prescription de sejour -->
    {{if $prescription->object_class == "CSejour"}}
    {{assign var=sejour value=$prescription->_ref_object}}
    {{assign var=prescriptions value=$sejour->_ref_prescriptions}}
    {{assign var=prescription_pre_admission value=$prescriptions.pre_admission}}
    {{assign var=prescription_sejour value=$prescriptions.sejour}}
    {{assign var=prescription_sortie value=$prescriptions.sortie}}
    
    <tr>
      <td>
        <form name="mode_affichage" action="?" method="get">
          <!-- Permet de rester en mode lecture (et de ne pas recuperer l'operation_id en session) -->
          <input type="hidden" name="mode_sejour" value="{{$mode_sejour}}" />
        </form>
        <table style="width: 100%;">
          <tr>
            <!-- Pre-admission -->
            <td id="pre_admission" class="step {{if $prescription->type == 'pre_admission'}}selected{{/if}}">
            {{if $prescription_pre_admission->_id}}
  	            <a href="#1" onclick="Prescription.reloadPrescSejour('{{$prescription_pre_admission->_id}}','{{$prescription->object_id}}');">
  	              Pr�-adm.
  	            </a>
              {{else}}
  	            <form name="addPrescriptionPread" method="post" action="?">
  	              <input type="hidden" name="m" value="dPprescription" />
  	              <input type="hidden" name="dosql" value="do_prescription_aed" />
  	              <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
  	              <input type="hidden" name="object_id" value="{{$sejour->_id}}" />
  	              <input type="hidden" name="object_class" value="CSejour" />
  	              <input type="hidden" name="type" value="pre_admission" />
  	              <input type="hidden" name="callback" value="reloadPrescription" />
  	              <button type="button" class="add singleclick" onclick="submitFormAjax(this.form, 'systemMsg');">
  	                Pr�-admission
  	              </button> 
  	            </form>
              {{/if}}
            </td>
            
            <!-- S�jour -->
            <td id="sejour" class="step {{if $prescription->type == 'sejour'}}selected{{/if}}">
  	          {{if $prescription_sejour->_id}}
  		          <a href="#1" onclick="Prescription.reloadPrescSejour('{{$prescription_sejour->_id}}','{{$prescription->object_id}}');">
  		            {{tr}}CPrescription.type.sejour{{/tr}}
  		          </a>
  	          {{else}}
                <form name="addPrescriptionSejour" method="post" action="?">
                  <input type="hidden" name="m" value="dPprescription" />
                  {{if $prescription_pre_admission->_id}}
                    <input type="hidden" name="dosql" value="do_duplicate_prescription_aed" />
                    <input type="hidden" name="prescription_id" value="{{$prescription_pre_admission->_id}}" />
                  {{else}}
                    <input type="hidden" name="dosql" value="do_prescription_aed" />
                    <input type="hidden" name="object_id" value="{{$sejour->_id}}" />
                    <input type="hidden" name="object_class" value="CSejour" />
                  {{/if}}
                  <input type="hidden" name="type" value="sejour" />
                  <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
                  <input type="hidden" name="callback" value="reloadPrescription" />
                  <button type="button" class="add singleclick" onclick="submitFormAjax(this.form, 'systemMsg');">
                    S�jour
                  </button> 
                </form>
              {{/if}}
            </td>
           
            <td id="sortie" class="step {{if $prescription->type == 'sortie'}}selected{{/if}}">
            {{if $prescriptions.sortie->_id}}
                <a href="#1" onclick="Prescription.reloadPrescSejour('{{$prescription_sortie->_id}}','{{$prescription->object_id}}');">
  		            {{tr}}CPrescription.type.sortie{{/tr}}
  		          </a>
  	        {{else}}
  	          <form name="addPrescriptionSortie" method="post" action="">
  	            <input type="hidden" name="m" value="dPprescription" />
  	            {{if $prescription_sejour->_id}}
  	              <input type="hidden" name="prescription_id" value="{{$prescription_sejour->_id}}" />
  	              <input type="hidden" name="dosql" value="do_duplicate_prescription_aed" />
  	            {{else}}
  	              <input type="hidden" name="dosql" value="do_prescription_aed" />
  	              <input type="hidden" name="object_id" value="{{$sejour->_id}}" />
  	              <input type="hidden" name="object_class" value="CSejour" />
  	            {{/if}}
  	            <input type="hidden" name="type" value="sortie" />
  	            <input type="hidden" name="callback" value="reloadPrescription" />
  	            <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
  	            <button type="button" class="add singleclick" onclick="submitFormAjax(this.form, 'systemMsg');">             
  	              Sortie
  	            </button>
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
      <td class="step selected">
         Prescription {{tr}}CPrescription.type.externe{{/tr}} 
      </td>
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
  				  <table class="form">
  				    <tr>
  				      {{if $can->admin || (!$is_praticien && !$conf.dPprescription.CPrescription.role_propre)}}
                  <th class="title">Praticien</th>
                {{/if}}
                {{if $operations|@count > 1}}
                  <th class="title">Intervention</th>
                {{/if}}
  				      <th class="title" >{{mb_title object=$prescription field="type"}}</th>
  				      <th class="title" >Protocole</th>
  				    </tr>
  				    <tr>
  				    {{if $can->admin || (!$is_praticien && !$conf.dPprescription.CPrescription.role_propre)}}
  				      <td>
  				      <select name="praticien_id" id="choix_prat" onchange="$V(getForm('applyProtocoleFirst').praticien_id, this.value); emptyProtocole();">
  			         <optgroup label="Responsables">
  		              {{if $chir->_id}}
                      <option class="mediuser"
                              style="border-color: #{{$chir->_ref_function->color}};" 
                              value="{{$chir->_id}}">{{$chir->_view}}</option>
                    {{/if}}
                    {{if $anesth->_id}}
                      <option class="mediuser"
                              style="border-color: #{{$anesth->_ref_function->color}};"
                              value="{{$anesth->_id}}">{{$anesth->_view}}</option>
                    {{/if}}
  		            </optgroup>
  		            <optgroup label="Tous les praticiens">
  		              {{foreach from=$listPrats item=_praticien}}
  		                <option class="mediuser"
  		                        style="border-color: #{{$_praticien->_ref_function->color}};" 
  		                        value="{{$_praticien->_id}}"
  		                        {{if $_praticien->_id == $prescription->_current_praticien_id}}selected="selected"{{/if}}>{{$_praticien->_view}}
  		                </option>
  		              {{/foreach}}
  		            </optgroup>
  			        </select>
  				      </td>
  				    {{else}}
  				      <input type="hidden" id="choix_prat" value="{{$app->user_id}}" />
              {{/if}}
                {{if $operations|@count > 1}}
  	              <td>
  		              <select name="operation_id" onchange="$V(getForm('applyProtocoleFirst').operation_id, this.value);">
  		                {{foreach from=$operations item=_operation name=operations}}
  		                  <option value="{{$_operation->_id}}" {{if $smarty.foreach.operations.index == $smarty.foreach.operations.last}}selected="selected"{{/if}}>
                          {{$_operation->_view}}
                        </option>
  		                {{/foreach}}
  		              </select>
  	              </td>
                {{/if}}
  				      <td>
  							  <select name="type" id="type_prescription" onchange="emptyProtocole();">
  					        <option value="pre_admission">Pr�-admission</option>
  					        <option value="sejour" selected="selected">S�jour</option>
  					        <option value="sortie">Sortie</option>
  					      </select>
  				      </td>
  				      <td>
  				        <form name="applyProtocoleFirst" method="post" action="?" onsubmit="return false;">
                    <input type="hidden" name="m" value="dPprescription" />
                    <input type="hidden" name="dosql" value="do_apply_protocole_aed" />
                    <input type="hidden" name="del" value="0" />
                    <input type="hidden" name="prescription_id" value="" />
                    <input type="hidden" name="pratSel_id" value="{{$praticien_sejour}}" />
                    <input type="hidden" name="praticien_id" value="" />
                    <input type="hidden" name="pack_protocole_id" value="" />
                    {{assign var=last_operation value=$operations|@end}}
                    {{if $operations|@count >= 1}}
                      <input type="hidden" name="operation_id" value="{{$last_operation->_id}}" />
                    {{/if}}
  					        <input type="text"   name="libelle_protocole" value="&mdash; Aucun protocole" class="autocomplete" style="font-weight: bold; font-size: 1.3em; width: 250px;"/>
  	                <div style="display:none; width: 200px;" class="autocomplete" id="protocole_auto_complete"></div>
                  </form>
                  
  				        <form action="?m=dPprescription" method="post" name="addPrescription" >
  				          <input type="hidden" name="m" value="dPprescription" />
  				          <input type="hidden" name="dosql" value="do_prescription_aed" />
  				          <input type="hidden" name="prescription_id" value="" />
  				          <input type="hidden" name="del" value="0" />
  				          <input type="hidden" name="object_id" value="{{$sejour_id}}"/>
  				          <input type="hidden" name="object_class" value="CSejour" />
  				          <input type="hidden" name="type" value="sejour"/>
  				          <input type="hidden" name="callback" value="addProtocole" />
  	                <button type="button" class="submit singleclick" onclick="WaitingMessage.cover('prescription_sejour'); onSubmitFormAjax(this.form);">Cr�er</button>         
                  </form>
  				      </td>
  				    </tr>
  				    <tr>
  				    </tr>
  					</table>
  		  </td>
  		</tr>
     {{else}}
  		  <tr>
  		    <td>
  		      <div class="small-info">
  		        {{if $full_mode}}
  		          Veuillez s�lectionner un s�jour ou une intervention pour pouvoir cr�er une prescription.
  		        {{else}}
  		          Veuillez choisir un contexte (s�jour ou consultation) pour la prescription.
  		        {{/if}}
  		      </div>
  		    </td>
  		  </tr>
      {{/if}}
    {{/if}}
  </table>
{{/if}}
{{if $dialog}}
  </div>
{{/if}}