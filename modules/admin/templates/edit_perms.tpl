{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(function () {
  Control.Tabs.create('tab_permissions', true);
});
</script>

<table class="main">
  <tr>
    <th class="title">
      {{if $user->template}}
      Profil utilisateur '{{$user->_view}}' 
      {{else}}
        Utilisateur '{{$user->_view}}' 
        &mdash; bas� sur
        {{if $profile->_id}} 
        le profil 
        <a class="action" href="?m={{$m}}&amp;tab={{$tab}}&user_id={{$profile->_id}}">
          '{{$profile->user_username}}'
        </a>
        {{else}}
        aucun profil  
        {{/if}}
      {{/if}}
    </th>
  </tr>
  <tr>
    <td>
      <ul id="tab_permissions" class="control_tabs">
        <li><a href="#module">Droits sur les modules</a></li>
        <li><a href="#object">Droits sur les objets</a></li>
        {{assign var=usedProfile value=$user->template|ternary:$user:$profile}}
        {{if $usedProfile->_id}}
        <li><a href="#profile">Utilisateurs bas�s sur le profil '{{$usedProfile->user_username}}'</a></li>
        {{/if}}
      </ul>
      <hr class="control_tabs" />
      
      <div id="module" style="display: none;">
        {{mb_include template=inc_perms_modules}}
      </div>
      <div id="object" style="display: none;">
        {{mb_include template=inc_perms_objects}}
      </div>
      
      {{if $usedProfile->_id}}
      {{assign var=numCols value=1}}
      {{math equation="100/$numCols" assign=width format="%.1f"}}
      <table class="tbl main" id="profile" style="display: none;">
        <tr>
        {{foreach from=$profilesList item=curr_user name="users"}}
          <td style="width: {{$width}}%; text-align: right;">
            <a href="?m={{$m}}&amp;tab=vw_edit_users&amp;user_id={{$curr_user->_id}}" style="float: left;">
              {{$curr_user->_view}}
            </a>
            <button class="search" onclick="window.location='?m={{$m}}&amp;tab=edit_perms&amp;user_id={{$curr_user->_id}}'">
              Droits
            </button>
            <button class="search" onclick="window.location='?m={{$m}}&amp;tab=edit_prefs&amp;user_id={{$curr_user->_id}}'">
              Pr�f�rences
            </button>
            {{include file="loginas.tpl" loginas_user=$curr_user}}
          </td>
          {{if ($smarty.foreach.users.index % $numCols) == ($numCols-1) && !$smarty.foreach.users.last}}</tr><tr>{{/if}}
        {{foreachelse}}
          <td class="empty">{{tr}}CMediusers.none{{/tr}}</td>
        {{/foreach}}
        </tr>
      </table>
      {{/if}}
    </td>
  </tr>
</table>