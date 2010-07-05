{{mb_include_script module="ssr" script="planning"}}
{{mb_include_script module="ssr" script="planification"}}

<script type="text/javascript">
	
onSelect = function(oDiv, css_class){
  var sejour_id = css_class.match(/CSejour-([0-9]+)/)[1];
	var _equipement_id = css_class.match(/CEquipement-([0-9]+)/);
  var equipement_id = _equipement_id ? _equipement_id[1] : '';

  // refresh du planning du patient concern�
	Planification.refreshSejour(sejour_id, false);
	
	// refresh du planning de l'equipement concern�
	equipement_id ? PlanningEquipement.show(equipement_id, sejour_id) : PlanningEquipement.hide();
	
	$('planning-technicien').select('.elt_selected').invoke('removeClassName', 'elt_selected');
  oDiv.addClassName('elt_selected');
}

submitValidation = function(oForm){
  if(!$V(oForm.token_elts)){
    $V(oForm.del, '0');
    $V(oForm.realise, '0');
	  return;
	}
  return onSubmitFormAjax(oForm, { onComplete: function(){ 
	  PlanningTechnicien.show('{{$kine_id}}', null, null, 650, true);
		$V(oForm.del, '0');
		$V(oForm.realise, '0');
		$V(oForm.token_elts, '');
	} });
}

Main.add(function(){
  Planification.showWeek(null, true);
  PlanningTechnicien.show('{{$kine_id}}', null, null, 650, true, true);

  oFormSelectedEvents = getForm("editSelectedEvent");
  tab_selected = new TokenField(oFormSelectedEvents.token_elts); 
});

onCompleteShowWeek = function(){
  PlanningTechnicien.show(); 
  PlanningEquipement.hide();
  $('planning-sejour').update('');
}

updateSelectedEvents = function(){
$V(oFormSelectedEvents.token_elts, "");
  $$(".event.selected").each(function(e){
    if(e.className.match(/CEvenementSSR-([0-9]+)/)){
     var evt_id = e.className.match(/CEvenementSSR-([0-9]+)/)[1];
     tab_selected.add(evt_id);
    }
  });
  // Rafraichissement du contenu de la modale	
	if($V(oFormSelectedEvents.realise) == 1 && $V(oFormSelectedEvents.token_elts)){
    updateModalEvenements();
	}
}

updateModalEvenements = function(token_field_evts){
  var oFormSelectedEvents = getForm("editSelectedEvent");

  var url = new Url("ssr", "ajax_update_modal_evenements");
	url.addParam("token_field_evts", $V(oFormSelectedEvents.token_elts));
	url.requestUpdate("modal_evenements", { 
	  onComplete: viewModalEvenements
	});
}

viewModalEvenements = function(){
  modalWindow = modal($('modal_evenements'), {
    className: 'modal'
  });
}

</script>

{{if !$kine->code_intervenant_cdarr}}
  <div class="small-warning">{{tr}}CMediusers-code_intervenant_cdarr-none{{/tr}}</div>
{{/if}}

<!-- Affichage de la modale -->
<div id="modal_evenements" style="display: none;"></div>

<table class="main">
	<tr>
    <td id="week-changer"></td>
		<td>
      <form name="selectKine" method="get" action="">
        <input type="hidden" name="m" value="ssr" />
        <input type="hidden" name="tab" value="vw_kine_board" />
        <select name="kine_id" onchange="this.form.submit();">
          {{foreach from=$kines item=_kine}}
            <option value="{{$_kine->_id}}" class="mediuser"
                    style="border-color: #{{$_kine->_ref_function->color}}"
                    {{if $kine_id == $_kine->_id}}selected="selected"{{/if}}>{{$_kine->_view}}</option>
          {{/foreach}}
        </select>
      </form>
  	</td>
  </tr>
	<tr>
		<td style="width: 65%">
			<div style="position: relative">
			  <div style="position: absolute; top: 0px; right: 0px;">
          <button type="button" class="change notext" onclick="PlanningTechnicien.toggle();"></button>
        </div>
				<div id="planning-technicien"></div>
			</div>
		</td>
		<td>
  		<form name="editSelectedEvent" method="post" action="?">
        <input type="hidden" name="m" value="ssr" />
        <input type="hidden" name="dosql" value="do_modify_evenements_aed" />
        <input type="hidden" name="token_elts" value="" />
        <input type="hidden" name="del" value="0" />    
        <input type="hidden" name="realise" value="0" />
       		
  			<table class="form">
  			<tr>
  			  <th colspan="2" class="category">
  			  	R�aliser les �v�nements s�lectionn�s
  			  </th>
  			</tr>
  			<tr>
  				<td colspan="2" class="button">
  			    <button type="button" class="tick" onclick="$V(this.form.realise, '1'); updateSelectedEvents();">{{tr}}Validate{{/tr}}</button>
            <button type="button" class="cancel" onclick="updateSelectedEvents(); submitValidation(this.form);">{{tr}}Cancel{{/tr}}</button>
        	</td>
  			</tr>
  			<tr>
          <th class="category" colspan="2">
            Suppression des �v�nements s�lectionn�s
          </th>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button type="button" class="trash" onclick="$V(this.form.del, '1'); updateSelectedEvents(); submitValidation(this.form);">
              {{tr}}Delete{{/tr}}
            </button>
          </td>
        </tr>
  			</table>
  		</form>
				
		  <table>
				<tr>
				  <td id="planning-sejour"></td>
				</tr>
				<tr>
				<td><hr /></td>
				</tr>
				<tr>
					<td id="planning-equipement"></td>
				</tr>	
			</table>
		</td>
	</tr>
</table>