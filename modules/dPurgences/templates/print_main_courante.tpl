<table class="main">
  <tr>
    <th>
      <a href="#" onclick="window.print()">
        Main courante du {{$date|date_format:"%A %d %b %Y"}}
      </a>
    </th>
  </tr>
  <tr>
    <td>
      <table class="tbl">
        <tr>
		    <th>Degr� d'urgence</th>
		    <th>Patient</th>
		    <th>Arriv�e du patient</th>
		    <th>Responsable</th>
		    <th>Diagnostic Infirmier</th>
		    <th>Prise en charge</th>
		  </tr>
		  {{foreach from=$listSejours item="sejour"}}
		  <tr>
		    <td>
		    {{if $sejour->_ref_rpu->ccmu}}
		      {{tr}}CRPU.ccmu.{{$sejour->_ref_rpu->ccmu}}{{/tr}}
		    {{/if}}
		    </td>
		    <td>{{$sejour->_ref_patient->_view}}</td>
		    <td>{{$sejour->_entree|date_format:"%d/%m/%Y � %Hh%M"}}</td>
		    <td>{{$sejour->_ref_praticien->_view}}</td>
		    <td>{{$sejour->_ref_rpu->diag_infirmier|nl2br}}</td>
		    <td>
		    {{if $sejour->_ref_rpu->_count_consultations > 0}}
		    {{$sejour->_ref_rpu->_ref_consult->_ref_plageconsult->_ref_chir->_view}}
		    {{/if}}
		    </td>
		  </tr>
		  {{/foreach}}
		</table>
  </td>
  </tr>
</table>