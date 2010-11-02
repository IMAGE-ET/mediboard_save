{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

var oFormClick = window.opener.document.click;

function submitAdmission(){
  oFormAdministration = getForm("addAdministration");
  checkForm(oFormAdministration);
  return onSubmitFormAjax(oFormAdministration);
}

function submitCancelAdm(){
  oFormTransmission   = getForm("editTrans");
  $V(oFormTransmission.text, "Administration annul�e");
  
	oFormAdministration = getForm("addAdministration");
  $V(oFormAdministration.quantite, '0');
  return onSubmitFormAjax(oFormAdministration);
}

function submitPlanification(){
  var oForm = document.addPlanification;
  submitFormAjax(oForm, 'systemMsg', { onComplete: function(){ 
    {{if $mode_plan}}
      window.opener.calculSoinSemaine('{{$date_sel}}',"{{$prescription_id}}"); 
    {{else}} 
      window.opener.Prescription.loadTraitement('{{$sejour->_id}}','{{$date_sel}}', oFormClick.nb_decalage.value,'{{$mode_dossier}}','{{$line->_id}}','{{$line->_class_name}}',{{$key_tab|json}});
    {{/if}}
    window.close();
  } } ); 
}

// Fonction appel�e en callback du formulaire d'administration
function submitTransmission(administration_id){
  oFormTransmission   = getForm("editTrans");
  oFormTransmission.object_class.value = "CAdministration";
  oFormTransmission.object_id.value = administration_id;
  if(oFormTransmission.text.value != ''){
    submitFormAjax(oFormTransmission, 'systemMsg', { onComplete: function(){ 
      {{if $mode_plan}}
        window.opener.calculSoinSemaine('{{$date_sel}}',"{{$prescription_id}}"); 
      {{else}}
        window.opener.Prescription.loadTraitement('{{$sejour->_id}}','{{$date_sel}}', oFormClick.nb_decalage.value,'{{$mode_dossier}}','{{$line->_id}}','{{$line->_class_name}}',{{$key_tab|json}});
      {{/if}}
      window.opener.loadSuivi('{{$sejour->_id}}');
      window.close();
    } } )
  } else {
    {{if $mode_plan}}
      window.opener.calculSoinSemaine('{{$date_sel}}',"{{$prescription_id}}"); 
    {{else}}
      window.opener.Prescription.loadTraitement('{{$sejour->_id}}','{{$date_sel}}', oFormClick.nb_decalage.value,'{{$mode_dossier}}','{{$line->_id}}','{{$line->_class_name}}',{{$key_tab|json}});
    {{/if}}
    window.close();
  }
}

function cancelAdministration(administration_id){
  var oFormDelAdministration = document.delAdministration;
  oFormDelAdministration.administration_id.value = administration_id;
  submitFormAjax(oFormDelAdministration, 'systemMsg', { onComplete: function(){
    {{if $mode_plan}}
      window.opener.calculSoinSemaine('{{$date_sel}}',"{{$prescription_id}}"); 
    {{else}} 
      window.opener.Prescription.loadTraitement('{{$sejour->_id}}','{{$date_sel}}', oFormClick.nb_decalage.value,'{{$mode_dossier}}','{{$line->_id}}','{{$line->_class_name}}',{{$key_tab|json}});
    {{/if}}
    window.close();
  } } );
}

function checkTransmission(quantite_prevue, quantite_saisie){
  var oFormTrans = document.editTrans;
  if(quantite_prevue && parseFloat(quantite_prevue) != parseFloat(quantite_saisie) && oFormTrans.text.value == ""){
    alert("Veuillez saisir une transmission");
    return false;
  }
  return true;
}

updateQuantite = function(ratio_UI, oField){
  if(!ratio_UI){
	  return;
	}
	var oForm = getForm("addAdministration");
  
	if(oField.name == "quantite"){
	  var quantite_UI = ($V(oField) / ratio_UI).toFixed(3);
		$V(oForm._quantite_UI, quantite_UI, false);
	}
	if(oField.name == "_quantite_UI"){
    var quantite = ($V(oField) * ratio_UI).toFixed(3);
    $V(oForm.quantite, quantite, false);
  }
}

</script>

<h2>
  Soins de {{$sejour->_ref_patient->_view}} 
  ({{if $sejour->_ref_patient->_ref_curr_affectation->_id}}
    {{$sejour->_ref_patient->_ref_curr_affectation->_view}}
  {{else}}
    Non plac� actuellement
  {{/if}})
</h2>


<form name="delAdministration" method="post" action="?">
  <input type="hidden" name="dosql" value="do_administration_aed" />
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="del" value="1" />
  <input type="hidden" name="administration_id" value="" />
</form>

<hr class="control_tabs" />

{{if $mode_dossier == "administration" || $mode_plan}}
{{if $administrations || $mode_plan}}
	<table class="form">
	  <tr>
	    <th class="title">Liste des soins</th>
	  </tr>
	  {{foreach from=$administrations item=_administration}}
	  {{assign var=log value=$_administration->_ref_log}}
	  <tr>
	    <td>
	      <button class="cancel notext" type="button" onclick="cancelAdministration('{{$_administration->_id}}')"></button>
	      {{$log->_ref_object->quantite}} 
	      {{if $line->_class_name == "CPrescriptionLineMedicament"}}
				  {{if $line->_ref_produit_prescription->_id}}
					  {{$_administration->_ref_object->_ref_produit_prescription->unite_prise}} 
          {{else}}
	          {{$_administration->_ref_object->_ref_produit->libelle_unite_presentation}} 
					{{/if}}
	      {{else}}
	        {{$line->_unite_prise}}
	      {{/if}}
	      administr� par {{$log->_ref_user->_view}} le {{$log->_ref_object->dateTime|date_format:$dPconfig.datetime}}
	    </td>
	  </tr>
	  {{foreachelse}}
	  <tr>
	    <td>Aucune administration</td>
	  </tr>
	  {{/foreach}}
	</table>
{{/if}}


<form name="addAdministration" method="post" action="?" onsubmit="return checkTransmission('{{$prise->quantite}}', this.quantite.value)">
  <input type="hidden" name="dosql" value="do_administration_aed" />
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="administration_id" value="" />
  <input type="hidden" name="administrateur_id" value="{{$app->user_id}}" />
  <input type="hidden" name="object_id" value="{{$line->_id}}" />
  <input type="hidden" name="object_class" value="{{$line->_class_name}}" />
  <input type="hidden" name="unite_prise" value="{{$unite_prise}}" />
  <input type="hidden" name="dateTime" value="{{$dateTime}}" />
  <input type="hidden" name="prise_id" value="{{$prise_id}}" />
  <input type="hidden" name="callback" value="submitTransmission" />
	<table class="form">
	  <tr>
	    <th class="title" colspan="2">Administration de {{$line->_view}}<br />{{$dateTime|date_format:"%d/%m/%Y � %H:%M"}}</th>
	  </tr>
	  <tr>
	    <td>
        {{if $notToday}}
          <div class="small-info">
            {{if $mode_plan}}
              Attention, vous �tes sur le point d'administrer pour le {{$dateTime|date_format:"%d/%m/%Y"}}, 
	            or nous sommes le {{$smarty.now|date_format:"%d/%m/%Y"}}.
	          {{else}}
	            Attention, cette prise est pour le {{$dateTime|date_format:"%d/%m/%Y � %H:%M"}}, 
	            or nous sommes le {{$smarty.now|date_format:"%d/%m/%Y"}}.
            {{/if}}
          </div>
        {{/if}}
				
				{{assign var=ratio_UI value=$line->_ref_produit->_ratio_UI}}
				
				{{mb_label object=$prise field=quantite}}
	      {{mb_field object=$prise field=quantite min=0 increment=1 form=addAdministration onchange="updateQuantite('$ratio_UI', this)"}}
	      
	      {{if $line instanceof CPrescriptionLineMedicament}}
				  {{if $line->_ref_produit_prescription->_id}}
					  {{$line->_ref_produit_prescription->unite_prise}}
					{{else}}
	          {{$line->_ref_produit->libelle_unite_presentation}}
					{{/if}}
	      {{else}}
	        {{$line->_unite_prise}}
	      {{/if}} 
	      
	      {{if $mode_plan}}
	      �
	      <select name="_hour" class="notNull" onchange="$V(this.form.dateTime, '{{$dateTime}} '+this.value);">
	        <option value="">&mdash; Heure</option>
	        {{foreach from=$hours item=_hour}}
	        <option value="{{$_hour}}:00:00">{{$_hour}}h</option>
	        {{/foreach}}
	      </select>
	      {{/if}}
				
				{{if $line->_ref_produit->_ratio_UI}}
					soit 
				  {{mb_field object=$prise field=_quantite_UI min=0 increment=1 form=addAdministration onchange="updateQuantite('$ratio_UI', this)"}} UI
        {{/if}}
				
	    </td>
	  </tr>
	</table>
</form>

<table class="form">
  <tr>
    <td>
			<form name="editTrans" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
				<input type="hidden" name="dosql" value="do_transmission_aed" />
				<input type="hidden" name="del" value="0" />
				<input type="hidden" name="m" value="dPhospi" />
				<input type="hidden" name="object_class" value="" />
				<input type="hidden" name="object_id" value="" />
				<input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
				<input type="hidden" name="user_id" value="{{$app->user_id}}" />
				<input type="hidden" name="date" value="now" />
				<div style="float: right">
			    <select name="_helpers_text" size="1" onchange="pasteHelperContent(this);" class="helper">
			      <option value="">&mdash; Aide</option>
			      {{html_options options=$transmission->_aides.text.no_enum}}
			    </select>
			    <button class="new notext" title="Ajouter une aide � la saisie" type="button" onclick="addHelp('CTransmissionMedicale', this.form.text, null, null, null, null, {{$user_id}})">{{tr}}New{{/tr}}</button><br />      
		    </div>
				{{mb_field object=$transmission field="degre"}}
				{{mb_field object=$transmission field="type" typeEnum=radio}}
				<br />
				{{mb_field object=$transmission field="text"}}
			</form>
	  </td>
	</tr>
	<tr>
	  <td>
	  <button type="button" class="add" onclick="submitAdmission()">{{tr}}Add{{/tr}}</button>
		<button type="button" class="cancel" onclick="submitCancelAdm();">{{tr}}Cancel{{/tr}}</button>
	  </td>
	</tr>
</table>
{{/if}}

{{if $mode_dossier == "planification"}}
	{{if $planification->_id}}
		<table class="form">
		  <tr>
		    <th class="title">Planification</th>
		  </tr>
		  {{assign var=log value=$planification->_ref_log}}
		  <tr>
		    <td>
		      <button class="cancel notext" type="button" onclick="cancelAdministration('{{$planification->_id}}')"></button>
		      {{$log->_ref_object->quantite}} 
		      {{if $line->_class_name == "CPrescriptionLineMedicament"}}
		        {{$planification->_ref_object->_ref_produit->libelle_unite_presentation}} 
		      {{else}}
		        {{$line->_unite_prise}}
		      {{/if}}
		      le {{$log->_ref_object->dateTime|date_format:"%d/%m/%Y � %Hh%M"}}
		    </td>
		  </tr>
		</table>
	{{/if}}
  {{if !$planification->_id && !$prise->quantite}}
	<form name="addPlanification" method="post" action="?">
	  <input type="hidden" name="dosql" value="do_administration_aed" />
	  <input type="hidden" name="m" value="dPprescription" />
	  <input type="hidden" name="del" value="0" />
	  <input type="hidden" name="administration_id" value="" />
	  <input type="hidden" name="administrateur_id" value="{{$app->user_id}}" />
	  <input type="hidden" name="object_id" value="{{$line->_id}}" />
	  <input type="hidden" name="object_class" value="{{$line->_class_name}}" />
	  <input type="hidden" name="unite_prise" value="{{$unite_prise}}" />
	  <input type="hidden" name="dateTime" value="{{$dateTime}}" />
	  <input type="hidden" name="prise_id" value="{{$prise_id}}" />
	  <input type="hidden" name="planification" value="1" />
		<table class="form">
		  <tr>
		    <th class="title" colspan="2">Planification d'administration de {{$line->_view}}</th>
		  </tr>
		  <tr>
		    <td>
		      {{mb_label object=$prise field=quantite}}
		      {{mb_field object=$prise field=quantite min=1 increment=1 form=addPlanification}}
		      
		      {{if $line->_class_name == "CPrescriptionLineMedicament"}}
					  {{if $line->_ref_produit_prescription->_id}}
						  {{$line->_ref_produit_prescription->unite_prise}}
						{{else}}
		          {{$line->_ref_produit->libelle_unite_presentation}}
		        {{/if}}
					{{else}}
		        {{$line->_unite_prise}}
		      {{/if}} 
		      
		      {{if $mode_plan}}
		      �
		      <select name="_hour" class="notNull" onchange="$V(this.form.dateTime, '{{$date}} '+this.value);">
		        <option value="">&mdash; Heure</option>
		        {{foreach from=$hours item=_hour}}
		        <option value="{{$_hour}}:00:00">{{$_hour}}h</option>
		        {{/foreach}}
		      </select>
		      {{else}}
		      � {{$dateTime|date_format:"%Hh%M"}}
		      {{/if}}
		    </td>
		  </tr>
		  <tr>
		    <td colspan="2" style="text-align: center;">
		      <button type="button" class="submit" onclick="submitPlanification();">Planifier</button>
		    </td>
		  </tr>
		</table>
	</form>
	{{elseif !$planification->_id}}
     <div class="small-info">
       Il est impossible de planifier sur cette case car elle poss�de d�j� une prise pr�vue.
     </div>
	{{/if}}
{{/if}}