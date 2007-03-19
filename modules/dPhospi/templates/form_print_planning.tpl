<!-- $Id$ -->

<script type="text/javascript">
function checkFormPrint() {
  var form = document.paramFrm;
  
  if(!(checkForm(form))){
    return false;
  }
  
  popPlanning();
}

function popPlanning() {
  var form = document.paramFrm;

  var url = new Url;
  url.setModuleAction("dPhospi", "print_planning");
  url.addElement(form.deb);
  url.addElement(form.fin);
  url.addElement(form.ordre);
  url.addElement(form.service);
  url.addElement(form.type);
  url.addElement(form.chir);
  url.addElement(form.spe);
  url.addElement(form.conv);
  url.popup(700, 500, "Planning");
  return;
}

function changeDate(sDebut, sFin){
  var oForm = document.paramFrm;
  var date_debut  = makeDateFromDATETIME(sDebut);
  var date_fin    = makeDateFromDATETIME(sFin);
  
  oForm.deb.value = sDebut;
  oForm.fin.value = sFin;
  $('paramFrm_deb_da').innerHTML = makeLocaleDateTimeFromDate(date_debut);
  $('paramFrm_fin_da').innerHTML = makeLocaleDateTimeFromDate(date_fin);
}

function changeDateCal(){
  var oForm = document.paramFrm;
  oForm.select_days[0].checked = false;
  oForm.select_days[1].checked = false;
}

function pageMain() {
  regFieldCalendar("paramFrm", "deb", true);
  regFieldCalendar("paramFrm", "fin", true);
}

</script>

<form name="paramFrm" action="?m=dPhospi" method="post" onsubmit="return checkFormPrint()">

<table class="main">
  <tr>
    <td>

      <table class="form">
        <tr><th class="category" colspan="3">Choix de la p�riode</th></tr>
        
        <tr>
          <th><label for="deb">D�but</label></th>
          <td class="date">
            <div id="paramFrm_deb_da">{{$today_deb|date_format:"%d/%m/%Y %H:%M"}}</div>
            <input type="hidden" onchange="changeDateCal()" name="deb" class="notNull dateTime" value="{{$today_deb}}" />
            <img id="paramFrm_deb_trigger" src="./images/icons/calendar.gif" alt="calendar" title="Choisir une date de d�but"/>
          </td>
          <td rowspan="2">
            <input type="radio" name="select_days" onclick="changeDate('{{$today_deb}}','{{$today_fin}}');" value="today" checked="checked" /> 
              <label for="select_days_today">Aujourd'hui</label>
            <br /><input type="radio" name="select_days" onclick="changeDate('{{$tomorrow_deb}}','{{$tomorrow_fin}}');" value="tomorrow" /> 
              <label for="select_days_tomorrow">Lendemain</label>
          </td>
        </tr>

        <tr>
          <th><label for="fin">Fin</label></th>
          <td class="date">
            <div id="paramFrm_fin_da">{{$today_fin|date_format:"%d/%m/%Y %H:%M"}}</div>
            <input type="hidden" onchange="changeDateCal()" name="fin" class="notNull dateTime moreEquals|deb" value="{{$today_fin}}" />
            <img id="paramFrm_fin_trigger" src="./images/icons/calendar.gif" alt="calendar" title="Choisir une date de fin"/>
          </td>
        </tr>

        <tr>
          <th><label for="ordre">Classement des admissions</label></th>
          <td colspan="2">
            <select name="ordre">
              <option value="heure">Par heure d'admission</option>
              <option value="nom">Par nom du patient</option>
            </select>
          </td>
        </tr>

        <tr>
          <th><label for="service">Service</label></th>
          <td colspan="2"><select name="service">
            <option value="0">&mdash; Tous les services &mdash;</option>
            {{foreach from=$listServ item=curr_serv}}
            <option value="{{$curr_serv->service_id}}">{{$curr_serv->nom}}</option>
            {{/foreach}}
          </select></td>
        </tr>
      </table>

    </td>
    <td>

      <table class="form">
        <tr><th class="category" colspan="2">Param�tres de filtrage</th></tr>
        <tr>
          <th><label for="type">Type d'admission</label></th>
          <td><select name="type">
            <option value="0">&mdash; Tous types d'admission &mdash;</option>
            {{foreach from=$sejour->_enumsTrans.type key=curr_key item=curr_type}}
              <option value="{{$curr_key}}">{{$curr_type}}</option>
            {{/foreach}}
          </select></td>
        </tr>
        <tr>
          <th><label for="chir">Praticien</label></th>
          <td><select name="chir">
            <option value="0">&mdash; Tous les praticiens &mdash;</option>
            {{foreach from=$listPrat item=curr_prat}}
              <option class="mediuser" style="border-color: #{{$curr_prat->_ref_function->color}};" value="{{$curr_prat->user_id}}">{{$curr_prat->_view}}</option>
            {{/foreach}}
          </select></td>
        </tr>
        <tr>
          <th><label for="spe">Specialit�</label></th>
          <td><select name="spe">
            <option value="0">&mdash; Toutes les sp�cialit�s &mdash;</option>
            {{foreach from=$listSpec item=curr_spec}}
              <option class="mediuser" style="border-color: #{{$curr_spec->color}};" value="{{$curr_spec->function_id}}">{{$curr_spec->text}}</option>
            {{/foreach}}
          </select></td>
        </tr>
        <tr>
          <th><label for="conv">Convalescence</label></th>
          <td><select name="conv">
            <option value="0">&mdash; Indiff�rent &mdash;</option>
	        <option value="o">avec</option>
	        <option value="n">sans</option>
          </select></td>
        </tr>
      </table>

    </td>
  </tr>
  <tr>
    <td colspan="2">

      <table class="form"><tr><td class="button"><button type="button" onclick="checkFormPrint()" class="print">Afficher</button></td></tr></table>

    </td>
  </tr>
</table>

</form>