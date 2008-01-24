        <tr>
          <th>
            <a href="?m=dPbloc&amp;tab=vw_edit_planning&amp;date={{$curr_day}}">
              <strong>{{$curr_day|date_format:"%a %d %b"}}</strong>
            </a>
          </th>
          {{foreach from=$listHours|smarty:nodefaults item=curr_hours}}
          <th colspan="4" class="heure">{{$curr_hours}}:00</th>
          {{/foreach}}
        </tr>
        {{foreach from=$listSalles item=curr_salle key=keySalle}}
        <tr>
          <td class="salle">{{$curr_salle->nom}}</td>
          {{foreach from=$listHours|smarty:nodefaults item=curr_hour}}
          {{foreach from=$listMins|smarty:nodefaults item=curr_min key=keymin}}
            {{assign var="keyAff" value="$curr_day-s$keySalle-$curr_hour:$curr_min:00"}}
            {{assign var=affichage value=$affichages.$keyAff}}
            
            {{if $affichage === "empty"}}
              <td class="empty{{if !$keymin}} firsthour{{/if}}"></td>
            {{elseif $affichage === "full"}}
           
            {{else}}
            {{assign var=_listPlages value=$listPlages.$curr_day}}
            {{assign var=plage value=$_listPlages.$affichage}}
         
              {{mb_ternary var=colorCell test=$plage->chir_id value=$plage->_ref_chir->_ref_function->color other=$plage->_ref_spec->color}}
             
              {{assign var="pct" value=$plage->_fill_rate}}
              {{if $pct gt 100}}
              {{assign var="pct" value=100}}
              {{/if}}
              {{if $pct lt 50}}{{assign var="backgroundClass" value="empty"}}
              {{elseif $pct lt 90}}{{assign var="backgroundClass" value="normal"}}
              {{elseif $pct lt 100}}{{assign var="backgroundClass" value="booked"}}
              {{else}}{{assign var="backgroundClass" value="full"}}
              {{/if}}
              <td nowrap="nowrap" style="vertical-align: top; text-align: center;white-space: normal;background-color:#{{$colorCell}};" colspan="{{$plage->_nbQuartHeure}}" title="{{$plage->_fill_rate}} % du temps occup�">
                <div class="progressBar" style="height: 3px;">
                  <div class="bar {{$backgroundClass}}" style="width: {{$pct}}%;height: 3px;border-right: 2px solid #000;">
                  </div>
                </div>
                <strong>
                <a href="?m=dPbloc&amp;tab=vw_edit_interventions&amp;plageop_id={{$plage->plageop_id}}" title="Agencer les interventions">
                  {{$plage->_view}}
                </a> ({{$plage->_nb_operations_placees}}/{{$plage->_nb_operations}})
                </strong>
                <a href="?m=dPbloc&amp;tab=vw_edit_planning&amp;plageop_id={{$plage->plageop_id}}&amp;date={{$curr_day}}">
                  <img src="images/icons/edit.png" alt="Editer la plage" title="Editer la plage" border="0" height="16" width="16" />
                </a>
                {{if ($plage->_ref_affectations_personnel.op|@count) || ($plage->_ref_affectations_personnel.op_panseuse|@count)}}
                  <img src="images/icons/personnel.png" alt="personnel" title="Liste du personnel pr�vu" border="0" height="16" width="16" />
                {{/if}}
              </td>
            {{/if}}
           {{/foreach}}
          {{/foreach}}
        </tr>
        {{/foreach}} 