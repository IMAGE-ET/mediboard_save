<script>
  Main.add(function() {
    Control.Tabs.create('tabs-configure', true);
    var url = new Url("facturation", "ajax_clean_facture");
    url.requestUpdate("resolutions");
  });
</script>

<ul id="tabs-configure" class="control_tabs">
  <li><a href="#general">Général</a></li>
  <li><a href="#relances">{{tr}}CRelance{{/tr}}</a></li>
  <li><a href="#resolutions">Résolutions</a></li>
  <li><a href="#actions">Actions</a></li>
</ul>

<hr class="control_tabs" />

<div id="general" style="display: none">
  {{mb_include template=CFactureEtablissement_config}}
</div>

<div id="relances" style="display: none">
  {{mb_include template=CRelance_config}}
</div>

<div id="resolutions" style="display: none;">
</div>

<div id="actions" style="display: none">
  {{mb_include template=actions_config}}
</div>
