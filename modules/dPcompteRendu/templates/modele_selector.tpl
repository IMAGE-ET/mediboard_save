<script>
  function setClose(modele_id, object_id, fast_edit) {
    if (window.opener) {
      var oSelector = window.opener.modeleSelector[{{$target_id}}];
      oSelector.set(modele_id, object_id, fast_edit);
    }
    window.close();
  }
</script>

<h2>Mod�les pour {{$praticien->_view}} ({{tr}}{{$target_class}}{{/tr}})</h2>

<!-- Choix du praticien -->
{{if is_array($praticiens)}}
  <form name="addConsFrm" action="?" method="get">
    <input type="hidden" name="m" value="{{$m}}" />
    <input type="hidden" name="a" value="{{$a}}" />
    <input type="hidden" name="dialog" value="{{$dialog}}" />
    <input type="hidden" name="object_id" value="{{$target_id}}" />
    <input type="hidden" name="object_class" value="{{$target_class}}" />

    <label for="praticien_id" title="Choisir un autre utilisateur permet d'acc�der � ses mod�les">
      Changer d'utilisateur
    </label> :

    <select name="praticien_id" class="ref" onchange="this.form.submit()">
      <option value="">&mdash; Choisir un utilisateur</option>
      {{mb_include module=mediusers template=inc_options_mediuser list=$praticiens selected=$praticien->_id}}
    </select>
  </form>
{{/if}}

{{if !$praticien->_can->edit}}
  <div class="big-info">
    Les mod�les pour l'utilisateur courant ne vous sont pas accessibles.<br />
    Vous devriez changer l'utilisateur pour acc�der � d'autres mod�les.
  </div>
  {{mb_return}}
{{/if}}

<!-- Choix du mod�le praticien -->
<script>
  Main.add(function () {
    Control.Tabs.create('tabs-modeles');
  });
</script>

<ul id="tabs-modeles" class="control_tabs">
  {{foreach from=$modelesCompat key=class item=modeles}}
    <li><a href="#{{$class}}" {{if !$modeles.prat|@count && !$modeles.func|@count && !$modeles.etab|@count}}class="empty"{{/if}}>{{tr}}{{$class}}{{/tr}}</a></li>
  {{/foreach}}
  {{foreach from=$modelesNonCompat key=class item=modeles}}
    <li><a href="#{{$class}}" class="{{if !$modeles.prat|@count && !$modeles.func|@count && !$modeles.etab|@count}}empty{{else}}wrong{{/if}}">{{tr}}{{$class}}{{/tr}}</a></li>
  {{/foreach}}
</ul>

{{foreach from=$modelesCompat key=class item=modeles}}
  {{mb_include template=inc_vw_list_models}}
{{/foreach}}

{{foreach from=$modelesNonCompat key=class item=modeles}}
  {{mb_include template=inc_vw_list_models}}
{{/foreach}}

<div class="small-info">
  Cliquez sur un mod�le pour l'utiliser !
  <br />
  <span class="wrong" style="width: 50px; display: inline-block; margin-bottom: 1px;">&nbsp;</span> Sections potentiellement incompatibles avec le contexte courant.<br />
  <span class="empty" style="width: 50px; display: inline-block;">&nbsp;</span> Sections vides.
</div>