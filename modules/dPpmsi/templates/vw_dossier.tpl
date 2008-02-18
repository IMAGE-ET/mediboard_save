<!-- $Id$ -->

{{include file="../../dPfiles/templates/inc_files_functions.tpl"}}
{{mb_include_script module="dPpatients" script="pat_selector"}}
{{mb_include_script module="dPplanningOp" script="cim10_selector"}}

<script type="text/javascript">
  
CIM10Selector.initDP = function(sejour_id){
  this.sForm = "editDP-"+sejour_id;
  this.sView = "DP";
  this.sChir = "_praticien_id";
  this.selfClose = true;
  this.pop();
}

CIM10Selector.initDAS = function(sejour_id){
  this.sForm = "editDossierMedical-"+sejour_id;
  this.sView = "_added_code_cim";
  this.sChir = "_praticien_id";
  this.selfClose = true;
  this.pop();
}

function choosePreselection(oSelect) {
  if (!oSelect.value) { 
    return;
  }
  
  var aParts = oSelect.value.split("|");
  var sLibelle = aParts.pop();
  var sCode = aParts.pop();

  var oForm = oSelect.form;
  oForm.code_uf.value = sCode;
  oForm.libelle_uf.value = sLibelle;
  
  oSelect.value = "";
}

function imprimerDocument(doc_id) {
  var url = new Url();
  url.setModuleAction("dPcompteRendu", "print_cr");
  url.addParam("compte_rendu_id", doc_id);
  url.popup(700, 600, "Compte-rendu");
}

function submitSHSLink() {
  var oPatForm = document.editPatFrm;
  //debugObject(oPatForm);
  var oOpForm = document.editOpFrm;
  //submitFormAjax(oPatForm, 'systemMsg');
  submitFormAjax(oOpForm, 'systemMsg');
}

function exporterHPRIM(object_id, typeObject, oOptions) {
  oDefaultOptions = {
  	onlySentFiles : false
  };
  
  Object.extend(oDefaultOptions, oOptions);
  
  var url = new Url();
  url.setModuleAction("dPinterop", "export_hprim");
  url.addParam("object_id", object_id);
  url.addParam("typeObject", typeObject);
  url.addParam("sent_files", oDefaultOptions.onlySentFiles ? 1 : 0);
  
  oRequestOptions = {
    waitingText: oDefaultOptions.onlySentFiles ? 
  	  "Chargement des fichers envoy�s" : 
  	  "Export H'XML vers Sa@nt�.com"
  }
  
  url.requestUpdate("hprim_export_" + typeObject + object_id, oRequestOptions); 
}

function submitPatForm() {
  var oForm = document.forms.editPatFrm;
  var iTarget = "updatePat";
  submitFormAjax(oForm, iTarget);
}

function submitSejourForm(sejour_id) {
  var oForm = document.forms["editSejourFrm" + sejour_id];
  var iTarget = "updateSejour" + sejour_id;
  submitFormAjax(oForm, iTarget);
}

function submitOpForm(operation_id) {
  var oForm = document.forms["editOpFrm" + operation_id];
  var iTarget = "updateOp" + operation_id;
  submitFormAjax(oForm, iTarget);
}

function submitAllForms(operation_id, sejour_id) {
  submitPatForm();
  submitSejourForm(sejour_id);
  submitOpForm(operation_id);
}

function ZoomAjax(objectClass, objectId, elementClass, elementId, sfn){
  popFile(objectClass, objectId, elementClass, elementId, sfn);
}

function printFeuilleBloc(oper_id) {
  var url = new Url;
  url.setModuleAction("dPsalleOp", "print_feuille_bloc");
  url.addParam("operation_id", oper_id);
  url.popup(700, 600, 'FeuilleBloc');
}

