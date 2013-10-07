<script>
  resolve = function(resolution){
    var url = new Url("facturation", "ajax_clean_facture");
    url.addParam("resolution" , resolution);
    url.requestUpdate("resolutions");
  }    
</script>
Il y a {{$factures|@count}} probl�mes de doubles facture
<button type="button" class="tick" onclick="resolve(1);">R�soudre</button>

<table class="main tbl">
  <tr>
    <th>Facture</th>
    <th>Patient</th>
    <th>Praticien</th>
    <th>Date Consultation</th>
  </tr>
  {{foreach from=$factures item=facture}}
    {{foreach from=$facture->_ref_consults item=consult}}
    <tr>
        <td>{{$facture->_view}}</td>
        <td>
          <a href="#" onmouseover="ObjectTooltip.createEx(this, '{{$facture->_ref_patient->_guid}}')">
            {{$facture->_ref_patient->_view|truncate:30:"...":true}}</a>
        </td>
        <td>{{$facture->_ref_praticien->_view}}</td>
        <td>
          <a href="#" onmouseover="ObjectTooltip.createEx(this, '{{$consult->_guid}}')">
            {{$consult->_date}}</a>
        </td>
    </tr>
    {{/foreach}}
  {{/foreach}}
</table>

<br/>
Il y a {{$liaisons|@count}} probl�mes de facture non s�par�es
<button type="button" class="tick"  onclick="resolve(2);">R�soudre</button>

<table class="main tbl">
  <tr>
    <th>Facture</th>
    <th>Patient</th>
    <th>Praticien</th>
    <th>Date Consultation1</th>
    <th>Date Consultation2</th>
  </tr>
  {{foreach from=$liaisons item=liaison}}
    <tr>
    {{assign var=facture value=$liaison->_ref_facture}}
        <td>{{$facture->_view}}</td>
        <td>
          <a href="#" onmouseover="ObjectTooltip.createEx(this, '{{$facture->_ref_patient->_guid}}')">
            {{$facture->_ref_patient->_view|truncate:30:"...":true}}</a>
        </td>
        <td>{{$facture->_ref_praticien->_view}}</td>
        <td>
          <a href="#" onmouseover="ObjectTooltip.createEx(this, '{{$facture->_ref_last_consult->_guid}}')">
            {{$facture->_ref_first_consult->_date}}</a>
        </td>
        <td>
          <a href="#" onmouseover="ObjectTooltip.createEx(this, '{{$facture->_ref_last_consult->_guid}}')">
            {{$facture->_ref_last_consult->_date}}</a>
        </td>
    </tr>
  {{/foreach}}
</table>
