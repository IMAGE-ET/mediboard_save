<script type="text/javascript">

function printPlanning() {
  url = new Url;
  url.setModuleAction("dPadmissions", "print_sorties");
  url.addParam("date", "{{$date}}");
  url.popup(700, 550, "Sorties");
}

function loadTransfert(form, mode_sortie){
  // si Transfert, affichage du select
  if(mode_sortie=="transfert"){
    //Chargement de la liste des etablissement externes
    var url = new Url();
    url.setModuleAction("dPadmissions", "httpreq_vw_etab_externes");
    url.requestUpdate('listEtabExterne-'+form.name, { waitingText : null });
  } else {
    // sinon, on vide le contenu de la div
    $("listEtabExterne-" + form.name).innerHTML = "";
  }
}


function reloadAmbu() {
  var ambuUrl = new Url;
  ambuUrl.setModuleAction("dPadmissions", "httpreq_vw_sorties_ambu");
  ambuUrl.addParam("date", "{{$date}}");
  ambuUrl.addParam("vue", "{{$vue}}");
  ambuUrl.requestUpdate('sortiesAmbu', { waitingText : null });
}


function submitAmbu(oForm) {
  submitFormAjax(oForm, 'systemMsg', { onComplete : reloadAmbu });
}


function reloadComp() {
  var compUrl = new Url;
  compUrl.setModuleAction("dPadmissions", "httpreq_vw_sorties_comp");
  compUrl.addParam("date", "{{$date}}");
  compUrl.addParam("vue", "{{$vue}}");
  compUrl.requestUpdate('sortiesComp', { waitingText : null });
}

function submitComp(oForm) {
  submitFormAjax(oForm, 'systemMsg', { onComplete : reloadComp });
}


function confirmationComp(oForm){
   if(confirm('La date enregistr�e de sortie est diff�rente de la date pr�vue, souhaitez vous confimer la sortie du patient ?')){
     submitComp(oForm);
   }
}

function confirmationAmbu(oForm){
   if(confirm('La date enregistr�e de sortie est diff�rente de la date pr�vue, souhaitez vous confimer la sortie du patient ?')){
     submitAmbu(oForm);
   }
}


function pageMain() {
  
  var ambuUpdater = new Url;
  ambuUpdater.setModuleAction("dPadmissions", "httpreq_vw_sorties_ambu");
  ambuUpdater.addParam("date", "{{$date}}");
  ambuUpdater.addParam("vue", "{{$vue}}");
  ambuUpdater.periodicalUpdate('sortiesAmbu', { frequency: 90 });
  
  var compUpdater = new Url;
  compUpdater.setModuleAction("dPadmissions", "httpreq_vw_sorties_comp");
  compUpdater.addParam("date", "{{$date}}");
  compUpdater.addParam("vue", "{{$vue}}");
  compUpdater.periodicalUpdate('sortiesComp', { frequency: 90 });

  regRedirectPopupCal("{{$date}}", "index.php?m={{$m}}&tab={{$tab}}&date=");
}

</script>

<table class="main">
  <tr>
    <td class="halfPane">
      <form name="typeVue" action="?m={{$m}}" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <label for="vue" title="Choisir un type de vue">Type de vue</label>
      <select name="vue" onchange="submit()">
        <option value="0" {{if $vue == 0}}selected="selected"{{/if}}>Tout afficher</option>
        <option value="1" {{if $vue == 1}}selected="selected"{{/if}}>Ne pas afficher les valid�s</option>
      </select>
      </form>
    </td>
    <td class="halfPane" style="text-align: center">
      <a style="float: right;" href="#" onclick="printPlanning()" class="buttonprint">Imprimer</a>
      <strong>
        <a href="index.php?m=dPadmissions&amp;tab=vw_idx_sortie&amp;date={{$hier}}"> <<< </a>
        {{$date|date_format:"%A %d %B %Y"}}
        <img id="changeDate" src="./images/icons/calendar.gif" title="Choisir la date" alt="calendar" />
        <a href="index.php?m=dPadmissions&amp;tab=vw_idx_sortie&amp;date={{$demain}}"> >>> </a>
      </strong>
    </td>
  </tr>
  <tr>
    <td id="sortiesAmbu">
    </td>
    <td id="sortiesComp">
    </td>
  </tr>
</table>
