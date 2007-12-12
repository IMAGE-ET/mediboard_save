{{if $catalogue->_id || $search}}
<table class="tbl">
  <tr>
    <th class="title" colspan="6">
      <div style="float: left">
        <form name="frmRecherche">
          <input type="text" name="search" />
        </form>
          <button class="search notext" onclick="search()">Recherche</button>
      </div>
      <a style="float:right;" href="#nothing" onclick="view_log('{{$catalogue->_class_name}}', {{$catalogue->_id}})">
        <img src="images/icons/history.gif" alt="historique" title="Voir l'historique" />
      </a>
      {{if $search}}
        {{$listExams|@count}}
        {{if $listExams|@count != $countExams}}/{{$countExams}}{{/if}} 
        R�sultats pour la recherche ({{$recherche}})
      {{else}}
        {{$catalogue->_view}}
      {{/if}}
    </th>
  </tr>

  <tr>
    <th>Analyse</th>
    <th>Unit�</th>
    <th>R�f�rences</th>
  </tr>

  {{if !$search}}
    {{assign var="listExams" value=$catalogue->_ref_examens_labo}}
  {{else}}
    {{assign var="listExams" value=$listExams}}
  {{/if}}
  
  {{foreach from=$listExams item="curr_examen"}}
  <tr>
    <td>
      <div class="draggable" id="examenCat-{{$curr_examen->_id}}">
      <script type="text/javascript">
        new Draggable('examenCat-{{$curr_examen->_id}}', oDragOptions);
      </script>
      {{$curr_examen->_view}}
      </div>
      <button type="button" class="search notext" onclick="ObjectTooltip.create(this, { popup: true, params: { object_class: 'CExamenLabo', object_id: {{$curr_examen->_id}} } })">
        view
      </button>
    </td>
    {{if $curr_examen->type == "num"}}
    <td>{{$curr_examen->unite}}</td>
    <td>{{$curr_examen->min}} &ndash; {{$curr_examen->max}}</td>
    {{else}}
    <td colspan="2">{{mb_value object=$curr_examen field="type"}}</td>
    {{/if}}
  </tr>
  {{/foreach}}
</table>
{{/if}}