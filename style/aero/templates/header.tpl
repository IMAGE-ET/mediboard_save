<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>Mediboard :: Syst�me de gestion des structures de sant�</title>
  <meta http-equiv="Content-Type" content="text/html;charset={{$localeCharSet}}" />
  <meta name="Description" content="Mediboard: Plateforme Open Source pour les Etablissement de Sant�" />
  <meta name="Version" content="{{$mediboardVersion}}" />
  {{$mediboardShortIcon|smarty:nodefaults}}
  {{$mediboardCommonStyle|smarty:nodefaults}}
  {{$mediboardStyle|smarty:nodefaults}}
  {{$mediboardScript|smarty:nodefaults}}
</head>

<body onload="main()">

<div id="waitingMsgMask" class="chargementMask" style="display: none;"></div>
<div id="waitingMsgText" class="chargementText" style="display: none;">
  <table class="tbl">
    <tr>
      <th class="title">
        <div class="loading"><span id="waitingInnerMsgText">Chargement en cours</span></div>
      </th>
    </tr>
  </table>
</div>

<script type="text/javascript">
function popChgPwd() {
  var url = new Url;
  url.setModuleAction("admin", "chpwd");
  url.popup(400, 300, "ChangePassword");
}

function chgMenu(id, type) {
  $(id).style.visibility = type;
}
</script>

{{if !$dialog}}

{{foreach from=$messages item=currMsg}}
  <div style='background: #aaa; color: #fff;'><strong>{{$currMsg->titre}}</strong> : {{$currMsg->corps}}</div>
{{/foreach}}

<table id="headerMenu">
  <tr>
    <td class="menuTitle" id="modMenu" onmouseover="chgMenu('modMenuList', 'visible')" onmouseout="chgMenu('modMenuList', 'hidden')">
      Modules
      <div id="modMenuList" style="visibility: hidden; position: absolute">
        {{foreach from=$affModule item=currModule}}
        <a href="?m={{$currModule.modName}}" title="{{$currModule.modNameLong}}">
        <div class="menuItem {{if $currModule.modName==$m}}selected{{/if}}">
          {{assign var="modname" value=$currModule.modName}}
          {{thumb src="modules/$modname/images/$modname.png" h="16" w="16" f="png"}}
          {{$currModule.modNameCourt}}
        </div>
        </a>
        {{/foreach}}
      </div>
    </td>
    <td class="menuTitle" id="toolMenu" onmouseover="chgMenu('toolMenuList', 'visible')" onmouseout="chgMenu('toolMenuList', 'hidden')">
      Outils
      <div id="toolMenuList" style="visibility: hidden; position: absolute">
        <div class="menuItem">
          <img src="style/aero/images/help.png" alt="Aide" border="0" height="16" width="16" />
          {{$helpOnline|smarty:nodefaults}}
        </div>
        <div class="menuItem">
          <img src="style/aero/images/modif.png" alt="Suggestions" border="0" height="16" width="16" />
          {{$suggestion|smarty:nodefaults}}
        </div>
        <a href="#" onclick="popChgPwd();">
        <div class="menuItem">
            <img src="style/aero/images/passwd.png" alt="Mot de passe" border="0" height="16" width="16" />
            Changez votre mot de passe
        </div>
        </a>
        <a href="?m=admin&amp;a=edit_prefs&amp;user_id={{$AppUI->user_id}}">
        <div class="menuItem">
          <img src="style/aero/images/prefs.png" alt="Preferences" border="0" height="16" width="16" />
          {{tr}}Pr�f�rences{{/tr}}
        </div>
        </a>
        <a href="?logout=-1">
        <div class="menuItem">
          <img src="style/aero/images/logout.png" alt="{{tr}}Logout{{/tr}}" border="0" height="16" width="16" />
          {{tr}}Logout{{/tr}}
        </div>
        </a>
      </div>
    </td>
    {{if $titleBlockData.icon}}
    <td class="titlecell">
      {{$titleBlockData.icon|smarty:nodefaults}}
    </td>
    {{/if}}
    <td class="titlecell">
      {{tr}}{{$titleBlockData.name}}{{/tr}}
    </td>
    <td class="message">
      <div {{if $dialog}}class="dialog" {{if !$errorMessage}} style="display: none"{{/if}}{{/if}} id="systemMsg">
        {{$errorMessage|smarty:nodefaults}}
      </div>
    </td>
    <td class="end">
      <form name="ChangeGroup" action="" method="get">
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
  </tr>
</table>

{{/if}}

<table id="main" class="{{$m}}">
  <tr>