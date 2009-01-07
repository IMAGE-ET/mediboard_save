{{if $prescription->_id}}
	<table class="tbl">
	  <tr>
	    <th>Alerte</th>
	    <th>M�dicament</th>
	    <th>Praticien</th>
	    <th>D�but</th>
	    <th>Dur�e</th>
	    <th>Posologie</th>
	  </tr>
	  {{foreach from=$prescription->_ref_prescription_lines item=curr_line}}
	    {{include file="../../dPprescription/templates/inc_vw_line_medicament_lite.tpl" prescription_reelle=$prescription mode_induction_perop=true}}
    {{/foreach}}
    
    {{foreach from=$prescription_traitement->_ref_prescription_lines item=curr_line_tp}}
	    {{include file="../../dPprescription/templates/inc_vw_line_medicament_lite.tpl" 
	    					prescription_reelle=$prescription 
	    					mode_induction_perop=true
	    					curr_line=$curr_line_tp}}
    {{/foreach}}
    
{{else}}
  <div class="small-info">
  Aucune prescription de s�jour
  </div>
{{/if}}