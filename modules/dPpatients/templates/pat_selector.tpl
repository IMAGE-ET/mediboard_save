{{* $Id$ *}}

{{if $app->user_prefs.GestionFSE}}
  {{if $app->user_prefs.VitaleVision}}
    {{include file="../../dPpatients/templates/inc_vitalevision.tpl" debug=false keepFiles=true}}
  {{else}}
    {{include file="../../dPpatients/templates/inc_intermax.tpl" debug=false}}
  {{/if}}
{{/if}}

<script type="text/javascript">

var Patient = {
  create: function(useVitale) {
    this.edit(0, useVitale);
  },

  edit: function(patient_id, useVitale) {
    var url = new Url("dPpatients", "vw_edit_patients");
    url.addParam("patient_id", patient_id);
    url.addParam("dialog", 1);

	  var oForm = null;
    if (oForm = document.patientSearch) {
      url.addElement(oForm.name);
      url.addElement(oForm.firstName);
    }
    
    if (useVitale || (oForm = document.patientEdit)) {
      url.addParam("useVitale", 1);
    }

    url.redirect();
  },

  selectAndUpdate: function(patient_id) {
    var oForm = document.patientEdit;
    oForm.patient_id.value = patient_id;
    submitFormAjax(oForm, 'systemMsg');
    Patient.select(patient_id, oForm.nom.value);
  },
  
  select: function(patient_id, patient_view) {
		window.opener.PatSelector.set(patient_id, patient_view);
    window.close();
  }
}

{{if $app->user_prefs.GestionFSE && !$app->user_prefs.VitaleVision}}
Intermax.ResultHandler["Consulter Vitale"] =
Intermax.ResultHandler["Lire Vitale"] = function() {
  var url = new Url("dPpatients", "pat_selector");
  url.addParam("useVitale", 1);
  url.addParam("dialog", 1);
  url.redirect();
}
{{/if}}
</script>

<div id="modal-beneficiaire" style="display:none; text-align:center;">
  <p id="msg-multiple-benef">
    Cette carte vitale semble contenir plusieurs b�n�ficiaires, merci de s�lectionner la personne voulue :
  </p>
  <p id="msg-confirm-benef" style="display: none;"></p>
	<p id="benef-nom">
	  <select id="modal-beneficiaire-select"></select>
    <span></span>
  </p>
  <div>
  	<button type="button" class="tick" onclick="VitaleVision.search(getForm('patientSearch'), $V($('modal-beneficiaire-select'))); VitaleVision.modalWindow.close();">{{tr}}Choose{{/tr}}</button>
	  <button type="button" class="cancel" onclick="VitaleVision.modalWindow.close();">{{tr}}Cancel{{/tr}}</button>
  </div>
</div>

{{if $patVitale}}

<!-- Formulaire de mise � jour Vitale -->
<form name="patientEdit" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
<input type="hidden" name="m" value="dPpatients" />
<input type="hidden" name="dosql" value="do_patients_aed" />
<input type="hidden" name="_bind_vitale" value="do" />
{{mb_field object=$patVitale field="patient_id" hidden="true"}}

<table class="form">

	<tr>
	  <th class="category" colspan="4">Valeurs SESAM Vitale</th>
	</tr>
	
	<tr>
	  <th>{{mb_label object=$patVitale field="nom"}}</th>
	  <td>
	    {{mb_value object=$patVitale field="nom"}}
	    {{mb_field object=$patVitale field="nom" hidden="true"}}
	  </td>
	  <th rowspan="2">{{mb_label object=$patVitale field="adresse"}}</th>
	  <td rowspan="2">
	    {{mb_value object=$patVitale field="adresse"}}
	    {{mb_field object=$patVitale field="adresse" hidden="true"}}
	  </td>
	</tr>
	
	<tr>
	  <th>{{mb_label object=$patVitale field="prenom"}}</th>
	  <td colspan="3">
	    {{mb_value object=$patVitale field="prenom"}}
	    {{mb_field object=$patVitale field="prenom" hidden="true"}}
	  </td>
	</tr>
	
	<tr>
	  <th>{{mb_label object=$patVitale field="naissance"}}</th>
	  <td>
	    {{mb_value object=$patVitale field="naissance"}}
	    {{mb_field object=$patVitale field="naissance" hidden="true"}}
	    {{mb_field object=$patVitale field="rang_naissance" hidden="true"}}
	  </td>
	  <th>{{mb_label object=$patVitale field="cp"}}</th>
	  <td>
	    {{mb_value object=$patVitale field="cp"}}
	    {{mb_field object=$patVitale field="cp" hidden="true"}}
	  </td>
	</tr>
	
	<tr>
	  <th>{{mb_label object=$patVitale field="matricule"}}</th>
	  <td>
	    {{mb_value object=$patVitale field="matricule"}}
	    {{mb_field object=$patVitale field="matricule" hidden="true"}}
	    {{mb_field object=$patVitale field="assure_matricule" hidden="true"}}
	    {{mb_field object=$patVitale field="rang_beneficiaire" hidden="true"}}
	  </td>
	  <th>{{mb_label object=$patVitale field="ville"}}</th>
	  <td>
	    {{mb_value object=$patVitale field="ville"}}
	    {{mb_field object=$patVitale field="ville" hidden="true"}}
	  </td>
	</tr>

	<tr>
	  <th>{{mb_label object=$patVitale field="regime_sante"}}</th>
	  <td colspan="3">
	    {{mb_value object=$patVitale field="regime_sante"}}
	    {{mb_field object=$patVitale field="code_regime" hidden="true"}}
	    {{mb_field object=$patVitale field="caisse_gest" hidden="true"}}
	    {{mb_field object=$patVitale field="centre_gest" hidden="true"}}
	    {{mb_field object=$patVitale field="regime_sante" hidden="true"}}
	  </td>
	</tr>
	<tr>
	  <td class="button">
      {{if $can->edit}}
        <button class="new" type="button" onclick="Patient.create({{$useVitale}});">
          {{tr}}Create{{/tr}}
          {{if $useVitale}}avec Vitale{{/if}}
        </button>
      {{/if}}
    </td>
    <td class="button">
      <button class="cancel" type="button" onclick="window.close()">Annuler</button>
    </td>
	</tr>