function reloadDiagnostic(sejour_id, modeDAS) {
  var urlDiag = new Url();
  urlDiag.setModuleAction("dPpmsi", "httpreq_diagnostic");
  urlDiag.addParam("sejour_id", sejour_id);
  urlDiag.addParam("modeDAS", modeDAS);
  urlDiag.requestUpdate("cim-"+sejour_id, { 
		waitingText : null,
  	onComplete: CIM10Selector.close 
  } );
  var urlListDiag = new Url();
  urlListDiag.setModuleAction("dPpmsi", "httpreq_list_diags");
  urlListDiag.addParam("sejour_id", sejour_id);
  urlListDiag.requestUpdate("cim-list-"+sejour_id, { 
		waitingText : null
  } );
  var urlGHM = new Url();
  urlGHM.setModuleAction("dPpmsi", "httpreq_vw_GHM");
  urlGHM.addParam("sejour_id", sejour_id);
  urlGHM.requestUpdate("GHM-"+sejour_id, { 
		waitingText : null
  } );
}

function pageMain() {
  PairEffect.initGroup("effectSejour");
}
</script>

<form name="FrmClass" action="?m={{$m}}" method="get" onsubmit="reloadListFileDossier('load'); return false;">
<input type="hidden" name="selKey"   value="" />
<input type="hidden" name="selClass" value="" />
<input type="hidden" name="selView"  value="" />
<input type="hidden" name="keywords" value="" />
<input type="hidden" name="file_id"  value="" />
<input type="hidden" name="typeVue"  value="0" />
</form>

