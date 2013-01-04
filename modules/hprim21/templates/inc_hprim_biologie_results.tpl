<h1>
  <small style="float: right">[{{$header.0}}]</small>
  R�sultats de laboratoire &ndash; {{$header.9}}
</h1>

<table class="main form">
  <tr>
    <th colspan="4" class="title">{{$header.1}} {{$header.2}} &ndash; {{$header.6}} [{{$header.8}}]</th>
  </tr>
  <tr>
    <th>Exp�diteur</th>
    <td><small>[{{$header.10.0}}]</small> {{$header.10.1}}</td>

    <th>Destinataire</th>
    <td><small>[{{$header.11.0}}]</small> {{$header.11.1}}</td>
  </tr>
  <tr>
    <td colspan="4"><hr /></td>
  </tr>
  <tr>
    <th>Adresse</th>
    <td>
    {{$header.3}}<br />
    {{$header.4}}<br />
    {{$header.5}}
    </td>

    <th>Num�ro de s�curit� sociale</th>
    <td>{{$header.7}}</td>
  </tr>
</table>

<h1>Courrier</h1>
<pre>{{$text}}</pre>

<h1>R�sultats</h1>
<table class="main tbl">
  <tr>
    <th>Libell�</th>
  {{*<th>Code</th>*}}
  {{*<th>Type de r�sultat</th>*}}
    <th>R�sultat</th>
    <th>Unit�</th>
    <th>Val. normale inf.</th>
    <th>Val. normale sup.</th>
    <th>Anorm.</th>
    <th>Statut</th>
    <th>R�sultat 2</th>
    <th>Unit� 2</th>
    <th>Val. norm. inf. 2</th>
    <th>Val. norm. sup. 2</th>
  </tr>
{{foreach from=$results item=_result}}
  <tr>
    <td>{{$_result.label}}</td>
  {{*<td>{{$_result.code}}</td>*}}
  {{*<td>{{$_result.type}}</td>*}}
    <td class="{{$_result.anormal_class}}">
      {{$_result.value}}
    </td>
    <td>{{$_result.unit}}</td>
    <td>{{$_result.min}}</td>
    <td>{{$_result.max}}</td>
    <td>
      {{if $_result.anormal != "N"}}
          {{$_result.anormal_text}}
        {{/if}}
    </td>
    <td>{{$_result.status}}</td>
    <td>{{$_result.value2}}</td>
    <td>{{$_result.unit2}}</td>
    <td>{{$_result.min2}}</td>
    <td>{{$_result.max2}}</td>
  </tr>
{{/foreach}}
</table>