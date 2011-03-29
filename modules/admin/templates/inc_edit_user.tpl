{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="dosql" value="do_user_aed" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="user_id" value="{{$user->user_id}}" />

<table class="form">
  <tr>
    {{if $user->_id}}
    <th class="title modify" colspan="2">
      {{assign var=object value=$user}}
      {{mb_include module=system template=inc_object_idsante400}}
      {{mb_include module=system template=inc_object_history}}
      {{mb_include module=system template=inc_object_notes}}
      Utilisateur '{{$user}}'
    {{else}}
    <th class="title" colspan="2">
      Cr�ation d'utilisateur
    {{/if}}
    </th>
  </tr>


  <tr>
    <th class="category" colspan="2">Informations de connexion</th>
  </tr>
  
  <tr>
    <th>{{mb_label object=$user field="user_username"}}</th>
    <td>{{mb_field object=$user field="user_username"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$user field="template"}}</th>
    <td>{{mb_field object=$user field="template"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$user field="dont_log_connection"}}</th>
    <td>{{mb_field object=$user field="dont_log_connection"}}</td>
  </tr>
  <tr>
    <th><label for="_user_password" title="Saisir le mot de passe. Obligatoire">Mot de passe</label></th>
    <td><input  type="password" name="_user_password" class="{{$specs._user_password}}{{if !$user->user_id}} notNull{{/if}}" value="" onkeyup="checkFormElement(this)" />
    <span id="editFrm__user_password_message"></span>
    </td>
  </tr>
  <tr>
    <th><label for="_user_password2" title="Re-saisir le mot de passe pour confimer. Obligatoire">Mot de passe (bis)</label></th>
    <td><input type="password" name="_user_password2" class="password sameAs|_user_password" value="" /></td>
  </tr>


  <tr>
    <th class="category" colspan="2">Identit�</th>
  </tr>

  <tr>
    <th>{{mb_label object=$user field="user_last_name" }}</th>
    <td>{{mb_field object=$user field="user_last_name"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$user field="user_first_name"}}</th>
    <td>{{mb_field object=$user field="user_first_name"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$user field="user_type"}}</th>
    <td>
      <select name="user_type" class="{{$user->_props.user_type}}">
        {{foreach from=$utypes key=curr_key item=type}}
        <option value="{{$curr_key}}" {{if $curr_key == $user->user_type}}selected="selected"{{/if}}>{{$type}}</option>
        {{/foreach}}
      </select>
    </td>
  </tr>

  <tr>
    <td class="button" colspan="2">
      {{if $user->user_id}}
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      <button class="trash" type="button" onclick="confirmDeletion(this.form, {
          typeName:'l\'utilisateur',
          objName:'{{$user->user_username|smarty:nodefaults|JSAttribute}}'
        })">
        {{tr}}Delete{{/tr}}
      </button>
      {{else}}
      <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
      {{/if}}
    </td>
  </tr>
  
  {{if $user->_ref_mediuser}}
  <!-- Link to CMediusers -->
  <tr>
    <td colspan="2" class="button">
      {{if $user->_ref_mediuser->_id}}
        <div class="small-success">
          Cet utilisateur est bien int�gr� � l'organigramme.
          <br />
          <a class="button edit" href="?m=mediusers&tab=vw_idx_mediusers&user_id={{$user->_id}}">
            G�rer cet utilisateur dans l'organigramme
          </a>
        </div>
      {{else}}
        {{if $user->template}}
        <div class="small-info">
          Cet utilisateur n'est pas dans l'organigramme.
          <br />
          C'est <strong>normal pour un Profil</strong>.
        </div>
        {{else}}
        <div class="small-warning">
          Cet utilisateur n'est pas dans l'organigramme, 
          <br />
          C'est <strong>anormal pour un utilisateur r�el</strong>.
        </div>
        {{/if}}
      {{/if}}
    </td>
  </tr>
  {{/if}}
  
</table>

</form>