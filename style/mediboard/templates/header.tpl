{{mb_include template=common nodebug=true}}

{{if !$offline && !$dialog}}

{{mb_include template=message nodebug=true}}

<script>
var Menu = {
  toggle: function () {
    var oCNs = Element.classNames("menubar");
    oCNs.flip("iconed", "uniconed");
    oCNs.save("menubar", Date.year);
  },
  
  init: function() {
    var oCNs = Element.classNames("menubar");
    oCNs.load("menubar");
  }
}
</script>

<table id="header">
</table>
{{/if}}

<table id="main" class="{{if $dialog}}dialog{{/if}} {{$m}}">
  {{if !$dialog}}
  {{if @$app->user_prefs.MenuPosition == "left"}}
  <tr>
    <td id="menubar" class="iconed" rowspan="10" style="width: 1px; text-align: center; vertical-align: top;">
      <ul id="nav-vert">  
        {{foreach from=$modules key=mod_name item=currModule}} 
        {{if $currModule->_can->view && $currModule->mod_ui_active}}
        <li class={{if $mod_name==$m}}selected{{else}}nonSelected{{/if}}>
        <a href="?m={{$mod_name}}">
          <img src="./modules/{{$mod_name}}/images/icon.png" alt="{{tr}}module-{{$mod_name}}-court{{/tr}}" height="48" width="48" />
         {{tr}}module-{{$mod_name}}-court{{/tr}}
        </a>
        </li>
        {{/if}}
        {{/foreach}}
      </ul>
    </td>
  </tr>
  {{else}}
  <tr>
    <td id="menubar" class="iconed">
      <ul id="nav">
        {{foreach from=$modules key=mod_name item=currModule}} 
        {{if $currModule->_can->view && $currModule->mod_ui_active}}
        <li class={{if $mod_name==$m}}selected{{else}}nonSelected{{/if}}>
        <a href="?m={{$mod_name}}">
          <img src="./modules/{{$mod_name}}/images/icon.png" alt="{{tr}}module-{{$mod_name}}-court{{/tr}}" height="48" width="48" />
         {{tr}}module-{{$mod_name}}-court{{/tr}}
        </a>
        </li>
        {{/if}}
        {{/foreach}}
      </ul>
    </td>
  </tr>
  {{/if}}
  <tr>
    <td id="user">
      <button id="toggleIcons" class="vslip notext" onclick="Menu.toggle()" type="button" title="{{tr}}menu-toggleIcons{{/tr}}">{{tr}}menu-toggleIcons{{/tr}}</button>
      <script>Menu.init();</script>
      <span title="{{tr}}Last connection{{/tr}} : {{$app->user_last_login|date_format:$conf.datetime}}">
        {{$app->user_first_name}} {{$app->user_last_name}}
      </span>
      - 
      {{mb_include template=change_group}}
      {{mb_include template=svnstatus}} 
      
      <div class="menu">

        {{mb_include template=inc_help}}

        {{if $portal.tracker}}
          <a title="{{tr}}portal-tracker{{/tr}}" href="{{$portal.tracker}}" target="_blank">
            <img src="style/{{$uistyle}}/images/icons/modif.png" alt="{{tr}}portal-tracker{{/tr}}" />
          </a>
        {{/if}}

        <a title="{{tr}}menu-changePassword{{/tr}}" href="#1" onclick="popChgPwd()">
          <img src="style/{{$uistyle}}/images/icons/passwd.png" alt="{{tr}}menu-changePassword{{/tr}}" />
        </a>
        <a title="{{tr}}menu-myInfo{{/tr}}" href="?m=mediusers&amp;a=edit_infos">
          <img src="style/{{$uistyle}}/images/icons/myinfos.png" alt="{{tr}}menu-myInfo{{/tr}}" />
        </a>
        <a title="{{tr}}menu-switchUser{{/tr}}" href="#1" onclick="UserSwitch.popup()">
          <img src="images/icons/switch.png" alt="{{tr}}menu-switchUser{{/tr}}" />
        </a>
        <a title="{{tr}}menu-lockSession{{/tr}}" href="#1" onclick="Session.lock()">
          <img src="style/{{$uistyle}}/images/icons/lock.png" alt="{{tr}}menu-lockSession{{/tr}}" />
        </a>
        <a title="{{tr}}menu-logout{{/tr}}" href="?logout=-1">
          <img src="style/{{$uistyle}}/images/icons/logout.png" alt="{{tr}}menu-logout{{/tr}}" />
        </a>
      </div>
    </td>
  </tr>
  {{/if}}
  <tr>
    <td style="vertical-align: top;">
      {{mb_include template=obsolete_module}}
      <div id="systemMsg">
        {{$errorMessage|nl2br|smarty:nodefaults}}
      </div>
      {{if !$dialog && !$offline}}
      <table class="titleblock">
        <tr>
          <td>
            <img src="./modules/{{$m}}/images/icon.png" alt="{{tr}}module-{{$m}}-court{{/tr}}" height="24" width="24" />
          </td>
          <td class="titlecell">
            {{tr}}module-{{$m}}-long{{/tr}}
          </td>
        </tr>
      </table>
      {{/if}}