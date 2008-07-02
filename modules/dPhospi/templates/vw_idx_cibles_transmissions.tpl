<table class="main">
  <tr>
    <td class="halfPane">
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;categorie_cible_transmission_id=0" class="buttonnew">
        Cr�er une cat�gorie de cibles
      </a>
      <!-- Liste des cibles -->
      <table class="tbl">
        <tr>
          <th colspan="2">Liste des cat�gories de cible</th>
        </tr>
        <tr>
          <th>Intitul�</th>
          <th>Cibles</th>
        </tr>
		    {{foreach from=$categories item=curr_cat}}
        <tr {{if $curr_cat->_id == $categorie->_id}}class="selected"{{/if}}>
          <td>
            <a href="?m={{$m}}&amp;tab={{$tab}}&amp;categorie_cible_transmission_id={{$curr_cat->_id}}">
              {{$curr_cat->_view}}
            </a>
          </td>
          <td>
            {{$curr_cat->_back.cibles|@count}}
          </td>
        </tr>
        {{/foreach}}
      </table>
    </td> 
    <td class="halfPane">
      <!-- Formulaire pour les cat�gories -->
      <form name="editCategorie" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

      <input type="hidden" name="dosql" value="do_categorie_cible_aed" />
      <input type="hidden" name="categorie_cible_transmission_id" value="{{$categorie->_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {{if $categorie->_id}}
          <th class="title modify" colspan="2">
            <a style="float:right;" href="#" onclick="view_log('{{$categorie->_class_name}}',{{$categorie->_id}})">
              <img src="images/icons/history.gif" alt="historique" />
            </a>
            Modification de la cat�gorie &lsquo;{{$categorie->_view}}&rsquo;
          </th>
          {{else}}
          <th class="title" colspan="2">
            Cr�ation d'une categorie
          </th>
          {{/if}}
        </tr>
        <tr>
          <th>{{mb_label object=$categorie field="libelle"}}</th>
          <td>{{mb_field object=$categorie field="libelle"}}</td>
        </tr>
          <th>{{mb_label object=$categorie field="description"}}</th>
          <td>{{mb_field object=$categorie field="description"}}</td>
        </tr>    
        <tr>
          <td class="button" colspan="2">
            {{if $categorie->_id}}
            <button class="modify" type="submit">Valider</button>
            <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la cat�gorie ',objName:'{{$categorie->libelle|smarty:nodefaults|JSAttribute}}'})">
              Supprimer
            </button>
            {{else}}
            <button class="submit" type="submit">Cr�er</button>
            {{/if}}
          </td>
        </tr>
      </table> 
      </form>
      
      {{if $categorie->_id}}
      
      <!-- Formulaire pour les cibles -->
      {{if $cible->_id}}
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;cible_transmission_id=0" class="buttonnew">
        Cr�er une cible
      </a>
      {{/if}}
      <form name="editCible" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

      <input type="hidden" name="dosql" value="do_cible_aed" />
      <input type="hidden" name="cible_transmission_id" value="{{$cible->_id}}" />
      <input type="hidden" name="categorie_cible_transmission_id" value="{{$categorie->_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        {{if $cible->_id}}
        <th class="title modify" colspan="2">
          <a style="float:right;" href="#" onclick="view_log('{{$cible->_class_name}}',{{$cible->_id}})">
            <img src="images/icons/history.gif" alt="historique" />
          </a>
          Modification de la cible &lsquo;{{$cible->_view}}&rsquo;
        </th>
        {{else}}
        <th class="title" colspan="2">
          Cr�ation d'une cible dans &lsquo;{{$categorie->_view}}&rsquo;
        </th>
        {{/if}}
        <tr>
          <th>{{mb_label object=$cible field="libelle"}}</th>
          <td>{{mb_field object=$cible field="libelle"}}</td>
        </tr>
          <th>{{mb_label object=$cible field="description"}}</th>
          <td>{{mb_field object=$cible field="description"}}</td>
        </tr>    
        <tr>
          <td class="button" colspan="2">
            {{if $cible->_id}}
            <button class="modify" type="submit">Valider</button>
            <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la cible ',objName:'{{$cible->libelle|smarty:nodefaults|JSAttribute}}'})">
              Supprimer
            </button>
            {{else}}
            <button class="submit" type="submit">Cr�er</button>
            {{/if}}
          </td>
        </tr>
      </table>
      </form>
      
      <table class="tbl">
        <tr>
          <th class="title" colspan="2">
            Liste des cibles
          </th>
        </tr>
        {{foreach from=$categorie->_back.cibles item=curr_cible}}
        <tr>
          <td colspan="2">
            <a href="?m={{$m}}&amp;tab={{$tab}}&amp;cible_transmission_id={{$curr_cible->_id}}">
              {{$curr_cible->_view}}
            </a>
          </td>
        </tr>
        {{/foreach}}
      </table>
      
      {{/if}}  
    </td>
  </tr>
</table>
