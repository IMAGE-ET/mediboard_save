{literal}
<script type="text/javascript">
function pageMain() {
  toggleFunction({/literal}{$mediuserSel->function_id}{literal});
}

function collapseFunctions() {
  var trs = getElementsByClassName("tr", "function", false);
  
  var trsIt = 0;
  while (tr = trs[trsIt++]) {
    tr.style.display = "none";
  }
  
  var imgs = getElementsByClassName("img", "action");

  imgIt = 0;
  while (img = imgs[imgIt++]) {
    img.src = img.src.replace(/collapse/, "expand");
  }
}

function expandFunctions() {
  var trs = getElementsByClassName("tr", "function", false);
  
  var trsIt = 0;
  while (tr = trs[trsIt++]) {
    tr.style.display = "";
  }
  
  var imgs = getElementsByClassName("img", "action");
  
  imgIt = 0;
  while (img = imgs[imgIt++]) {
    img.src = img.src.replace(/expand/, "collapse");
  }
}

function toggleFunction(function_id) {
  var trs = getElementsByClassName("tr", "function" + function_id, true);
  
  var trsIt = 0;
  while (tr = trs[trsIt++]) {
    tr.style.display = tr.style.display == "none" ? "" : "none";
  }
  
  var img = document.getElementById("function" + function_id);
  if (img.src.indexOf("expand") != -1) {
    img.src = img.src.replace(/expand/, "collapse");
  } else {
    img.src = img.src.replace(/collapse/, "expand");
  }
}

</script>
{/literal}

