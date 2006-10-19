<script language="Javascript" type="text/javascript">
{{if $canEdit}}
var oCookie = new CJL_CookieUtil("EIAccordion");
var showTabAcc = 0;

if(oCookie.getSubValue("showTab")){
  showTabAcc = oCookie.getSubValue("showTab");
}

function storeVoletAcc(objAcc){
  var aArray = oAccord.accordionTabs;
  for ( var i=0 ; i < aArray.length ; i++ ){
    if(objAcc == aArray[i]){
      oCookie.setSubValue("showTab", i.toString());
    }
  }
}

function writeHeader(iId, sValue){
  $(iId).innerHTML = sValue;
}
{{/if}}

{{if $canAdmin}}
function search_AllEI(){
  var oForm = document.EiALL_TERM;
  var url = new Url;
  url.setModuleAction("dPqualite", "httpreq_vw_allEi");
  url.addParam("allEi_user_id", oForm.allEi_user_id.value);
  url.requestUpdate('QualAllEIContent');
  // , { waitingText : null }
}

function annuleFiche(oForm,annulation){
  oForm.annulee.value = annulation;
  oForm._validation.value = 1;
  oForm.submit();
}

function refusMesures(oForm){
  if(oForm.remarques.value == ""){
    alert("Veuillez saisir vos remarques dans la zone 'Remarques'.");
    oForm.remarques.focus();
  }else{
    oForm.service_date_validation.value = "";
    oForm._validation.value= 1;
    oForm.submit();
  }
}

function saveVerifControle(oForm){
  oForm._validation.value= 1;
  oForm.submit();  
}
{{/if}}

function printIncident(ficheId){
  var url = new Url;
  url.setModuleAction("dPqualite", "print_fiche"); 
  url.addParam("fiche_ei_id", ficheId);
  url.popup(700, 500, "printFicheEi");
  return;
}

