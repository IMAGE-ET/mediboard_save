{{include file="../../mediboard/templates/common.tpl"}}

{{if !$offline}}
{{if !$dialog}}

{{foreach from=$messages item=currMsg}}
  <div style='{{if $currMsg->urgence == "urgent"}}background: #eee; color: #f00;{{else}}background: #aaa; color: #fff;{{/if}}'>
    <strong>{{$currMsg->titre}}</strong> : {{$currMsg->corps}}
  </div>
{{/foreach}}

<table id="header" cellspacing="0">
  <tr>
    <td id="menubar">
      <table>
        <tbody id="menuIcons">
          <!-- Module icons -->
	        <tr>
	          <td />
	          {{foreach from=$modules key=mod_name item=currModule}}
	          {{if $currModule->_can->view && $currModule->mod_ui_active}}
	          <td align="center" class="{{if $mod_name==$m}}iconSelected{{else}}iconNonSelected{{/if}}">
	            <a href="?m={{$mod_name}}" title="{{tr}}module-{{$mod_name}}-long{{/tr}}">
	              <img src="images/modules/{{$mod_name}}.png" alt="{{$mod_name}}" height="48" width="48" />
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
<script language="JavaScript" type="text/javascript">
  new PairEffect("menuIcons", { bStartVisible: true });
</script>
{{/if}}
{{/if}}
<table id="main" class="{{if $dialog}}dialog{{/if}} {{$m}}">
  <tr>
    <td>
      <!-- System messages -->
      <div id="systemMsg">
        {{$errorMessage|nl2br|smarty:nodefaults}}
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