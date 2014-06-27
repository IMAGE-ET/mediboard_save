{{mb_include style=mediboard template=common nodebug=true}}

{{if !$offline && !$dialog}}

{{mb_include style=mediboard template=message nodebug=true}}

<script type="text/javascript">
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
  <tr>
    <td id="menubar">
      <table>
        <tbody id="menuIcons">
          <!-- Module icons -->
          <tr>
            <td></td>
            {{foreach from=$modules key=mod_name item=currModule}}
            {{if $currModule->_can->view && $currModule->mod_ui_active}}
            <td align="center" class="{{if $mod_name==$m}}iconSelected{{else}}iconNonSelected{{/if}}">
              <a href="?m={{$mod_name}}" title="{{tr}}module-{{$mod_name}}-long{{/tr}}">
                <img src="./modules/{{$mod_name}}/images/icon.png" alt="{{$mod_name}}" height="48" width="48" />
              </a>
            </td>
            {{/if}}
            {{/foreach}}
          </tr>
        </tbody>
        
        <!-- Modules names -->
        <tr>
          <td>
            <button id="menuIcons-trigger" type="button" style="float:left" class="notext">{{tr}}Show/Hide{{/tr}}</button>
          </td>
          {{foreach from=$modules key=mod_name item=currModule}}
          {{if $currModule->_can->view && $currModule->mod_ui_active}}
          <td align="center" class="{{if $mod_name==$m}}textSelected{{else}}textNonSelected{{/if}}" title="{{tr}}module-{{$mod_name}}-long{{/tr}}">
            <a href="?m={{$mod_name}}">
              <strong>{{tr}}module-{{$mod_name}}-court{{/tr}}</strong>
            </a>
          </td>
          {{/if}}
          {{/foreach}}
        </tr>
      </table>

    </td>
  </tr>
  <tr>
    <td id="user">
      <script type="text/javascript">Menu.init();</script>
      
      <span title="{{tr}}Last connection{{/tr}} : {{$app->user_last_login|date_format:$conf.datetime}}">
        {{$app->user_first_name}} {{$app->user_last_name}}
      </span>
      -  
      {{mb_include style=mediboard template=change_group}}
      {{mb_include style=mediboard template=svnstatus}}
      {{mb_include style=mediboard template=inc_help show=false show_img=true root=true}}
      
      {{if $portal.tracker}}
        <a title="{{tr}}portal-tracker{{/tr}}" href="{{$portal.tracker}}" target="_blank">
          <img src="style/mediboard/images/icons/modif.png" alt="{{tr}}portal-tracker{{/tr}}" />
        </a>
      {{/if}}
      
      <a title="{{tr}}menu-changePassword{{/tr}}" href="#1" onclick="popChgPwd()">
        <img src="style/mediboard/images/icons/passwd.png" alt="{{tr}}menu-changePassword{{/tr}}" />
      </a>
      <a title="{{tr}}menu-myInfo{{/tr}}" href="?m=mediusers&amp;a=edit_infos">
        <img src="style/mediboard/images/icons/myinfos.png" alt="{{tr}}menu-myInfo{{/tr}}" />
      </a>
      <a title="{{tr}}menu-switchUser{{/tr}}" href="#1" onclick="UserSwitch.popup()">
        <img src="images/icons/switch.png" alt="{{tr}}menu-switchUser{{/tr}}" />
      </a>
      <a title="{{tr}}menu-lockSession{{/tr}}" href="#1" onclick="Session.lock()">
        <img src="style/mediboard/images/icons/lock.png" alt="{{tr}}menu-lockSession{{/tr}}" />
      </a>
      <a title="{{tr}}menu-logout{{/tr}}" href="?logout=-1">
        <img src="style/mediboard/images/icons/logout.png" alt="{{tr}}menu-logout{{/tr}}" />
      </a>
    </td>
  </tr>
</table>
<script type="text/javascript">
  new PairEffect("menuIcons", { bStartVisible: true });
</script>
{{/if}}

<table id="main" class="{{if $dialog}}dialog{{/if}} {{$m}}">
  <tr>
    <td>
      {{mb_include style=mediboard template=obsolete_module}}
      <!-- System messages -->
      <div id="systemMsg">
        {{$errorMessage|nl2br|smarty:nodefaults}}
      </div>
      {{if !$dialog && !$offline}}
      <table class='titleblock'>
        <tr>
          <td>
            <img src="./modules/{{$m}}/images/icon.png" alt="Icone {{$m}}" height="24" width="24" />
          </td>
          <td class='titlecell'>
            {{tr}}module-{{$m}}-long{{/tr}}
          </td>
        </tr>
      </table>
      {{/if}}