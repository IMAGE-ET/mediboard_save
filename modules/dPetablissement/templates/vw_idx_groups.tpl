<table class="main">
  <tr>
    <td class="halfPane">
      <a href="index.php?m={{$m}}&amp;tab={{$tab}}&amp;group_id=0" class="buttonnew">
        Cr�er un �tablissement
      </a>
      <table class="tbl">
        <tr>
          <th>liste des �tablissements</th>
          <th>Fonctions associ�es</th>
        </tr>
        {{foreach from=$listGroups item=curr_group}}
        <tr>
          <td>
            <a href="index.php?m={{$m}}&amp;tab={{$tab}}&amp;group_id={{$curr_group->group_id}}">
              {{$curr_group->text}}
            </a>
          </td>
          <td>
            <a href="index.php?m={{$m}}&amp;tab={{$tab}}&amp;group_id={{$curr_group->group_id}}">
              {{$curr_group->_ref_functions|@count}}
            </a>
          </td>
        </tr>
        {{/foreach}}
      </table>
    </td>
    <td class="halfPane">
      <form name="group" action="./index.php?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_groups_aed" />
	  <input type="hidden" name="group_id" value="{{$usergroup->group_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          <th class="category" colspan="2">
          {{if $usergroup->group_id}}
            <a style="float:right;" href="javascript:view_log('CGroups',{{$usergroup->group_id}})">
              <img src="images/history.gif" alt="historique" />
            </a>
            Modification de l'�tablissement &lsquo;{{$usergroup->text}}&rsquo;
          {{else}}
            Cr�ation d'un �tablissement
          {{/if}}
          </th>
        </tr>
        <tr>
          <th>
            <label for="text" title="intitul� de l'�tablissement, obligatoire.">Intitul�</label>
          </th>
          <td>
            <input type="text" title="{{$usergroup->_props.text}}" name="text" size="30" id="group_text" value="{{$usergroup->text}}" />
          </td>
        </tr>
        <tr>
          <td class="button" colspan="2">
          {{if $usergroup->group_id}}
            <button class="modify" type="submit">Valider</button>
            <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'l\'�tablissement,objName:'{{$usergroup->text|escape:javascript}}'})">
              Supprimer
            </button>
          {{else}}
            <button class="submit" type="submit" name="btnFuseAction">Cr�er</button>
          {{/if}}
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
</table>