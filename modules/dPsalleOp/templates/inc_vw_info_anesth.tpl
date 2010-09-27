{{assign var="consult_anesth" value=$selOp->_ref_consult_anesth}}
{{if !@$modeles_prat_id}}
  {{assign var="modeles_prat_id" value=$selOp->_ref_anesth->_id}}
{{/if}}

<script type="text/javascript">
refreshAidesPreAnesth = function(user_id) {
  if(!document.visiteAnesth._helpers_rques_visite_anesth){
	  return;
	}
  
  var url = new Url('dPcompteRendu', 'httpreq_vw_select_aides');
  url.addParam('object_class', 'COperation');
  url.addParam('field', 'rques_visite_anesth');
  url.addParam('user_id', user_id);
  url.addParam('no_enum', 1);
  url.requestUpdate(document.visiteAnesth._helpers_rques_visite_anesth);
}

reloadDocumentsAnesth = function () {
  if(document.anesthTiming) {
    var sAnesth_id = document.anesthTiming.anesth_id.value;
  } else {
    var sAnesth_id = document.visiteAnesth.prat_anesth_id.value;
  }
  $$('.documents-CConsultAnesth-{{$consult_anesth->_id}}').each(function(doc){
    Document.refresh(doc, {praticien_id: sAnesth_id });
  });    
}

refreshAnesthPerops = function(operation_id){
  var url = new Url("dPsalleOp", "httpreq_vw_anesth_perop");
	url.addParam("operation_id", operation_id);
	url.requestUpdate("list_perops_"+operation_id);
}

{{if $dialog}}
reloadAnesth = function() {
  window.opener.location.reload(true);
  window.location.reload(true);
}
{{/if}}

Main.add(function(){
  var oFormVisiteAnesth = getForm('visiteAnesth');
	
  refreshAidesPreAnesth($V(oFormVisiteAnesth.prat_visite_anesth_id));
	
  {{if !$selOp->date_visite_anesth}}
	  var dates = {};
	  /*dates.limit = {
	    start: '{{$smarty.now|@date_format:"%Y-%m-%d %H:%M:%S"}}',
	    stop: '{{$smarty.now|@date_format:"%Y-%m-%d %H:%M:%S"}}'
	  };*/
	  Calendar.regField(oFormVisiteAnesth.date_visite_anesth, dates);
	  
	  // Initialisation du champ date
	  $("visiteAnesth_date_visite_anesth_da").value = "Heure actuelle";
	  $V(oFormVisiteAnesth.date_visite_anesth, "current");
  {{/if}}
	
	if ($('anesth_tab_group')){
    Control.Tabs.create('anesth_tab_group', true);
  }
	
  // Refresh tab perop
  if($("tab_perop").visible()){
    refreshAnesthPerops('{{$selOp->_id}}');
    Prescription.updatePerop('{{$selOp->sejour_id}}');
  }
});
</script>

{{if $dialog}}
  {{assign var=onSubmit value="return onSubmitFormAjax(this, {onComplete: reloadAnesth})"}}
{{else}}
  {{assign var=onSubmit value="return checkForm(this)"}}
{{/if}}

<ul id="anesth_tab_group" class="control_tabs">
	<li><a href="#tab_preanesth">Pr�-anesth�sie</a></li>
	<li onmousedown="refreshAnesthPerops('{{$selOp->_id}}');"><a href="#tab_perop">Per-op�ratoire</a></li>
</ul>
<hr class="control_tabs" />