{{if  $canAdmin && $fiche->qualite_date_validation && (!$fiche->qualite_date_verification || !$fiche->qualite_date_controle)}}
function pageMain() {
  {{if !$fiche->qualite_date_verification}}
  regFieldCalendar("ProcEditFrm", "qualite_date_verification");
  {{else}}
  regFieldCalendar("ProcEditFrm", "qualite_date_controle");
  {{/if}}
}
{{/if}}
</script>
{{assign var="listeFichesTitle" value=""}}
{{assign var="voletAcc" value=""}}
<table class="main">
  <tr>
    <td class="halfPane">
      {{if $canAdmin || $canEdit}}
      <div class="accordionMain" id="accordionConsult">
        {{if !$canAdmin}}
        
        <div id="CSATraiter">
          <div id="CSATraiterHeader" class="accordionTabTitleBar">
		    Fiches � Traiter ({{$listFiches.ATT_CS|@count}})
		  </div>
		  <div id="CSATraiterContent"  class="accordionTabContentBox">
            {{assign var="listeFiches" value=$listFiches.ATT_CS}}
            {{include file="inc_ei_liste.tpl"}}
		  </div>
		</div>
		<div id="CSEnCours">
          <div id="CSEnCoursHeader" class="accordionTabTitleBar">
		    En attente de Validation du service Qualit� ({{$listFiches.ATT_QUALITE|@count}})
		  </div>
		  <div id="CSEnCoursContent"  class="accordionTabContentBox">
            {{assign var="listeFiches" value=$listFiches.ATT_QUALITE}}
            {{include file="inc_ei_liste.tpl"}}
		  </div>
		</div>
		
		<div id="CSAllEI">
          <div id="CSAllEIHeader" class="accordionTabTitleBar">
		    Fiches d'EI Trait�es ({{$listFiches.ALL_TERM|@count}})
		  </div>
		  <div id="CSAllEIContent"  class="accordionTabContentBox">
            {{assign var="listeFiches" value=$listFiches.ALL_TERM}}
            {{include file="inc_ei_liste.tpl"}}
		  </div>
		</div>
        {{else}}
        
        <div id="QualNewFiches">
          <div id="QualNewFichesHeader" class="accordionTabTitleBar">
		    Nouvelles fiches � Traiter ({{$listFiches.VALID_FICHE|@count}})
		  </div>
		  <div id="QualNewFichesContent"  class="accordionTabContentBox">
            {{assign var="listeFiches" value=$listFiches.VALID_FICHE}}
            {{include file="inc_ei_liste.tpl"}}
		  </div>
		</div>
		<div id="QualAttCS">
          <div id="QualAttCSHeader" class="accordionTabTitleBar">
		    En Attente du chef de Service ({{$listFiches.ATT_CS|@count}})
		  </div>
		  <div id="QualAttCSContent"  class="accordionTabContentBox">
            {{assign var="listeFiches" value=$listFiches.ATT_CS}}
            {{include file="inc_ei_liste.tpl"}}
		  </div>
		</div>
		<div id="QualValidMesures">
          <div id="QualValidMesuresHeader" class="accordionTabTitleBar">
		    Mesures � valider ({{$listFiches.ATT_QUALITE|@count}})
		  </div>
		  <div id="QualValidMesuresContent"  class="accordionTabContentBox">
            {{assign var="listeFiches" value=$listFiches.ATT_QUALITE}}
            {{include file="inc_ei_liste.tpl"}}
		  </div>
		</div>
		<div id="QualVerif">
          <div id="QualVerifHeader" class="accordionTabTitleBar">
		    Fiches en Attente de V�rification ({{$listFiches.ATT_VERIF|@count}})
		  </div>
		  <div id="QualVerifContent"  class="accordionTabContentBox">
            {{assign var="listeFiches" value=$listFiches.ATT_VERIF}}
            {{include file="inc_ei_liste.tpl"}}
		  </div>
		</div>
		<div id="QualCtrl">
          <div id="QualCtrlHeader" class="accordionTabTitleBar">
		    Fiches en Attente de Contr�le ({{$listFiches.ATT_CTRL|@count}})
		  </div>
		  <div id="QualCtrlContent"  class="accordionTabContentBox">
            {{assign var="listeFiches" value=$listFiches.ATT_CTRL}}
            {{include file="inc_ei_liste.tpl"}}
		  </div>
		</div>
		
		<div id="QualAllEI">
          <div id="QualAllEIHeader" class="accordionTabTitleBar">
		    Toutes les fiches d'EI Trait�es {{if $allEi_user_id}}pour {{$listUsersTermine.$allEi_user_id->_view}}{{/if}} ({{$listFiches.ALL_TERM|@count}})
		  </div>
		  <div id="QualAllEIContent"  class="accordionTabContentBox">
            {{assign var="listeFiches" value=$listFiches.ALL_TERM}}
            {{assign var="voletAcc" value="ALL_TERM"}}
            {{include file="inc_ei_liste.tpl"}}
            {{assign var="voletAcc" value=""}}
		  </div>
		</div>
		
		<div id="QualAnnuleEI">
          <div id="QualAnnuleEIHeader" class="accordionTabTitleBar">
		    Fiches d'Ei annul�es ({{$listFiches.ANNULE|@count}})
		  </div>
		  <div id="QualAnnuleEIContent"  class="accordionTabContentBox">
            {{assign var="listeFiches" value=$listFiches.ANNULE}}
            {{include file="inc_ei_liste.tpl"}}
		  </div>
		</div>
		
        {{/if}}

      </div>
      <script language="Javascript" type="text/javascript">
      var oAccord = new Rico.Accordion( $('accordionConsult'), { 
        panelHeight: 300,
        showDelay:50,
        onShowTab: storeVoletAcc,
        showSteps:3,
        onLoadShowTab: showTabAcc
      } );
      </script>
      {{/if}}
      <br />
      {{assign var="listeFichesTitle" value="Mes fiches d'EI"}}
      {{assign var="listeFiches" value=$listFiches.AUTHOR}}
      {{include file="inc_ei_liste.tpl"}}
    </td>
    <td class="halfPane">
      {{if $fiche->fiche_ei_id}}
      
      <form name="ProcEditFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_ficheEi_aed" />
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="annulee" value="{{$fiche->annulee}}" />
      <input type="hidden" name="fiche_ei_id" value="{{$fiche->fiche_ei_id}}" />
      <input type="hidden" name="_validation" value="0" />
      <input type="hidden" name="service_date_validation" value="{{$fiche->service_date_validation}}" />
            
      <table class="form">
        {{include file="inc_incident_infos.tpl"}}
        
      {{if $canAdmin && !$fiche->date_validation &&!$fiche->annulee}}
        <tr>
          <th><label for="degre_urgence" title="Veuillez s�lectionner le degr� d'urgence">Degr� d'Urgence</label></th>
          <td>
            <select name="degre_urgence" title="{{$fiche->_props.degre_urgence}}|notNull">
            <option value="">&mdash; Veuillez Choisir</option>
            {{html_options options=$fiche->_enumsTrans.degre_urgence}}
            </select>
          </td>
        </tr>
        <tr>
          <th><label for="service_valid_user_id" title="Veuillez s�lectionner le chef de service � qui transmettre la fiche">Chef de Service � qui transmettre la fiche</label></th>
          <td>
            <select name="service_valid_user_id" title="{{$fiche->_props.service_valid_user_id}}|notNull">
            <option value="">&mdash; Veuillez Choisir &mdash;</option>
            {{foreach from=$listUsersEdit item=currUser}}
            <option value="{{$currUser->user_id}}">{{$currUser->_view}}</option>
            {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <td colspan="2" class="button">
            <input type="hidden" name="valid_user_id" value="{{$user_id}}" />
            <button class="edit" type="button" onclick="window.location.href='index.php?m={{$m}}&amp;tab=vw_incident&amp;fiche_ei_id={{$fiche->fiche_ei_id}}';">
              Editer la Fiche
            </button>
            <button class="modify" type="submit">
              Transmettre
            </button>
            <button class="cancel" type="button" onclick="annuleFiche(this.form,1);" title="Annuler la Fiche d'EI">
              Annuler
            </button>
          </td>
        </tr>
      {{/if}}
      
      {{if $fiche->service_valid_user_id && $fiche->service_valid_user_id==$user && !$fiche->service_date_validation}}
        <tr>
          <th colspan="2" class="category">
            Validation du Chef de Service
          </th>
        </tr>
        {{if $fiche->remarques}}
        <tr>
          <th><strong>Mesures refus� par</strong></th>
          <td class="text">
            {{$fiche->_ref_qualite_valid->_view}}
          </td>
        </tr>
        <tr>
          <th><strong>Remarques</strong></th>
          <td class="text" style="color:#f00;">
            <strong>{{$fiche->remarques|nl2br}}</strong>
          </td>
        </tr>
        {{/if}}
        <tr>
          <th>
            <label for="service_actions" title="Veuillez d�crire les actions mises en place">Actions mises en Place</label>
          </th>
          <td>
            <textarea name="service_actions" title="{{$fiche->_props.service_actions}}|notNull">{{$fiche->service_actions}}</textarea>
          </td>
        </tr>
        <tr>
          <th>
            <label for="service_descr_consequences" title="Veuillez d�crire les cons�quences">Description des cons�quences</label>
          </th>
          <td>
            <textarea name="service_descr_consequences" title="{{$fiche->_props.service_descr_consequences}}|notNull">{{$fiche->service_descr_consequences}}</textarea>
          </td>
        </tr>
        <tr>
          <td colspan="2" class="button">
            <input type="hidden" name="remarques" value="" />
            <button class="modify" type="submit">
              Transmettre
            </button>
          </td>
        </tr>
      {{/if}}
      {{if $canAdmin && $fiche->service_date_validation}}
        {{if !$fiche->qualite_date_validation}}
        <tr>
          <td colspan="2" class="button">
            <input type="hidden" name="qualite_user_id" value="{{$user_id}}" />
            <button class="modify" type="submit">
              Valider ces mesures
            </button>
            <button class="cancel" type="button" onclick="refusMesures(this.form);">
              Refuser ces mesures
            </button>
          </td>
        </tr>
        <tr>
          <th>
            <label for="remarques" title="Veuillez saisir vos remarques en cas de refus de ces mesures">
              Remarques en cas de refus
            </label>
          </th>
          <td>
            <textarea name="remarques" title="{{$fiche->_props.remarques}}"></textarea>
          </td>
        </tr>
        {{else}}
        {{if !$fiche->qualite_date_verification}}
        <tr>
          <th><label for="qualite_date_verification" title="Veuillez saisir la date de v�rification">Date de V�rification</label></th>
          <td class="date">
            <div id="ProcEditFrm_qualite_date_verification_da">{{$today|date_format:"%d/%m/%Y"}}</div>
            <input type="hidden" name="qualite_date_verification" value="{{$today|date_format:"%Y-%m-%d"}}" />
            <img id="ProcEditFrm_qualite_date_verification_trigger" src="./images/calendar.gif" alt="calendar" title="Choisir une date de v�rification" />
          </td>
        </tr>
        {{elseif !$fiche->qualite_date_controle}}
        <tr>
          <th><label for="qualite_date_controle" title="Veuillez saisir la date de contr�le">Date de Contr�le</label></th>
          <td class="date">
            <div id="ProcEditFrm_qualite_date_controle_da">{{$today|date_format:"%d/%m/%Y"}}</div>
            <input type="hidden" name="qualite_date_controle" value="{{$today|date_format:"%Y-%m-%d"}}" />
            <img id="ProcEditFrm_qualite_date_controle_trigger" src="./images/calendar.gif" alt="calendar" title="Choisir une date de contr�le" />
          </td>
        </tr>
        {{/if}}
        {{if !$fiche->qualite_date_verification || !$fiche->qualite_date_controle}}
        <tr>
          <td colspan="2" class="button">
            <button class="modify" type="button" onclick="saveVerifControle(this.form);">
              Enregister la date
            </button>
          </td>
        </tr>
        {{/if}}
        {{/if}}
      {{/if}}
      
      {{if $canAdmin && ($fiche->annulee || $fiche->date_validation)}}
        <tr>
          <td colspan="2" class="button">
            {{if $fiche->annulee}}
            <button class="change" type="button" onclick="annuleFiche(this.form,0);" title="R�tablir la Fiche d'EI">
              R�tablir
            </button>
            {{else}}
            <button class="print" type="button" onclick="printIncident({{$fiche->fiche_ei_id}});">
              Imprimer la fiche
            </button>
            {{/if}}
          </td>
        </tr>
      {{/if}}

      </table>
      </form>
      {{/if}}
    </td>
  </tr>
</table>