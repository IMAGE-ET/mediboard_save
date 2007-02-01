<!-- $Id$ -->

<script type="text/javascript">

function checkFormPrint() {
  var form = document.paramFrm;
  if(!(checkForm(form))){
    return false;
  }
  popPlages();
}

function popPlages() {
  var form = document.paramFrm;

  var url = new Url;
  url.setModuleAction("dPcabinet", "print_plages");
  url.addElement(form.deb);
  url.addElement(form.fin);
  url.addElement(form.chir);
  url.popup(700, 550, "Planning");
}

function pageMain() {
  regFieldCalendar("paramFrm", "deb");
  regFieldCalendar("paramFrm", "fin");
}


</script>

<form name="paramFrm" action="?m=dPcabinet" method="post" onsubmit="return checkFormPrint()">

<table class="main">
  <tr>
    <td>

      <table class="form">
        <tr><th class="category" colspan="3">Choix de la p�riode</th></tr>
        <tr>
          <th><label for="deb" title="Date de d�but de la p�riode">D�but</label></th>
          <td class="date" colspan="2">
            <div id="paramFrm_deb_da">{{$deb|date_format:"%d/%m/%Y"}}</div>
            <input type="hidden" title="notNull date" name="deb" value="{{$deb}}" />
            <img id="paramFrm_deb_trigger" src="./images/icons/calendar.gif" alt="calendar" title="Choisir une date de d�but"/>
          </td>
        </tr>
        <tr>
          <th><label for="fin" title="Date de fin de la p�riode">Fin</label></th>
          <td class="date" colspan="2">
            <div id="paramFrm_fin_da">{{$fin|date_format:"%d/%m/%Y"}}</div>
            <input type="hidden" title="notNull date moreEquals|deb" name="fin" value="{{$fin}}" />
            <img id="paramFrm_fin_trigger" src="./images/icons/calendar.gif" alt="calendar" title="Choisir une date de fin"/>
          </td>
        </tr>
      </table>

    </td>
    <td>

      <table class="form">
        <tr><th class="category" colspan="2">Choix des param�tres de tri</th></tr>
        <tr>
          <th><label for="chir" title="Praticien">Praticien</label></th>
          <td><select name="chir">
            <option value="0">&mdash; Tous</option>
            {{foreach from=$listChir item=curr_chir}}
	            <option class="mediuser" style="border-color: #{{$curr_chir->_ref_function->color}};" value="{{$curr_chir->user_id}}">{{$curr_chir->_view}}</option>
            {{/foreach}}
          </select></td>
        </tr>
      </table>

    </td>
  </tr>
  <tr>
    <td colspan="2">

      <table class="form">
        <tr>
          <td class="button">
            <button type="button" class="print" onclick="checkFormPrint()">Afficher</button>
          </td>
        </tr>
        </table>

    </td>
  </tr>
</table>
</form>