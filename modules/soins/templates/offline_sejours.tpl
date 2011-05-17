<style>
  @media print {
    div.modal_view {
      display: block !important;
      height: auto !important;
      width: 100% !important;
      page-break-after: always;
      font-size: 8pt !important;
      left: auto !important;
      top: auto !important;\
      position: static !important;
    }
    table.table_print {
      page-break-after: always;
    }
    table {
      width: 100% !important;
      font-size: inherit; !important
    }
  }
</style>
<script type="text/javascript">
 // La div du dossier qui a �t� pass� dans la fonction modal()
 // a du style suppl�mentaire, qu'il faut �craser lors de l'impression
 // d'un dossier seul.
  printOneDossier = function(sejour_id) {
    $("dossier-"+sejour_id).print();
  }
</script>
<table class="tbl table_print">
  <tr>
    <th class="title" colspan="6">
      <button class="print not-printable" style="float: right;" onclick="window.print();">{{tr}}Print{{/tr}}</button>
      ({{$sejours|@count}}) S�jours du {{$date|date_format:$conf.longdate}} {{$hour|date_format:$conf.time}} - Service {{$service}}
    </th>
  </tr>
  <tr>
    <th>
      {{tr}}CSejour-patient_id{{/tr}}
    </th>
    <th>
      {{tr}}CSejour-entree{{/tr}}
    </th>
    <th>
      {{tr}}CSejour-sortie{{/tr}}
    </th>
    <th>
      {{tr}}CSejour-libelle{{/tr}}
    </th>
    <th>
      {{tr}}CSejour-praticien_id{{/tr}}
    </th>
    <th style="width: 1%">
    </th>
  </tr>
  {{foreach from=$sejours item=_sejour}}
    <tr>
      <td>
        <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_ref_patient->_guid}}')">
          {{$_sejour->_ref_patient->_view}}
        </span>
      </td>
      <td>
        {{mb_value object=$_sejour field=entree format=$conf.date}}
      </td>
      <td>
        {{mb_value object=$_sejour field=sortie format=$conf.date}}
      </td>
      <td>
        {{mb_value object=$_sejour field=libelle}}
      </td>
      <td>
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_sejour->_ref_praticien}}
      </td>
      <td>
        <button class="search" onclick="modal($('dossier-{{$_sejour->_id}}'))">Dossier soins</button>
        <button class="print notext" onclick="printOneDossier('{{$_sejour->_id}}')" title="Imprimer le dossier"></button>
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="6">{{tr}}CSejour.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>

{{foreach from=$dossiers_complets item=_dossier key=sejour_id}}
  <div id="dossier-{{$sejour_id}}" class="modal modal_view" style="display: none; width: 700px; height: 500px; overflow-x: hidden; overflow-y: scroll;">
    {{$_dossier|smarty:nodefaults}}
  </div>
{{/foreach}}