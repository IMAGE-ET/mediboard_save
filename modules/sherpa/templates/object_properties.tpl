{{if !$suppressHeaders}}
<!-- Filter -->
<form name="Filter" action="?m={{$m}}" method="get" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="tab" value="{{$tab}}" />

<table class="form">
  <tr>
    <th class="category" colspan="10">Propri�t� d'objets pour</th>
  </tr>

  <tr>
    <th>{{mb_label object=$filter field=object_class}}</th>
    <td>
      <select name="object_class">
        <option value="">&mdash; Choisir une classe</option>
		    {{foreach from=$queriable item=_class}}
		    <option value="{{$_class}}" {{if $filter->object_class == $_class}}selected="selected"{{/if}}>
		      {{$_class}}
		    </option>
		    {{/foreach}} 
      </select>
    </td>
  </tr>

  <tr>
    <th>{{mb_label object=$filter field=id400}}</th>
    <td>{{mb_field object=$filter field=id400}}</td>
  </tr>

  <tr>
    <td class="button" colspan="10">
      <button class="tick" type="submit">
        Lancer la requ�te
      </button>
    </td>
  </tr>
</table>

</form>


<div class="big-info">
  Merci de choisir la classe et l'identifiant externe.
  Les classes disponibles sont 
  <ul>
    {{foreach from=$queriable item=_class}}
    <li><tt>{{$_class}}</tt> : {{tr}}{{$_class}}{{/tr}}</li>
    {{/foreach}} 
  </ul>
  
  Pour obtenir une version fichier, sans balises HTML, il faut rajouter le param�tre HTTP
  <ul>
    <li><tt>suppressHeaders=1</tt></li>
  </ul>
</div>

<pre>
{{/if}}
{{foreach from=$response key=propName item=propValue}}
{{$propName}} = {{$propValue}}
{{/foreach}}
{{if !$suppressHeaders}}
</pre>
{{/if}}