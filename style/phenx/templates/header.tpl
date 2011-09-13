{{include file="../../mediboard/templates/common.tpl" nodebug=true}}

<table style="width: 100%;">
  <tr>
    {{if !$offline && !$dialog}}
    <td style="vertical-align: top; width: 1%; padding-right: 0; text-align: center" id="leftMenu">
      
      {{mb_include style="mediboard" template="logo" id="mediboard-logo" alt="MediBoard logo" width="140"}}
      
      <div id="user">
        <div class="welcome" title="{{tr}}Last connection{{/tr}} : {{$app->user_last_login|date_format:$conf.datetime}}">
          {{$app->user_first_name}} {{$app->user_last_name}}
        </div>
        
        {{mb_include module=mediboard template=svnstatus}}    
        
        <div class="menu">
          <a href="{{$portal.help}}" title="{{tr}}portal-help{{/tr}}" target="_blank"><img src="style/{{$uistyle}}/images/icons/help.png" alt="{{tr}}portal-help{{/tr}}" /></a>
          <a href="{{$portal.tracker}}" title="{{tr}}portal-tracker{{/tr}}" target="_blank"><img src="style/{{$uistyle}}/images/icons/modif.png" alt="{{tr}}portal-tracker{{/tr}}" /></a>
          <a href="#1" onclick="popChgPwd()" title="{{tr}}menu-changePassword{{/tr}}"><img src="style/{{$uistyle}}/images/icons/passwd.png" alt="{{tr}}menu-changePassword{{/tr}}" /></a>
          <a href="?m=mediusers&amp;a=edit_infos" title="{{tr}}menu-myInfo{{/tr}}"><img src="style/{{$uistyle}}/images/icons/myinfos.png" alt="{{tr}}menu-myInfo{{/tr}}" /></a>
          <a href="#1" onclick="UserSwitch.popup()" title="{{tr}}menu-switchUser{{/tr}}"><img src="./images/icons/switch.png" alt="{{tr}}menu-switchUser{{/tr}}" /></a>
          <a href="#1" onclick="Session.lock()" title="{{tr}}menu-lockSession{{/tr}}"><img src="style/{{$uistyle}}/images/icons/lock.png" alt="{{tr}}menu-lockSession{{/tr}}" /></a>
          <a href="?logout=-1" title="{{tr}}menu-logout{{/tr}}"><img src="style/{{$uistyle}}/images/icons/logout.png" alt="{{tr}}menu-logout{{/tr}}" /></a>
        </div>
        
        {{assign var=style value="max-width: 140px;"}}
        {{mb_include module=mediboard template=change_group}}
      </div>
      
      <ul id="nav">
         {{foreach from=$modules key=mod_name item=currModule}}
         {{if $currModule->mod_ui_active && $currModule->_can->view}}    
        <li {{if $mod_name==$m}}class="selected"{{/if}}>
        <a href="?m={{$mod_name}}">
          <img src="./modules/{{$mod_name}}/images/icon.png" alt="{{tr}}module-{{$mod_name}}-court{{/tr}}" />
          {{tr}}module-{{$mod_name}}-court{{/tr}}
        </a>
        </li>
        {{/if}}
        {{/foreach}}
      </ul>
    </td>
    {{/if}}
    <td id="main" class="{{if $dialog}}dialog{{/if}} {{$m}}">
      {{mb_include module=mediboard template=obsolete_module}}
      {{include file="../../mediboard/templates/message.tpl" nodebug=true}}
      
      <div id="systemMsg">
        {{$errorMessage|nl2br|smarty:nodefaults|nl2br}}
      </div>
      
      {{if !$dialog && !$offline}}
      <div class="title">
        <img src="./modules/{{$m}}/images/icon.png" alt="Icone {{$m}}" height="24" width="24" />
        <h1>{{tr}}module-{{$m}}-long{{/tr}}</h1>
      </div>
      {{/if}}
