<script type="text/javascript">

var cim10url = new Url;

function selectCim10(code) {
  cim10url.setModuleAction("dPcim10", "code_finder");
  cim10url.addParam("code", code);
  cim10url.popup(800, 500, "CIM10");
}

function putCim10(code) {
  var oForm = document.editDiagFrm;
  aCim10 = oForm.listCim10.value.split("|");
  // Si la chaine est vide, il cr�e un tableau � un �l�ment vide donc :
  aCim10.removeByValue("");
  aCim10.push(code);
  aCim10.removeDuplicates();
  oForm.listCim10.value = aCim10.join("|");
  submitFormAjax(oForm, 'systemMsg', { onComplete : reloadAntecedents });
}

function delCim10(code) {
  var oForm = document.editDiagFrm;
  var aCim10 = oForm.listCim10.value.split("|");
  aCim10.removeByValue(code);
  oForm.listCim10.value = aCim10.join("|");
  submitFormAjax(oForm, 'systemMsg', { onComplete : reloadAntecedents });
}

function dateAntecedent(){
  var oForm = document.editAntFrm;
  var oEnCours = oForm._date_ant;
  var oHiddenField = oForm.date;
  oForm._helpers_rques.value = "";
  oForm.rques.value = "";
  var oViewField = document.getElementById('editAntFrm_date_da');
  var oTriggerField = document.getElementById('editAntFrm_date_trigger');
  if (oEnCours.checked) {
    oHiddenField.value = "{{$today}}";
    oViewField.innerHTML = "{{$today|date_format:"%d/%m/%Y"}}";
    oTriggerField.style.display = "inline";
  }else{
    oHiddenField.value = "";
    oViewField.innerHTML = "";
    oTriggerField.style.display = "none";
  }   
}

function finTrmt() {
  var oForm = document.editTrmtFrm;
  oForm.traitement.value = "";
  oForm._helpers_traitement.value = "";
  var oEnCours = oForm._en_cours;
  var oHiddenField = oForm.fin;
  var oViewField = document.getElementById('editTrmtFrm_fin_da');
  var oTriggerField = document.getElementById('editTrmtFrm_fin_trigger');
  if (oEnCours.checked) {

    oHiddenField.value = "{{$today}}";
    oViewField.innerHTML = "{{$today|date_format:"%d/%m/%Y"}}";
    oTriggerField.style.display = "inline";

  } else {
    oHiddenField.value = "";
    oViewField.innerHTML = "En cours";
    oTriggerField.style.display = "none";
  }
}

function closeCIM10() {
  cim10url.close();
}

function reloadAntecedents() {
  var antUrl = new Url;
  antUrl.setModuleAction("dPcabinet", "httpreq_vw_list_antecedents");
  antUrl.addParam("patient_id", document.editDiagFrm.patient_id.value);
  antUrl.requestUpdate('listAnt', { waitingText : null, onComplete : closeCIM10 });
}

function submitAnt(oForm) {
  submitFormAjax(oForm, 'systemMsg', { onComplete : reloadAntecedents });
}

function submitAntDelete(oForm) {
  submitFormAjax(oForm, 'systemMsg', { onComplete : reloadAntecedents });
}

function incAntecedantsMain() {
  PairEffect.initGroup("effectCategory");
}

</script>

