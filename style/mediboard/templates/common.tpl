<!DOCTYPE html>

{{* MOTW http://msdn.microsoft.com/en-us/library/ms537628(v=vs.85).aspx *}}
{{if $allInOne}}
<!-- saved from url=(0014)about:internet --> 
{{/if}}
 
<html lang="{{$localeInfo.alpha2}}">
<head>
  <!-- Content-Type meta tags need to be the first in the page (even before title) -->
  <meta http-equiv="Content-Type" content="text/html;charset={{$localeInfo.charset}}" />
  <meta http-equiv="X-UA-Compatible" content="IE=8" /> {{* For IE in All-in-one mode *}}
  
  <title>
    {{if !$dialog}}
      {{$conf.page_title}} 
      &mdash; {{tr}}module-{{$m}}-court{{/tr}}
      {{if $a || $tab}}
        &mdash; {{tr}}mod-{{$m}}-tab-{{if $tab}}{{$tab}}{{else}}{{$a}}{{/if}}{{/tr}}
      {{/if}}
    {{else}}
      {{tr}}mod-{{$m}}-tab-{{if $tab}}{{$tab}}{{else}}{{$a}}{{/if}}{{/tr}}
    {{/if}}
  </title>
  
  <meta name="Description" content="Mediboard: Plateforme Open Source pour les Etablissements de Sant�" />
  <meta name="Version" content="{{$version.string}}" />
  
  <!-- iOS specific -->
  {{* Can't use the "apple-mobile-web-app-capable" meta tags because any hyperlink will be opened in Safari *}}
  <link rel="apple-touch-icon" href="images/icons/apple-touch-icon.png?{{$version.build}}" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />
  <meta name="format-detection" content="telephone=no" />
  
  {{if $browser.name == "msie"}}
    <!-- IE9 specific JumpLists -->
    <meta name="application-name" content="{{$conf.page_title}}" />
    <meta name="application-tooltip" content="{{$conf.page_title}}" />
    <meta name="msapplication-starturl" content="./" />
    
    {{foreach from=$modules key=mod_name item=currModule}} 
      {{if $currModule->_can->view && $currModule->mod_ui_active}}
        <meta name="msapplication-task" content="name={{tr}}module-{{$mod_name}}-court{{/tr}};action-uri=./?m={{$mod_name}};icon-uri=./lib/phpThumb/phpThumb.php?src=../../modules/{{$mod_name}}/images/icon.png&amp;ws=16&amp;f=ico" />
      {{/if}}
    {{/foreach}}
  {{/if}}
  
  <script type="text/javascript">
    // This needs to be at the very beginning of the page
    __loadStart = (new Date).getTime();
    
    {{if $offline}}
    var config = {{$configOffline|@json}};
    {{/if}}
    
    var Preferences = {{$app->user_prefs|@json}},
        User = {{if $app->_ref_user}}{{"utf8_encode"|array_map_recursive:$app->_ref_user->_basic_info|@json}}{{else}}{}{{/if}},
        sessionLocked = {{$smarty.session.locked|@json}},
        App = { 
          m: "{{$m}}",
          a: "{{$a}}",
          tab: "{{$tab}}",
          action: "{{$action}}",
          actionType: "{{$actionType}}",
          dialog: "{{$dialog}}",
          config: {
            log_js_errors: {{if $conf.log_js_errors}}true{{else}}false{{/if}}
          },
          readonly: "{{$conf.readonly}}" == 1 && User.id != null,
          touchDevice: /^ipad|iphone$/i.test("{{$browser.name}}")
        };
    
    var Mediboard = {{$version|@json}};
  </script>

  {{$mediboardShortIcon|smarty:nodefaults}}
  {{if $uistyle != 'mediboard'}}
    {{$mediboardCommonStyle|smarty:nodefaults}}
  {{/if}}
  {{$mediboardStyle|smarty:nodefaults}}
  
  <!--[if lte IE 8]>
  <link rel="stylesheet" type="text/css" href="style/mediboard/ie.css?build={{$version.build}}" media="all" />
  <![endif]-->
  
  {{$mediboardScript|smarty:nodefaults}}
  
  <script type="text/javascript">
    {{if $dialog}}
      Event.observe(document, 'keydown', closeWindowByEscape);
    {{/if}}
    
  	{{if @$conf.weinre_debug_host}}
      setTimeout(function() {
        $$('head')[0].insert(DOM.script({src: 'http://{{$conf.weinre_debug_host}}/target/target-script-min.js'}));
      }, 0);
    {{/if}}
    
    {{if $allInOne}}
      {{* any ajax method > /dev/null *}}
      Class.extend(Url, {
        requestUpdate: function(){},
        requestJSON: function(){},
        periodicalUpdate: function(){}
      });
    {{/if}}
  </script>
</head>

<body class="{{if @$app->user_prefs.touchscreen == 1 || $browser.name == 'ipad'}} touchscreen {{/if}} {{if $browser.name == 'ipad'}} ipad {{/if}}">

{{if $browser.name == "msie"}}
  {{include file="../../mediboard/templates/ie.tpl" nodebug=true}}
{{/if}}

{{if $conf.readonly}}
<div class="big-info not-printable">
  <big>{{tr}}Mode-readonly-title{{/tr}}</big>
  <div>{{tr}}Mode-readonly-description{{/tr}}</div>
  <div>{{tr}}Mode-readonly-disclaimer{{/tr}}</div>
</div>
{{/if}}

<!-- Loading divs -->
<div id="waitingMsgMask" style="display: none;"></div>

<div id="waitingMsgText" style="top: -1500px;"><!-- This trick is to preload the background image -->
  <div class="loading">{{tr}}Loading in progress{{/tr}}</div>
</div>

<div id="sessionLock" style="display: none;">
  {{if $app->_ref_user}}
  <h1>{{tr}}Session locked{{/tr}} - {{$app->_ref_user}}</h1>
  <form name="sessionLockForm" method="post" action="?" onsubmit="return Session.request(this)">
    <input type="hidden" name="unlock" value="unlock" />
    <input type="hidden" name="username" value="{{$app->_ref_user->_user_username}}" />
    <div>
      <label for="password">{{tr}}Password{{/tr}}</label>
      <input type="password" name="password" />
    </div>
    <div>
      <button type="submit" class="tick">{{tr}}Unlock{{/tr}}</button>
      <button type="button" class="cancel" onclick="Session.close()">{{tr}}Logout{{/tr}}</button>
      <button type="button" class="tick" onclick="Session.window.close(); $('main').show(); UserSwitch.popup();">{{tr}}User switch{{/tr}}</button>
    </div>
    <div class="login-message"></div>
  </form>
  {{/if}}
</div>

<div id="userSwitch" style="display: none;">
  <h1>{{tr}}User switch{{/tr}}</h1>
  <form name="userSwitchForm" method="post" action="?" onsubmit="return UserSwitch.login(this)">
    <input type="hidden" name="m" value="admin" />
    <input type="hidden" name="dosql" value="do_login_as" />
    <div style="text-align: right;">
      <label for="username">{{tr}}User{{/tr}} </label> <input name="username" tabIndex="1000" type="text" class="notNull" />
      
      {{if ($app->user_type != 1) || $conf.admin.LDAP.ldap_connection}}
        <br /><label for="password">{{tr}}Password{{/tr}} </label> <input name="password" tabIndex="1001" type="password" />
      {{/if}}
    </div>
    <div>
      <button type="submit" class="tick">{{tr}}Switch{{/tr}}</button>
      <button type="button" class="cancel" onclick="UserSwitch.cancel()">{{tr}}Cancel{{/tr}}</button>
    </div>
    <div class="login-message"></div>
  </form>
</div>

<!-- Javascript Console -->
<div id="console" style="display: none;">
  <div class="title">
    <div class="close" onclick="Console.hide()">X</div>
    <div class="toggle" onclick="Console.toggle()">_</div>
    Javascript console
  </div>
  <div class="body"></div>
  <form name="debug-console" method="get" onsubmit="return Console.exec(this)">
    <input type="text" size="90" name="code" value="" />
    <button class="tick notext" onclick=""></button>
  </form>
</div>

<!-- Mails -->
{{if !$dialog && @count($mails)}}
<div class="small-mail not-printable" onmouseover="ObjectTooltip.createDOM(this, 'mail-details');">
  <label>
    {{tr}}CMbMail{{/tr}} :
    
    {{if array_key_exists("received", $mails)}}
      {{$mails.received|@count}} {{tr}}CMbMail._to_state.received{{/tr}}
    {{/if}}
    
    {{if count($mails) == 2}}&ndash;{{/if}}
    
    {{if array_key_exists("starred", $mails)}}
      {{$mails.starred|@count}} {{tr}}CMbMail._to_state.starred{{/tr}}
    {{/if}}
  </label>
</div>

<div id="mail-details not-printable" style="display: none;">
  <table class="tbl">
  {{foreach from=$mails key=to_state item=_mails}}
    <tr>
      <th class="category" colspan="10">{{tr}}CMbMail._to_state.{{$to_state}}{{/tr}}</th>
    </tr>
    {{foreach from=$_mails item=_mail}}
      <tr>
        <td>
          <div class="mediuser" style="border-color: #{{$_mail->_ref_user_from->_ref_function->color}};">{{$_mail->_ref_user_from}}</div>
        </td>
        <td>
          <a href="#Read-{{$_mail->_guid}}" onclick="MbMail.edit({{$_mail->_id}})">{{$_mail->subject}}</a>
        </td>
        <td>
          <label title="{{mb_value object=$_mail field=date_sent}}">
            {{mb_value object=$_mail field=date_sent format=relative}}
          </label>
        </td>
      </tr>
    {{/foreach}}
  {{/foreach}}
  </table>
</div>
{{/if}}