<script type="text/javascript">

function signeActes(oForm, subject_id, praticien_id) {
	{{if $dialog}}
  return onSubmitFormAjax(oForm, {onComplete: function() { reloadAll(subject_id, praticien_id); } } );
  {{else}}
  oForm.submit();
  {{/if}}
}

function reloadAll(subject_id, praticien_id) {
  if(window.opener) {
    window.opener.ActesCCAM.refreshList(subject_id, praticien_id);
  }
  window.location.reload();
  
}

</script>

<table class="main">
  {{if !$dialog}}
  <tr>
    <th>
      <script type="text/javascript">
        Main.add(function () {
          Calendar.regField(getForm("typeAffichage").date, null, {noView: true});
        });
      </script>
      <form name="typeAffichage" action="?" method="get">
        <input type="hidden" name="m" value="dPsalleOp" />
        <input type="hidden" name="tab" value="vw_signature_actes" />
        <div style="float: right">
          Filtres
    		  <select name="praticien_id" onchange="submit();">
      		  <option value="">&mdash; Tous les praticiens</option>
      		  {{foreach from=$listPraticien item=praticien}}
      		    <option value="{{$praticien->_id}}" {{if $praticien_id == $praticien->_id}}selected="selected"{{/if}}>{{$praticien->_view}}</option>
      		  {{/foreach}}
    		  </select>
        </div>
        
        {{$date|date_format:$conf.longdate}}
        <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
		  </form>
    </th>
  </tr>
  {{/if}}
  <tr>
    <td>
			<table class="tbl">
			
				  <!-- Parcours des operations -->
				  {{foreach from=$tabOperations item=operation key=operation_id}}
				    {{assign var=_operation value=$operations.$operation_id}}
				    <tr>
				      <th colspan="10">{{$_operation->_view}}</th>
				    </tr>
				    {{foreach from=$operation item=praticien key=praticien_id}}
				      {{assign var=_praticien value=$praticiens.$praticien_id}}
				      
				      {{foreach from=$praticien item=curr_acte name="acte"}}
				      
								{{if $smarty.foreach.acte.first}}
								<tr>
								  <th>{{mb_title object=$curr_acte field=code_acte}}</th>
								  <th>{{mb_title object=$curr_acte field=code_activite}}</th>
								  <th>{{mb_title object=$curr_acte field=code_phase}}</th>
								  <th>{{mb_title object=$curr_acte field=modificateurs}}</th>
								  <th>{{mb_title object=$curr_acte field=code_association}}</th>
								  {{if $conf.dPsalleOp.CActeCCAM.tarif}}
								  <th>{{mb_title object=$curr_acte field=rembourse}}</th>
								  <th>{{mb_title object=$curr_acte field=montant_base}}</th>
								  <th>{{mb_title object=$curr_acte field=montant_depassement}}</th>
								  <th>{{mb_title object=$curr_acte field=_montant_facture}}</th>
								  {{/if}}
								  <th>{{mb_title object=$curr_acte field=signe}}</th>
								</tr>
								{{/if}}
								<tr>
								  <td>{{mb_value object=$curr_acte field=code_acte}}</td>
								  <td>{{mb_value object=$curr_acte field=code_activite}}</td>
								  <td>{{mb_value object=$curr_acte field=code_phase}}</td>
								  <td>{{mb_value object=$curr_acte field=modificateurs}}</td>
								  <td>{{mb_value object=$curr_acte field=code_association}}</td>
								  {{if $conf.dPsalleOp.CActeCCAM.tarif}}
								  <td>{{mb_value object=$curr_acte field=rembourse}}</td>
								  <td>{{mb_value object=$curr_acte field=montant_base}}</td>
								  <td>{{mb_value object=$curr_acte field=montant_depassement}}</td>
								  <td>{{mb_value object=$curr_acte field=_montant_facture}}</td>
								  {{/if}}
								  <td style="text-align: center">
			            {{if $curr_acte->signe}}
			              <img src="images/icons/tick.png" alt="" />
			            {{else}}
			              <img src="images/icons/cross.png" alt="" />
			            {{/if}}
			            </td>
							  </tr>  
				      {{/foreach}}
				      <!-- Formulaire de signature des actes ccam -->
				      {{if @$nonSigne.$operation_id.$praticien_id}}
				      <tr>
				        <td {{if $conf.dPsalleOp.CActeCCAM.tarif}}colspan="8"{{else}}colspan="4"{{/if}}>
				        </td>
				        <td colspan="2" style="text-align: right">
				        {{$_praticien->_view}}
				          <form name="signature_acte-{{$_praticien->_id}}-{{$_operation->_id}}" action="?" method="post" onsubmit="return signeActes(this, '{{$_operation->_id}}', '{{$_praticien->_id}}');">
							      <input type="hidden" name="dosql" value="do_signature_actes_aed" />
							      <input type="hidden" name="m" value="dPsalleOp" />
							      <input type="hidden" name="del" value="0" />
							      <input type="hidden" name="dialog" value="{{$dialog}}" />
							      <input type="hidden" name="date" value="{{$date}}" />
							      <input type="hidden" name="object_id" value="{{$_operation->_id}}" />
							      <input type="hidden" name="object_class" value="{{$_operation->_class}}" />
							      <input type="hidden" name="praticien_id" value="{{$_praticien->_id}}" />
							      <input type="password"  class="notNull str" size="10" maxlength="32" name="password" />
						        <button type="button" class="submit" onclick="this.form.onsubmit();">Signer</button>
							    </form>
				        </td>
				      </tr>
				    {{/if}}
				  {{/foreach}}
		    {{/foreach}}
		</table>
    </td>
  </tr>
</table>