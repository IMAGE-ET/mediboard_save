{{mb_script module="dPcabinet" script="icone_selector"}}

<table class="main">
  <tr>
    <td>
      <form  name="choixCabinet" action="?m={{$m}}" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <select name="selCabinet" onchange="submit()">
        <option  value="">&mdash; Choix du cabinet</option>
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
    <a id="vw_categorie_a_button_new" href="?m={{$m}}&amp;tab={{$tab}}&amp;categorie_id=0" class="button new">
    Cr�er une cat�gorie
    </a>
    <table id="vw_categorie_table_liste_categories" class="tbl">
      <tr>
        <th colspan="3">Liste des cat�gories du cabinet</th>
    </tr>
    <tr>
      <th>Cat�gorie</th>
      <th>Icone</th>
      <th class="narrow">Dur�e</th>
    </tr>
    {{foreach from=$categories item=_categorie}}
    <tr {{if $_categorie->_id == $categorie->_id}}class="selected"{{/if}}>
      <td><a href="?m={{$m}}&amp;tab={{$tab}}&amp;categorie_id={{$_categorie->_id}}">{{$_categorie->nom_categorie|spancate}}</a></td>
      <td>
        {{mb_include module=cabinet template=inc_icone_categorie_consult 
          categorie=$_categorie
        }}
      </td>
      <td>x{{$_categorie->duree}}</td>
    </tr>
    {{foreachelse}}
    <tr>
      <td class="empty" colspan="3">{{tr}}CConsultationCategorie.none{{/tr}}</td>
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
          
          Modification de la cat�gorie &lsquo;{{$categorie->nom_categorie|spancate:35}}&rsquo;
        </th>
        {{else}}
        <th class="title" colspan="2">
          Cr�ation d'une cat�gorie
        </th>
        {{/if}}
      </tr>
      <tr>
        <th>{{mb_label object=$categorie field="nom_categorie"}}</th>
        <td >{{mb_field object=$categorie field="nom_categorie"}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$categorie field="nom_icone"}}</th>
        <td>
        {{if $categorie->_id}}
          {{mb_include module=cabinet template=inc_icone_categorie_consult 
            categorie=$categorie 
            id="iconeBackground" 
            onclick="IconeSelector.init()"
          }}
        {{else}}
          <img style="cursor:pointer" id="iconeBackground" src="images/icons/search.png" onclick="IconeSelector.init()"/>
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
        <td id="vw_categorie_td_choix_duree">
          {{foreach from=1|range:15 item=i}}
            <label>
              <input type="radio" value="{{$i}}" name="duree" {{if $categorie->duree == $i}}checked{{/if}}>x{{$i}}
            </label>
          {{/foreach}}
        </td>
      </tr>
      <tr>
        <th>{{mb_label object=$categorie field="commentaire"}}</th>
        <td id="vw_categorie_td_commentaires">{{mb_field object=$categorie field="commentaire" form="editFrm"}}</td>
      </tr>
      <tr>
        <td class="button" colspan="2">
          {{if $categorie->_id}}
          <button id="vw_categorie_button_modif_categorie" class="modify" type="submit">Valider</button>
          <button id="vw_categorie_button_trash_categorie" class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la cat�gorie ',objName:'{{$categorie->nom_categorie|smarty:nodefaults|JSAttribute}}'})">
            Supprimer
          </button>
          {{else}}
          <button id="vw_categorie_button_create_categorie" class="submit" name="btnFuseAction" type="submit">Cr�er</button>
          {{/if}}
        </td>
      </tr>
    </table>   
      </form>
    </td>  
  </tr>
  {{/if}}
</table>