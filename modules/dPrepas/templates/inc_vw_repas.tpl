<table class="tbl">
{{if $service_id}}
{{foreach from=$service->_ref_chambres item=curr_chambre}}
  {{foreach from=$curr_chambre->_ref_lits item=curr_lit}}
    {{foreach from=$curr_lit->_ref_affectations item=curr_affect}}
      {{assign var="repas" value=$curr_affect->_list_repas.$date.$type}}
      <tr id="affect{{$curr_affect->_id}}-trigger">
        <th class="category">
        <div style="float:right">
          {{if $repas->_is_modif}}
            <em>{{$repas->_ref_menu->nom}}</em>
          {{else}}
            {{$repas->_ref_menu->nom}}
          {{/if}}
        </div>
        Chambre {{$curr_chambre->_view}} - {{$curr_lit->_view}}
        </th>
      </tr>
      {{if $repas->_ref_menu->menu_id}}
      <tbody class="effectChambre" id="affect{{$curr_affect->_id}}" style="display:none;">
      <tr>
        <td>
          {{foreach from=$plat->_enums.type item=curr_typePlat}}
            {{if $repas->$curr_typePlat}}
              {{assign var="ref" value=_ref_$curr_typePlat}}
              <em>{{$repas->$ref->nom}}</em><br />
            {{elseif $repas->_ref_menu->$curr_typePlat}}
              {{$repas->_ref_menu->$curr_typePlat}}<br />
            {{/if}}
          {{/foreach}}
        </td>
      </tr>
      </tbody>
      {{/if}}
    {{/foreach}}
  {{/foreach}}
{{foreachelse}}
<tr>
  <th class="category">Pas de repas pr�vu dans ce service</th>
</tr>
{{/foreach}}
{{else}}
<tr>
  <th class="category">Veuillez s�lectionner un service</th>
</tr>
{{/if}}
</table>

<script type="text/javascript">
PairEffect.initGroup("effectChambre", { sEffect: "appear"});
</script>