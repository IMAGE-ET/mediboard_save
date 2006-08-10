<script language="Javascript" type="text/javascript">
function checkPlage() {
  var form = document.editFrm;
  
  if (!checkForm(form)) {
    return false;
   }
    
  if (form.chir_id.value == 0 && form.id_spec.value == 0) {
    alert("Merci de choisir un chirurgien ou une sp�cialit�");
    form.chir_id.focus();
    return false;
  }
  
  if (form._heurefin.value < form._heuredeb.value || (form._heurefin.value == form._heuredeb.value && form._minutefin.value <= form._minutedeb.value)) {
    alert("L'heure de d�but doit �tre sup�rieure � la l'heure de fin");
    form._heurefin.focus();
    return false;
  }
  
  return true;
}

function pageMain() {
  regRedirectFlatCal("{{$date}}", "index.php?m={{$m}}&tab={{$tab}}&date=");
}

</script>

<table class="main">
  <tr>
    <td class="greedyPane">
      <table id="planningBloc">
        <tr>
          <th><strong>{{$date|date_format:"%a %d %b"}}</strong></th>
          {{foreach from=$listHours item=curr_hours}}
          <th colspan="4" class="heure">{{$curr_hours}}:00</th>
          {{/foreach}}         
        </tr>
        {{foreach from=$listSalles item=curr_salle key=keySalle}}
        <tr>
          <td class="salle">{{$curr_salle->nom}}</td>
          {{foreach from=$listHours item=curr_hour}}
          {{foreach from=$listMins item=curr_min key=keymin}}
            {{assign var="keyAff" value="$keySalle-$curr_hour:$curr_min"}}
            
            {{if is_string($arrayAffichage.$keyAff) &&  $arrayAffichage.$keyAff== "empty"}}
              <td class="empty{{if !$keymin}} firsthour{{/if}}"></td>
            {{elseif is_string($arrayAffichage.$keyAff) &&  $arrayAffichage.$keyAff== "full"}}
           
            {{else}}
              {{if $arrayAffichage.$keyAff->chir_id}}
                {{assign var=colorCell value=$arrayAffichage.$keyAff->_ref_chir->_ref_function->color}}
              {{else}}
                {{assign var=colorCell value=$arrayAffichage.$keyAff->_ref_spec->color}}
              {{/if}}
              <td nowrap="nowrap" style="text-align: center;white-space: normal;background-color:#{{$colorCell}};" colspan="{{$arrayAffichage.$keyAff->_nbQuartHeure}}">
                <strong>
                <a href="index.php?m=dPbloc&amp;tab=vw_edit_interventions&amp;id={{$arrayAffichage.$keyAff->id}}" target="_self">
                  {{$arrayAffichage.$keyAff->_view}}
                </a> ({{$arrayAffichage.$keyAff->_nb_operations}})
                <a href="index.php?m=dPbloc&amp;tab=vw_edit_planning&amp;id={{$arrayAffichage.$keyAff->id}}&amp;date={{$date}}">
                  <img src="./modules/dPbloc/images/edit.png" alt="editer la plage" border="0" height="16" width="16" />
                </a>
                </strong>
              </td>
            {{/if}}
           {{/foreach}}
          {{/foreach}}
        </tr>
        {{/foreach}} 
      </table>

      {{if $canEdit}}
      <form name='editFrm' action='?m={{$m}}' method='post' onsubmit='return checkPlage()'>
      <input type='hidden' name='dosql' value='do_plagesop_aed' />
      <input type='hidden' name='del' value='0' />
      <input type='hidden' name='id' value='{{$plagesel->id}}' />

      <table class="form">
        <tr>
          <th class="category" colspan="6">
            {{if $plagesel->id}}
            <a style="float:right;" href="javascript:view_log('CPlageOp',{{$plagesel->id}})">
              <img src="images/history.gif" alt="historique" />
            </a>
            Modifier la plage op�ratoire
            {{else}}
            Ajouter une plage op�ratoire
            {{/if}}
          </th>
        </tr>
        <tr>
         <th><label for="chir_id" title="Nom du chirurgien">Chirurgien</label></th>
         <td>
          <select name="chir_id" title="{{$plagesel->_props.chir_id}}">
            <option value="">&mdash; Choisir un chirurgien</option>
            <optgroup label="Chirurgiens">
            {{foreach from=$chirs item=chir}}
              <option value="{{$chir->user_id}}" {{if $plagesel->chir_id == $chir->user_id}} selected="selected" {{/if}} >
                {{$chir->_view}}
              </option>
            {{/foreach}}
            </optgroup>

            <optgroup label="Anesth�sistes">
            {{foreach from=$anesths item=anesth}}
              <option value="{{$anesth->user_id}}" {{if $plagesel->chir_id == $anesth->user_id}} selected="selected" {{/if}} >
                {{$anesth->_view}}
              </option>
            {{/foreach}}
            </optgroup>
          </select>
        </td>
        <th>
          <label for="id_salle" title="Nom de la salle. Obligatoire.">Salle</label>
        </th>
        <td>
          <select name="id_salle" title="{{$plagesel->_props.id_salle}}">
            <option value="">&mdash; Choisir une salle</option>
            {{foreach from=$listSalles item=salle}}
            <option value="{{$salle->id}}" {{if $plagesel->id_salle == $salle->id}} selected="selected"{{/if}} >
              {{$salle->nom}}
            </option>
            {{/foreach}}
          </select>
        </td>
        <th><label for="_heuredeb" title="Heure de d�but de la plage. Obligatoire">D�but</label></th>
        <td>
          <select name="_heuredeb" title="notNull|num">
          {{foreach from=$listHours item=heure}}
            <option {{if $plagesel->_heuredeb == $heure}} selected="selected" {{/if}} >
              {{$heure|string_format:"%02d"}}
            </option>
          {{/foreach}}
          </select>
          :
          <select name='_minutedeb'>
          {{foreach from=$listMins item=minute}}
            <option {{if $plagesel->_minutedeb == $minute}} selected="selected" {{/if}} >
              {{$minute|string_format:"%02d"}}
            </option>
          {{/foreach}}
          </select>
        </td>
      </tr>
      <tr>
        <th><label for="id_spec" title="Sp�cialit� associ�e � la plage op�ratoire">Sp�cialit�</label></th>
        <td>
          <select name="id_spec" title="{{$plagesel->_props.id_spec}}">
            <option value="">&mdash; Choisir une sp�cialit�</option>
            {{foreach from=$specs item=spec}}
              <option value="{{$spec->function_id}}" {{if $spec->function_id == $plagesel->id_spec}} selected="selected" {{/if}} >
                {{$spec->text}}
              </option>
            {{/foreach}}
          </select>
        </td>
        <th><label for="date" title="Date de la plage op�ratoire.">Date</label></th>
        <td class="date">
          {{if $plagesel->id}}
          <div id="editFrm_date_da">{{$plagesel->date|date_format:"%d/%m/%Y"}}</div>
          <input type="hidden" name="date" value="{{$plagesel->date}}" />
          {{else}}
          <div id="editFrm_date_da">{{$date|date_format:"%d/%m/%Y"}}</div>
          <input type="hidden" name="date" value="{{$date}}" />
          {{/if}}
          <!-- Possibilit� de changer la date ? -->
          <!--img id="editFrm_date_trigger" src="./images/calendar.gif" alt="calendar" title="Choisir une date"/-->
        </td>
    
        <th><label for="_heurefin" title="Heure de fin de la plage. Obligatoire">Fin</label></th>
        <td>
          <select name="_heurefin" title="notNull|num">
            {{foreach from=$listHours item=heure}}
            <option {{if $plagesel->_heurefin == $heure}} selected="selected" {{/if}} >
              {{$heure|string_format:"%02d"}}
            </option>
            {{/foreach}}
          </select>
          :
          <select name='_minutefin'>
            {{foreach from=$listMins item=minute}}
            <option {{if $plagesel->_minutefin == $minute}} selected="selected" {{/if}} >
              {{$minute|string_format:"%02d"}}
            </option>
            {{/foreach}}
          </select>
        </td>
      </tr>
      
      <tr>
        <th><label for="anest_id" title="Anesth�siste associ� � la plage op�ratoire">Anesth�siste</label></th>
        <td colspan="5">
          <select name="anesth_id">
            <option value="">&mdash; Choisir un anesth�siste</option>
            {{foreach from=$anesths item=anesth}}
            <option value="{{$anesth->user_id}}" {{if $plagesel->anesth_id == $anesth->user_id}} selected="selected" {{/if}} >
              {{$anesth->_view}}
            </option>
            {{/foreach}}
    	  </select>
        </td>
      </tr>
      
      <tr>
        <th>
          <label for="_repeat" title="Nombre de r�p�titions h�bdomadaire pour cette plage">Dur�e de r�p�tition</label>
        </th>
        <td>
          <input type="text" title="notNull|num|min|1" name="_repeat" size="1" value="1" />
          semaine(s)
        </td>
        <td colspan="4">
          <input type="checkbox" name="_double" />
          <label for="_double" title="Appliquer les r�p�titions une semaine sur deux">Une semaine sur deux</label>
        </td>
      </tr>
      <tr>
        <td class="button" colspan="6">
        {{if $plagesel->id}}
          <button type="submit" class="modify">Modifier</button>
        {{else}}
          <button type="submit" class="new">Ajouter</button>
        {{/if}}
        </td>
      </tr>
    </table>
    </form>

    {{if $plagesel->id}}
      <form name='removeFrm' action='?m={{$m}}' method='post'>
      <input type='hidden' name='dosql' value='do_plagesop_aed' />
      <input type='hidden' name='del' value='1' />
      <input type='hidden' name='id' value='{{$plagesel->id}}' /> 
      <table class="form">
        <tr>
          <th class="category" colspan="2">Supprimer la plage op�ratoire</th>
        </tr>  
        <tr>
          <th>Supprimer cette plage pendant</th> 
          <td><input type='text' name='_repeat' size="1" value='1' /> semaine(s)</td>
        </tr>   
        <tr>
          <td class="button" colspan="2">
            <button type="submit" class="trash">Supprimer</button>
          </td>
        </tr>
      </table>
      </form>
    {{/if}}
    {{/if}}
   </td>
   <td>
     {{include file="vw_idx_planning.tpl"}}
   </td>
  </tr>
</table>