<script>
function checkRapport(){
  var oForm = document.printFrm;
  // Mode comptabilite
  var compta = 0;
  
  if(!oForm.chir.value && oForm.a.value == "print_actes") {
    alert('Vous devez choisir un praticien');
    return false;
  }
  if(!(checkForm(oForm))){
    return false;
  }
  var url = new Url();
  url.setModuleAction("facturation", oForm.a.value);
  url.addParam("compta", compta);
  url.addElement(oForm.a);
  url.addElement(oForm._date_min);
  url.addElement(oForm._date_max);
  url.addElement(oForm.chir);
  url.addElement(oForm._etat_reglement_patient);
  url.addElement(oForm._etat_reglement_tiers);
  url.addElement(oForm.mode);
  url.addElement(oForm._type_affichage);
  url.addParam("cs", $V(oForm.cs));
  if(compta == 1){
    url.popup(950, 600, "Rapport Comptabilit�");
  } else {
    url.popup(950, 600, "Rapport");
  }
  return false;
}

function changeDate(sDebut, sFin){ 
  var oForm = document.printFrm;
  oForm._date_min.value = sDebut;
  oForm._date_max.value = sFin;
  oForm._date_min_da.value = Date.fromDATE(sDebut).toLocaleDate();
  oForm._date_max_da.value = Date.fromDATE(sFin).toLocaleDate();  
}

function viewActes(){
  var oForm = document.printFrm;
  if(!oForm.chir.value) {
    alert('Vous devez choisir un praticien');
    return false;
  }

  var url = new Url();
  url.setModuleAction("dPplanningOp", "vw_actes_realises");
  url.addElement(oForm._date_min);
  url.addElement(oForm._date_max);
  url.addElement(oForm.chir);
  url.addElement(oForm.typeVue);
  url.popup(950, 550, "Rapport des actes r�alis�s");
  
  return false;
}

printFacture = function(edit_justificatif, edit_bvr, facture_class) {
  var oForm = document.printFrm;
  if(!oForm.chir.value) {
    alert('Vous devez choisir un praticien');
    return false;
  }
  var url = new Url('facturation', 'ajax_edit_bvr');
  url.addParam('facture_class'   , facture_class);
  url.addParam('edition_justificatif', edit_justificatif);
  url.addParam('edition_bvr'     , edit_bvr);
  url.addParam('_date_min'       , oForm._date_min.value);
  url.addParam('_date_max'       , oForm._date_max.value);
  url.addParam('prat_id'         , oForm.chir.value);
  url.addParam('suppressHeaders' , '1');
  url.popup(1000, 600);
}
checkBills = function(){
  var oForm = getForm("printFrm");
  var url = new Url('tarmed', 'ajax_send_file_http');
  url.addParam('prat_id'  , oForm.chir.value);
  url.addParam('date_min' , $V(oForm._date_min));
  url.addParam('date_max' , $V(oForm._date_max));
  url.addParam('check'    ,true);
//  url.addParam('user'     , $V(oForm.user));
//  url.addParam('pwd'      , $V(oForm.pwd));
  url.requestUpdate("response_send_bill", sendBill);
  
}
sendBill = function() {
  if (!$('not_create')) {
    var oForm = getForm("printFrm");
    var url = new Url('tarmed', 'ajax_send_file_http');
    url.addParam('prat_id', oForm.chir.value);
    url.addParam('date_min', $V(oForm._date_min));
    url.addParam('date_max', $V(oForm._date_max));
//    url.addParam('user', $V(oForm.user));
//    url.addParam('pwd', $V(oForm.pwd));
    url.addParam('suppressHeaders', '1');
    url.popup(1000, 600);
  }
}

viewTotaux = function() {
  var oForm = getForm("printFrm");
  var url = new Url("facturation", "ajax_total_cotation");
  url.addParam("chir_id", $V(oForm.chir));
  url.addParam('date_min', $V(oForm._date_min));
  url.addParam('date_max', $V(oForm._date_max));
  url.popup(1000, 600);
}
</script>

