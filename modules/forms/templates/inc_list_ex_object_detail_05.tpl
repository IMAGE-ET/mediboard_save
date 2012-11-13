<script type="text/javascript">
showExClassFormSelect = function(select){
  var selected = select.options[select.selectedIndex];
  var reference_class = selected.get("reference_class");
  var reference_id    = selected.get("reference_id");
  var host_class      = selected.get("host_class");
  var event_name      = selected.get("event_name");
  
  showExClassForm(selected.value, reference_class+"-"+reference_id, host_class+"-"+event_name, null, event_name, '@ExObject.refreshSelf.{{$self_guid}}');
  
  select.selectedIndex = 0;
};

loadExObjectsList = function(element, reference_class, reference_id, ex_class_id){
  element = $(element);
  
  var row  = element.up('tr');
  var body = element.up('tbody');
  
  var listContainer = body.down('.list-container');
  if (listContainer.visible()) {
    listContainer.hide();
    row.removeClassName('selected');
    body.removeClassName('opened');
    return;
  }
  
  element.up('table').select('.list-container').each(function(c){
    c.previous('tr').removeClassName('selected');
  });
  
  listContainer.show();
  
  row.addClassName('selected');
  body.addClassName('opened');
  ExObject.loadExObjects(reference_class, reference_id, listContainer.down('td'), 1, ex_class_id);
};

filterExClasses = function(input){
  var keyword = input.value.toLowerCase();
  var lines = $(input).up('table').select("tbody[data-name]");
  
  if (keyword == "") {
    lines.invoke("show");
    return;
  }
  
  lines.invoke("hide");
  
  lines.filter(function(line){
    return line.get('name').toLowerCase().indexOf(keyword) > -1;
  }).invoke("show");
};
</script>

<table class="main tbl treegrid">
  <tr>
    <td colspan="2">
      <label style="float: right;">
        Recherche
        <input type="text"{{* type="search" *}} onkeyup="filterExClasses(this)" />
        <button class="cancel notext" onclick="var input = $(this).previous(); $V(input,''); filterExClasses(input)"></button>
      </label>
      
      {{if $ex_classes_creation|@count}}
      <select onchange="showExClassFormSelect(this)" style="width: 20em;">
        <option value=""> &ndash; Remplir nouveau formulaire </option>
        {{foreach from=$ex_classes_creation item=_ex_class_events key=_ex_class_id}}
          {{if $_ex_class_events|@count > 1}}
            <optgroup label="{{$ex_classes.$_ex_class_id}}">
          
            {{foreach from=$_ex_class_events item=_ex_class_event}}
              <option value="{{$_ex_class_event->ex_class_id}}" 
                      data-reference_class="{{$reference_class}}" 
                      data-reference_id="{{$reference_id}}"
                      data-host_class="{{$_ex_class_event->host_class}}"
                      data-event_name="{{$_ex_class_event->event_name}}">
                {{$_ex_class_event}}
              </option>
            {{/foreach}}
            
            </optgroup>
          {{else}}
            {{foreach from=$_ex_class_events item=_ex_class_event}}
              <option value="{{$_ex_class_event->ex_class_id}}" 
                      data-reference_class="{{$reference_class}}" 
                      data-reference_id="{{$reference_id}}"
                      data-host_class="{{$_ex_class_event->host_class}}"
                      data-event_name="{{$_ex_class_event->event_name}}">
                {{$ex_classes.$_ex_class_id}}
              </option>
            {{/foreach}}
          {{/if}}
        {{/foreach}}
      </select>
      {{/if}}
    </td>
  </tr>
 
  
  {{foreach from=$ex_objects_counts item=_ex_objects_count key=_ex_class_id}}
    {{if $_ex_objects_count}}
    <tbody data-name="{{$ex_classes.$_ex_class_id->name}}">
      <tr>
        <td class="text">
          <a href="#1" class="tree-folding" onclick="loadExObjectsList(this, '{{$reference_class}}', '{{$reference_id}}', '{{$_ex_class_id}}'); return false;">
            {{$ex_classes.$_ex_class_id->name}}
          </a>
        </td>
        <td class="narrow" style="text-align: right;">
          {{$_ex_objects_count}}
        </td>
      </tr>
      <tr style="display: none;" class="list-container">
        <td colspan="2"></td>
      </tr>
    </tbody>
    {{/if}}
  {{foreachelse}}
    <tr>
      <td colspan="2" class="empty">Aucun formulaire saisi</td>
    </tr>
  {{/foreach}}
</table>