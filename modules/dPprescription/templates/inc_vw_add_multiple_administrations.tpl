{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
var oFormClick = window.opener.document.forms.click;
var anyFormSubmitted = false;

function submitAllAdministrations() {
  var submitForms = $('administrations').select('form');
  var transForm = getForm('editTrans');

	if (transForm.text.value) {
	  submitFormAjax(transForm, 'systemMsg');
	}
		
	for (var i = 0; i < submitForms.length; i++) {
    var f = submitForms[i];  
    if (f != transForm) {
      if (!checkForm(f)) return false;
      if(i == (submitForms.length - 2)) {
        submitFormAjax(f, 'systemMsg', { onComplete: function(){ 
          closeApplyAdministrations.delay(1);
        } } );
      } else {
        submitFormAjax(f, 'systemMsg'); 
      }
      anyFormSubmitted = true;
      f.up().update('Soin effectu�');
    }
  }
  return true;
}

function submitAllPlanifications(){
  var submitForms = $('planifications').select('form');

  for (var i = 0; i < submitForms.length; i++) {
    var f = submitForms[i];    
    if (!checkForm(f)) return false;
    if(i == (submitForms.length - 1)){
      submitFormAjax(f, 'systemMsg', { onComplete: function(){ 
        closeApplyAdministrations.delay(1);
      } } );
    } else {
      submitFormAjax(f, 'systemMsg');
    }
    anyFormSubmitted = true;
    f.up().update('Soin effectu�');
  }
  return true;
}

function closeApplyAdministrations(dontClose) {
  {{if $administrations|@count && $sejour->_id && $date_sel}}
    if (anyFormSubmitted && window.opener) {
      // refresh des chapitres concern�s
      {{foreach from=$tabs_refresh key=chapitre item=_tab_refresh}}
        window.opener.Prescription.loadTraitement('{{$sejour->_id}}','{{$date_sel}}', oFormClick.nb_decalage.value,'{{$mode_dossier}}', null, null, null, '{{$chapitre}}');
      {{/foreach}}
			if(window.opener.loadSuivi){
        window.opener.loadSuivi('{{$sejour->_id}}');
			}
    }
  {{/if}}
  if (!dontClose) {
    window.close();
  }
}

// Initialisation des onglets
Main.add( function(){
  menuTabs = Control.Tabs.create('administrations_multiple_tab_group', false);
	
  {{if $refresh_popup == 1}}
	  $V(getForm("all_adm").adm, $V(window.opener.getForm('adm_multiple')._administrations));
  {{/if}}
} );

</script>

<form name="all_adm" method='post' action='?m={{$m}}&a={{$a}}&dialog={{$dialog}}&refresh_popup=0&mode_dossier={{$mode_dossier}}'>
	<input type="hidden" name="adm" value="" onchange="this.form.submit();"/>
</form>

{{if $sejour->_id}}
		<h2>
		  Soins de {{$sejour->_ref_patient->_view}} 
		  ({{if $sejour->_ref_patient->_ref_curr_affectation->_id}}
		    {{$sejour->_ref_patient->_ref_curr_affectation->_view}}
		  {{else}}
		    Non plac� actuellement
		  {{/if}})
		</h2>
{{/if}}

{{if $mode_dossier == "administration"}}
	{{if $sejour->_id}}
		<button type="button" class="cancel" onclick="closeApplyAdministrations()">{{tr}}Cancel{{/tr}}</button>
		<button type="button" class="tick" onclick="submitAllAdministrations()">Tout valider</button>
		
		<table class="form" id="administrations">
		{{foreach from=$administrations item=adm key=line_id name=by_adm}}
		  {{foreach from=$adm item=by_unite_prise key=unite_prise name=adm_by_unite_prise}}
		    {{foreach from=$by_unite_prise item=by_date key=date}}
		      {{foreach from=$by_date item=by_hour key=hour}}
	          {{assign var=_unite value=$by_hour.unite_prise|smarty:nodefaults|JSAttribute}}
	          {{assign var=_prise value=$by_hour.prise_id|smarty:nodefaults|JSAttribute}}
	          {{assign var=key value="$line_id-$_unite-$_prise-$date-$hour"|md5}}
	          {{if $smarty.foreach.adm_by_unite_prise.first}}
		        <tr>
		          <th class="title" colspan="2">{{$by_hour.line->_view}}</th>
		        </tr>
		        {{/if}}
		        <tr>
		          <td id="adm_{{$key}}">
		            {{if $by_hour.line->_class_name == "CPrescriptionLineMedicament"}}
								  {{if $by_hour.line->_ref_produit_prescription->_id}}
									  {{assign var=unite_prise value=$by_hour.line->_ref_produit_prescription->unite_prise}}
                  {{else}}
		                {{assign var=unite_prise value=$by_hour.line->_ref_produit->libelle_unite_presentation}}
		              {{/if}}
								{{else}}
		              {{assign var=unite_prise value=$by_hour.line->_unite_prise}}
		            {{/if}}
		            {{if !$by_hour.notToday}}
		              <form name="addAdministration_{{$key}}" method="post" action="?" onsubmit="return checkForm(this)" style="float: left;">
		                <input type="hidden" name="dosql" value="do_administration_aed" />
		                <input type="hidden" name="m" value="dPprescription" />
		                <input type="hidden" name="del" value="0" />
		                <input type="hidden" name="administration_id" value="" />
		                <input type="hidden" name="administrateur_id" value="{{$app->user_id}}" />
		                <input type="hidden" name="object_id" value="{{$by_hour.line->_id}}" />
		                <input type="hidden" name="object_class" value="{{$by_hour.line->_class_name}}" />
		                <input type="hidden" name="unite_prise" value="{{$by_hour.unite_prise|smarty:nodefaults|JSAttribute}}" />
		                <input type="hidden" name="dateTime" value="{{$by_hour.dateTime}}" />
		                <input type="hidden" name="prise_id" value="{{$by_hour.prise_id}}" />
		                <input type="hidden" name="quantite_prevue" disabled="disabled" value="{{$by_hour.prise->quantite}}" />
		                <input type="hidden" name="_quantite_prevue" value="{{$by_hour.prise->quantite}}" />
		                
		                <b>{{$date|date_format:"%d/%m/%Y"}}, {{$hour}}h</b> : 
		                {{mb_label object=$by_hour.prise field=quantite}}
		                {{mb_field object=$by_hour.prise field=quantite min=0 increment=1 form="addAdministration_$key"}}
		                {{$unite_prise}}
		              </form>
		            {{else}}
		              <div class="small-info">
		                Attention, cette prise de {{mb_value object=$by_hour.prise field=quantite}} {{$unite_prise}} est pour le {{$date|date_format:"%d/%m/%Y"}} � {{$hour|date_format:"%Hh%M"}}, 
		                or nous sommes le {{$smarty.now|date_format:"%d/%m/%Y"}}.
		              </div>
		            {{/if}}
		          </td>
		        </tr>
		      {{/foreach}}
		    {{/foreach}}
		  {{/foreach}}
		{{foreachelse}}
		  <tr><td>Veuillez choisir au moins un soin � effectuer</td></tr>
		{{/foreach}}
		  <tr>
		    <th class="title" colspan="2">{{tr}}CTransmissionMedicale{{/tr}}</th>
		  </tr>
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
              <button class="new notext" title="Ajouter une aide � la saisie" type="button" onclick="addHelp('CTransmissionMedicale', this.form.text, '', '', '', '', {{$user_id}})">{{tr}}New{{/tr}}</button><br />      
            </div>
            {{mb_field object=$transmission field="degre"}}
            {{mb_field object=$transmission field="type" typeEnum=radio}}
            <br />
            {{mb_field object=$transmission field="text"}}
		      </form>
		    </td>
		  </tr>
		</table>
	{{else}}
	  <div class="small-info">
	    Veuillez s�lectionner des prises prevues pour ajouter des administrations
	  </div>
	{{/if}}
{{/if}}

{{if $mode_dossier == "planification"}}
	{{if $sejour->_id}}	
		<button type="button" class="cancel" onclick="closeApplyAdministrations()">{{tr}}Cancel{{/tr}}</button>
		<button type="button" class="tick" onclick="submitAllPlanifications()">Tout valider</button>
		
		<table class="form" id="planifications">
		{{foreach from=$administrations item=adm key=line_id name=by_adm}}
		  {{foreach from=$adm item=by_unite_prise key=unite_prise name=adm_by_unite_prise}}
		    {{foreach from=$by_unite_prise item=by_date key=date}}
		      {{foreach from=$by_date item=by_hour key=hour}}
	          {{assign var=_unite value=$by_hour.unite_prise|smarty:nodefaults|JSAttribute}}
	          {{assign var=_prise value=$by_hour.prise_id|smarty:nodefaults|JSAttribute}}
	          {{assign var=key value="$line_id-$_unite-$_prise-$date-$hour"|smarty:nodefaults|JSAttribute}}
	          {{if $smarty.foreach.adm_by_unite_prise.first}}
		        <tr>
		          <th class="title" colspan="2">{{$by_hour.line->_view}}</th>
		        </tr>
		        {{/if}}
		        {{assign var=planification value=$by_hour.prise}}
		        {{if $planification->quantite}}
		        <tr>
		          <td>
		            <div class="small-info">
		              Il est impossible de planifier sur cette case car elle poss�de d�j� une prise pr�vue.
		            </div>
		          </td>
		        </tr>
		        {{else}}
		        <tr>
		          <td id="adm_{{$key}}">
		            {{if $by_hour.line->_class_name == "CPrescriptionLineMedicament"}}
		              {{assign var=unite_prise value=$by_hour.line->_ref_produit->libelle_unite_presentation}}
		            {{else}}
		              {{assign var=unite_prise value=$by_hour.line->_unite_prise}}
		            {{/if}}
	              <form name="addPlanification_{{$key}}" method="post" action="?" onsubmit="return checkForm(this)" style="float: left;">
	                <input type="hidden" name="dosql" value="do_administration_aed" />
	                <input type="hidden" name="m" value="dPprescription" />
	                <input type="hidden" name="del" value="0" />
	                <input type="hidden" name="administration_id" value="" />
	                <input type="hidden" name="administrateur_id" value="{{$app->user_id}}" />
	                <input type="hidden" name="object_id" value="{{$by_hour.line->_id}}" />
	                <input type="hidden" name="object_class" value="{{$by_hour.line->_class_name}}" />
	                <input type="hidden" name="unite_prise" value="{{$by_hour.unite_prise}}" />
	                <input type="hidden" name="dateTime" value="{{$by_hour.dateTime}}" />
	                <input type="hidden" name="prise_id" value="{{$by_hour.prise_id}}" />
	                <input type="hidden" name="planification" value="1" />
	                <input type="hidden" name="quantite_prevue" disabled="disabled" value="{{$by_hour.prise->quantite}}" />
	                <b>{{$date|date_format:"%d/%m/%Y"}}, {{$hour}}h</b> : 
	                {{mb_label object=$by_hour.prise field=quantite}}
	                {{mb_field object=$by_hour.prise field=quantite min=1 increment=1 form="addPlanification_$key"}}
	                {{$unite_prise}}
	              </form>
		          </td>
		        </tr>
		        {{/if}}
		      {{/foreach}}
		    {{/foreach}}
		  {{/foreach}}
		{{foreachelse}}
		  <tr><td>Veuillez choisir au moins un soin � planifier</td></tr>
		{{/foreach}}
		</table>
	{{else}}
	  <div class="small-info">
	    Veuillez s�lectionner des prises prevues pour ajouter des administrations
	  </div>
	{{/if}}
{{/if}}