</table>

</form>

{{else}}

<!-- Formulaire de recherche -->
<form action="?" name="patientSearch" method="get">

<input type="hidden" name="m" value="dPpatients" />
<input type="hidden" name="a" value="pat_selector" />
<input type="hidden" name="dialog" value="1" />
<input type="hidden" name="useVitale" value="" />

<table class="form">

<tr>
  <th class="category" colspan="6">Crit�res de s�lection</th>
</tr>

<tr>
  <th><label for="name" title="Nom du patient � rechercher, au moins les premi�res lettres">Nom</label></th>
  <td><input name="name" value="{{$name|stripslashes}}" size="30" tabindex="1" /></td>
  
  {{if $dPconfig.dPpatients.CPatient.tag_ipp && $dPsanteInstalled}}
      <th>IPP</th>
      <td>
        <input tabindex="6" type="text" name="patient_ipp" value="{{$patient_ipp}}"/>
      </td>
  {{/if}}
</tr>

<tr>
  <th><label for="firstName" title="Pr�nom du patient � rechercher, au moins les premi�res lettres">Pr�nom</label></th>
  <td><input name="firstName" value="{{$firstName|stripslashes}}" size="30" tabindex="2" /></td>
  
  <th><label for="naissance" title="Date de naissance">Date de naissance</label></th>
  <td>
    {{html_select_date
      time=$datePat
      start_year=1900
      field_order=DMY
      day_empty="Jour"
      month_empty="Mois"
      year_empty="Ann�e"
      day_extra="tabindex='4'"
      month_extra="tabindex='5'"
      year_extra="tabindex='6'"
      all_extra="style='display:inline;'"}}         
  </td>
</tr>

<tr>
  <th><label for="nomjf" title="Nom de naissance">Nom de naissance</label></th>
  <td><input name="nomjf" value="{{$nomjf|stripslashes}}" size="30" tabindex="3" /></td>
  <td colspan="3"></td>
</tr>  
  
<tr>
  <td>
  {{if $app->user_prefs.GestionFSE}}
    {{if $app->user_prefs.VitaleVision}}
      <button class="search" type="button" onclick="$V(this.form.useVitale, 1); VitaleVision.read();">
        Lire Vitale
      </button>
    {{else}}
      <button class="search" type="button" onclick="Intermax.trigger('Lire Vitale');">
        Lire Vitale
      </button>
      <button class="change intermax-result" type="button" onclick="Intermax.result();">
        R�sultat Vitale
      </button>
    {{/if}}
  {{/if}}
  </td>
  <td class="button"
    <button class="search" type="submit">Rechercher</button>
  </td>
  <td class="button">
    {{if $can->edit}}
      {{if $name || $firstName || $nomjf || $patient_ipp || ($datePat && $datePat != "--")}}
      <button class="new" type="button" onclick="Patient.create({{$useVitale}});">
        {{tr}}Create{{/tr}}
      </button>
      {{/if}}
    {{/if}}
  </td>
  <td class="button">
    <button class="cancel" type="button" onclick="window.close()">Annuler</button>
  </td>
</tr>    
</table>

</form>
{{/if}}

<!-- Liste de patients -->
<table class="tbl">
  <tr>
    <th class="category" colspan="5">Choisissez un patient dans la liste</th>
  </tr>
  <tr>
    <th align="center">Patient</th>
    <th align="center">Date de naissance</th>
    {{if $patVitale}}
    <th align="center">{{mb_label object=$patVitale field="matricule"}}</th>
    <th align="center">{{mb_label object=$patVitale field="adresse"}}</th>
    {{else}}
    <th align="center">T�l�phone</th>
    <th align="center">Mobile</th>
    {{/if}}
    <th align="center">Actions</th>
  </tr>
  
  <!-- Recherche exacte -->
  {{foreach from=$patients item=_patient}}
    {{include file="inc_line_pat_selector.tpl"}}
  {{foreachelse}}
  {{if $name || $firstName}}
  <tr>
    <td class="button" colspan="5">
      Aucun r�sultat exact
    </td>
  </tr>
  {{/if}}
  {{/foreach}}

  <!-- Recherche phon�tique -->
  {{if $patientsSoundex|@count}}
  <tr>
    <th colspan="5">
      <em>R�sultats proches</em>
    </th>
  </tr>
  {{/if}}

  {{foreach from=$patientsSoundex item=_patient}}
    {{include file="inc_line_pat_selector.tpl"}}
  {{/foreach}}
</table>
