  <tr>
    <td class="halfPane">
      <a class="buttonnew" href="index.php?m={{$m}}&amp;tab=vw_edit_plats&amp;typerepas_id=0">
        Cr�er un nouveau type de repas
      </a>
      <table class="tbl">
        <tr>
          <th>Nom</th>
          <th>D�but</th>
          <th>Fin</th>
        </tr>
        {{foreach from=$listTypeRepas item=curr_type}}
        <tr>
          <td>
            <a href="index.php?m={{$m}}&amp;tab=vw_edit_plats&amp;typerepas_id={{$curr_type->typerepas_id}}" title="Modifier le type de plat">
              {{$curr_type->nom}}
            </a>
          </td>
          <td>
            <a href="index.php?m={{$m}}&amp;tab=vw_edit_plats&amp;typerepas_id={{$curr_type->typerepas_id}}" title="Modifier le type de plat">
              {{$curr_type->debut|date_format:"%Hh%M"}}
            </a>
          </td>
          <td>
            <a href="index.php?m={{$m}}&amp;tab=vw_edit_plats&amp;typerepas_id={{$curr_type->typerepas_id}}" title="Modifier le type de plat">
              {{$curr_type->fin|date_format:"%Hh%M"}}
            </a>
          </td>
        </tr>
        {{/foreach}}
      </table>  
    </td>
    <td class="halfPane">
      <form name="editTypeRepas" action="./index.php?m={{$m}}&amp;tab=vw_edit_plats" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="m" value="dPrepas" />
      <input type="hidden" name="dosql" value="do_typerepas_aed" />
	  <input type="hidden" name="typerepas_id" value="{{$typeRepas->typerepas_id}}" />
	  <input type="hidden" name="group_id" value="{{if $typeRepas->typerepas_id}}{{$typeRepas->group_id}}{{else}}{{$g}}{{/if}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
      <table class="form">
        <tr>
          {{if $typeRepas->typerepas_id}}
          <th class="title modify" colspan="2">Modification du type de repas {{$typeRepas->_view}}</th>
          {{else}}
          <th class="title" colspan="2">Cr�ation d'un type de repas</th>
          {{/if}}
        </tr>
        <tr>
          <th><label for="nom" title="Nom du type de plat, obligatoire">Nom</label></th>
          <td><input name="nom" title="{{$typeRepas->_props.nom}}" type="text" value="{{$typeRepas->nom}}" /></td>
        </tr>
        <tr>
          <th><label for="_debut" title="Heure de d�but">D�but</label></th>
          <td>
            {{html_options name="_debut" options=$listHours title="num" selected=$typeRepas->_debut}}
            h
          </td>
        </tr>
        <tr>
          <th><label for="_fin" title="Heure de fin">Fin</label></th>
          <td>
            {{html_options name="_fin" options=$listHours title="num moreThan|_debut" selected=$typeRepas->_fin}}
            h
          </td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            {{if $typeRepas->typerepas_id}}
              <button class="submit" type="modify">Modifier</button>
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le type de repas',objName:'{{$typeRepas->_view|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
            {{else}}
              <button class="submit" type="submit">Cr�er</button>
            {{/if}}
          </td>
        </tr>  
      </table>
      </form>
    </td>
  </tr>