<!-- $Id$ -->

<table class="main">
  <tr><th>Mat�riel � commander</th></tr>
  <tr>
    <td>
	  <table class="tbl">
	    <tr>
		  <th>Date</th>
		  <th>Chirurgien</th>
		  <th>Patient</th>
		  <th>Op�ration</th>
		  <th>Mat�riel � commander</th>
		</tr>
		{foreach from=$op1 item=curr_op}
		<tr>
		  <td>{$curr_op->_ref_plageop->date|date_format:"%d/%m/%Y"}</td>
		  <td class="text">Dr. {$curr_op->_ref_chir->_view}</td>
		  <td class="text">{$curr_op->_ref_pat->_view}</td>
		  <td class="text">
        {foreach from=$curr_op->_ext_codes_ccam item=curr_code}
        {$curr_code->code} : <em>{$curr_code->libelleLong}</em><br />
        {/foreach}
        (C�t� : {$curr_op->cote})
		  <td class="text">{$curr_op->materiel|nl2br}</td>
		</tr>
		{/foreach}
	  </table>
	</td>
  </tr>
  <tr><th>Mat�riel command�</th></tr>
  <tr>
    <td>
      <table class="tbl">
	    <tr>
		  <th>Date</th>
		  <th>Chirurgien</th>
		  <th>Patient</th>
		  <th>Op�ration</th>
		  <th>Mat�riel command�</th>
		</tr>
		{foreach from=$op2 item=curr_op}
		<tr>
		  <td>{$curr_op->_ref_plageop->date|date_format:"%d/%m/%Y"}</td>
		  <td class="text">Dr. {$curr_op->_ref_chir->_view}</td>
		  <td class="text">{$curr_op->_ref_pat->_view}</td>
		  <td class="text">
        {foreach from=$curr_op->_ext_codes_ccam item=curr_code}
        {$curr_code->code} : <em>{$curr_code->libelleLong}</em><br />
        {/foreach}
        (C�t� : {$curr_op->cote})
		  </td>
		  <td class="text">{$curr_op->materiel|nl2br}</td>
		</tr>
		{/foreach}
	  </table>
	</td>
  </tr>
</table>