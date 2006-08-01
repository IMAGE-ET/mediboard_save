<table class="main">
  <tr>
    <td class="halfPane">
      <a class="buttonnew" href="index.php?m=mediusers&amp;tab=vw_idx_disciplines&amp;discipline_id=0">
        Ajouter une nouvelle sp�cialit� m�dicale
      </a>
      <table class="tbl">
        <tr>
          <th>id</th>
          <th>Sp�cialit� M�dicale</th>
        </tr>
        {{foreach from=$listDiscipline item=curr_discipline}}
        <tr>
          <td>
            <a href="index.php?m=mediusers&amp;tab=vw_idx_disciplines&amp;discipline_id={{$curr_discipline->discipline_id}}" title="Modifier la sp�cialit�">
              {{$curr_discipline->discipline_id}}
            </a>
          </td>
          <td class="text">
            <a href="index.php?m=mediusers&amp;tab=vw_idx_disciplines&amp;discipline_id={{$curr_discipline->discipline_id}}" title="Modifier la sp�cialit�">
              {{$curr_discipline->text}}
            </a>
          </td>        
        </tr>
        {{/foreach}}
      </table>
    </td>
    <td class="halfPane">
      {{if $canEdit}}
      <form name="editSpeMed" action="./index.php?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_disciplines_aed" />
	  <input type="hidden" name="discipline_id" value="{{$specialite->discipline_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {{if $specialite->discipline_id}}
          <th class="title" colspan="2" style="color:#f00;">{{$specialite->_view}}</th>
          {{else}}
          <th class="title" colspan="2">Cr�ation d'une sp�cialit� M�dicale</th>
          {{/if}}
        </tr>
        <tr>
          <th><label for="text" title="Veuillez saisir le nom d'une sp�cialit� m�dicale, obligatoire">Sp�cialit� M�dicale</label></th>
          <td><input name="text" size="60" title="{{$specialite->_props.text}}" type="text" value="{{$specialite->text}}" /></td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            {{if $specialite->discipline_id}}
              <button class="modify" type="submit">Modifier</button>
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la sp�cialit� m�dicale',objName:'{{$fournisseur->_view|escape:javascript}}'})">Supprimer</button>
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
           <td class="text">Dr. {{$curr_user->_view}}</td>
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