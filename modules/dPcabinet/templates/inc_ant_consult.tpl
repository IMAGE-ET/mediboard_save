{{mb_include_script module="dPplanningOp" script="cim10_selector"}}
{{mb_include_script module="dPmedicament" script="medicament_selector"}}

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
}

updateTokenCim10 = function() {
  var oForm = getForm("editDiagFrm");
  onSubmitFormAjax(oForm, { onComplete : DossierMedical.reloadDossierPatient });
}

updateTokenCim10Anesth = function(){
  var oForm = getForm("editDiagAnesthFrm");
  onSubmitFormAjax(oForm, { onComplete : DossierMedical.reloadDossierSejour });
}

onSubmitAnt = function (oForm) {
	if (oForm.rques.value.blank()) {
    return false;
  }

  onSubmitFormAjax(oForm, {
    onComplete : DossierMedical.reloadDossiersMedicaux 
  } );
  
  // Nettoyage du formulaire
  oForm._hidden_rques.value = oForm.rques.value;
  oForm.rques.value = "";

  return false;
}

onSubmitTraitement = function (oForm) {
	if (oForm.traitement.value.blank()) {
    return false;
  }
  
  onSubmitFormAjax(oForm, {
    onComplete : DossierMedical.reloadDossiersMedicaux 
  } );
  
  // Nettoyage du formulaire
  oForm._hidden_traitement.value = oForm.traitement.value;
  oForm.traitement.value = "";
  oForm._helpers_traitement.value = "";

  return false;
}

easyMode = function() {
  var url = new Url("dPcabinet", "vw_ant_easymode");
  url.addParam("user_id", "{{$userSel->_id}}");
  url.addParam("patient_id", "{{$patient->_id}}");
  {{if isset($consult|smarty:nodefaults)}}
    url.addParam("consult_id", "{{$consult->_id}}");
  {{/if}}
  url.pop(900, 600, "Mode grille");
}

/**
 * Mise a jour du champ _sejour_id pour la creation d'antecedent et de traitement
 * et la widget de dossier medical
 */
DossierMedical = {
  sejour_id: '{{$sejour_id}}',
  updateSejourId : function(sejour_id) {
    this.sejour_id = sejour_id;
    
    // Mise � jour des formulaire
    if(document.editTrmtFrm){
      document.editTrmtFrm._sejour_id.value   = sejour_id;
    }
    if(document.editAntFrm){
      document.editAntFrm._sejour_id.value    = sejour_id;
    }
  },
  
  reloadDossierPatient: function() {
	  var antUrl = new Url("dPcabinet", "httpreq_vw_list_antecedents");
	  antUrl.addParam("patient_id", "{{$patient->_id}}");
	  antUrl.addParam("_is_anesth", "{{$_is_anesth}}");
	  {{if $_is_anesth}}
	    antUrl.addParam("sejour_id", DossierMedical.sejour_id);
	  {{/if}}
	  antUrl.requestUpdate('listAnt');
	},
	reloadDossierSejour: function(){
      var antUrl = new Url("dPcabinet", "httpreq_vw_list_antecedents_anesth");  
	  antUrl.addParam("sejour_id", DossierMedical.sejour_id);
	  antUrl.requestUpdate('listAntCAnesth');
	},
	reloadDossiersMedicaux: function(){
	  DossierMedical.reloadDossierPatient();
    {{if $_is_anesth}}
    DossierMedical.reloadDossierSejour();
    {{/if}}
	}
}

refreshAidesAntecedents = function(){
  var oForm = document.editAntFrm;
  var url = new Url("dPcompteRendu", "httpreq_vw_select_aides");
  url.addParam("object_class", "CAntecedent");
  url.addParam("depend_value_1", oForm.type.value);
  url.addParam("depend_value_2", oForm.appareil.value);
  url.addParam("user_id", "{{$userSel->_id}}")
  url.addParam("field", "rques");
  url.requestUpdate(document.editAntFrm._helpers_rques);
}
 
