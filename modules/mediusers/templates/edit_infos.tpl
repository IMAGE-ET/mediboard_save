{{mb_script module=admin     script=preferences     ajax=true}}
{{mb_script module=patients  script=autocomplete    ajax=true}}
{{mb_script module=system    script=exchange_source ajax=true}}

<script type="text/javascript">

  Main.add(function () {
    Control.Tabs.create("tab_edit_mediuser", true, {
      afterChange: function (container) {
        switch (container.id) {
          case "edit-preferences":
            Preferences.refresh('{{$user->_id}}');
            break;
          case "edit-exchange_source":
            ExchangeSource.refreshUserSources();
            break;
          case "edit-holidays":
            PlageConge.refresh();
            break;
          case "edit-astreintes":
            PlageAstreinte.refreshList('edit-astreintes', '{{$user->_id}}');
            break;
          case "list_bris_de_glace":
            BrisDeGlace.refreshList('list_bris_de_glace', '{{$user->_id}}');
            break;

          case "edit-factureox":
            factureUser();
            break;
          case "support" :
          case "didac" :
          case "edit-mediuser":
          default :
            break;
        }
      }
    });
  });
</script>

<ul id="tab_edit_mediuser" class="control_tabs">
  <li><a href="#edit-mediuser">{{tr}}Identity{{/tr}}</a></li>

  <li><a href="#edit-exchange_source">{{tr}}CExchangeSource.plural{{/tr}}</a></li>

  <li><a href="#edit-preferences">{{tr}}Preferences{{/tr}}</a></li>

  {{if @$modules.dPpersonnel->_can->read}}
    <li>
      {{mb_script module=personnel script=plage}}
      <script>
        PlageConge.refresh = function() {
          PlageConge.content();
          PlageConge.loadUser('{{$user->_id}}', '');
          PlageConge.edit('','{{$user->_id}}');
        }
      </script>
      <a href="#edit-holidays">{{tr}}Holidays{{/tr}}</a>
    </li>
  {{/if}}

  {{if "astreintes"|module_active}}
    {{mb_script module=astreintes script=plage}}
    <li><a href="#edit-astreintes">{{tr}}CPlageAstreinte{{/tr}}</a></li>
  {{/if}}

  {{if $b2g}}
    {{mb_script module=admin script=brisDeGlace}}
    <li><a href="#list_bris_de_glace">{{tr}}CBrisDeGlace{{/tr}}</a></li>
  {{/if}}

  {{if "oxFacturation"|module_active}}
    <script>
      factureUser = function() {
        var url = new Url("oxFacturation" , "vw_factures_user");
        url.requestUpdate('edit-factureox');
      }
    </script>
    <li><a href="#edit-factureox" id="edit-factureox-count">{{tr}}CFactureOX{{/tr}}</a></li>
  {{/if}}

  {{if "ecap"|module_active}}
    {{mb_script module=astreintes script=plage}}
    <li><a href="#support">{{tr}}Support{{/tr}}</a></li>
  {{/if}}

  {{if "didacticiel"|module_active}}
    <li><a href="#didac">{{tr}}E-learning{{/tr}}</a></li>
  {{/if}}
</ul>

<div id="edit-mediuser" style="display: block;">
<table class="main">
  <tr>
    <td class="halfPane" style="width: 60%">
      {{mb_include template=inc_info_mediuser}}
    </td>

    <td class="halfPane">
      {{mb_include template=inc_info_function}}
    </td>
  </tr>
</table>
</div>

<div id="edit-exchange_source" style="display: none;">
</div>

<div id="edit-preferences" style="display: none;">
</div>

{{if "ecap"|module_active}}
<div id="support" style="display: none;">
  {{mb_include module=ecap template=support}}
</div>
{{/if}}

{{if "astreintes"|module_active}}
  <div id="edit-astreintes" style="display: none;">
  </div>
{{/if}}

{{if $b2g}}
  <div id="list_bris_de_glace" style="display: none;">
  </div>
{{/if}}

{{if @$modules.dPpersonnel->_can->read}}
<div id="edit-holidays" style="display: none;">
  <table class="main">
    <tr>
      <td class="halfPane" id = "vw_user">
      </td> 
      <td class="halfPane" id = "edit_plage">
      </td>
    </tr>
    <tr>
      <td colspan="2" id="planningconge"></td>
    </tr>
  </table>
</div>
{{/if}}

{{if "oxFacturation"|module_active}}
  <div id="edit-factureox" style="display: none;">
  </div>
  <script>
    Main.add(function() {
      factureUser();
    });
  </script>
{{/if}}

{{if "didacticiel"|module_active}}
  <div id="didac" style="display: none;">
    {{mb_include module=didacticiel template=vw_didacticiels}}
  </div>
{{/if}}