{{if count($listPrat)}}
<table class="main">
  <tr>
    <td>
      <form name="printFrm" action="?" method="get" onSubmit="return checkRapport()">
      <input type="hidden" name="a" value="" />
      <input type="hidden" name="dialog" value="1" />
      <table class="form">
        <tr>
          <th class="title" colspan="4">Edition de rapports</th>
        </tr>
        <tr>
          <th class="category" colspan="3"">Choix de la periode</th>
          <th class="category">{{mb_label object=$filter field="_prat_id"}}</th>
        </tr>
        <tr>
          <th>{{mb_label object=$filter field="_date_min"}}</th>
          <td>{{mb_field object=$filter field="_date_min" form="printFrm" canNull="false" register=true}}</td>
          <td rowspan="2" style="max-width:200px;">
            <table>
              <tr>
                <td>
                  <input type="radio" name="select_days" onclick="changeDate('{{$now}}','{{$now}}');"  value="day" checked="checked" /> 
                  <label for="select_days_day">Jour courant</label>
                  <br />
                  <input type="radio" name="select_days" onclick="changeDate('{{$yesterday}}','{{$yesterday}}');"  value="yesterday" /> 
                  <label for="select_days_yesterday">La veille</label>
                  <br />
                  <input type="radio" name="select_days" onclick="changeDate('{{$week_deb}}','{{$week_fin}}');" value="week" /> 
                  <label for="select_days_week">Semaine courante</label>
                  <br />
                </td>
                <td>
                  <input type="radio" name="select_days" onclick="changeDate('{{$month_deb}}','{{$month_fin}}');" value="month" /> 
                  <label for="select_days_month">Mois courant</label>
                  <br />
                  <input type="radio" name="select_days" onclick="changeDate('{{$three_month_deb}}','{{$month_fin}}');" value="three_month" /> 
                  <label for="select_days_three_month">3 derniers mois</label>
                </td>
              </tr>
            </table>
          </td>
          <td rowspan="2">
            <select name="chir">
              {{if $listPrat|@count > 1}}
              <option value="">&mdash; Tous</option>
              {{/if}}
              {{mb_include module=mediusers template=inc_options_mediuser list=$listPrat}}
            </select>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$filter field="_date_max"}}</th>
          <td>{{mb_field object=$filter field="_date_max" form="printFrm" canNull="false" register=true}} </td>
        </tr> 
        
        <tr>
          <th class="category" colspan="4">Comptabilit� Cabinet</th>
        </tr>
        <tr>
          <td colspan="2" class="button" style="width:50%;">
            <button class="print" type="submit" onclick="document.printFrm.a.value='print_noncote';">Consultation non cot�s</button>
            <div class="small-info" style="text-align:center;">
              Affichage des consultations non cot�s, en fonction de la date de consultation.
            </div>
          </td>
          <td colspan="2" class="button">
            <button class="print" type="submit" onclick="document.printFrm.a.value='print_retrocession';">R�trocession des remplacements</button>
            <div class="small-info" style="text-align:center;">
              Affichage des consultations effectu�es par un rempla�ant, en fonction de la date de consultation.
            </div>
          </td>
        </tr>
        <tr>
          <td class="button" colspan="4" style="padding-top:5px;padding-bottom:5px;">
            <hr />
          </td>
        </tr>
        <tr>
          <th>Consultations gratuites</th>
          <td>
            Oui<input type="radio" name="cs" value="1" checked="checked"/>
            Non<input type="radio" name="cs" value="0" />
          </td>
          <td colspan="2"></td>
        </tr>
        <tr>
          <th>{{mb_label object=$filter field="_etat_reglement_patient"}}</th>
          <td>{{mb_field object=$filter field="_etat_reglement_patient" emptyLabel="All" canNull="true"}}</td>
          <th>{{mb_label object=$filter_reglement field="mode"}}</th>
          <td>{{mb_field object=$filter_reglement field="mode" emptyLabel="All" canNull="true"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$filter field="_etat_reglement_tiers"}}</th>
          <td>{{mb_field object=$filter field="_etat_reglement_tiers" emptyLabel="All" canNull="true"}}</td>
          <th>{{mb_label object=$filter field="_type_affichage"}}</th>
          <td>{{mb_field object=$filter field="_type_affichage" canNull="true"}}</td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button class="search" type="submit" onclick="document.printFrm.a.value='print_rapport';">Validation paiements</button>
            {{if $app->user_prefs.GestionFSE}}
              <button class="search" type="submit" onclick="document.printFrm.a.value='print_noemie';">Rapprochements Noemie</button>
            {{/if}}
          </td>
          <td>
            <button class="print" type="submit" onclick="document.printFrm.a.value='print_compta';" style="float:right;">Impression compta</button>
          </td>
          <td>
            <button class="print" type="submit" onclick="document.printFrm.a.value='print_bordereau';" style="float:left;">Impression bordereau</button>
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <div class="small-info" style="text-align:center;">
              Affichage de l'�tat des paiements, en fonction des dates de consultation.<br/>
              Permet de rentrer de nouveaux paiements.
            </div>
          </td>
          <td colspan="2">
            <div class="small-info" style="text-align:center;">
              Affichage des r�glements effectu�s, en fonction de la date de paiement.
            </div>
          </td>
        </tr>
        {{if @$modules.tarmed->_can->read && $conf.tarmed.CCodeTarmed.use_cotation_tarmed}}
          <tr>
            <td class="button" colspan="4">
              <hr />
            </td>
          </tr>
          <tr>
            <td class="button" colspan="2">
              <button type="button" class="pdf" onclick="printFacture(0, 1, 'CFactureCabinet');">Impression des BVR</button>
              <button type="button" class="pdf" onclick="printFacture(1, 0, 'CFactureCabinet');">Justificatifs de remboursement</button>
            </td>
            <td colspan="2" rowspan="2" class="text">
              <div class="small-info" id="response_send_bill">
                Envoi des factures � la Caisse des M�decins et stockage au format pdf.
              </div>
            </td>
          </tr>
          <tr>
            <td class="button" colspan="2">
              {{*
              Identifiant:  <input name="user" type="text" value="" /><br/>
              Mot de passe: <input name="pwd"  type="password" value="" /><br/>
              <button type="button" class="send" onclick="checkBills();">Envoi � la Caisse des Medecins</button>
              *}}
              <button type="button" class="send" onclick="checkBills();">G�n�rer dossier pour la Caisse des medecins</button>
            </td>
          </tr>
        {{/if}}
        <tr>
          <th class="category" colspan="4">Comptabilit� Etablissement</th>
        </tr>
        <tr>
          <th>
            <label for="typeVue">Type d'affichage</label>
          </th>
          <td>
            <select name="typeVue">
            <option value="1">Liste compl�te</option>
            <option value="2">Totaux</option>
            </select>
          </td>
          <td colspan="2" rowspan="2" class="text">
            <div class="small-info">
              Affichage de l'�tat des r�glements de la liste des actes r�alis�s par l'�tablissement.
            </div>
          </td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            {{if $conf.ref_pays == 1}}
              <button type="button" class="search" onclick="viewActes();">Validation paiements</button>
            {{else}}
              <button class="print" type="submit" onclick="document.printFrm.a.value='print_actes';">Validation paiements</button>
            {{/if}}
          </td>
        </tr>
        {{if @$modules.tarmed->_can->read && $conf.tarmed.CCodeTarmed.use_cotation_tarmed}}
          <tr>
            <td class="button" colspan="4">
              <hr />
            </td>
          </tr>
          <tr>
            <td class="button" colspan="2">
              <button type="button" class="pdf" onclick="printFacture(0, 1, 'CFactureEtablissement');">Impression des BVR</button>
              <button type="button" class="pdf" onclick="printFacture(1, 0, 'CFactureEtablissement');">Justificatifs de remboursement</button>
            </td>
            <td colspan="2" class="text">
              <div class="small-info" id="response_send_bill">
                Envoi des factures � la Caisse des M�decins et stockage au format pdf.
              </div>
            </td>
          </tr>
        {{/if}}
        <tr>
          <th class="category" colspan="4">
            Totaux
          </th>
        </tr>
        <tr>
          <td colspan="2" class="button">
            <button type="button" class="search" onclick="viewTotaux();">Totaux des cotations</button>
          </td>
          <td colspan="2"></td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
</table>
{{else}}
  <div class="big-info">
    Vous n'avez acc�s � la comptabilit� d'aucun praticien.<br/>
    Veuillez contacter un administrateur
  </div>
{{/if}}