{{mb_script module="dPplanningOp" script="cim10_selector" ajax=1}}

{{if "dPmedicament"|module_active}}
{{mb_script module="dPmedicament" script="medicament_selector" ajax=1}}
{{/if}}

<script type="text/javascript">

var cim10url = new Url;

reloadCim10 = function(sCode){
  var oForm = getForm("addDiagFrm");
  
  oCimField.add(sCode);
 
  {{if $_is_anesth}}
  if(DossierMedical.sejour_id){
    oCimAnesthField.add(sCode);
  }
  {{/if}}
  $V(oForm.code_diag, '');
  $V(oForm.keywords_code, '');
};

onSubmitAnt = function (form) {
  var rques = $(form.rques);
  if (!rques.present()) {
    return false;
  }

  onSubmitFormAjax(form, {onComplete : function() {
    DossierMedical.reloadDossiersMedicaux();
    if (window.reloadAtcd) {
      reloadAtcd();
    }
  }});
  
  // Apr�s l'ajout d'ant�c�dents
  if (Preferences.empty_form_atcd == "1") {
    $V(form.date    , "");
    $V(form.date_da , "");
    $V(form.type    , "");
    $V(form.appareil, "");
  }

  $V(form.keywords_composant, "");
  $V(form.cds, "");

  // Dans le cas des CDS, on vide le type
  if ($V(form._idex_tag) == "COMPENDIUM_CDS") {
    $V(form.type    , "");
  }

  $V(form._idex_code, "");
  $V(form._idex_tag, "");

  rques.clear().focus();

  return false;
};

onSubmitTraitement = function (form) {
  var trait = $(form.traitement);
  if (!trait.present()) {
    return false;
  }
  
  onSubmitFormAjax(form, {
    onComplete : DossierMedical.reloadDossiersMedicaux 
  } );
  
  trait.clear().focus();

  return false;
};

easyMode = function() {
  var url = new Url("dPcabinet", "vw_ant_easymode");
  url.addParam("patient_id", "{{$patient->_id}}");
  {{if isset($consult|smarty:nodefaults)}}
    url.addParam("consult_id", "{{$consult->_id}}");
  {{/if}}
  url.pop(900, 600, "Mode grille");
};

/**
 * Mise a jour du champ _sejour_id pour la creation d'antecedent et de traitement
 * et la widget de dossier medical
 */
DossierMedical = {
  sejour_id: '{{$sejour_id}}',
  sort_by_date : Preferences.sort_atc_by_date,
  updateSejourId : function(sejour_id) {
    this.sejour_id = sejour_id;
    
    // Mise � jour des formulaire
    if(document.editTrmtFrm){
      $V(document.editTrmtFrm._sejour_id, sejour_id, false);
    }
    if(document.editAntFrm){
      $V(document.editAntFrm._sejour_id, sejour_id, false);
    }
  },
  
  reloadDossierPatient: function() {
    var antUrl = new Url("dPcabinet", "httpreq_vw_list_antecedents");
    antUrl.addParam("patient_id", "{{$patient->_id}}");
    antUrl.addParam("_is_anesth", "{{$_is_anesth}}");
    antUrl.addParam("sort_by_date", DossierMedical.sort_by_date);
    {{if $_is_anesth}}
      antUrl.addParam("sejour_id", DossierMedical.sejour_id);
    {{/if}}
    antUrl.requestUpdate('listAnt');
    {{if $conf.ref_pays == 2 && $m == "dPurgences"}}
      refreshAntecedentsPatient();
    {{/if}}
  },

  toggleSortAntecedent: function () {
    if (DossierMedical.sort_by_date == 1) {
      DossierMedical.sort_by_date = 0;
    }
    else {
      DossierMedical.sort_by_date = 1;
    }
    DossierMedical.reloadDossierPatient();


  },
  reloadDossierSejour: function(){
      var antUrl = new Url("dPcabinet", "httpreq_vw_list_antecedents_anesth");  
    antUrl.addParam("sejour_id", DossierMedical.sejour_id);
    antUrl.requestUpdate('listAntCAnesth');
  },
  reloadDossiersMedicaux: function(){
    DossierMedical.reloadDossierPatient();
    {{if $_is_anesth || $sejour_id}}
    DossierMedical.reloadDossierSejour();
    {{/if}}
  }
};
 
