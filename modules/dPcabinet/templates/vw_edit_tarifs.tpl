{{mb_script module=dPcabinet script=tarif}}

<script type="text/javascript">

Main.add(function () {
  Tarif.updateTotal();
  Tarif.chir_id     = "{{$prat->user_id}}";
  Tarif.function_id = "{{$prat->function_id}}";
	{{if $user->_is_praticien || ($user->_is_secretaire && $tarif->_id)}}
	  Tarif.updateOwner();
	{{/if}}
});

</script>

<table class="main">
  <tr>
    <td colspan="2" class="halfPane">
      <a class="button new" href="?m={{$m}}&amp;tab={{$tab}}&amp;tarif_id=0">
      	{{tr}}CTarif-title-create{{/tr}}
      </a>
    </td>
  </tr>
  <tr>
    <td class="halfPane">
      {{include file=inc_list_tarifs.tpl}}
    </td>
    
    <td>
      <form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)" class="{{$tarif->_spec}}">
      <input type="hidden" name="dosql" value="do_tarif_aed" />
      <input type="hidden" name="del" value="0" />
      {{mb_key object=$tarif}}

      <table class="form">
        {{if $tarif->_id}}
        <tr>
        	<th class="title modify text" colspan="2">
            {{mb_include  module=system template=inc_object_notes      object=$tarif}}
            {{mb_include  module=system template=inc_object_history    object=$tarif}}
        		{{mb_include  module=system template=inc_object_idsante400 object=$tarif}}
        		{{tr}}CTarif-title-modify{{/tr}} '{{$tarif}}'
        	</th>
        </tr>
        {{else}}
        <tr><th class="title" colspan="2">{{tr}}CTarif-title-create{{/tr}}</th></tr>
        {{/if}}
        <tr>
          <th>{{mb_label object=$tarif field="_type"}}</th>
          <td>
            {{if $user->_is_praticien || ($user->_is_secretaire && $tarif->_id)}}
			      {{mb_field object=$tarif field="function_id" hidden=1}}
			      <input type="hidden" name="chir_id" value="{{$prat->user_id}}" />
						
            <select name="_type" onchange="Tarif.updateOwner();">
              <option value="chir"     {{if $tarif->chir_id}}     selected="selected" {{/if}}>Tarif personnel</option>
              <option value="function" {{if $tarif->function_id}} selected="selected" {{/if}}>Tarif de cabinet</option>
            </select>
            
            {{else}}
			      <input  type="hidden" name="function_id" value="" />
            <select name="chir_id">
              <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
              {{mb_include module=mediusers template=inc_options_mediuser list=$listPrat selected=$prat->_id}}
            </select>
            {{/if}}
          </td>
        </tr>

        <tr>
          <th>{{mb_label object=$tarif field="description"}}</th>
          <td>{{mb_field object=$tarif field="description"}}</td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$tarif field=codes_ccam}}</th>
			    <td>
			    	{{foreach from=$tarif->_codes_ccam item=_code_ccam}}
						<span onmouseover="ObjectTooltip.createDOM(this, 'DetailCCAM-{{$_code_ccam}}');">{{$_code_ccam}}</span>
						<div id="DetailCCAM-{{$_code_ccam}}" style="display: none">
							{{mb_include module=system template=CMbObject_view object=$tarif->_new_actes.$_code_ccam}}
						</div>
            <br/>
			    	{{foreachelse}}
			    	<em>{{tr}}None{{/tr}}</em>
						{{/foreach}}
			    </td>
        </tr>

        <tr>
          <th>{{mb_label object=$tarif field=codes_ngap}}</th>
          <td>
         	  {{foreach from=$tarif->_codes_ngap item=_code_ngap}}
					  <span onmouseover="ObjectTooltip.createDOM(this, 'DetailNGAP-{{$_code_ngap}}');">{{$_code_ngap}}</span>
						<br/>
					  <div id="DetailNGAP-{{$_code_ngap}}" style="display: none">
				 	    {{mb_include module=system template=CMbObject_view object=$tarif->_new_actes.$_code_ngap}}
			 		  </div>
          	{{foreachelse}}
            <em>{{tr}}None{{/tr}}</em>
	 				{{/foreach}}
          </td>
        </tr>

        <tr>
          <th>{{mb_label object=$tarif field=secteur1}}</th>
          <td>
            {{if count($tarif->_new_actes)}}
              {{mb_field object=$tarif field=secteur1 hidden=1}}
              {{mb_value object=$tarif field=secteur1}}
						{{else}}
	          	{{mb_field object=$tarif field=secteur1 onchange="Tarif.updateTotal();"}}
	          	<input type="hidden" name="_tarif" />
						{{/if}}
          </td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$tarif field=secteur2}}</th>
          <td>
            {{if count($tarif->_new_actes)}}
              <div id="force-recompute"  class="info" style="float: right; display: none;" onmouseover="ObjectTooltip.createDOM(this, 'force-recompute-info')">
							  {{tr}}Info{{/tr}}
							</div>
              <div id="force-recompute-info" class="small-info" style="display: none;">
                {{tr}}CTarif-_secteur1_uptodate-force{{/tr}}
              </div>
	          	{{mb_field object=$tarif field=secteur2 onchange="Tarif.updateTotal(); Tarif.forceRecompute();"}}
						{{else}}
	          	{{mb_field object=$tarif field=secteur2 onchange="Tarif.updateTotal();"}}
						{{/if}}
          </td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$tarif field=_somme}}</th>
          <td>
            {{if count($tarif->_new_actes)}}
	          	{{mb_field object=$tarif field=_somme readonly=1}}
						{{else}}
	            {{mb_field object=$tarif field=_somme onchange="Tarif.updateSecteur2();"}}
						{{/if}}
          
          </td>
        </tr>
        
        <tr>
          <td class="button" colspan="2">
            {{if $tarif->_id}}
	            <button name="save" class="modify" type="submit">{{tr}}Save{{/tr}}</button>

              {{if count($tarif->_new_actes) && !$tarif->_has_mto}}
              <input type="hidden" name="_add_mto" value="0" />
	            <button class="add" type="submit" onclick="$V(this.form._add_mto, '1');">
	            	{{tr}}Add{{/tr}} MTO
							</button>
              {{/if}}

              {{if count($tarif->_new_actes)}}
              <input type="hidden" name="_update_montants" value="0" />
              <button class="change" type="submit" onclick="$V(this.form._update_montants, '1');">
                {{tr}}Recompute{{/tr}}
              </button>
              {{/if}}

	            <button class="trash" type="button" onclick="confirmDeletion(this.form, { typeName: 'le tarif', objName: this.form.description.value } )">
	            	{{tr}}Delete{{/tr}}
							</button>
            {{else}}
            <button class="new" type="submit">{{tr}}Create{{/tr}}</button>
            {{/if}}
          </td>
        </tr>
        
      </table>
      
      </form>
      
      {{if $tarif->_id}}
	      {{if $tarif->_precode_ready}}
	      <div class="small-success">
	      	{{tr}}CTarif-_precode_ready-OK{{/tr}}
	      </div>
	      {{else}}
	      <div class="small-warning">
          {{tr}}CTarif-_precode_ready-KO{{/tr}}
	      </div>
				{{/if}}

        {{if !$tarif->_secteur1_uptodate}}
        <div class="small-warning">
          {{tr}}CTarif-_secteur1_uptodate-KO{{/tr}}
        </div>
			  {{/if}}

      {{else}}
      <div class="big-info">
        Pour cr�er un tarif contenant des codes CCAM et NGAP, effectuer une cotation r�elle
        pendant une consultation en trois �tapes :
        <ul>
          <li><em>Ajouter</em> des actes dans le volet <strong>Actes</strong></li>
          <li><em>Valider</em> la cotation dans le volet <strong>Docs. et R�glements</strong></li>
          <li><em>Cliquer</em> <strong>Nouveau tarif</strong> dans cette m�me section</li>
        </ul>
      </div>
			{{/if}}
    </td>
  </tr>
</table>