<div id="tab_preanesth" style="display: none;">
  {{if $consult_anesth->_id}}
		<table class="form">
			{{if $selOp->_ref_consult_anesth->_ref_techniques|@count}}
		  <tr>
		    <th colspan="2" class="title">Techniques compl�mentaires</th>
		  </tr>
		  <tr>
		    <td colspan="2">
					<ul>
		      {{foreach from=$selOp->_ref_consult_anesth->_ref_techniques item=_technique}}
					  <li>{{$_technique->technique}}</li>
					{{/foreach}}
					</ul>
		    </td>
		  </tr>
			{{/if}}
		</table>
		<table class="tbl">
		  <!-- Affichage d'information complementaire pour l'anestesie -->
		  <tr>
		    <th class="title">Consultation de pr�-anesth�sie</th>
		  </tr>
		  <tr>
		    <td class="text">
		      <button type="button" class="print" onclick="printFicheAnesth('{{$consult_anesth->_ref_consultation->_id}}')" style="float: right">
		        Consulter la fiche
		      </button>
		      {{if $dialog}}
		        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$consult_anesth->_ref_consultation->_ref_chir}}
		        -
		        <span onmouseover="ObjectTooltip.createEx(this, '{{$consult_anesth->_guid}}')">
		        le {{mb_value object=$consult_anesth->_ref_consultation field="_date"}}
		      </span>
		      {{else}}
		      <a href="?m=dPcabinet&amp;tab=edit_consultation&amp;selConsult={{$consult_anesth->_ref_consultation->_id}}">
		        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$consult_anesth->_ref_consultation->_ref_chir}}
		        -
		        <span onmouseover="ObjectTooltip.createEx(this, '{{$consult_anesth->_guid}}')">
		        le {{mb_value object=$consult_anesth->_ref_consultation field="_date"}}
		        </span>
		      </a>
		      {{/if}}
		    </td>
		  </tr>
		</table>
	{{else}}
	  {{mb_include module=dPcabinet template=inc_choose_dossier_anesth}}
	{{/if}}
	
	<form name="visiteAnesth" action="?m={{$m}}" method="post" onsubmit="{{$onSubmit}}">
		<input type="hidden" name="dosql" value="do_planning_aed" />
		<input type="hidden" name="m" value="dPplanningOp" />
		<input type="hidden" name="del" value="0" />
		{{mb_key object=$selOp}}
		{{if $selOp->date_visite_anesth}}
		  <input name="prat_visite_anesth_id" type="hidden" value="{{$selOp->prat_visite_anesth_id}}" />
		  <input name="date_visite_anesth"    type="hidden" value="" />
		{{/if}}
	
	<table class="form">
	  <tr>
	    <th class="title" colspan="2">Visite de pr�-anesth�sie</th>
	  </tr>
	  <tr>
	    <th>{{mb_label object=$selOp field="date_visite_anesth"}}</th>
	    <td>
	    	{{if $selOp->date_visite_anesth}}
	  		  {{mb_value object=$selOp field="date_visite_anesth"}}
			  {{else}}
				  {{mb_field object=$selOp field="date_visite_anesth"}}
				{{/if}}
			</td>
	  </tr>
	  <tr>
	    <th>{{mb_label object=$selOp field="prat_visite_anesth_id"}}</th>
	    <td>
	      {{if $selOp->date_visite_anesth}}
	      Dr {{mb_value object=$selOp field="prat_visite_anesth_id"}}
	      {{elseif $currUser->_is_anesth}}
	      <input name="prat_visite_anesth_id" type="hidden" value="{{$currUser->_id}}" />
	      Dr {{$currUser->_view}}
	      {{else}}
	      <select name="prat_visite_anesth_id" class="notNull" onchange="refreshAidesPreAnesth($V(this))">
	        <option value="">&mdash; Anesth�siste</option>
	        {{foreach from=$listAnesths item=curr_anesth}}
	        <option value="{{$curr_anesth->user_id}}" {{if $selOp->prat_visite_anesth_id == $curr_anesth->user_id}} selected="selected" {{/if}}>
	          {{$curr_anesth->_view}}
	        </option>
	        {{/foreach}}
	      </select>
	    {{/if}}
	    </td>
	  </tr>
	  <tr>
	    <th>
	      {{mb_label object=$selOp field="rques_visite_anesth"}}
	      {{if !$selOp->date_visite_anesth}}
	      <!-- Ajout des aides � la saisie : mais il faut les charger selon le praticien qu'on indique !! -->
	      <select name="_helpers_rques_visite_anesth" style="width: 7em;" onchange="pasteHelperContent(this)" class="helper">
	      </select>
				<button class="new notext" title="Ajouter une aide � la saisie" type="button" onclick="addHelp('COperation', this.form.rques_visite_anesth, null, null, null, null, {{$user_id}})">
				  {{tr}}New{{/tr}}
				</button>
	      {{/if}}
	    </th>
	    <td class="text">
	      {{if $selOp->date_visite_anesth}}
	        {{mb_value object=$selOp field="rques_visite_anesth"}}
	      {{else}}
	        {{mb_field object=$selOp field="rques_visite_anesth"}}
	      {{/if}}
	    </td>
	  </tr>
	  <tr>
	    <th>{{mb_label object=$selOp field="autorisation_anesth"}}</th>
	    <td>
	      {{if $selOp->date_visite_anesth}}
	        {{mb_value object=$selOp field="autorisation_anesth"}}
	      {{else}}
	        {{mb_field object=$selOp field="autorisation_anesth"}}
	      {{/if}}
	    </td>
	  </tr>
	  {{if !$selOp->date_visite_anesth && !$currUser->_is_anesth}}
	  <tr>
	    <th>{{mb_label object=$selOp field="_password_visite_anesth"}}</th>
	    <td>{{mb_field object=$selOp field="_password_visite_anesth"}}</td>
	  </tr>
	  {{/if}}
	  {{if !$selOp->date_visite_anesth}}
	  <tr>
	    <td class="button" colspan="2">
	      <button class="submit" type="submit">
	        {{tr}}Validate{{/tr}}
	      </button>
	    </td>
	  </tr>
	  {{else}}
	  <tr>
	    <th>{{mb_label object=$selOp field="_password_visite_anesth"}}</th>
	    <td>{{mb_field object=$selOp field="_password_visite_anesth"}}</td>
	  </tr>
	  <tr>
	    <td class="button" colspan="2">
	      {{mb_field class="COperation" hidden="hidden" field="date_visite_anesth"}}
	      {{mb_field class="COperation" hidden="hidden" field="rques_visite_anesth"}}
	      {{mb_field class="COperation" hidden="hidden" field="autorisation_anesth"}}
	      <button class="trash" type="submit">
	        {{tr}}Cancel{{/tr}}
	      </button>
	    </td>
	  </tr>
	  {{/if}}
	</table>
	
	</form>
	
	{{if $consult_anesth->_id}}
	<table class="form">
	  <tr>
	    <th colspan="2" class="title">Fichiers / Documents</th>
	  </tr>
	  <tr>
	    <td>
	      {{mb_include_script module="dPcabinet" script="file"}}
	      <div id="files-anesth">
	      <script type="text/javascript">
	        File.register('{{$consult_anesth->_id}}','{{$consult_anesth->_class_name}}', 'files-anesth');
	      </script>
	      </div>
	    </td>
	    <td>
	      <div id="documents-anesth">
	        {{mb_include_script module="dPcompteRendu" script="document"}}
	        <script type="text/javascript">
	          Document.register('{{$consult_anesth->_id}}','{{$consult_anesth->_class_name}}','{{$modeles_prat_id}}','documents-anesth');
	        </script>
				</div>
	    </td>
	  </tr>
	</table>
	{{/if}}