<table class="main">
  <tr>
    <td class="greedyPane">
      <a href="index.php?m={$m}&amp;tab={$tab}&amp;user_id=0"><strong>Cr�er un utilisateur</strong></a>
      <table class="tbl">
        <tr>
          <th style="width: 32px;">
            <img src="modules/{$m}/images/collapse.gif" onclick="collapseFunctions()" alt="r�duire" />
            <img src="modules/{$m}/images/expand.gif"  onclick="expandFunctions()" alt="agrandir" />
          </th>
          <th>Utilisateur</th>
          <th>Nom</th>
          <th>Pr�nom</th>
          <th>Type</th>
        </tr>
        {foreach from=$functions item=curr_function}
        <tr>
          <td style="background: #{$curr_function->color}" onclick="toggleFunction({$curr_function->function_id})">
            <img class="action" id="function{$curr_function->function_id}" src="modules/{$m}/images/expand.gif"  style="background: #{$curr_function->color}" alt="toggle" />
          </td>
          <td colspan="4" style="background: #{$curr_function->color}" >
            <strong>{$curr_function->text}</strong> -
            {$curr_function->_ref_users|@count} utilisateur(s) -
            groupe {$curr_function->_ref_group->text}
          </td>
        </tr>
        {foreach from=$curr_function->_ref_users item=curr_user}
        <tr class="function{$curr_function->function_id}" style="display: none">
          <td style="background: #{$curr_function->color}"></td>
          {eval var=$curr_user->user_id assign=user_id}
          {assign var="href" value="index.php?m=$m&amp;tab=$tab&amp;user_id=$user_id"}
          <td><a href="{$href}">{$curr_user->_user_username}</a></td>
          <td><a href="{$href}">{$curr_user->_user_last_name}</a></td>
          <td><a href="{$href}">{$curr_user->_user_first_name}</a></td>
          <td><a href="{$href}">{$curr_user->_user_type}</a></td>
        </tr>
        {/foreach}
        {/foreach}
      </table>
    </td>
    <td class="pane">
      <form name="mediuser" action="./index.php?m={$m}" method="post" onSubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_mediusers_aed" />
      <input type="hidden" name="user_id" value="{$mediuserSel->user_id}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          <th class="category" colspan="2">
            {if $mediuserSel->user_id}
            <a style="float:right;" href="javascript:view_log('CMediusers',{$mediuserSel->user_id})">
              <img src="images/history.gif" alt="historique" />
            </a>
            Modification de l'utilisateur &lsquo;{$mediuserSel->_user_username}&rsquo;
            {else}
            Cr�ation d'un nouvel utilisateur
            {/if}
          </th>
        </tr>
        <tr>
          <th><label for="_user_username" title="Nom du compte pour se connecter � Mediboard. Obligatoire">Login</label></th>
          <td><input type="text" name="_user_username" title="{$mediuserSel->_user_props._user_username}" value="{$mediuserSel->_user_username}" /></td>
        </tr>
        <tr>
          <th><label for="_user_password" title="Mot de passe pour se connecter � Mediboard. Obligatoire">Mot de passe</label></th>
          <td><input type="password" name="_user_password" title="notNull|str" value="{$mediuserSel->_user_password}" /></td>
        </tr>
        <tr>
          <th><label for="_user_password2" title="Re-saisir le mot de passe pour confimer. Obligatoire">Mot de passe (v�rif.)</label></th>
          <td><input type="password" name="_user_password2" title="notNull|str|sameAs|_user_password" value="{$mediuserSel->_user_password}" /></td>
        </tr>
        <tr>
          <th><label for="remote_0" title="Permet ou non � l'utilisateur de se connecter � distance">Acc�s distant</label></th>
          <td>
            <input type="radio" name="remote" value="0" {if $mediuserSel->remote == "0"} checked="checked" {/if} />
            <label for="remote_0" title="Acc�s distant authoris�">oui</label>
            <input type="radio" name="remote" value="1" {if $mediuserSel->remote == "1"} checked="checked" {/if} />
            <label for="remote_1" title="Acc�s distant interdit">non</label>
          </td>
        </tr>
        <tr>
          <th><label for="function_id" title="Fonction de l'utilisateur au sein de l'�tablissement. Obligatoire">Fonction</label></th>
          <td>
            <select name="function_id" title="{$mediuserSel->_props.function_id}">
              <option value="">&mdash; Choisir une fonction &mdash;</option>
              {foreach from=$functions item=curr_function}
              <option value="{$curr_function->function_id}" {if $curr_function->function_id == $mediuserSel->function_id} selected="selected" {/if}>
                {$curr_function->text}
              </option>
              {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <th><label for="discipline_id" title="Sp�cialit� de l'utilisateur. Optionnel">Sp�cialit�</label></th>
          <td>
            <select name="discipline_id" title="{$mediuserSel->_props.discipline_id}">
              <option value="">&mdash; Choisir une sp�cialit� &mdash;</option>
              {foreach from=$disciplines item=curr_discipline}
              <option value="{$curr_discipline->discipline_id}" {if $curr_discipline->discipline_id == $mediuserSel->discipline_id} selected="selected" {/if}>
                {$curr_discipline->text|strtolower}
              </option>
              {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <th><label for="_profile_id" title="Profil de droits utilisateur. Obligatoire">Profil</label></th>
          <td>
            <select name="_profile_id">
              <option value="0">&mdash; Choisir un profil</option>	
              {foreach from=$profiles item=curr_profile}
              <option value="{$curr_profile->user_id}">{$curr_profile->user_username}</option>
              {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <th><label for="_user_last_name" title="Nom de famille de l'utilisateur. Obligatoire">Nom</label></th>
          <td><input type="text" name="_user_last_name" title="{$mediuserSel->_user_props._user_last_name}" value="{$mediuserSel->_user_last_name}" /></td>
        </tr>
        <tr>
          <th><label for="_user_first_name" title="Pr�nom de l'utilisateur">Pr�nom</label></th>
          <td><input type="text" name="_user_first_name"  title="{$mediuserSel->_user_props._user_first_name}" value="{$mediuserSel->_user_first_name}" /></td>
        </tr>
        <tr>
          <th><label for="adeli" title="Numero Adeli de l'utilisateur">Code Adeli</label></th>
          <td><input type="text" name="adeli" size="9" maxlength="9" title="{$mediuserSel->_props.adeli}" value="{$mediuserSel->adeli}" /></td>
        </tr>
        <tr>
          <th><label for="_user_email" title="Email de l'utilisateur">Email</label></th>
          <td><input type="text" name="_user_email" title="{$mediuserSel->_user_props._user_email}" value="{$mediuserSel->_user_email}" /></td>
        </tr>
        <tr>
          <th><label for="_user_phone" title="Num�ro de t�l�phone de l'utilisateur">T�l</label></th>
          <td><input type="text" name="_user_phone" title="{$mediuserSel->_user_props._user_phone}" value="{$mediuserSel->_user_phone}" /></td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            {if $mediuserSel->user_id}
            <input type="reset" value="R�initialiser" />
            <input type="submit" value="Valider" />
            <input type="button" value="Supprimer" onclick="confirmDeletion(this.form,{ldelim}typeName:'l\'utilisateur',objName:'{$mediuserSel->_user_username|escape:javascript}'{rdelim})" />
            {else}
            <input type="submit" name="btnFuseAction" value="Cr�er" />
            {/if}
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
</table>