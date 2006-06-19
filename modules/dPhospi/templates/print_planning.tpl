<!-- $Id$ -->

{literal}
<script type="text/javascript">
//<![CDATA[

function printAdmission(id) {
  var url = new Url;
  url.setModuleAction("dPadmissions", "print_admission");
  url.addParam("id", id);
  url.popup(700, 550, 'Admissions');
}

//]]>
</script>
{/literal}

<table class="main">
  <tr>
    <th>
      <a href="javascript:window.print()">
        Planning du {$deb|date_format:"%A %d %b %Y � %Hh%M"}
        au {$fin|date_format:"%A %d %B %Y � %Hh%M"} ({$total} admissions)
      </a>
    </th>
  </tr>
  {foreach from=$listDays key=key_day item=curr_day}
  {foreach from=$curr_day key=key_prat item=curr_prat}
  {assign var="praticien" value=$listPrats.$key_prat}
  <tr>
    <td>
      <strong>
        {$key_day|date_format:"%a %d %b %Y"} 
        &mdash; Dr. {$praticien->_view} : {$curr_prat|@count} admission(s)
      </strong>
    </td>
  </tr>
  <tr>
    <td>
	  <table class="tbl">
	    <tr>
		  <th colspan="6"><strong>Admission</strong></th>
		  <th colspan="5"><strong>Intervention(s)</strong></th>
		  <th colspan="3"><strong>Patient</strong></th>
		</tr>
		<tr>
		  <th>Heure</th>
		  <th>Type</th>
          <th>Dur�e</th>
          <th>Conv.</th>
          <th>Chambre</th>
          <th>Remarques</th>
          <th>Date</th>
          <th>D�nomination</th>
          <th>C�t�</th>
          <th>Bilan</th>
          <th>Remarques</th>
          <th>Nom / Prenom</th>
          <th>Naissance (Age)</th>
          <th>Remarques</th>
        </tr>
        {foreach from=$curr_prat item=curr_sejour}
        <tr>
          <td>{$curr_sejour->entree_prevue|date_format:"%Hh%M"}</td>
          <td>{$curr_sejour->type|truncate:1:""|capitalize}</td>
          <td>{$curr_sejour->_duree_prevue} j</td>
          <td class="text">{$curr_sejour->convalescence|nl2br}</td>
          <td class="text">
            {assign var="affectation" value=$curr_sejour->_ref_first_affectation}
            {if $affectation->affectation_id}
            {$affectation->_ref_lit->_view}
            {else}
            Non plac�
            {/if}
            ({$curr_sejour->chambre_seule})
          </td>
          <td class="text">{$curr_sejour->rques}</td>
          <td>
            {foreach from=$curr_sejour->_ref_operations item=curr_operation}
            {$curr_operation->_datetime|date_format:"%d/%m/%Y"}
            {if $curr_operation->time_operation != "00:00:00"}
              � {$curr_operation->time_operation|date_format:"%Hh%M"}
            {/if}
            <br />
            {/foreach}
          </td>
          <td class="text">
            {foreach from=$curr_sejour->_ref_operations item=curr_operation}
              {foreach from=$curr_operation->_ext_codes_ccam item=curr_code}
              {$curr_code->libelleLong|truncate:80:"...":false} <em>({$curr_code->code})</em>
              {/foreach}
              <br />
            {/foreach}
          </td>
          <td>
            {foreach from=$curr_sejour->_ref_operations item=curr_operation}
              {$curr_operation->cote|truncate:1:""|capitalize}
              <br />
            {/foreach}
          </td>
          <td class="text">
            {foreach from=$curr_sejour->_ref_operations item=curr_operation}
              {$curr_operation->examen|nl2br}
              <br />
            {/foreach}
          </td>
          <td class="text">
            {foreach from=$curr_sejour->_ref_operations item=curr_operation}
              {$curr_operation->rques|nl2br}
              <br />
            {/foreach}
          </td>
          <td>
            <a href="javascript:printAdmission({$curr_sejour->sejour_id})">
              {$curr_sejour->_ref_patient->_view}
            </a>
          </td>
          <td>
            <a href="javascript:printAdmission({$curr_sejour->sejour_id})">
              {$curr_sejour->_ref_patient->_naissance} ({$curr_sejour->_ref_patient->_age})
            </a>
          </td>
          <td class="text">
            <a href="javascript:printAdmission({$curr_sejour->sejour_id})">
              {$curr_sejour->_ref_patient->rques}
            </a>
          </td>
        </tr>
        {/foreach}
      </table>
    </td>
  </tr>
  {/foreach}
  {/foreach}
</table>