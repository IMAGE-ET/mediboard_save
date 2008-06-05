<table class="tbl">
  <tr>
    <th class="title" colspan="5">Sortie {{tr}}CSejour.type.{{$mode}}{{/tr}}</th>
  </tr>
  <tr>
    <th>Effectuer la sortie</th>
    
    <th>
    {{mb_colonne class="CSejour" field="_nomPatient" order_col=$order_col order_way=$order_way url="?m=$m&tab=vw_idx_sortie&date=$date&vue=$vue"}}
    </th>
    
    <th>
    {{mb_colonne class="CSejour" field="sortie_prevue" order_col=$order_col order_way=$order_way url="?m=$m&tab=vw_idx_sortie&date=$date&vue=$vue"}}
    </th>
    
    <th>
    {{mb_colonne class="CSejour" field="_nomPraticien" order_col=$order_col order_way=$order_way url="?m=$m&tab=vw_idx_sortie&date=$date&vue=$vue"}}
    </th>

    <th>Chambre</th>
  </tr>
  {{foreach from=$listSejour item=curr_sortie}}
  <tr>
    <td style="{{if !$curr_sortie->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
      <form name="editFrm{{$curr_sortie->_id}}" action="?m={{$m}}" method="post">
      <input type="hidden" name="m" value="dPplanningOp" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_sejour_aed" />
      <input type="hidden" name="sejour_id" value="{{$curr_sortie->_id}}" />
      
      {{if $curr_sortie->sortie_reelle}}
      <input type="hidden" name="mode_sortie" value="{{$curr_sortie->mode_sortie}}" />
      <input type="hidden" name="etablissement_transfert_id" value="{{$curr_sortie->etablissement_transfert_id}}" />
      <input type="hidden" name="_modifier_sortie" value="0" />
      <button class="cancel" type="button" onclick="submitSortie(this.form,'{{$mode}}')">
        Annuler la sortie
      </button>
      <br />
      {{if ($curr_sortie->sortie_reelle < $date_min) || ($curr_sortie->sortie_reelle > $date_max)}}
        {{$curr_sortie->sortie_reelle|date_format:"%d/%m/%Y � %Hh%M"}}
      {{else}}
        {{$curr_sortie->sortie_reelle|date_format:"%Hh%M"}}
      {{/if}}
      / 
      {{tr}}CSejour.mode_sortie.{{$curr_sortie->mode_sortie}}{{/tr}}
      {{if $curr_sortie->etablissement_transfert_id}}
        <br />{{$curr_sortie->_ref_etabExterne->_view}}
      {{/if}}
      {{else}}
      <input type="hidden" name="_modifier_sortie" value="1" />
      <input type="hidden" name="entree_reelle" value="{{$curr_sortie->entree_reelle}}" />
      <button class="tick" type="button" onclick="confirmation('{{$date_actuelle}}', '{{$date_demain}}', '{{$curr_sortie->sortie_prevue}}', '{{$curr_sortie->entree_reelle}}', this.form, '{{$mode}}');">
        Effectuer la sortie
      </button>
      <br />
      <button class="tick notext" type="button" onclick="this.form._modifier_sortie.value = '0'; submitSortie(this.form, '{{$mode}}');">
        Valider le mode de transfert
      </button>     
      {{mb_field object=$curr_sortie field="mode_sortie" onchange="loadTransfert(this.form)"}}
      <br />
      <div id="listEtabExterne-editFrm{{$curr_sortie->_id}}" style="display: inline;"></div>
      <script type="text/javascript">
        loadTransfert(document.editFrm{{$curr_sortie->_id}});
      </script>
      {{/if}}
    
      </form>
    </td>
    <td class="text" style="{{if !$curr_sortie->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
	  <a class="action" style="float: right"  title="Modifier le dossier administratif" href="?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id={{$curr_sortie->_ref_patient->patient_id}}">
        <img src="images/icons/edit.png" alt="modifier" />
 	  </a>
      {{if $canPlanningOp->read}}
        <a class="action" style="float: right"  title="Modifier le s�jour" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$curr_sortie->_id}}">
        <img src="images/icons/planning.png" alt="modifier" />
        </a>
      {{/if}}
	  {{if $curr_sortie->_num_dossier}}[{{$curr_sortie->_num_dossier}}]{{/if}}
      <b>{{$curr_sortie->_ref_patient->_view}}</b>

    </td>
    <td style="{{if !$curr_sortie->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
      {{$curr_sortie->sortie_prevue|date_format:"%Hh%M"}}
      {{if $curr_sortie->_ref_last_affectation->confirme}}
        <img src="images/icons/tick.png" alt="Sortie confirm�e par le praticien" />
      {{/if}}
        
    </td>
    <td class="text" style="{{if !$curr_sortie->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">Dr. {{$curr_sortie->_ref_praticien->_view}}</td>
    <td class="text" style="{{if !$curr_sortie->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
      
      {{foreach from=$curr_sortie->_ref_affectations item="affectation"}}
        {{if $affectation->effectue}}
          <div style="display: inline;" class="effectue">{{$affectation->_ref_lit->_view}}</div>
        {{else}}
          {{$affectation->_ref_lit->_view}}
        {{/if}}
        <br />
      {{/foreach}}
      
      {{if !$curr_sortie->_ref_affectations|@count}}
        Aucune chambre
      {{/if}}
         
    </td>
  </tr>
  {{/foreach}}
</table>