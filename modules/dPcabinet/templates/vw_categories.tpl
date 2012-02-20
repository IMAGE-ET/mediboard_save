{{mb_script module="dPcabinet" script="icone_selector"}}

<table class="main">
  <tr>
    <td>
      <form name="choixCabinet" action="?m={{$m}}" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <select name="selCabinet" onchange="submit()">
        <option value="">&mdash; Choix du cabinet</option>
        {{foreach from=$listFunctions item="cabinet"}}
        <option class="mediuser" style="border-color: #{{$cabinet->color}}" value="{{$cabinet->_id}}" {{if $selCabinet == $cabinet->_id}}selected=selected{{/if}}>{{$cabinet->_view}}</option>
        {{/foreach}}
      </select>
      </form>
    </td>
  </tr>
  
  {{if $selCabinet && $droit}}
  <tr>
    <td class="halfPane">
    <a href="?m={{$m}}&amp;tab={{$tab}}&amp;categorie_id=0" class="button new">
    Cr�er une cat�gorie
    </a>
    <table class="tbl">
      <tr>
        <th colspan="3">Liste des cat�gories du cabinet</th>
    </tr>
    <tr>
      <th>Cat�gorie</th>
      <th>Icone</th>
    </tr>
    {{foreach from=$categories item=_categorie}}
    <tr {{if $_categorie->_id == $categorie->_id}}class="selected"{{/if}}>
      <td><a href="?m={{$m}}&amp;tab={{$tab}}&amp;categorie_id={{$_categorie->_id}}">{{$_categorie->nom_categorie}}</a></td>
      <td><img src="./modules/dPcabinet/images/categories/{{$_categorie->nom_icone}}" /></td>
    </tr>
    {{/foreach}}
    </table>
  </td> 
  
  <td class="halfPane">
    <form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_categorie_aed" />
    <input type="hidden" name="categorie_id" value="{{$categorie->_id}}" />
    <input type="hidden" name="function_id" value="{{$selCabinet}}" />
    <input type="hidden" name="del" value="0" />
    <table class="form">
      <tr>
      {{if $categorie->_id}}
        <th class="title modify" colspan="2">
          
          {{mb_include module=system template=inc_object_idsante400 object=$categorie}}
          {{mb_include module=system template=inc_object_history object=$categorie}}
          
          Modification de la cat�gorie &lsquo;{{$categorie->nom_categorie}}&rsquo;
        </th>
        {{else}}
        <th class="title" colspan="2">
          Cr�ation d'une cat�gorie
        </th>
        {{/if}}
      </tr>
      <tr>
        <th>{{mb_label object=$categorie field="nom_categorie"}}</th>
        <td>{{mb_field object=$categorie field="nom_categorie"}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$categorie field="nom_icone"}}</th>
        <td>
        {{if $categorie->_id}}
         <img id="iconeBackground" onclick="IconeSelector.init()" src="./modules/dPcabinet/images/categories/{{$categorie->nom_icone}}" />
        {{else}}
          <img id="iconeBackground" src="images/icons/search.png" onclick="IconeSelector.init()" />
         {{/if}}
         <input type="hidden" name="nom_icone" value="{{$categorie->nom_icone}}"  class="notNull" />
         <script type="text/javascript">
            IconeSelector.init = function(){
              this.sForm = "editFrm";
              this.sView = "nom_icone";
              this.pop();
            }
         </script>
       </td>
      </tr>
      <tr>
        <th>{{mb_label object=$categorie field="duree"}}</th>
        <td>
          <select name="duree">
            <option value="1" {{if $categorie->duree == 1}} selected="selected" {{/if}}>x1</option>
            <option value="2" {{if $categorie->duree == 2}} selected="selected" {{/if}}>x2</option>
            <option value="3" {{if $categorie->duree == 3}} selected="selected" {{/if}}>x3</option>
            <option value="4" {{if $categorie->duree == 4}} selected="selected" {{/if}}>x4</option>
            <option value="5" {{if $categorie->duree == 5}} selected="selected" {{/if}}>x5</option>
            <option value="6" {{if $categorie->duree == 6}} selected="selected" {{/if}}>x6</option>
          </select>
        </td>
      </tr>
      <tr>
        <th>{{mb_label object=$categorie field="commentaire"}}</th>
        <td>{{mb_field object=$categorie field="commentaire" form="editFrm"}}</td>
      </tr>
      <tr>
        <td class="button" colspan="2">
          {{if $categorie->_id}}
          <button class="modify" type="submit">Valider</button>
          <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la cat�gorie ',objName:'{{$categorie->nom_categorie|smarty:nodefaults|JSAttribute}}'})">
            Supprimer
          </button>
          {{else}}
          <button class="submit" name="btnFuseAction" type="submit">Cr�er</button>
          {{/if}}
        </td>
      </tr>
    </table>   
      </form>
    </td>  
  </tr>
  {{/if}}
</table>