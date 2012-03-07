{{* $Id: *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=admissions script=admissions}}

<script type="text/javascript">

function showLegend() {
  var url = new Url("dPadmissions", "vw_legende").requestModal();
}

function printAmbu(){
  var url = new Url("dPadmissions", "print_ambu");
  url.addParam("date", "{{$date}}");
  url.popup(800,600,"Ambu");
}

function printPlanning() {
  var oForm = getForm("selType");
  var url = new Url("dPadmissions", "print_sorties");
  url.addParam("date"       , "{{$date}}");
  url.addParam("type_sejour", $V(oForm._type_admission));
  url.addParam("service_id" , $V(oForm.service_id));
  url.popup(700, 550, "Sorties");
}

function printDHE(type, object_id) {
  var url = new Url("dPplanningOp", "view_planning");
  url.addParam(type, object_id);
  url.popup(700, 550, "DHE");
}

function loadTransfert(oForm){
  sejour_id   = $V(oForm.sejour_id)
  mode_sortie = $V(oForm.mode_sortie);
  // si Transfert, affichage du select
  if(mode_sortie=="transfert"){
    //Chargement de la liste des etablissement externes
    var url = new Url("dPadmissions", "httpreq_vw_etab_externes");
    url.addParam("sejour_id", sejour_id);
    url.requestUpdate('listEtabExterne-'+oForm.name);
  } else {
    // sinon, on vide le contenu de la div
    $("listEtabExterne-" + oForm.name).innerHTML = "";
  }
}

var changeEtablissementId = function(oForm) {
  $V(oForm._modifier_sortie, '0');
  var type = $V(oForm.type);
  submitSortie(oForm, type);
}

function reloadFullSorties(filterFunction) {
  var oForm = getForm("selType");
  var url = new Url("dPadmissions", "httpreq_vw_all_sorties");
  url.addParam("date"      , "{{$date}}");
  url.addParam("type"      , $V(oForm._type_admission));
  url.addParam("service_id", $V(oForm.service_id));
  url.addParam("prat_id"   , $V(oForm.prat_id));
  url.requestUpdate('allSorties');
  reloadSorties(filterFunction);
}

function reloadSorties(filterFunction) {
  var oForm = getForm("selType");
  var url = new Url("dPadmissions", "httpreq_vw_sorties");
  url.addParam("date"      , "{{$date}}");
  url.addParam("type"      , $V(oForm._type_admission));
  url.addParam("service_id", $V(oForm.service_id));
  url.addParam("prat_id"   , $V(oForm.prat_id));
  if(!Object.isUndefined(filterFunction)){
    url.addParam("filterFunction" , filterFunction);
  }
  url.requestUpdate("listSorties");
}

function submitSortie(oForm) {
  return onSubmitFormAjax(oForm, { onComplete : reloadSorties });
}

function confirmation(oForm, type){
   if(!checkForm(oForm)){
     return false;
   }
   if(confirm('La date enregistr�e de sortie est diff�rente de la date pr�vue, souhaitez vous confimer la sortie du patient ?')){
     submitSortie(oForm, type);
   }
}

function confirmation(date_actuelle, date_demain, sortie_prevue, entree_reelle, oForm){
  if(entree_reelle == ""){
    if(!confirm('Attention, ce patient ne poss�de pas de date d\'entr�e r�elle, souhaitez vous confirmer la sortie du patient ?')){
     return false;
    }
  }
  if(date_actuelle > sortie_prevue || date_demain < sortie_prevue) {
    if(!confirm('La date enregistr�e de sortie est diff�rente de la date pr�vue, souhaitez vous confimer la sortie du patient ?')){
     return false;
    }
  }
  submitSortie(oForm);    
}

Main.add(function () {
  var totalUpdater = new Url("dPadmissions", "httpreq_vw_all_sorties");
  totalUpdater.addParam("date", "{{$date}}");
  totalUpdater.periodicalUpdate('allSorties', { frequency: 120 });
  
  var listUpdater = new Url("dPadmissions", "httpreq_vw_sorties");
  listUpdater.addParam("selSortis", "{{$selSortis}}");
  listUpdater.addParam("date", "{{$date}}");
  listUpdater.periodicalUpdate('listSorties', { frequency: 120 });
});

</script>

<table class="main">
<tr>
  <td>
    <a href="#legend" onclick="showLegend()" class="button search">L�gende</a>
  </td>
  <td style="float: right">
    <form action="?" name="selType" method="get">
      {{mb_field object=$sejour field="_type_admission" emptyLabel="CSejour.all" onchange="reloadFullSorties();"}}
      <select name="service_id" onchange="reloadFullSorties();">
        <option value="">&mdash; Tous les services</option>
        {{foreach from=$services item=_service}}
          <option value="{{$_service->_id}}"{{if $_service->_id == $sejour->service_id}}selected="selected"{{/if}}}>{{$_service}}</option>
        {{/foreach}}
      </select>
      <select name="prat_id" onchange="reloadFullSorties();">
        <option value="">&mdash; Tous les praticiens</option>
        {{foreach from=$prats item=_prat}}
          <option value="{{$_prat->_id}}"{{if $_prat->_id == $sejour->praticien_id}}selected="selected"{{/if}}}>{{$_prat}}</option>
        {{/foreach}}
      </select>
    </form>
    <a href="#" onclick="printPlanning()" class="button print">Imprimer</a>
  </td>
</tr>
  <tr>
    <td id="allSorties" style="width: 250px">
    </td>
    <td id="listSorties" style="width: 100%">
    </td>
  </tr>
</table>