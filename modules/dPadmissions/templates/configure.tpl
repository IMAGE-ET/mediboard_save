<script type="text/javascript">
  Main.add(function() {
    Control.Tabs.create('tabs-config', true, {afterChange: function(container) {
      if (container.id == 'groups-configs') {
        Configuration.edit('dPadmissions', ['CGroups'], $('groups-configs'));
      }
    }});
  });
</script>

<ul id="tabs-config" class="control_tabs">
  <li><a href="#configs">Configurations</a></li>
  <li><a href="#groups-configs">Configuration par �tablissement</a></li>
</ul>

<div id="configs" style="display: none;">

  <form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

    <input type="hidden" name="dosql" value="do_configure" />
    <input type="hidden" name="m" value="system" />

    <table class="form">
      <tr>
        <th class="title" colspan="2">Affichage</th>
      </tr>

      {{mb_include module=system template=inc_config_enum var=fiche_admission values="a4|a5"}}
      {{mb_include module=system template=inc_config_bool var=show_dh}}
      {{mb_include module=system template=inc_config_bool var=show_prestations_sorties}}
      <tr>
        {{assign var="var" value="hour_matin_soir"}}
        <th>
          <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}{{/tr}}">
            {{tr}}config-{{$m}}-{{$var}}{{/tr}}
          </label>
        </th>
        <td class="greedyPane">
          <input type="hidden" name="{{$m}}[{{$var}}]" class="time" value="{{$conf.$m.$var}}"/>
          <script type="text/javascript">
            Calendar.regField(getForm("editConfig").elements["{{$m}}[{{$var}}]"], null, {datePicker: false, timePicker: true});
          </script>
        </td>
      </tr>

      {{mb_include module=system template=inc_config_bool var=show_curr_affectation}}
      {{mb_include module=system template=inc_config_enum var=auto_refresh_frequency values="90|180|300|600"}}
      {{mb_include module=system template=inc_config_bool var=show_deficience}}

      <tr>
        <th style="width: 50%"></th>
        <td class="text">
          {{assign var=antecedents value=$conf.dPpatients.CAntecedent.types}}
          {{if preg_match("/deficience/", $antecedents)}}
            <div class="small-success">
              Le type d'ant�c�dent <strong>D�ficience</strong> est bien coch� dans le volet Ant�c�dents de
              <a href="?m=patients&tab=configure">l'onglet Configurer du module Dossier Patient</a>
            </div>
          {{else}}
            <div class="small-warning">
              Pour afficher cette ic�ne, le type d'ant�c�dent <strong>D�ficience</strong>
              doit �tre coch� dans le volet Ant�c�dents de
              <a href="?m=patients&tab=configure">l'onglet Configurer du module Dossier Patient</a>
            </div>
          {{/if}}
        </td>
      </tr>
      <tr>
        <td class="button" colspan="100">
          <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
        </td>
      </tr>
    </table>
  </form>
</div>

<div id="groups-configs" style="display: none;"></div>