<table class="form">
  <tr>
    <td class="text">
      {{if $_is_anesth}}
      <hr />
      <form name="editTabacFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
      <input type="hidden" name="consultation_anesth_id" value="{{$consult_anesth->consultation_anesth_id}}" />
      <table class="form">
      <tr>
        <td>
          <label for="tabac" title="Comportement tabagique">Tabac</label>
          <select name="_helpers_tabac" size="1" onchange="pasteHelperContent(this);this.form.tabac.onchange();">
            <option value="">&mdash; Choisir une aide</option>
            {{html_options options=$consult_anesth->_aides.tabac}}
          </select>
        </td>
        <td>
          <label for="oenolisme" title="Comportement alcoolique">Oenolisme</label>
          <select name="_helpers_oenolisme" size="1" onchange="pasteHelperContent(this);this.form.oenolisme.onchange();">
            <option value="">&mdash; Choisir une aide</option>
            {{html_options options=$consult_anesth->_aides.oenolisme}}
          </select>
        </td>
      </tr>
      <tr>  
        <td>
          <textarea name="tabac" onchange="submitForm(this.form);">{{$consult_anesth->tabac}}</textarea>
        </td>
        <td>
          <textarea name="oenolisme" onchange="submitForm(this.form);">{{$consult_anesth->oenolisme}}</textarea>
        </td>
      </tr>
      </table>
      </form>
      {{/if}}
      <hr />
      
      <form name="editAntFrm" action="?m=dPcabinet" method="post">
      <input type="hidden" name="m" value="dPpatients" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_antecedent_aed" />
      <input type="hidden" name="patient_id" value="{{$patient->patient_id}}" />
      <table class="form">
        <tr>
          <td colspan="2"><strong>Ajouter un ant�c�dent</strong></td>
          <td>
            <label for="rques" title="Remarques sur l'ant�c�dent">Remarques</label>
            <select name="_helpers_rques" size="1" onchange="pasteHelperContent(this)">
              <option value="">&mdash; Choisir une aide</option>
              {{html_options options=$antecedent->_aides.rques}}
            </select>
          </td>

        </tr>
        <tr>
          <th>
            <input type="checkbox" name="_date_ant" onclick="dateAntecedent()" />
            <label for="date" title="Date de l'ant�c�dent">Date</label>
          </th>
          <td class="date">
            <div id="editAntFrm_date_da"></div>
            <input type="hidden" name="date" value="" />
            <img id="editAntFrm_date_trigger" src="./images/calendar.gif" alt="calendar" title="Choisir une date de d�but" style="display:none;" />
          </td>
          <td rowspan="2">
            <textarea name="rques" onblur="if(this.value!=''){submitAnt(this.form);dateAntecedent();}"></textarea>
          </td>
        </tr>
        <tr>
          <th><label for="type" title="Type d'ant�c�dent">Type</label></th>
          <td>
            {{html_options name="type" options=$antecedent->_enumsTrans.type}}
          </td>
        </tr>
        <tr>
          <td class="button" colspan="3">
            <button class="submit" type="button" onclick="if(this.form.rques.value!=''){submitAnt(this.form);dateAntecedent();}">Ajouter</button>
          </td>
        </tr>
      </table>
      </form>
      
      <hr />

      <form name="editTrmtFrm" action="?m=dPcabinet" method="post" onsubmit="return checkForm(this)">
      
      <input type="hidden" name="m" value="dPpatients" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_traitement_aed" />
      <input type="hidden" name="patient_id" value="{{$patient->patient_id}}" />
      
      <table class="form">
        <tr>
          <td colspan="2"><strong>Ajouter un traitement</strong></td>
          <td>
            <label for="traitement" title="Traitement">Traitement</label>
            <select name="_helpers_traitement" size="1" onchange="pasteHelperContent(this)">
              <option value="">&mdash; Choisir une aide</option>
              {{html_options options=$traitement->_aides.traitement}}
            </select>
          </td>
        </tr>
        <tr>
          <th><label for="debut" title="D�but du traitement">D�but</label></th>
          <td class="date">
            <div id="editTrmtFrm_debut_da">{{$today|date_format:"%d/%m/%Y"}}</div>
            <input type="hidden" name="debut" title="{{$traitement->_props.debut}}" value="{{$today}}" />
            <img id="editTrmtFrm_debut_trigger" src="./images/calendar.gif" alt="calendar" title="Choisir une date de d�but"/>
          </td>
          <td rowspan="2">
            <textarea name="traitement" onblur="if(this.value!=''){submitAnt(this.form);finTrmt();}"></textarea>
          </td>
        </tr>
        <tr>
          <th>
            <input type="checkbox" checked="checked" name="_en_cours" onclick="finTrmt()" />
            <label for="fin" title="Fin du traitement">Fin</label>
          </th>
          <td class="date">
            <div id="editTrmtFrm_fin_da">{{$today|date_format:"%d/%m/%Y"}}</div>
            <input type="hidden" name="fin" title="{{$traitement->_props.fin}}" value="{{$today}}" />
            <img id="editTrmtFrm_fin_trigger" src="./images/calendar.gif" alt="calendar" title="Choisir une date de fin"/>
          </td>
        </tr>
        <tr>
          <td class="button" colspan="3">
            <button class="submit" type="button" onclick="if(this.form.traitement.value!=''){submitAnt(this.form);finTrmt();}">Ajouter</button>
          </td>
        </tr>
      </table>
      </form>
      
      <hr />
      <strong>Ajouter un diagnostic</strong>
      <form name="editDiagFrm" action="?m={{$m}}" method="post">

      <input type="hidden" name="m" value="dPpatients" />
      <input type="hidden" name="tab" value="edit_consultation" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_patients_aed" />
      <input type="hidden" name="patient_id" value="{{$patient->patient_id}}" />
      <input type="hidden" name="listCim10" value="{{$patient->listCim10}}" />
      
      <table style="width: 100%">
      {{foreach from=$patient->_static_cim10 key=cat item=curr_cat}}
        <tr id="category{{$cat}}-trigger">
          <td>{{$cat}}</td>
        </tr>
        <tbody class="effectCategory" id="category{{$cat}}">
          {{foreach from=$curr_cat item=curr_code}}
          <tr>
            <td class="text">
              <button class="tick notext" type="button" onclick="putCim10('{{$curr_code->code}}')"></button>
              <button class="down notext" type="button" onclick="selectCim10('{{$curr_code->code}}')"></button>
              {{$curr_code->code}}: {{$curr_code->libelle}}
            </td>
          </tr>
           {{/foreach}}
        </tbody>
      {{/foreach}}
      </table>
      </form>      
    </td>
    <td class="text" id="listAnt">
      {{include file="inc_list_ant.tpl"}}
    </td>
  </tr>
</table>