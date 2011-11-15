{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="find" action="" method="get">

<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="tab" value="{{$tab}}" />
  
<table class="form">
  <tr>
    <th class="title" colspan="4">Recherche d'un utilisateur</th>
  </tr>
  
  <tr>
    <th><label for="user_last_name" title="Nom de l'utilisateur � rechercher, au moins les premi�res lettres">Nom</label></th>
    <td><input type="text" name="user_last_name" value="{{$user_last_name|stripslashes}}" /></td>
    <th><label for="user_first_name" title="Nom d'utilisateur (login) � rechercher, au moins les premi�res lettres">Utilisateur</label></th>
    <td><input type="text" name="user_username" value="{{$user_username|stripslashes}}" /></td>
  </tr>
    
  <tr>
    <th><label for="user_first_name" title="Pr�nom de l'utilisateur � rechercher, au moins les premi�res lettres">Pr�nom</label></th>
    <td><input type="text" name="user_first_name" value="{{$user_first_name|stripslashes}}" /></td>
    <th><label for="user_type" title="Type de l'utilisateur">Type</label></th>
    
    <td>
      <select name="user_type">
        <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
        {{foreach from=$utypes key=_key item=type}}
        <option value="{{$_key}}" {{if $_key == $user_type}}selected="selected"{{/if}}>{{$type}}</option>
        {{/foreach}}
      </select>
    </td>
   </tr>

   <tr>
	   <th colspan="3"><label for="template" title="Statut">Statut</label></th>

	   <td class="text">
			<select name="template">
				<option value="">&mdash; {{tr}}Choose{{/tr}}</option>
				<option value="0" {{if $template == "0"}}selected="selected"{{/if}}>Utilisateur</option>
				<option value="1" {{if $template == "1"}}selected="selected"{{/if}}>Profil</option>
			</select>
    </td>
  </tr>
    
  <tr>
    <td class="button" colspan="4"><button class="search" type="submit">{{tr}}Search{{/tr}}</button></td>
  </tr>
</table>

</form>

<table class="tbl">
  <tr>
    <th>Login</th>
    <th>Utilisateur</th>
    <th>Type</th>
    <th colspan="3">Administration</th>
  </tr>

  {{foreach from=$users item=_user}}
  <tr {{if $_user->_id == $user->_id}}class="selected"{{/if}} {{if $_user->template}}style="font-weight: bold"{{/if}}>
    <td>
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;user_id={{$_user->_id}}">
        {{$_user->user_username}}
      </a>
       
    </td>
    <td class="text">
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;user_id={{$_user->_id}}">
        {{$_user->_view}}
      </a>
    </td>
    <td class="text {{if !$_user->user_type}}empty{{/if}}">
    	{{if $_user->user_type}}
	      {{assign var="type" value=$_user->user_type}}
	      	{{if $_user->template}}
					  {{mb_label object=$_user field=template}} : 
					{{/if}}
	        {{$utypes.$type}}
			{{else}}
			  Aucun type
			{{/if}}
    </td>
    
    <td class="button" style="white-space: nowrap; text-align: left;">
      <button class="search" onclick="location.href='?m={{$m}}&amp;tab=edit_perms&amp;user_id={{$_user->_id}}'">
      	Droits
      </button>
      <button class="search" onclick="location.href='?m={{$m}}&amp;tab=edit_prefs&amp;user_id={{$_user->_id}}'">
      	Pr�f�rences
      </button>
      {{assign var="loginas_user" value=$_user}}
      {{include file="loginas.tpl"}}
      {{include file="unlock.tpl"}}
    </td>
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="10" class="empty">
      {{tr}}CUser.none{{/tr}}
    </td>
  </tr>
  {{/foreach}}
    
</table>
  