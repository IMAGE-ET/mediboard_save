<table class="main">
  <tr>
    <td class="halfPane">
      <a class="buttonnew" href="?m=mediusers&amp;tab=vw_idx_disciplines&amp;discipline_id=0">
        Ajouter une nouvelle sp�cialit� m�dicale
      </a>
      <table class="tbl">
        <tr>
          <th>Sp�cialit� M�dicale</th>
          <th>Cat�gorie</th>
          <th>
            Praticiens
            <br />
            ({{$group->_view}})
          </th>
        </tr>
        {{foreach from=$listDiscipline item=curr_discipline}}
        <tr>
          <td class="text">
            <a href="?m=mediusers&amp;tab=vw_idx_disciplines&amp;discipline_id={{$curr_discipline->discipline_id}}" title="Modifier la sp�cialit�">
              {{$curr_discipline->_view}}
            </a>
          </td>
          <td>
            <a href="?m=mediusers&amp;tab=vw_idx_disciplines&amp;discipline_id={{$curr_discipline->discipline_id}}" title="Modifier la sp�cialit�">
              {{$curr_discipline->categorie}}
            </a>
          </td>
          <td>
            {{$curr_discipline->_ref_users|@count}}
          </td>      
        </tr>
        {{/foreach}}
      </table>
    </td>
    <td class="halfPane">
      {{if $can->edit}}
      <form name="editSpeMed" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_discipline_aed" />
      {{mb_field object=$specialite field="discipline_id" hidden=1 prop=""}}
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {{if $specialite->discipline_id}}
          <th class="title modify" colspan="2">{{$specialite->_view}}</th>
          {{else}}
          <th class="title" colspan="2">Cr�ation d'une sp�cialit� M�dicale</th>
          {{/if}}
        </tr>
        <tr>
          <th>{{mb_label object=$specialite field="text"}}</th>
          <td>{{mb_field object=$specialite field="text"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$specialite field="categorie"}}</th>
          <td>{{mb_field object=$specialite field="categorie" defaultOption="&mdash;"}}</td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            {{if $specialite->discipline_id}}
              <button class="modify" type="submit">Modifier</button>
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la sp�cialit� m�dicale',objName:'{{$specialite->_view|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
            {{else}}
              <button class="submit" type="submit">Cr�er</button>
            {{/if}}
          </td>
        </tr> 
      </table>
      </form>
      {{/if}}
      {{if $specialite->discipline_id}}
      <table class="tbl">
        <tr>
          <th class="title" colspan="4">Utilisateur(s) correspondant(s)</th>
        </tr>
        <tr>
           <th>Nom Pr�nom</th>
         </tr>
         {{foreach from=$specialite->_ref_users item=curr_user}}
         <tr>
           <td class="text"><a href="?m=mediusers&amp;tab=vw_idx_mediusers&amp;user_id={{$curr_user->user_id}}" title="Modifier cet utilisateur">Dr {{$curr_user->_view}}</a></td>
         </tr>
         {{foreachelse}}
         <tr>
           <td class="button" colspan="4">Aucun utilisateur trouv�</td>
         </tr>
         {{/foreach}}
       </table>
       {{/if}}      
    </td>
  </tr>
</table>