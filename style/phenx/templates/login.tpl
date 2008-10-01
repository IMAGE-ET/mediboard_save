{{include file="common.tpl" nodebug=true}}

<div id="login" {{if $dialog}}style="height: 50px; margin: auto; position: relative; top: 0; left: 0;"{{/if}}>
  {{if !$dialog}}
    <a href="http://www.mediboard.org/">
      <img src="images/pictures/mbNormal.gif" alt="{{$dPconfig.company_name}}" width="290" height="107" /><br />
    </a>
  {{/if}}

  <form name="loginFrm" action="?" method="post" onsubmit="return checkForm(this)">
    <input type="hidden" name="login" value="{{$time}}" />
    <input type="hidden" name="redirect" value="{{$redirect|smarty:nodefaults}}" />
    <input type="hidden" name="dialog" value="{{$dialog}}" />
    <table class="form">
      <tr>
        <th style="width: 1%;"><label for="username" title="{{tr}}CUser-user_username-desc{{/tr}}">{{tr}}CUser-user_username{{/tr}}</label></th>
        <td style="width: 1%;"><input type="text" class="notNull str" size="25" maxlength="20" name="username" /></td>
      </tr>
      <tr>
        <th><label for="password" title="{{tr}}CUser-user_password-desc{{/tr}}">{{tr}}CUser-user_password{{/tr}}</label></th>
        <td>
          <input type="password" class="notNull str" size="25" maxlength="32" name="password" />
          <button class="tick" type="submit" name="login">{{tr}}Login{{/tr}}</button>
        </td>
      </tr>
    </table>
  </form>
  <div id="systemMsg">{{$errorMessage|nl2br|smarty:nodefaults}}</div>
</div>

{{if !$dialog}}
  <div id="version">Mediboard {{$version.string}}</div>
  <a id="sponsor" href="http://www.mozilla-europe.org/fr/products/firefox/" title="Pour un meilleur confort et plus de s�curit�, nous recommandons d'utiliser le navigateur Firefox">
    <img src="http://www.spreadfirefox.com/community/images/affiliates/Buttons/80x15/firefox_80x15.png" alt="Firefox Logo" />
  </a>
{{/if}}


</body>
</html>