</div>

<div id="tab_perop" style="display: none;">
	<table class="form">
	  <tr>
	    <th class="title" colspan="2">Per-operatoire</th>
	  </tr>
	  <tr>
	    <td style="width: 50%">
	      <!-- Affichage et gestion des gestes perop -->
	      <form name="addAnesthPerop" action="?" method="post" 
	            onsubmit="return onSubmitFormAjax(this, { onComplete: function(){ refreshAnesthPerops('{{$selOp->_id}}'); $V(this.libelle, ''); }.bind(this)  } )">
	
	        <input type="hidden" name="m" value="dPsalleOp" />
	        <input type="hidden" name="del" value="0" />
	        <input type="hidden" name="dosql" value="do_anesth_perop_aed" />
	        <input type="hidden" name="operation_id" value="{{$selOp->_id}}" />
	        <input type="hidden" name="datetime" value="now" />
	        
	        {{mb_key object=$anesth_perop}}
	        {{mb_label object=$anesth_perop field="libelle"}}
	        <select name="_helpers_libelle" size="1" onchange="pasteHelperContent(this)" class="helper">
	          <option value="">&mdash; Aide</option>
	          {{html_options options=$anesth_perop->_aides.libelle.no_enum}}
	        </select>
	        <button class="new notext" title="Ajouter une aide � la saisie" type="button" onclick="addHelp('CAnesthPerop', this.form._hidden_libelle, 'libelle', null, null, null, {{$user_id}})">{{tr}}New{{/tr}}</button><br />
	        <input type="hidden" name="_hidden_libelle" value="" />
	        <textarea name="libelle" onblur="if(!$(this).emptyValue()){this.form.onsubmit();}"></textarea>
	        <button class="submit" type="button">Ajouter</button>
	      </form>
	    </td>
	    <td id="list_perops_{{$selOp->_id}}"></td>
	  </tr>
	</table>
	<div id="perop"></div>
</div>