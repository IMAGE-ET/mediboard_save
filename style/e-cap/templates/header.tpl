{{include file="../../mediboard/templates/common.tpl"}}

<script type="text/javascript">
var Menu = {
  toggle: function () {
    var oCNs = Element.classNames("menubar");
    oCNs.flip("iconed", "uniconed");
    oCNs.save("menubar");
  },
  
  init: function() {
    var oCNs = Element.classNames("menubar");
    oCNs.load("menubar");
  }
}
</script>

<table id="main" class="{{$m}}">
  <tr>
  
{{if !$dialog}}

{{foreach from=$messages item=currMsg}}
  <div style='background: #aaa; color: #fff;'>
    <strong>{{$currMsg->titre}}</strong> 
    : {{$currMsg->corps}}
  </div>
{{/foreach}}

{{if @$app->user_prefs.MenuPosition == "left"}}
<td id="leftMenu">
  {{thumb src="style/$uistyle/images/pictures/e-cap.jpg" w="140" f="png"}}
  
  {{if !$offline}}
  <!-- Changement d'�tablissement courant -->
  <form name="ChangeGroup" action="?" method="get">

  <input type="hidden" name="m" value="{{$m}}" />
  <select name="g" onchange="this.form.submit();">
    {{foreach from=$Etablissements item=currEtablissement}}
    <option value="{{$currEtablissement->_id}}" {{if $currEtablissement->_id == $g}}selected="selected"{{/if}}>
      {{$currEtablissement->_view}}
    </option>
    {{/foreach}}
  </select>

  </form>
  
  <!-- Welcome -->
  <label title="{{tr}}last connection{{/tr}} : {{$AppUI->user_last_login|date_format:"%A %d %B %Y %H:%M"}}">
  {{tr}}Welcome{{/tr}} {{$AppUI->user_first_name}} {{$AppUI->user_last_name}}
  </label>
  {{/if}}

  <div id="menubar" class="iconed">
    <a href="#" onclick="Menu.toggle()">{{tr}}Menu icons{{/tr}}</a>
    {{$helpOnline|smarty:nodefaults}}
    {{foreach from=$affModule item=currModule}}
    {{if $currModule.modName == $m}}
    <a href="?m={{$currModule.modName}}" title="{{tr}}module-{{$currModule.modName}}-long{{/tr}}" class="textSelected">
    {{else}}
    <a href="?m={{$currModule.modName}}" title="{{tr}}module-{{$currModule.modName}}-long{{/tr}}"class="textNonSelected">
    {{/if}}
      <img src="images/modules/{{$currModule.modName}}.png" alt="Icone {{$currModule.modName}}" />
      {{$currModule.modNameCourt}}
    </a>
    {{/foreach}}
    <a href="#" onclick="popChgPwd()">{{tr}}Change password{{/tr}}</a>
    <a href="?m=mediusers&amp;a=edit_infos">{{tr}}My Info{{/tr}}</a>
    <a href="?m=admin&amp;a=edit_prefs&amp;user_id={{$AppUI->user_id}}">{{tr}}Pr�f�rences{{/tr}}</a>
    <a href="?logout=-1">{{tr}}Logout{{/tr}}</a>
  </div>
  
  <script type="text/javascript">Menu.init();</script>
  
  <!-- System messages -->
  <div id="systemMsg">
    {{$errorMessage|nl2br|smarty:nodefaults}}
  </div>
  
</td>
  
{{else}}
<td id="topMenu">
<table id="header" cellspacing="0">
  <tr>
    <td id="mainHeader">
      <table>
        <tr>
          <td class="logo">
            <img src="./style/{{$uistyle}}/images/pictures/e-cap.jpg" alt="eCap logo" />
          </td>
          <td width="1%">
            {{if !$offline}}
            <table class="titleblock">
              <tr>
                <td>
                  <img src="images/modules/{{$m}}.png" alt="Icone {{$m}}" width="24" height="24" />
                </td>
                <td class="titlecell">
                  {{tr}}module-{{$m}}-long{{/tr}}
                </td>
              </tr>
            </table>
            {{/if}}
          </td>
          <td>
            <div id="systemMsg">
              {{$errorMessage|nl2br|smarty:nodefaults}}
            </div>
          </td>
          <td class="welcome">
            {{if !$offline}}
            <form name="ChangeGroup" action="" method="get">

            <input type="hidden" name="m" value="{{$m}}" />
            CAPIO Sant� -
            <select name="g" onchange="this.form.submit();">
              {{foreach from=$Etablissements item=currEtablissement key=keyEtablissement}}
              <option value="{{$keyEtablissement}}" {{if $keyEtablissement==$g}}selected="selected"{{/if}}>
                {{$currEtablissement->_view}}
              </option>
              {{/foreach}}
            </select>
            </form>
            {{/if}}
            <br />
            <span title="{{tr}}last connection{{/tr}} : {{$AppUI->user_last_login|date_format:"%A %d %B %Y %H:%M"}}">
            {{tr}}Welcome{{/tr}} {{$AppUI->user_first_name}} {{$AppUI->user_last_name}}
            </span>

          </td>
        </tr>
      </table>
    </td>
  </tr>
  {{if !$offline}}
  <tr>
    <td id="menubar">
      {{$helpOnline|smarty:nodefaults}}
      {{foreach from=$affModule item=currModule}}
      <a href="?m={{$currModule.modName}}" class="{{if $currModule.modName==$m}}textSelected{{else}}textNonSelected{{/if}}">
        {{$currModule.modNameCourt}}</a>
      {{/foreach}}
      <a href="#" onclick="popChgPwd()">{{tr}}Change password{{/tr}}</a>
      <a href="?m=mediusers&amp;a=edit_infos">{{tr}}My Info{{/tr}}</a>
      <a href="?m=admin&amp;a=edit_prefs&amp;user_id={{$AppUI->user_id}}">{{tr}}Pr�f�rences{{/tr}}</a>
      <a href="?logout=-1">{{tr}}Logout{{/tr}}</a>
    </td>
  </tr>
  {{/if}}
</table>

</td>
</tr>
<tr>
{{/if}}
{{/if}}

<td id="mainPane">

{{if $dialog}}
<div class="dialog" {{if !$errorMessage}} style="display: none"{{/if}} id="systemMsg">
  {{$errorMessage|nl2br|smarty:nodefaults}}
</div>
{{/if}}