refreshAddPoso = function(code_cip){
  var url = new Url("dPprescription", "httpreq_vw_select_poso");
  url.addParam("code_cip", code_cip);
  url.requestUpdate("addPosoLine");
}

Main.add(function () {
  DossierMedical.reloadDossiersMedicaux();
  refreshAidesAntecedents();
	if($('tab_traitements_perso')){
	  Control.Tabs.create('tab_traitements_perso', false);
	}
});

</script>

<table class="main">
  <tr>
    <td class="halfPane">
      
<table class="form">
  <tr>
    <th class="category">
      <button style="float: left" class="edit" type="button" onclick="easyMode();">Mode grille</button>
      Ant�c�dents
    </th>
  </tr>
  <tr>
    <td class="text">      
      <!-- Ant�c�dents -->
      <form name="editAntFrm" action="?m=dPcabinet" method="post" onsubmit="return onSubmitAnt(this);">
      
      <input type="hidden" name="m" value="dPpatients" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_antecedent_aed" />
      <input type="hidden" name="_patient_id" value="{{$patient->_id}}" />
      
      <!-- dossier_medical_id du sejour si c'est une consultation_anesth -->
      {{if $_is_anesth}}
      <!-- On passe _sejour_id seulement s'il y a un sejour_id -->
      <input type="hidden" name="_sejour_id" value="{{$sejour_id}}" />
      {{/if}}

      <table class="form">
        <col style="width: 70px;" />
        <col  class="narrow" />
        
        <tr>
          {{if $app->user_prefs.showDatesAntecedents}}
            <th>{{mb_label object=$antecedent field=date}}</th>
            <td>{{mb_field object=$antecedent field=date form=editAntFrm register=true}}</td>
          {{else}}
            <td colspan="2" />
          {{/if}}
          
          <th id="listAides_Antecedent_rques">
            {{mb_label object=$antecedent field="rques"}}
            	<select name="_helpers_rques" style="width: 7em;" onchange="pasteHelperContent(this)" class="helper">
							</select>
						<input type="hidden" name="_hidden_rques" value="" />
            <button class="new notext" title="Ajouter une aide � la saisie" type="button" onclick="addHelp('CAntecedent', this.form._hidden_rques, 'rques', this.form.type.value, this.form.appareil.value, null, {{$userSel->_id}})">
              {{tr}}New{{/tr}}
            </button>
          </th>
        </tr>

        <tr>
          <!-- Auto-completion -->
          <th>{{mb_label object=$antecedent field=_search}}</th>
          <td>
            {{mb_field object=$antecedent field=_search size=10 class="autocomplete"}}
            <script type="text/javascript">
              Main.add(function(){
                var form = getForm("editAntFrm");
                var elements = form.elements;
                new AideSaisie.AutoComplete(elements.rques, {
                  searchField: elements._search, 
                  dependField1: elements.type, 
                  dependField2: elements.appareil, 
                  objectClass: "CAntecedent", 
                  // Probleme dans dPurgence si on prend l'utilisateur de la consult
                  //contextUserId: "{{$userSel->_id}}",
                  //contextUserView: "{{$userSel->_view}}",
                  timestamp: "{{$dPconfig.dPcompteRendu.CCompteRendu.timestamp}}"
                });
                
                $(elements._search).observe("blur", function(e){
                  $V(Event.element(e), "");
                });
              });
            </script>
          </td>
          <td rowspan="3">
            <textarea name="rques" rows=4 onblur="this.form.onsubmit();"></textarea>
          </td>
        </tr>

        <tr>
          <th>{{mb_label object=$antecedent field="type"}}</th>
          <td>{{mb_field object=$antecedent field="type" defaultOption="&mdash; Aucun" alphabet="1" onchange="refreshAidesAntecedents()"}}</td>
        </tr>

        <tr>
          <th>{{mb_label object=$antecedent field="appareil"}}</th>
          <td>{{mb_field object=$antecedent field="appareil" defaultOption="&mdash; Aucun" alphabet="1" onchange="refreshAidesAntecedents()"}}</td>
        </tr>
        
        <tr>
          <td class="button" colspan="2">
            <button class="tick" type="button">
              {{tr}}Add{{/tr}} un ant�c�dent
            </button>
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
  {{if $isPrescriptionInstalled || $dPconfig.dPpatients.CTraitement.enabled}}
  <tr>
    <th class="category">
      Traitements
    </th>
  </tr>
  <tr>
    <td class="text">
			<ul id="tab_traitements_perso" class="control_tabs small">
				{{if $isPrescriptionInstalled}}
				  <li><a href="#tp_base_med">Base de donn�es de m�dicaments</a></li>
				{{/if}}
				{{if $dPconfig.dPpatients.CTraitement.enabled}}
				  <li><a href="#tp_texte_simple">Texte simple</a></li>
				{{/if}}
			</ul>
			<hr class="control_tabs" /> 
    </td>
	</tr>
	{{/if}}
	
	{{if $isPrescriptionInstalled}}
	<tr id="tp_base_med">
		<td class="text">
      <!-- Formulaire d'ajout de traitements -->
      <form name="editLineTP" action="?m=dPcabinet" method="post">
        <input type="hidden" name="m" value="dPprescription" />
        <input type="hidden" name="dosql" value="do_add_line_tp_aed" />
        <input type="hidden" name="code_cip" value="" onchange="refreshAddPoso(this.value);"/>
        <input type="hidden" name="_patient_id" value="{{$patient->_id}}" />
        <input type="hidden" name="praticien_id" value="{{$userSel->_id}}" />
        
        <table class="form">
          <col style="width: 70px;" />
          <col class="narrow" />
        
          <tr>
            <th>Recherche</th>
            <td>
              <input type="text" name="produit" value="" size="12" class="autocomplete" />
              <button type="button" class="search notext" onclick="MedSelector.init('produit');"></button>
              <div style="display:none; width: 350px;" class="autocomplete" id="_produit_auto_complete"></div>
              <script type="text/javascript">
                  MedSelector.init = function(onglet){
                    this.sForm = "editLineTP";
                    this.sView = "produit";
                    this.sCode = "code_cip";
                    this.sSearch = document.editLineTP.produit.value;
                    this.selfClose = true;
                    this.sOnglet = onglet;
                    this.pop();
                  }
                  MedSelector.doSet = function(){
                    var oForm = document[MedSelector.sForm];
                    $('_libelle').update(MedSelector.prepared.nom);
                    $V(oForm.code_cip, MedSelector.prepared.code);
                    $('button_submit_traitement').focus();
                  }
              </script>
            </td>
            <td>
              <strong><div id="_libelle"></div></strong>
            </td>
          </tr>
          
          <tr>
            {{if $app->user_prefs.showDatesAntecedents}}
            <th>{{mb_label object=$line field="debut"}}</th>
            <td>{{mb_field object=$line field="debut" register=true form=editLineTP}}</td>
            {{else}}
            <td colspan="2"></td>
            {{/if}}
            <td rowspan="3" id="addPosoLine"></td>
          </tr>
          
          {{if $app->user_prefs.showDatesAntecedents}}
          <tr>
            <th>{{mb_label object=$line field="fin"}}</th>
            <td>{{mb_field object=$line field="fin" register=true form=editLineTP}}</td>
          </tr>
          {{/if}}
          
          <tr>
            <th>{{mb_label object=$line field="commentaire"}}</th>
            <td>{{mb_field object=$line field="commentaire" size=20}}</td>
          </tr>
          <tr>
            <td colspan="2" class="button">
              <button id="button_submit_traitement" class="tick" type="button" onclick="submitFormAjax(this.form, 'systemMsg', { 
                onComplete: function(){
                  DossierMedical.reloadDossiersMedicaux();
                  resetEditLineTP();
                  resetFormTP();
                }
               } ); this.form.produit.focus();">
                {{tr}}Add{{/tr}} un traitement
              </button>
            </td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
  {{/if}}
      
  <!-- Traitements -->
  {{if $dPconfig.dPpatients.CTraitement.enabled}}
  <tr id="tp_texte_simple">
    <td class="text">
      <form name="editTrmtFrm" action="?m=dPcabinet" method="post" onsubmit="return onSubmitTraitement(this);">
      <input type="hidden" name="m" value="dPpatients" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_traitement_aed" />
      <input type="hidden" name="_patient_id" value="{{$patient->_id}}" />
      
      {{if $_is_anesth}}
      <!-- On passe _sejour_id seulement s'il y a un sejour_id -->
      <input type="hidden" name="_sejour_id" value="{{$sejour_id}}" />
      {{/if}}
      
      <table class="form">
        <col style="width: 70px;" />
        <col  class="narrow" />
         
        <tr>
          {{if $app->user_prefs.showDatesAntecedents}}
          <th>{{mb_label object=$traitement field=debut}}</th>
          <td>{{mb_field object=$traitement field=debut form=editTrmtFrm register=true}}</td>
          {{else}}
          <td colspan="2"></td>
          {{/if}}
          <th>
            {{mb_label object=$traitement field="traitement"}}
            <select name="_helpers_traitement" size="1" style="width: 80px;" onchange="pasteHelperContent(this)" class="helper">
              <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
              {{html_options options=$traitement->_aides.traitement.no_enum}}
            </select>
            <input type="hidden" name="_hidden_traitement" value="" />
            <button class="new notext" title="Ajouter une aide � la saisie" type="button" onclick="addHelp('CTraitement', this.form._hidden_traitement, 'traitement', null, null, null, {{$userSel->_id}})">
              {{tr}}New{{/tr}}
            </button>
          </th>
        </tr>
        <tr>
          {{if $app->user_prefs.showDatesAntecedents}}
          <th>{{mb_label object=$traitement field=fin}}</th>
          <td>{{mb_field object=$traitement field=fin form=editTrmtFrm register=true}}</td>
          {{else}}
          <td colspan="2"></td>
          {{/if}}
          <td rowspan="3">
            <textarea name="traitement" onblur="this.form.onsubmit()"></textarea>
          </td>
        </tr>
        <tr>
          <!-- Auto-completion -->
          <th>{{mb_label object=$traitement field=_search}}</th>
          <td>
            {{mb_field object=$traitement field=_search size=10 class="autocomplete"}}
            <script type="text/javascript">
              Main.add(function(){
                var form = getForm("editTrmtFrm");
                var elements = form.elements;
                new AideSaisie.AutoComplete(elements.traitement, {
                  searchField: elements._search, 
                  objectClass: "CTraitement", 
                  // Probleme dans dPurgence si on prend l'utilisateur de la consult
                  //contextUserId: "{{$userSel->_id}}",
                  //contextUserView: "{{$userSel->_view}}",
                  timestamp: "{{$dPconfig.dPcompteRendu.CCompteRendu.timestamp}}"
                });
                
                $(elements._search).observe("blur", function(e){
                  $V(Event.element(e), "");
                });
              });
            </script>
          </td>
        </tr>

        <tr>
          <td class="button" colspan="2">
            <button class="tick" type="button">
              {{tr}}Add{{/tr}} un traitement
            </button>
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
  {{/if}}
  
  <!-- Diagnostics CIM -->
  <tr>
    <th class="category">
      Base de donn�es CIM
    </th>
  </tr>
  <tr>
    <td class="text">
      {{main}}
	      var url = new Url("dPcim10", "ajax_code_cim10_autocomplete");
	      url.autoComplete("addDiagFrm_keywords_code", '', {
	        minChars: 1,
	        dropdown: true,
	        width: "250px",
          afterUpdateElement: function(oHidden) {
            oForm = getForm("addDiagFrm");
            $V(oForm.code_diag, oHidden.value);
            reloadCim10($V(oForm.code_diag));
          }
	      });
      {{/main}}
      <form name="addDiagFrm" action="?m=dPcabinet" method="post" onsubmit="return false;">
        <strong>Ajouter un diagnostic</strong>
        <input type="hidden" name="chir" value="{{$userSel->_id}}" />
        <input type="text" name="keywords_code" class="autocomplete str code cim10" value="" size="10"/>
        <input type="hidden" name="code_diag" onchange="$V(this.form.keywords_code, this.value)"/>
        <button class="search" type="button" onclick="CIM10Selector.init()">{{tr}}Search{{/tr}}</button>
        <button class="tick notext" type="button" onclick="reloadCim10(this.form.code_diag.value)">{{tr}}Validate{{/tr}}</button>
        <script type="text/javascript">   
          CIM10Selector.init = function(){
            this.sForm = "addDiagFrm";
            this.sView = "code_diag";
            this.sChir = "chir";
            this.options.mode = "favoris";
            this.pop();
          }
        </script> 
      </form>
      
      <table style="width: 100%">
      {{foreach from=$patient->_static_cim10 key=cat item=curr_cat}}
        <tr id="category{{$cat}}-trigger">
          <td>{{$cat}}</td>
        </tr>
        <tbody id="category{{$cat}}">
          <tr class="script">
            <td>
              <script type="text/javascript">new PairEffect("category{{$cat}}");</script>
            </td>
          </tr>
          {{foreach from=$curr_cat item=curr_code key="key"}}
          <tr>
            <td class="text">
            <form name="code_finder-{{$curr_code->sid}}" action="?">
              <button class="tick notext" type="button" onclick="oCimField.add('{{$curr_code->code}}'); if(DossierMedical.sejour_id != '') { {{if $_is_anesth}}oCimAnesthField.add('{{$curr_code->code}}');{{/if}} }">
                Ajouter
              </button>
              
              <input type="hidden" name="codeCim" value="{{$curr_code->code}}" />
              <button class="down notext" type="button" onclick="CIM10Selector.initfind{{$curr_code->sid}}()">
                Parcourir
              </button>
              <script type="text/javascript">   
                CIM10Selector.initfind{{$curr_code->sid}} = function(){
                  this.sForm = "code_finder-{{$curr_code->sid}}";
                  this.sCode = "codeCim";
                  this.find();
                }
              </script> 
              {{$curr_code->code}}: {{$curr_code->libelle}}
              </form>
            </td>
          </tr>
           {{/foreach}}
        </tbody>
      {{/foreach}}
      </table>
    </td>
  </tr>
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
  {{if $_is_anesth}}
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
  if(oFormTP.code_cip.value){
    submitFormAjax(oFormTP, "systemMsg", { onComplete: function() { 
      updateTP(selected);
      DossierMedical.reloadDossiersMedicaux();
    } } );
  } else {
    updateTP(selected);
  }
}
  