<table class="main">
  <tr>
    <td>
      <form name="patFrm" action="?" method="get">
      <table class="form">
        <tr>
          <th><label for="patNom" title="Merci de choisir un patient pour voir son dossier">Choix du patient</label></th>
          <td class="readonly">
            <input type="hidden" name="m" value="dPpmsi" />
            <input type="hidden" name="pat_id" value="{{$patient->patient_id}}" onchange="this.form.submit()" />
            <input type="text" readonly="readonly" name="patNom" value="{{$patient->_view}}" />
          </td>
          <td class="button">
            <button class="search" type="button" onclick="PatSelector.init()">Chercher</button>
            <script type="text/javascript">
            PatSelector.init = function(){
              this.sForm = "patFrm";
              this.sId   = "pat_id";
              this.sView = "patNom";
              this.pop();
            }
            </script>
          </td>
        </tr>
      </table>
      </form>
      {{if $patient->patient_id}}
      <div id="vwPatient">
      {{include file="../../dPpatients/templates/inc_vw_patient.tpl"}}
      </div>
      {{/if}}
    </td>
    {{if $patient->patient_id}}
    <td>
      <table class="form">
        <tr>
          <td colspan="4">
            <form name="editPatFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

            <input type="hidden" name="dosql" value="do_patients_aed" />
            <input type="hidden" name="m" value="dPpatients" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="patient_id" value="{{$patient->patient_id}}" />
 
            <table class="form">
              <tr>
                <th class="category" colspan="3">
                  <em>Lien S@nt�.com</em> : Patient 
                  <button class="submit" type="button" onclick="submitPatForm()">Valider</button>
                </th>
              </tr>

              <tr>
                <th>
                  <label for="SHS" title="Choisir un identifiant de patient correspondant � l'op�ration">Identifiant de patient</label>
                </th>
                <td>
                  <input type="text" class="notNull {{$patient->_props.SHS}}" name="SHS" value="{{$patient->SHS}}" size="8" maxlength="8" />
                </td>
                <td id="updatePat" />
              </tr>
            </table>
 
            </form>
          </td>
        </tr>
        <tr>
          <th colspan="4" class="title">Liste des s�jours</th>
        </tr>
        {{foreach from=$patient->_ref_sejours item=curr_sejour}}
        <tr id="sejour{{$curr_sejour->sejour_id}}-trigger">
          <td colspan="4" style="background-color:#aaf;">
          	Dr. {{$curr_sejour->_ref_praticien->_view}} -
          	S�jour du {{$curr_sejour->entree_prevue|date_format:"%d %b %Y (%Hh%M)"}}
          	au {{$curr_sejour->sortie_prevue|date_format:"%d %b %Y (%Hh%M)"}}
          </td>
        </tr>
        <tbody class="effectSejour" id="sejour{{$curr_sejour->sejour_id}}">
        <tr>
          <td colspan="4">
            <form name="editSejourFrm{{$curr_sejour->sejour_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
            
            <input type="hidden" name="dosql" value="do_sejour_aed" />
            <input type="hidden" name="m" value="dPplanningOp" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="sejour_id" value="{{$curr_sejour->sejour_id}}" />
            <input type="hidden" name="entree_prevue" value="{{$curr_sejour->entree_prevue}}" />
            <input type="hidden" name="sortie_prevue" value="{{$curr_sejour->sortie_prevue}}" />
 
            <table class="form">
              <tr>
                <th class="category" colspan="3">
                  <em>Lien S@nt�.com</em> : S�jour
                  <button class="submit" type="button" onclick="submitSejourForm({{$curr_sejour->sejour_id}})">Valider</button>
                </th>
              </tr>
              <tr>
                <th>
                  <label for="venue_SHS" title="Choisir un identifiant pour la venue correspondant � l'op�ration">Identifiant de venue</label>
                </th>
                <td>
                  <input type="text" class="notNull {{$curr_sejour->_props.venue_SHS}}" name="venue_SHS" value="{{$curr_sejour->venue_SHS}}" size="8" maxlength="8" />
                </td>
                <td rowspan="2" id="updateSejour{{$curr_sejour->sejour_id}}" />
              </tr>
              <tr>
                <th>
                  Suggestion
                </th>
                <td>
                  {{$curr_sejour->_venue_SHS_guess}}
                </td>
              </tr>
            </table>

            </form>
          </td>
        </tr>
        <tr>
          <th class="category" colspan="4">
            <a style="float: right" title="Modifier le s�jour" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$curr_sejour->_id}}">
              <img src="images/icons/planning.png" alt="Planifier" />
            </a>
            <a style="float: right" title="Modifier les diagnostics" href="?m=dPpmsi&amp;tab=labo_groupage&amp;sejour_id={{$curr_sejour->_id}}">
              <img src="images/icons/edit.png" alt="Planifier" />
            </a>
            {{$curr_sejour->_view}}
          </th>
        </tr>
        <tr>
          <td class="text halfPane" colspan="2">
            <div id="cim-{{$curr_sejour->_id}}">
            {{assign var="sejour" value=$curr_sejour}}
            {{include file="inc_diagnostic.tpl"}}
            </div>
          </td>
          <td class="text halfPane" colspan="2">
            <div id="GHM-{{$curr_sejour->_id}}">
            {{include file="inc_vw_GHM.tpl"}}
            </div>
          </td>
        </tr>
        <tr>
          <td colspan="4" id="hprim_export_sej{{$sejour->_id}}">
          </td>
        </tr>
        <tr>
          <th class="category" colspan="2">Diagnostics CIM</th>
          <th class="category" colspan="2">Addicitions</th>
        </tr>
        <tr>
          <td class="text" colspan="2">
            <div id="cim-list-{{$curr_sejour->_id}}">
              {{include file="inc_list_diags.tpl"}}
		        </div>
          </td>
          <td class="text" colspan="2">
            <ul>
              <li>Du patient
                <ul>
	                {{foreach from=$patient->_ref_dossier_medical->_ref_types_addiction key=curr_type item=list_addiction}}
	                {{if $list_addiction|@count}}
					        <li>
					          {{tr}}CAddiction.type.{{$curr_type}}{{/tr}}
					          {{foreach from=$list_addiction item=curr_addiction}}
					          <ul>
					            <li>{{$curr_addiction->addiction}}</li>
					          </ul>
					          {{/foreach}}
					        </li>
					        {{/if}}
			            {{foreachelse}}
			            <li>Pas d'addiction</li>
		              {{/foreach}}
		            </ul>
		          </li>
              <li>Significatifs du s�jour
                <ul>
	                {{foreach from=$curr_sejour->_ref_dossier_medical->_ref_types_addiction key=curr_type item=list_addiction}}
	                {{if $list_addiction|@count}}
					        <li>
					          {{tr}}CAntecedent.type.{{$curr_type}}{{/tr}}
					          {{foreach from=$list_addiction item=curr_addiction}}
					          <ul>
					            <li>{{$curr_addiction->addiction}}</li>
					          </ul>
					          {{/foreach}}
					        </li>
					        {{/if}}
			            {{foreachelse}}
			            <li>Pas d'addiction</li>
		              {{/foreach}}
		            </ul>
		          </li>
            </ul>
          </td>
        </tr>
        <tr>
          <th class="category" colspan="2">Ant�cedents</th>
          <th class="category" colspan="2">Traitements</th>
        </tr>
        <tr>
          <td class="text" colspan="2">
            <ul>
              <li>Du patient
                <ul>
			            {{foreach from=$patient->_ref_dossier_medical->_ref_antecedents key=curr_type item=list_antecedent}}
			            {{if $list_antecedent|@count}}
					        <li>
					          {{tr}}CAntecedent.type.{{$curr_type}}{{/tr}}
					          {{foreach from=$list_antecedent item=curr_antecedent}}
					          <ul>
					            <li>
					              {{if $curr_antecedent->date}}
			                    {{$curr_antecedent->date|date_format:"%d %b %Y"}} -
			                  {{/if}}
			                  <em>{{$curr_antecedent->rques}}</em>
			                </li>
					          </ul>
					          {{/foreach}}
					        </li>
					        {{/if}}
			            {{foreachelse}}
			            <li>Pas d'ant�c�dents</li>
			            {{/foreach}}
			          </ul>
			        </li>
              <li>Significatifs du s�jour
                <ul>
			            {{foreach from=$curr_sejour->_ref_dossier_medical->_ref_antecedents key=curr_type item=list_antecedent}}
			            {{if $list_antecedent|@count}}
					        <li>
					          {{tr}}CAntecedent.type.{{$curr_type}}{{/tr}}
					          {{foreach from=$list_antecedent item=curr_antecedent}}
					          <ul>
					            <li>
					              {{if $curr_antecedent->date}}
			                    {{$curr_antecedent->date|date_format:"%d %b %Y"}} -
			                  {{/if}}
			                  <em>{{$curr_antecedent->rques}}</em>
			                </li>
					          </ul>
					          {{/foreach}}
					        </li>
					        {{/if}}
			            {{foreachelse}}
			            <li>Pas d'ant�c�dents</li>
			            {{/foreach}}
			          </ul>
			        </li>
            </ul>
          </td>
          <td class="text" colspan="2">
            <ul>
              <li>Du patient
                <ul>
		              {{foreach from=$patient->_ref_dossier_medical->_ref_traitements item=curr_trmt}}
		              <li>
		                {{if $curr_trmt->fin}}
		                  Du {{$curr_trmt->debut|date_format:"%d %b %Y"}} au {{$curr_trmt->fin|date_format:"%d %b %Y"}} :
		                {{elseif $curr_trmt->debut}}
		                  Depuis le {{$curr_trmt->debut|date_format:"%d %b %Y"}} :
		                {{/if}}
		                <em>{{$curr_trmt->traitement}}</em>
		              </li>
		              {{foreachelse}}
		              <li>Pas de traitements</li>
		              {{/foreach}}
		            </ul>
		          </li>
              <li>Significatifs du s�jour
                <ul>
		              {{foreach from=$curr_sejour->_ref_dossier_medical->_ref_traitements item=curr_trmt}}
		              <li>
		                {{if $curr_trmt->fin}}
		                  Du {{$curr_trmt->debut|date_format:"%d %b %Y"}} au {{$curr_trmt->fin|date_format:"%d %b %Y"}} :
		                {{elseif $curr_trmt->debut}}
		                  Depuis le {{$curr_trmt->debut|date_format:"%d %b %Y"}} :
		                {{/if}}
		                <em>{{$curr_trmt->traitement}}</em>
		              </li>
		              {{foreachelse}}
		              <li>Pas de traitements</li>
		              {{/foreach}}
		            </ul>
		          </li>
            </ul>
          </td>
        </tr>
        {{foreach from=$curr_sejour->_ref_operations item=curr_op}}
        <tr>
          <th class="category" colspan="4">
            Intervention par le Dr. {{$curr_op->_ref_chir->_view}}
            &mdash; {{$curr_op->_datetime|date_format:"%A %d %B %Y"}}
            &mdash; {{$curr_op->_ref_salle->nom}}
          </th>
        </tr>
        {{if $curr_op->libelle}}
        <tr>
          <th>Libell�</th>
          <td colspan="3" class="text"><em>{{$curr_op->libelle}}</em></td>
        {{/if}}
        {{foreach from=$curr_op->_ext_codes_ccam item=curr_code}}
        <tr>
          <th>{{$curr_code->code}}</th>
          <td class="text" colspan="3">{{$curr_code->libelleLong}}</td>
        </tr>
        {{/foreach}}
        {{if $curr_op->_ref_consult_anesth->consultation_anesth_id}}
        <tr>
          <td class="button" colspan="4">Consultation de pr�-anesth�sie le {{$curr_op->_ref_consult_anesth->_ref_plageconsult->date|date_format:"%A %d %b %Y"}}
            avec le Dr. {{$curr_op->_ref_consult_anesth->_ref_plageconsult->_ref_chir->_view}}
          </td>
        </tr>
        <tr>
          <td class="button">Poids</td>
          <td class="button">Taille</td>
          <td class="button">Groupe</td>
          <td class="button">Tension</td>
        </tr>
        <tr>
          <td class="button">{{$curr_op->_ref_consult_anesth->poid}} kg</td>
          <td class="button">{{$curr_op->_ref_consult_anesth->taille}} cm</td>
          <td class="button">{{tr}}CConsultAnesth.groupe.{{$curr_op->_ref_consult_anesth->groupe}}{{/tr}} {{tr}}CConsultAnesth.rhesus.{{$curr_op->_ref_consult_anesth->rhesus}}{{/tr}}</td>
          <td class="button">{{$curr_op->_ref_consult_anesth->tasys}}/{{$curr_op->_ref_consult_anesth->tadias}}</td>
        </tr>
        {{/if}}
        <tr>
          <td class="button" colspan="4">
            <button class="print" onclick="printFeuilleBloc({{$curr_op->operation_id}})">
              Imprimer la feuille de bloc
            </button>
            <br />
            <a class="buttonedit" href="?m=dPpmsi&amp;tab=edit_actes&amp;operation_id={{$curr_op->operation_id}}">
              Modifier les actes
            </a>
          </td>
        </tr>
        <tr>
          <td colspan="4">
            <table class="tbl">
              <tr>
                <th class="category">supprimer</th>
                <th class="category">Code</th>
                <th class="category">Activit�</th>
                <th class="category">Phase &mdash; Modifs.</th>
                <th class="category">Association</th>
              </tr>
              {{foreach from=$curr_op->_ref_actes_ccam item=curr_acte}}
              <tr>
                <td class="button">
                  <form name="formActe-{{$curr_acte->_view}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
                  <input type="hidden" name="m" value="dPsalleOp" />
                  <input type="hidden" name="dosql" value="do_acteccam_aed" />
                  <input type="hidden" name="del" value="0" />
                  <input type="hidden" name="acte_id" value="{{$curr_acte->acte_id}}" />
                  <button class="trash notext" type="button" onclick="confirmDeletion(this.form, {typeName:'l\'acte',objName:'{{$curr_acte->code_acte|smarty:nodefaults|JSAttribute}}'})">
                    {{tr}}Ajouter{{/tr}}
                  </button>
                  </form>
                </td>
                <td class="text">{{$curr_acte->_ref_executant->_view}} : {{$curr_acte->code_acte}}</td>
                <td class="button">{{$curr_acte->code_activite}}</td>
                <td class="button">
                  {{$curr_acte->code_phase}}
                  {{if $curr_acte->modificateurs}}
                    &mdash; {{$curr_acte->modificateurs}}
                  {{/if}}
                </td>
                <td>
                  {{$curr_acte->_guess_association}}
                </td>
              </tr>
              {{/foreach}}
            </table>
          </td>
        </tr>
        <tr>
          <td colspan="4">
            <form name="editOpFrm{{$curr_op->operation_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
            <input type="hidden" name="dosql" value="do_planning_aed" />
            <input type="hidden" name="m" value="dPplanningOp" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="operation_id" value="{{$curr_op->operation_id}}" />
 
            <table class="form">
              <tr>
                <th class="category" colspan="2">
                  <em>Lien S@nt�.com</em> : Intervention
                  <button class="submit" type="button" onclick="submitOpForm({{$curr_op->operation_id}})">Valider</button>
                </th>
              </tr>

              <tr>
                <th><label for="_cmca_uf_preselection" title="Choisir une pr�-selection pour remplir les unit�s fonctionnelles">Pr�-s�lection</label></th>
                <td>
                  <select name="_cmca_uf_preselection" onchange="choosePreselection(this)">
                    <option value="">&mdash; Choisir une pr�-selection</option>
                    <option value="ABS|ABSENT">(ABS) Absent</option>
                    <option value="AEC|ARRONDI EURO">(AEC) Arrondi Euro</option>
                    <option value="AEH|ARRONDI EURO">(AEH) Arrondi Euro</option>
                    <option value="AMB|CHIRURGIE AMBULATOIRE">(AMB) Chirurgie Ambulatoire</option>
                    <option value="CHI|CHIRURGIE">(CHI) Chirurgie</option>
                    <option value="CHO|CHIRURGIE COUTEUSE">(CHO) Chirurgie Co�teuse</option>
                    <option value="EST|ESTHETIQUE">(EST) Esth�tique</option>
                    <option value="EXL|EXL POUR RECUP V4 V5">(EXL) EXL pour r�cup. v4 v5</option>
                    <option value="EXT|EXTERNES">(EXT) Externes</option>
                    <option value="MED|MEDECINE">(MED) M�decine</option>
                    <option value="PNE|PNEUMOLOGUE">(PNE) Pneumologie</option>
                    <option value="TRF|TRANSFERT >48H">(TRF) Transfert > 48h</option>
                    <option value="TRI|TRANSFERT >48H">(TRI) Transfert > 48h</option>
                  </select>
                </td>
              </tr>

              <tr>
                <th><label for="code_uf" title="Choisir un code pour l'unit� fonctionnelle">Code d'unit� fonct.</label></th>
                <td><input type="text" class="notNull {{$curr_op->_props.code_uf}}" name="code_uf" value="{{$curr_op->code_uf}}" size="10" maxlength="10" /></td>
              </tr>

              <tr>
                <th><label for="libelle_uf" title="Choisir un libell� pour l'unit� fonctionnelle">Libell� d'unit� fonct.</label></th>
                <td><input type="text" class="notNull {{$curr_op->_props.libelle_uf}}" name="libelle_uf" value="{{$curr_op->libelle_uf}}" size="20" maxlength="35" /></td>
              </tr>

              <tr>
                <td colspan="2" id="updateOp{{$curr_op->operation_id}}" />
              </tr>

            </table>

            </form>
            
            <table class="form">
              <tr>
                <td class="button">
                  <button class="submit" type="button" onclick="submitAllForms({{$curr_op->_id}}, {{$curr_sejour->_id}})" >Tout valider</button>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <tr>
          <td class="button" colspan="4">
            <button class="tick" onclick="exporterHPRIM({{$curr_op->_id}}, 'op')">Exporter vers S@nt�.com</button>
          </td>
        </tr>
        <tr>
          <td class="text" id="hprim_export_op{{$curr_op->_id}}" colspan="4">
          </td>
        </tr>
        
        <tr>
          <td colspan="4">Documents attach�s :</th>
        </tr>
        <tr>
          <td colspan="4" id="File{{$curr_op->_class_name}}{{$curr_op->_id}}">
            <a href="#" onclick="setObject( {
              objClass: '{{$curr_op->_class_name}}', 
              keywords: '', 
              id: {{$curr_op->operation_id|smarty:nodefaults|JSAttribute}}, 
              view:'{{$curr_op->_view|smarty:nodefaults|JSAttribute}}'} )">
              Voir les Documents ({{$curr_op->_nb_files_docs}})
            </a>
          </td>
        </tr>
        {{/foreach}}
        </tbody>
        {{/foreach}}

       </table>
     </td>
     {{/if}}
    
  </tr>
</table>

