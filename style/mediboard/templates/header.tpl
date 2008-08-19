{{include file="common.tpl"}}

{{if !$offline}}
{{if !$dialog}}

{{foreach from=$messages item=currMsg}}
  <div style='{{if $currMsg->urgence == "urgent"}}background: #eee; color: #f00;{{else}}background: #aaa; color: #fff;{{/if}}'>
    <strong>{{$currMsg->titre}}</strong> : {{$currMsg->corps}}
  </div>
{{/foreach}}

<table id="header" cellspacing="0"><!-- IE Hack: cellspacing should be useless --> 
  <tr>
    <td id="banner">
      <p>Mediboard :: Syst�me de gestion des structures de sant�</p>
      <a href='http://www.mediboard.org'><img src="./style/{{$uistyle}}/images/pictures/mbSmall.gif" alt="Logo Mediboard"  /></a>
    </td>
  </tr>
  <tr>
    <td id="menubar">
      <table>
        <tr>
          <td id="nav">
            <ul>
              {{foreach from=$modules key=mod_name item=currModule}} 
              {{if $currModule->_can->view && $currModule->mod_ui_active}}
              <li {{if $mod_name==$m}}class="selected"{{/if}}>
              <a href="?m={{$mod_name}}">
                <img src="images/modules/{{$mod_name}}.png" alt="{{tr}}module-{{$mod_name}}-court{{/tr}}" height="48" width="48" />
               {{tr}}module-{{$mod_name}}-court{{/tr}}
              </a>
              </li>
              {{/if}}
              {{/foreach}}
            </ul>
          </td>
          <td id="new"></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td id="user">
      <table>
        <tr>
          <td id="userWelcome">
            <form name="ChangeGroup" action="" method="get">
              <span title="{{tr}}last connection{{/tr}} : {{$app->user_last_login|date_format:"%A %d %B %Y %Hh%M"}}">
                {{tr}}Welcome{{/tr}} {{$app->user_first_name}} {{$app->user_last_name}}
              </span>
              <input type="hidden" name="m" value="{{$m}}" />
              <select name="g" onchange="ChangeGroup.submit();">
                {{foreach from=$Etablissements item=currEtablissement key=keyEtablissement}}
                <option value="{{$keyEtablissement}}" {{if $keyEtablissement==$g}}selected="selected"{{/if}}>
                  {{$currEtablissement->_view}}
                </option>
                {{/foreach}}
              </select>
            </form>
          </td>
          <td id="userMenu">
            <a href="{{$portal.help}}" title="{{tr}}portal-help{{/tr}}" target="_blank">{{tr}}portal-help{{/tr}}</a> |
            <a href="{{$portal.tracker}}" title="{{tr}}portal-tracker{{/tr}}" target="_blank">{{tr}}portal-tracker{{/tr}}</a> |
            <a href="#" onclick="popChgPwd();">{{tr}}menu-changePassword{{/tr}}</a> |
            <a href="?m=mediusers&amp;a=edit_infos">{{tr}}menu-myInfo{{/tr}}</a> |
            <a href="?m=admin&amp;a=edit_prefs&amp;user_id={{$app->user_id}}">{{tr}}mod-admin-tab-edit_prefs{{/tr}}</a> |
            <a href="?logout=-1">{{tr}}menu-logout{{/tr}}</a>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
{{/if}}
{{/if}}
<table id="main" class="{{if $dialog}}dialog{{/if}} {{$m}}">
  <tr>
    <td>
      <div id="systemMsg">
        {{$errorMessage|nl2br|smarty:nodefaults|nl2br}}
      </div>
      {{if !$dialog && !$offline}}
      <table class='titleblock'>
        <tr>
          <td>
            <img src="./images/modules/{{$m}}.png" alt="Icone {{$m}}" height="24" width="24" />
          </td>
          <td class='titlecell'>
            {{tr}}module-{{$m}}-long{{/tr}}
          </td>
        </tr>
      </table>
      {{/if}}