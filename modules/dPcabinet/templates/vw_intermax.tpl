<!-- $Id$ -->

{{mb_script module=patients script=pat_selector}}
{{mb_include module=patients template=inc_intermax}}

<script type="text/javascript">

Intermax.ResultHandler["Consulter Vitale"] =
Intermax.ResultHandler["Lire Vitale"] = function() {
  PatSelector.set = function(id, view) {
    Console.debug(view, "Found and update patient");
    Console.debug(id, "With ID");
  }
  
  PatSelector.options.useVitale = 1;
  PatSelector.pop();
}

updateNomenclatureConsult = function(){
  var url = new Url("dPcabinet", "httpreq_update_nomenclature_consult");
  url.requestUpdate("update_nomenclature_consult");
}

</script>

<table class="tbl">
  <!-- Yoplets for InterMax -->
  <tr>
    <th class="title">
      <button class="change intermax-result" onclick="Intermax.result();" style="float: right">
        Int�grer r�sultat
      </button>
      Fonctions LogicMax disponibles
    </th>
  </tr>

  {{foreach from=$intermaxFunctions key=category item=_functions}}
  <tr>
    <th>{{$category}}</th>
  </tr>
  <tr>
    <td class="button">
      {{foreach from=$_functions item=_function}}
      <button class="search singleclick" onclick="Intermax.trigger('{{$_function}}');">
        {{$_function}}
      </button>
      {{/foreach}}
    </td>
  </tr>
  {{/foreach}}
  
	<tr>
	  <th>Mise � jour de la base AMMAX</th>
	</tr>
	<tr>
		<td class="button">
			<button type="button" class="submit" onclick="updateNomenclatureConsult();">
				Passage de la C de 22 � 23 euros
			</button>
			<span id="update_nomenclature_consult" style="display: inline-block;"></span>
		</td>
	</tr>
</table>

<hr />

{{if $fse->_spec->ds}}

<script type="text/javascript">

function checkBilanCPS() {
  var url = new Url("dPcabinet", "print_bilan_cps");
  url.popup("800", "500", "Bilan CPS");
  return false;
}

</script>

<table class="tbl"> 
  <tr>
    <th class="title">Bilan d'utilisation de LogicMax</th>
  </tr>
  <tr>
    <td class="button">
      <button class="search" onclick="checkBilanCPS()">
        {{tr}}Compute{{/tr}}
      </button>
    </td>
  </tr>
</table>

<hr />

<table class="form">
  <tr>
    <th class="title" colspan="2">
      Interrogation de la base
    </th>
  </tr>
  <tr>
    <td style="width: 50%">
    	{{mb_include template=inc_form_bilan_fse}}
    </td>
    <td style="width: 50%">
      {{mb_include template=inc_form_bilan_lot}}
    </td>
  </tr>
</table>
{{else}}
<div class="big-error">
  Base de donn�es LogicMax injoignable.
  <br/>
  Merci de v�rifier la configuration du module LogicMax. 
</td>

{{/if}}