refreshAddPoso = function(code){
  var url = new Url("dPprescription", "httpreq_vw_select_poso");
  url.addParam("_code", code);
  url.requestUpdate("addPosoLine");
};

Main.add(function () {
  DossierMedical.reloadDossiersMedicaux();
});

</script>

<table class="main">
  {{mb_default var=show_header value=0}}
  {{if $show_header}}
    <tr>
      <th class="title" colspan="2">
        <a style="float: left" href="?m=patients&amp;tab=vw_full_patients&amp;patient_id={{$patient->_id}}">
          {{mb_include module=patients template=inc_vw_photo_identite size=42}}
        </a>

        <h2 style="color: #fff; font-weight: bold;">
          {{$patient}}
          {{if isset($sejour|smarty:nodefaults)}}
          <span style="font-size: 0.7em;"> - {{$sejour->_shortview|replace:"Du":"S�jour du"}}</span>
          {{/if}}
        </h2>
      </th>
    </tr>
  {{/if}}
  
  <tr>
    <td class="halfPane">
      <table class="form">
        <tr>
          <td class="button">
            <button class="edit" type="button" onclick="easyMode();">Mode grille</button>
          </td>
        </tr>
        {{mb_include module=cabinet template=inc_ant_consult_trait}}
      </table>
    </td>
    <td class="halfPane">
      <table class="form">
        <tr>
          <th class="category">Dossier patient</th>
        </tr>
        <tr>
          <td class="text" id="listAnt"></td>
        </tr>
        {{if $_is_anesth || $sejour_id}}
        <tr>
          <th class="category">
            El�ments significatifs pour le s�jour
          </th>
        </tr>
        <tr>
          <td class="text" id="listAntCAnesth"></td>
        </tr>
        {{/if}}
      </table>
    </td>
  </tr>
</table>

{{if $isPrescriptionInstalled}}
<script type="text/javascript">
var oFormTP = getForm("editLineTP");

// UpdateFields de l'autocomplete de medicaments
updateFieldsMedicamentTP = function(selected) {
  // Submit du formulaire avant de faire le selection d'un nouveau produit
  if(oFormTP._code.value){
    submitFormAjax(oFormTP, "systemMsg", { onComplete: function() { 
      updateTP(selected);
      DossierMedical.reloadDossiersMedicaux();
    } } );
  } else {
    updateTP(selected);
  }
};
  
updateTP = function(selected){
  resetEditLineTP();
  Element.cleanWhitespace(selected);
  var dn = selected.childElements();
  dn = dn[0].innerHTML;

  // On peut saisir un traitement personnel seulement le code CIP est valide
  if (isNaN(parseInt(dn))) {
    return
  }
  $V(oFormTP._code, dn);
  $("_libelle").insert("<button type='button' class='cancel notext' onclick='resetEditLineTP(); resetFormTP();'></button>" +
                       "<a href=\"#nothing\" onclick=\"Prescription.viewProduit('','','"+selected.down(".code-cis").getText()+"')\">"+
                        selected.down(".libelle").getText()+"</a>");

  if (selected.down(".alias")) {
    $("_libelle").insert(selected.down(".alias").getText());
  }

  if (selected.down(".forme")) {
    $("_libelle").insert("<br /><span class='compact'>"+selected.down(".forme").getText()+"</span>");
  }

  $V(oFormTP.produit, '');
  $('button_submit_traitement').focus();
};

// Autocomplete des medicaments
var urlAuto = new Url("dPmedicament", "httpreq_do_medicament_autocomplete");
urlAuto.autoComplete(getForm('editLineTP').produit, "_produit_auto_complete", {
  minChars: 3,
  updateElement: updateFieldsMedicamentTP,
  callback: function(input, queryString){
    return (queryString + "&produit_max=40&only_prescriptible_sf=0&with_alias=1");
  }
} );

resetEditLineTP = function(){
  $("_libelle").update("");
  oFormTP._code.value = '';
};

resetFormTP = function(){
  $V(oFormTP.commentaire, '');
  $V(oFormTP.token_poso, '');
  $('addPosoLine').update('');
  
  $V(oFormTP.long_cours, 1);
  $V(oFormTP.__long_cours, true);
}
</script>
{{/if}}