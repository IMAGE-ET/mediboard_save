{{mb_include style=mediboard template=common nodebug=true}}

{{if !$offline && !$dialog}}

<table id="headerMenu">
  <tr>
    <td class="menuTitle" id="modMenu">
      <div class="dropdown-menu">
        Modules
        <ul>
          {{foreach from=$modules key=mod_name item=currModule}}
          {{if $currModule->mod_ui_active && $currModule->_can->view}}
            <li>
              <a href="?m={{$mod_name}}" title="{{tr}}module-{{$mod_name}}-long{{/tr}}" class="{{if $mod_name == $m}}selected{{/if}}">
                <img src="modules/{{$mod_name}}/images/icon.png" width="16" height="16" />
                {{tr}}module-{{$mod_name}}-court{{/tr}}
              </a>
            </li>
          {{/if}}
          {{/foreach}}
        </ul>
      </div>
    </td>
    
    <td class="menuTitle" id="toolMenu">
      <div class="dropdown-menu">
        Outils
        <ul>
          <li>
            {{mb_include style=mediboard template=inc_help show=true}}
          </li>
          
          {{if $portal.tracker}}
          <li>
            <a href="{{$portal.tracker}}" target="_blank">
              <img src="style/aero-blue/images/icons/modif.png" title="{{tr}}portal-tracker{{/tr}}" />
              {{tr}}portal-tracker{{/tr}}
            </a>
          </li>
          {{/if}}
          
          <li>
            <a href="#1" onclick="popChgPwd()">
              <img src="style/aero-blue/images/icons/passwd.png" title="{{tr}}menu-changePassword{{/tr}}" />
              {{tr}}menu-changePassword{{/tr}}
            </a>
          </li>
          
          <li>
            <a href="?m=mediusers&amp;a=edit_infos">
              <img src="style/aero-blue/images/icons/myinfos.png" title="{{tr}}menu-myInfo{{/tr}}" />
              {{tr}}menu-myInfo{{/tr}}
            </a>
          </li>
          
          <li>
            <a href="#1" onclick="Session.lock()">
              <img src="style/aero-blue/images/icons/lock.png" title="{{tr}}menu-lockSession{{/tr}}" />
              {{tr}}menu-lockSession{{/tr}}
            </a>
          </li>
          
          <li>
            <a href="#1" onclick="UserSwitch.popup()">
              <img src="images/icons/switch.png" title="{{tr}}menu-switchUser{{/tr}}" />
              {{tr}}menu-switchUser{{/tr}}
            </a>
          </li>
          
          <li>
            <a href="?logout=-1">
              <img src="style/aero-blue/images/icons/logout.png" title="{{tr}}menu-logout{{/tr}}" />
              {{tr}}menu-logout{{/tr}}
            </a>
          </li>
        </ul>
      </div>
    </td>
    <td class="titlecell" style="width: 1%;">
      <img src="./modules/{{$m}}/images/icon.png" height="24" width="24" />
    </td>
    <td class="titlecell">
      {{tr}}module-{{$m}}-long{{/tr}}
    </td>
    {{if !$dialog}}
      <td class="info">
        <div style="position: relative;">
          <div id="systemMsg">
            {{$errorMessage|nl2br|smarty:nodefaults}}
          </div>
        </div>
      </td>
    {{/if}}
    <td class="end">
      <span title="{{tr}}Last connection{{/tr}} : {{$app->user_last_login|date_format:$conf.datetime}}">
        {{$app->user_first_name}} {{$app->user_last_name}}
      </span>
      - 
      {{mb_include style=mediboard template=svnstatus}}
      {{if $applicationVersion.releaseCode}}-{{/if}}
      {{mb_include style=mediboard template=change_group}}
    </td>
  </tr>
</table>
{{/if}}

<table id="main" class="{{if $dialog}}dialog {{/if}}{{$m}}">
  {{if !$dialog}}
    <tr>
      <td>
        {{mb_include style=mediboard template=message nodebug=true}}
      </td>
    </tr>
  {{/if}}

  <tr>
    <td class="tabox">
      {{mb_include style=mediboard template=obsolete_module}}
      {{if $dialog}}
          <!-- System messages -->
          <div id="systemMsg">
            {{$errorMessage|nl2br|smarty:nodefaults}}
          </div>
      {{/if}}