updateTP = function(selected){
  resetEditLineTP();
  Element.cleanWhitespace(selected);
  var dn = selected.childElements();
  $V(oFormTP.code_cip, dn[0].innerHTML);
  $("_libelle").insert("<button type='button' class='cancel notext' onclick='resetEditLineTP(); resetFormTP();'></button><a href=\"#nothing\" onclick=\"Prescription.viewProduit('','"+dn[1].innerHTML+"','"+dn[2].innerHTML+"')\">"+dn[3].innerHTML.stripTags()+"</a>");
  $V(oFormTP.produit, '');
  $('button_submit_traitement').focus();
}  

// Autocomplete des medicaments
var urlAuto = new Url("dPmedicament", "httpreq_do_medicament_autocomplete");
urlAuto.autoComplete("editLineTP_produit", "_produit_auto_complete", {
  minChars: 3,
  updateElement: updateFieldsMedicamentTP, 
  callback: function(input, queryString){
    return (queryString + "&produit_max=40"); 
  }
} );

resetEditLineTP = function(){
  $("_libelle").update("");
  oFormTP.code_cip.value = '';
}

resetFormTP = function(){
  $V(oFormTP.commentaire, '');
  $V(oFormTP.token_poso, '');
  $('addPosoLine').update('');                   
}
</script>
{{/if}}