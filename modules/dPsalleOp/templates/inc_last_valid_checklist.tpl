{{if $date_checklist|date_format:$conf.date != $date|date_format:$conf.date}}
  <div style="float: right;text-align: center;">
    <button class="checklist" type="button" onclick="EditCheckList.edit('{{$object_id}}', '{{$date}}', '{{$type}}');">{{tr}}CDailyCheckList._type.{{$type}}{{/tr}}</button>
    {{if $date_checklist}}
      <div class="info">Derni�re validation: {{$date_checklist|date_format:$conf.date}}</div>
    {{/if}}
  </div>
{{/if}}