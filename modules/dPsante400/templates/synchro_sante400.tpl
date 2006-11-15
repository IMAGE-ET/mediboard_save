<script type="text/javascript">
function explain(iRec) {
  url = new Url();
  url.setModuleAction("{{$m}}", "{{$action}}");
  url.addParam("rec", iRec);
  url.addParam("verbose", 1);
  url.popup(800, 500, "Explaination Import Sante400");
}
  
</script>
  
{{if !$connection}}
<div class="big-error">
Impossible d'�tablir la connexion avec le serveur Sant�400<br/>
Merci de v�rifier les param�tres de la configuration ODBC pour la source 'sante400'
</div>
{{/if}}

<table class="main">

<tr>
  <td style="text-align: right">

    <form action="?" name="markFilter" method="get">

    <input type="hidden" name="m" value="{{$m}}" />
    <input type="hidden" name="{{$actionType}}" value="{{$action}}" />

    <label for="marked" title="Types de mouvements">Type de mouvements</label>
    <select name="marked" onchange="this.form.submit()">
      <option value="0" {{if !$marked}}selected="selected"{{/if}}>Mouvements � traiter</option>
      <option value="1" {{if  $marked}}selected="selected"{{/if}}>Mouvement trait�s avec un erreur</option>
    </select>

    </form>
  
  </td>
</tr>

<tr>
  <td>

<table class="tbl">

<tr>
  <th class="title" colspan="11">
  	Imports de {{$mouvs|@count}} mouvements de s�jours
  	sur {{$count}} disponibles</th>
</tr>

<tr>
  <th colspan="3">Sant� 400</th>
  <th colspan="8">Import Mediboard</th>
</tr>

<tr>
  <th>Num�ro</th>
  <th>Type</th>
  <th>Marque</th>
  <th>Etablissement</th>
  <th>Cabinet</th>
  <th>Chirurgien</th>
  <th>Patient</th>
  <th>Sejour</th>
  <th>Naissance</th>
  <th>Marque</th>

  {{if !$dialog}}
  <th>D�tails</th>
  {{/if}}

</tr>

{{foreach from=$mouvs item=curr_mouv}}
<tr>	
  <td>{{$curr_mouv->rec}}</td>
  <td>{{$curr_mouv->type}}</td>
  <td>{{$curr_mouv->prod}}</td>
  <td>{{if @$curr_mouv->status.1 == "E"}}<div class="message">Import�</div>{{else}}<div class="warning">Echec</div>{{/if}}</td>
  <td>{{if @$curr_mouv->status.2 == "F"}}<div class="message">Import�</div>{{else}}<div class="warning">Echec</div>{{/if}}</td>
  <td>{{if @$curr_mouv->status.3 == "C"}}<div class="message">Import�</div>{{else}}<div class="warning">Echec</div>{{/if}}</td>
  <td>{{if @$curr_mouv->status.4 == "P"}}<div class="message">Import�</div>{{else}}<div class="warning">Echec</div>{{/if}}</td>
  <td>{{if @$curr_mouv->status.5 == "S"}}<div class="message">Import�</div>{{else}}<div class="warning">Echec</div>{{/if}}</td>
  <td>{{if @$curr_mouv->status.6 == "N"}}<div class="message">Import�</div>{{else}}<div class="warning">Echec</div>{{/if}}</td>
  <td>{{$curr_mouv->status}}</td>

  {{if !$dialog}}
  <td>
    <button class="search" onclick="explain({{$curr_mouv->rec}})">Explications</button>
  </td>
  {{/if}}

</tr>
{{/foreach}}

</table>

  </td>
</tr>

</table>