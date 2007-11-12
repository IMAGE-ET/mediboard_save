<!-- $Id: view_planning.tpl 2798 2007-10-11 15:55:13Z alexis_granger $ -->

<script type="text/javascript">

function printAdmission(id) {
  var url = new Url;
  url.setModuleAction("dPadmissions", "print_admission");
  url.addParam("id", id);
  url.popup(700, 550, "Patient");
}

</script>


<table class="main">
  <tr>
    <th>
      <a href="#" onclick="window.print()">
        Planning du {{$filter->_date_min|date_format:"%d/%m/%Y"}}
        {{if $filter->_date_min != $filter->_date_max}}
        au {{$filter->_date_max|date_format:"%d/%m/%Y"}}
        {{/if}}
      </a>
    </th>
  </tr>
  {{foreach from=$plagesop item=curr_plageop}}
  <tr>
    <td class="text">
	  <strong>Dr. {{$curr_plageop->_ref_chir->_view}}</strong> -
	  <strong>{{$curr_plageop->_ref_salle->nom}}</strong>
	  de {{$curr_plageop->debut|date_format:"%Hh%M"}} � {{$curr_plageop->fin|date_format:"%Hh%M"}}
    le {{$curr_plageop->date|date_format:"%d/%m/%Y"}}
    
    {{if $curr_plageop->anesth_id}}
	    - Anesthesiste : <strong>Dr. {{$curr_plageop->_ref_anesth->_view}}</strong>
	  {{/if}}
	  
    {{if $curr_plageop->_ref_personnel}}
      - Personnel : 
      {{foreach from=$curr_plageop->_ref_personnel item=_personnel}}
      {{$_personnel->_ref_personnel->_ref_user->_view}};
      {{/foreach}}
    {{/if}}
	</td>
  </tr>
  <tr>
    <td>
	</td>
  </tr>
      
  <tr>
    <td>
	  <table class="tbl">
	    <tr>
		  <th class="title" colspan="2">Patient</th>
		  <th class="title" colspan="4">Sejour</th>
		  <th class="title" colspan="4">Intervention</th>
		</tr>
		<tr>
		  <!-- Patient -->
		  <th>Nom - Pr�nom</th>
		  <th>Date de naissance</th>

		  <!-- Sejour -->
      <th>Entree</th>
      <th>Sortie</th>
		  <th>Chambre</th>
		  <th>DP</th>
		  
		  <!-- Intervention -->
		  <th>Heure</th>
		  <th>Libell�</th>
		  <th>Codes pr�vus</th>
		  <th>Codage au bloc</th>
		</tr>

		{{foreach from=$curr_plageop->_ref_operations item=curr_op}}
    {{assign var=sejour value=$curr_op->_ref_sejour}}
    {{assign var=patient value=$sejour->_ref_patient}}
		<tr>
		  <!-- Patient -->
		  <td>
		    <a href="#" onclick="printAdmission({{$sejour->_id}})">
		      {{$patient->_view}}
		    </a>
		  </td>
		  <td>
		    <a href="#" onclick="printAdmission({{$sejour->_id}})">
		      {{$patient->_age}} ans
		    </a>
		  </td>
      
		  <!-- Sejour -->
		  <td>
		    {{mb_value object=$sejour field=_entree}}
		  </td>
		  <td>
		    {{mb_value object=$sejour field=_sortie}}
		  </td>
		  <td class="text">
        {{assign var="affectation" value=$sejour->_ref_first_affectation}}
		    {{if $affectation->_id}}
		    {{$affectation->_ref_lit->_view}}
		    {{else}}
		    Non plac�
		    {{/if}}
		  </td>
		  <td>
		    {{$sejour->DP}}
		  </td>

		  <!-- Intervention -->
		  {{if $curr_op->rank}}
		    <td>{{$curr_op->time_operation|date_format:"%Hh%M"}}</td>
		  {{else}}
		    <td>NP</td>
		  {{/if}}
		  <td class="text">
		    {{$curr_op->libelle}}
      </td>
      <td>
        <ul>
        {{foreach from=$curr_op->_ext_codes_ccam item=curr_code}}
          <li>{{$curr_code->code}}</li>
        {{/foreach}}
        </ul>
      </td>
      <td>
        <ul>
        {{foreach from=$curr_op->_ref_actes_ccam item=curr_acte}}
          <li>
            Dr. {{$curr_acte->_ref_executant->_view}} : {{$curr_acte->code_acte}}
            <br />
            Act. : {{$curr_acte->code_activite}}
            &mdash; Phase : {{$curr_acte->code_phase}}
            {{if $curr_acte->modificateurs}}
              &mdash; Modifs : {{$curr_acte->modificateurs}}
            {{/if}}
            &mdash; Asso : {{$curr_acte->_guess_association}}
          </li>
        {{/foreach}}
        </ul>
      </td>
		</tr>
		{{/foreach}}
	  </table>
	</td>
  </tr>
  {{/foreach}}
</table>