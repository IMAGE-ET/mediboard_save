<script type="text/javascript">

function changePraticien(prat_id) {
}

function setClose(modele_id, object_id) {
  if (window.opener) {
    var oSelector = window.opener.modeleSelector[{{$target_id}}];
    oSelector.set(modele_id, object_id);
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
  {{foreach from=$praticiens item=_praticien}}
    <option class="mediuser" style="border-color: #{{$_praticien->_ref_function->color}};" value="{{$_praticien->user_id}}" 
      {{if $_praticien->_id == $praticien->_id}} selected="selected" {{/if}}>
      {{$_praticien->_view}}
    </option>
  {{/foreach}}
</select>
</form>
{{/if}}

{{if $praticien->_can->edit}}
	<!-- Choix du mod�le praticien -->
	<script type="text/javascript">
		Main.add(function () {
		  new Control.Tabs('tabs-modeles');
		});
	</script>
	
	<ul id="tabs-modeles" class="control_tabs">
	{{foreach from=$modelesCompat key=class item=modeles}}
	  <li><a href="#{{$class}}">{{tr}}{{$class}}{{/tr}}</a></li>
	{{/foreach}}
	{{foreach from=$modelesNonCompat key=class item=modeles}}
	  <li><a href="#{{$class}}" class="wrong">{{tr}}{{$class}}{{/tr}}</a></li>
	{{/foreach}}
	</ul>
	<hr class="control_tabs" />
	
	{{foreach from=$modelesCompat key=class item=modeles}}
	  {{include file="inc_vw_list_models.tpl"}}
	{{/foreach}}
	
	{{foreach from=$modelesNonCompat key=class item=modeles}}
	  {{include file="inc_vw_list_models.tpl"}}
	{{/foreach}}
	
	<div class="small-info">
	  Cliquez sur un mod�le pour l'utiliser !
	  <br />
	  Les sections gris�es contiennent des champs potentiellement incompatibles avec le contexte courant.
	</div>
{{else}}
	<div class="big-info">
	  Les mod�les pour l'utilisateur courant ne vous sont pas accessibles.<br />
	  Vous devriez changer l'utilisateur pour acc�der � d'autres mod�les.
	</div>
{{/if}}
