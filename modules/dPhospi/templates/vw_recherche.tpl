{{if !$dialog}}
<script type="text/javascript">

function printRecherche() {
  var form = document.typeVue;
  var url = new Url("dPhospi", "vw_recherche");
  url.addElement(form.typeVue);
  url.addElement(form.selPrat);
  url.addElement(form.date_recherche);
  url.popup(800, 700, 'Planning');
}

selectServices = function() {
  var url = new Url("dPhospi", "ajax_select_services");
  url.addParam("view", "etat_lits");
  url.addParam("ajax_request", 0);
  url.requestModal(null, null, {maxHeight: '600'});
}

Main.add(function () {
  Calendar.regField(getForm("typeVue").date_recherche);
});

</script>

<form name="typeVue" action="?m={{$m}}" method="get">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="tab" value="{{$tab}}" />

  {{if $typeVue}}
  <button type="button" class="button print" style="float: right;" onclick="printRecherche()">{{tr}}Print{{/tr}}</button>
  
  <select name="selPrat" onchange="this.form.submit()" style="float: right;">
    <option value="">&mdash; Tous les praticiens</option>
    {{mb_include module=mediusers template=inc_options_mediuser selected=$selPrat list=$listPrat}}
  </select>
  {{/if}}
  <select name="typeVue" onchange="this.form.submit()">
    <option value="0" {{if $typeVue == 0}}selected="selected"{{/if}}>Afficher les lits disponibles</option>
    <option value="1" {{if $typeVue == 1}}selected="selected"{{/if}}>Afficher les patients pr�sents</option>
  </select>

  <button type="button" onclick="selectServices();" class="search">Services</button>

  <input type="hidden" name="date_recherche" class="dateTime" value="{{$date_recherche}}" onchange="this.form.submit()" />
</form>
{{/if}}

<table class="tbl main">
  {{if $typeVue == 0}}
  <tr>
    <th class="title" colspan="4">
      <button type="button" class="print not-printable notext" style="float: left;" onclick="this.up('table').print()"></button>
      {{$date_recherche|date_format:$conf.datetime}} : {{$libre|@count}} lit(s) disponible(s)
    </th>
  </tr>
  <tr>
    <th>Service</th>
    <th>Chambre</th>
    <th>Lit</th>
    <th>Fin de disponibilit�</th>
  </tr>
  {{foreach from=$libre item=curr_lit}}
  <tr>
    <td class="text">{{$curr_lit.service}}</td>
    <td class="text">
      {{$curr_lit.chambre}}
      {{if $curr_lit.caracteristiques != ""}}
        <div class="compact">
          {{$curr_lit.caracteristiques}}
        </div>
      {{/if}}
    </td>
    <td class="text">{{$curr_lit.lit}}</td>
    <td class="text">{{$curr_lit.limite|date_format:"%A %d %B %Y � %Hh%M"}}
  </tr>
  {{foreachelse}}
    <tr>
      <td class="empty" colspan="4">{{tr}}CLit.none{{/tr}}</td>
    </tr>
  {{/foreach}}
  
  {{else}}
  <tr>
    <th class="title" colspan="9">
      <button type="button" class="print not-printable notext" style="float: left;" onclick="this.up('table').print()"></button>
      {{if $selPrat}}
        Dr {{$listPrat.$selPrat->_view}} -
      {{/if}}
      {{$date_recherche|date_format:$conf.datetime}} : {{$listAff.Aff|@count}} patient(s) plac�(s)
      {{if $listAff.NotAff|@count}}- {{$listAff.NotAff|@count}} patient(s) non plac�(s){{/if}}
    </th>
  </tr>
  <tr>
    <th>{{mb_label class=CSejour field=patient_id}}</th>
    <th>{{mb_label class=CSejour field=praticien_id}}</th>
    <th>{{mb_title class=CAffectation field=lit_id}}</th>
    <th colspan="2">
     {{tr}}CAffectation{{/tr}} /
     {{mb_title class=CAffectation field=_duree}}
    </th>
    <th>Motif</th>
    <th>Bornes<br/>GHM</th>
  </tr>
    {{if $listAff.Aff|@count == 0 && $listAff.NotAff|@count == 0}}
        <tr>
          <td colspan="9" class="empty">{{tr}}CLit.none{{/tr}}</td>
        </tr>
      </table>
      {{mb_return}}
    {{/if}}
  {{foreach from=$listAff key=_type_aff item=_liste_aff}}
  {{foreach from=$_liste_aff item=_affectation}}
  {{if $_type_aff == "Aff"}}
  {{assign var=_sejour value=$_affectation->_ref_sejour}}
  {{else}}
  {{assign var=_sejour value=$_affectation}}
  {{/if}}
  {{assign var=_patient   value=$_sejour->_ref_patient}}
  {{assign var=_praticien value=$_sejour->_ref_praticien}}
  {{assign var=_GHM       value=$_sejour->_ref_GHM}}
  <tr>
    <td class="text">
      {{if $canPlanningOp->read && !$dialog}}
      <a class="action" style="float: right"  title="Modifier le dossier administratif" href="?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id={{$_patient->_id}}">
        <img src="images/icons/edit.png" alt="modifier" />
      </a>
      <a class="action" style="float: right"  title="Modifier le s�jour" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$_sejour->_id}}">
        <img src="images/icons/planning.png" alt="modifier" />
      </a>
      {{/if}}
      <span onmouseover="ObjectTooltip.createEx(this, '{{$_patient->_guid}}')">
        {{$_patient}}
      </span>
    </td>
    <td class="text">
      {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_praticien}}
    </td>
    {{if $_type_aff == "Aff"}}
    <td class="text">{{$_affectation->_view}}</td>
    <td class="text">
      <span onmouseover="ObjectTooltip.createEx(this, '{{$_affectation->_guid}}')">
        {{mb_include module=system template=inc_interval_datetime from=$_affectation->entree to=$_affectation->sortie}}
      </span>
    </td>
    {{else}}
    <td class="text empty">Non plac�</td>
    <td class="text">
      <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}')">
        {{mb_include module=system template=inc_interval_datetime from=$_sejour->entree to=$_sejour->sortie}}
      </span>
    </td>
    {{/if}}
    <td>{{$_affectation->_duree}}</td>
    
    <td class="text">
      {{if $_sejour->libelle}}
        {{$_sejour->libelle}}
      {{else}}
        {{foreach from=$_sejour->_ref_operations item=_operation}}
          {{mb_include module=planningOp template=inc_vw_operation operation=$_operation}}
        {{/foreach}}
      {{/if}}
    </td>
    
    <td style="text-align: center;">
      {{if $_GHM->_DP}}
        De {{$_GHM->_borne_basse}}
        � {{$_GHM->_borne_haute}} nuits
        <br />
        {{if $_GHM->_borne_basse > $_GHM->_duree}}
        <div class="warning">S�jour trop court</div>
        <img src="images/icons/cross.png" alt="alerte" />
        {{elseif $_GHM->_borne_haute < $_GHM->_duree}}
        <div class="warning">S�jour trop long</div>
        {{else}}
        <div class="info">Dans les bornes</div>
        {{/if}}
      {{else}}
      &mdash;
      {{/if}}
    </td>
  </tr>
  {{/foreach}}
  {{/foreach}}
  {{/if}}
</table>