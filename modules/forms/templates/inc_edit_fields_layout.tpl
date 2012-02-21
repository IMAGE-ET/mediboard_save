
<script type="text/javascript">
Main.add(function(){
  Control.Tabs.create("field_groups_layout");
  //ExClass.putCellSpans($$(".drop-grid")[0]);
});
</script>

<div class="small-info">Glissez-d�posez les champs et leur libell� dans la grille "Disposition"</div>

<form name="form-layout-field" method="post" action="" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="dosql" value="do_ex_class_field_aed" />
  <input type="hidden" name="ex_class_field_id" value="" />
  
  <input type="hidden" name="coord_label_x" class="coord" value="" />
  <input type="hidden" name="coord_label_y" class="coord" value="" />
  <input type="hidden" name="coord_field_x" class="coord" value="" />
  <input type="hidden" name="coord_field_y" class="coord" value="" />
</form>

<form name="form-layout-message" method="post" action="" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="dosql" value="do_ex_class_message_aed" />
  <input type="hidden" name="ex_class_message_id" value="" />
  
  <input type="hidden" name="coord_title_x" class="coord" value="" />
  <input type="hidden" name="coord_title_y" class="coord" value="" />
  <input type="hidden" name="coord_text_x" class="coord" value="" />
  <input type="hidden" name="coord_text_y" class="coord" value="" />
</form>

<form name="form-layout-hostfield" method="post" action="" onsubmit="return false">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="dosql" value="do_ex_class_host_field_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="ex_class_host_field_id" value="" />
  <input type="hidden" name="ex_class_id" value="{{$ex_class->_id}}" />
  <input type="hidden" name="ex_group_id" value="" />
  <input type="hidden" name="host_type" value="" />
  <input type="hidden" name="field" value="" />
  <input type="hidden" name="callback" value="" />
  
  <input type="hidden" name="coord_label_x" class="coord" value="" />
  <input type="hidden" name="coord_label_y" class="coord" value="" />
  <input type="hidden" name="coord_value_x" class="coord" value="" />
  <input type="hidden" name="coord_value_y" class="coord" value="" />
</form>

<style type="text/css">
  .out-of-grid div {
    /*display: inline-block;*/
  }
  
  .label-list .draggable,
  .field-list .draggable {
    border: 1px dotted #aaa;
    margin: 2px;
    padding: 2px;
  }
  
  .droppable.grid .draggable {
    display: block;
  }
  
  .droppable.grid .draggable.label {
    /*text-align: right;*/
    font-weight: bold;
    white-space: nowrap;
  }
  
  .field-list .field {
    white-space: normal;
  }
  
  .grid .field:hover .field-info,
  .field-list .field .field-info {
    display: block !important;
    position: absolute;
    top: -1.5em;
    color: #333;
    background: #ddd;
    padding: 0px 1em;
    -moz-border-radius-topleft: 4px;
    -moz-border-radius-topright: 4px;
    -webkit-border-top-left-radius: 4px;
    -webkit-border-top-right-radius: 4px;
    border-radius-topleft: 4px;
    border-radius-topright: 4px;
  }
  
  .grid .field:hover .field-info {
    color: #fff;
    background: #999;
  }
  
  .field-list .field {
    margin-top: 1.2em;
  }
  
  .field .field-content {
    overflow-y: hidden;
  }
  
  .field-list .field .field-content {
    max-height: 2.5em;
  }
  
  .cell[rowspan] .grid .field .field-content {
    max-height: none;
  }
  
  .grid .field .field-content,
  .cell[rowspan="1"] .grid .field .field-content {
    max-height: 3.5em;
  }
  
  .draggable .overlay {
    position:absolute;
    top:0;
    left:0;
    bottom:0;
    right:0;
  }
  
  .draggable .size .arrows {
    position: absolute;
  }
  
  .draggable .size .col {
    right: 0;
    top: 50%;
    margin-top: -8px;
  }
  
  .draggable .size .row {
    left: 50%;
    bottom: 0;
  }
  
  .draggable .size button {
    background: none;
    border: none;
  }
  
  .draggable .size {
    display: none;
  }
  
  .drop-grid .draggable:hover .size {
    /*display: block;*/
  }
  
  .grid .hostfield .field-name, 
  .hostfield.dragging .field-name {
    display: inline !important;
  }
  
  .hostfield {
    background-color: rgb(205,252,204);
    background-color: rgba(205,252,204,0.4);
  }
  
  .draggable.hr {
    padding: 6px;
  }
  
  .message_text {
    min-height: 1.8em;
  }

  div.ex-message-title {
    font-weight: bold;
    border-bottom: 1px solid #666;
    font-size: 1.2em;
    /*left: 0.5em; 
    right: 0.5em; 
    position: absolute;*/
  }
  
  table.cell-layout {
    empty-cells: hide;
    line-height: 10px;
    font-family: monospace;
    position: absolute;
    right: 0;
    top: -10px;
  }
  
  table.cell-layout td {
    padding: 0;
  }
  
  table.cell-layout a {
    display: inline;
    cursor: pointer;
  }
  
  table.cell-layout tr.middle {
    line-height: 6px;
  }
  
  div.cell-layout-wrapper {
    display: none;
  }
  
  .cell:hover div.cell-layout-wrapper {
    /*display: block;*/
  }
  
  form[name="form-grid-layout"].pickmode .draggable {
    cursor: pointer;
  }
  
  form[name="form-grid-layout"].pickmode .draggable.picked {
    outline: 2px solid red;
  }
  
  form[name="form-grid-layout"].pickmode .drop-grid .droppable:hover {
    outline: 2px solid green;
  }
</style>

<ul class="control_tabs" id="field_groups_layout" style="font-size: 0.9em;">
  {{foreach from=$ex_class->_ref_groups item=_group}}
    <li>
      <a href="#group-layout-{{$_group->_guid}}" style="padding: 2px 4px;">
        {{$_group->name}} <small>({{$_group->_ref_fields|@count}})</small>
      </a>
    </li>
  {{/foreach}}
  <li style="font-size: 1.2em; font-weight: bold;">
    <label title="Plut�t que glisser-d�poser">
      <input type="checkbox" onclick="ExClass.setPickMode(this.checked)" checked="checked" />
      Disposer par clic
    </label>
  </li>
</ul>
<hr class="control_tabs" />

{{assign var=groups value=$ex_class->_ref_groups}}

<form name="form-grid-layout" method="post" onsubmit="return false" class="prepared pickmode">
  
{{foreach from=$grid key=_group_id item=_grid}}

<div id="group-layout-{{$groups.$_group_id->_guid}}" style="display: none;" class="group-layout">
  
<div class="out-of-grid droppable">
  <script type="text/javascript">
  Main.add(function(){
    Control.Tabs.create("class-message-layout-tabs-{{$_group_id}}");
  });
  </script>
  
  <ul class="control_tabs" id="class-message-layout-tabs-{{$_group_id}}">
    <li>
      <a href="#outofgrid-class-fields-{{$_group_id}}">Champs</a>
    </li>
    <li>
      <a href="#outofgrid-messages-{{$_group_id}}">Textes / Messages</a>
    </li>
    {{if $ex_class->host_class != "CMbObject"}}
    <li>
      <a href="#outofgrid-hostfields-{{$_group_id}}">Champs de Mediboard</a>
    </li>
    {{/if}}
  </ul>
  <hr class="control_tabs" />
  
  <!-- Fields -->
  <div id="outofgrid-class-fields-{{$_group_id}}" style="display: none;">
    <table class="main tbl" style="table-layout: fixed;">
      <tr>
        <th>Libell�s</th>
        <th>Valeurs</th>
      </tr>
    </table>
    
    <table class="main layout" style="table-layout: fixed;">
      <tr>
        <td class="label-list" data-x="" data-y="" style="padding: 4px; height: 2em; vertical-align: top;">
          {{foreach from=$out_of_grid.$_group_id.label item=_field}}
            {{mb_include module=forms template=inc_ex_field_draggable _type="label" }}
          {{/foreach}}
        </td>
    
        <td class="field-list" data-x="" data-y="" style="padding: 4px; vertical-align: top;">
          {{foreach from=$out_of_grid.$_group_id.field item=_field}}
            {{mb_include module=forms template=inc_ex_field_draggable _type="field"}}
          {{/foreach}}
        </td>
      </tr>
    </table>
  </div>
  
  <!-- Messages -->
  <div id="outofgrid-messages-{{$_group_id}}" style="display: none;">
    <table class="main tbl" style="table-layout: fixed;">
      <tr>
        <th>Titres des messages (pas n�cessaire de les placer)</th>
        <th>Messages</th>
      </tr>
    </table>
    
    <table class="main layout" style="table-layout: fixed;">
      <tr>
        <td class="message_title-list" data-x="" data-y="" style="padding: 4px; vertical-align: top;">
          {{foreach from=$out_of_grid.$_group_id.message_title item=_field}}
            {{mb_include module=forms template=inc_ex_message_draggable _type="message_title"}}
          {{/foreach}}
        </td>
    
        <td class="message_text-list" data-x="" data-y="" style="padding: 4px; vertical-align: top;">
          {{foreach from=$out_of_grid.$_group_id.message_text item=_field}}
            {{mb_include module=forms template=inc_ex_message_draggable _type="message_text"}}
          {{/foreach}}
        </td>
      </tr>
    </table>
  </div>
  
  <!-- Host fields -->
  {{if $ex_class->host_class != "CMbObject"}}
  <div id="outofgrid-hostfields-{{$_group_id}}" style="display: none;">
    <table class="main tbl" style="table-layout: fixed;">
      {{assign var=class_options value=$ex_class->_host_class_options}}
      {{assign var=_host_class value=$ex_class->host_class}}
      
      <tr>
        <th>{{tr}}{{$ex_class->host_class}}{{/tr}}</th>

        {{if $class_options.reference1.0}}
          <th>
            {{if $class_options.reference1.1|strpos:"." === false}}
              {{tr}}{{$_host_class}}-{{$class_options.reference1.1}}{{/tr}}
            {{else}}
              {{tr}}{{$class_options.reference1.0}}{{/tr}}
            {{/if}}
          </th>
        {{/if}}
        
        {{if $class_options.reference2.0}}
          <th>
            {{if $class_options.reference2.1|strpos:"." === false}}
              {{tr}}{{$_host_class}}-{{$class_options.reference2.1}}{{/tr}}
            {{else}}
              {{tr}}{{$class_options.reference2.0}}{{/tr}}
            {{/if}}
          </th>
        {{/if}}
      </tr>
    </table>
    
    <table class="main layout" style="table-layout: fixed;" >
      <tr>
        <td class="hostfield-list" data-x="" data-y="" style="padding: 4px; height: 2em; vertical-align: top;">
          <div style="height: 100%; overflow-y: scroll; min-height: 140px;">
            <ul>
            {{foreach from=$host_object->_specs item=_spec key=_field}}
              {{if $_spec->show == 1 || $_field == "_view" || ($_spec->show == "" && $_field.0 !== "_")}}
                <li>
                  {{mb_include module=forms template=inc_ex_host_field_draggable ex_group_id=$_group_id host_object=$host_object host_type="host"}}
                </li>
              {{/if}}
            {{/foreach}}
            </ul>
          </div>
        </td>

        {{if $class_options.reference1.0}}
          <td class="hostfield-list" data-x="" data-y="" style="padding: 4px; height: 2em; vertical-align: top;">
            <div style="height: 100%; overflow-y: scroll; min-height: 140px;">
              <ul>
              {{foreach from=$reference1->_specs item=_spec key=_field}}
                {{if $_spec->show == 1 || $_field == "_view" || ($_spec->show == "" && $_field.0 !== "_")}}
                  <li>
                    {{mb_include module=forms template=inc_ex_host_field_draggable ex_group_id=$_group_id host_object=$reference1 host_type="reference1"}}
                  </li>
                {{/if}}
              {{/foreach}}
              </ul>
            </div>
          </td>
        {{/if}}
        
        {{if $class_options.reference2.0}}
          <td class="hostfield-list" data-x="" data-y="" style="padding: 4px; height: 2em; vertical-align: top;">
            <div style="height: 100%; overflow-y: scroll; min-height: 140px;">
              <ul>
              {{foreach from=$reference2->_specs item=_spec key=_field}}
                {{if $_spec->show == 1 || $_field == "_view" || ($_spec->show == "" && $_field.0 !== "_")}}
                  <li>
                    {{mb_include module=forms template=inc_ex_host_field_draggable ex_group_id=$_group_id host_object=$reference2 host_type="reference2"}}
                  </li>
                {{/if}}
              {{/foreach}}
              </ul>
            </div>
          </td>
        {{/if}}
      </tr>
    </table>
  </div>
  {{/if}}
</div>

<table class="main drop-grid" style="border-collapse: collapse;">
  <tr>
    <th colspan="5" class="title">Disposition</th>
  </tr>
  <tr>
    <th style="background: #ddd;"></th>
    {{foreach from=$_grid|@reset key=_x item=_field}}
      <th style="background: #ddd;">{{$_x}}</th>
    {{/foreach}}  
  </tr>
  
  {{foreach from=$_grid key=_y item=_line}}
  <tr>
    <th style="padding: 4px; width: 2em; text-align: right; background: #ddd;">{{$_y}}</th>
    {{foreach from=$_line key=_x item=_group}}
      <td style="border: 1px dotted #aaa; min-width: 2em; padding: 0; vertical-align: middle;" class="cell">
      
        {{*
        <div style="position: relative;" class="cell-layout-wrapper">
          <table class="layout cell-layout">
            <tr>
              <td></td>
              <td><a href="#">&#x25B2;</a><br /><a href="#">&#x25BC;</a></td>
              <td></td>
            </tr>
            <tr class="middle">
              <td><a href="#">&#x25C4;</a>&#x2005;<a href="#">&#x25BA;</a></td>
              <td></td>
              <td><a href="#">&#x25C4;</a>&#x2005;<a href="#">&#x25BA;</a></td>
            </tr>
            <tr>
              <td></td>
              <td><a href="#">&#x25B2;<br /><a href="#">&#x25BC;</a></td>
              <td></td>
            </tr>
          </table>
        </div>
        *}}
      
        <div class="droppable grid" data-x="{{$_x}}" data-y="{{$_y}}">
          {{if $_group.object}}
            {{if $_group.object instanceof CExClassField}}
              {{mb_include module=forms template=inc_ex_field_draggable 
                           _field=$_group.object 
                           _type=$_group.type}}
            {{elseif $_group.object instanceof CExClassHostField}}
              {{if $_group.object->host_type == "host"}}
                {{assign var=_host_object value=$host_object}}
              {{elseif $_group.object->host_type == "reference1"}}
                {{assign var=_host_object value=$reference1}}
              {{elseif $_group.object->host_type == "reference2"}}
                {{assign var=_host_object value=$reference2}}
              {{/if}}
            
              {{mb_include module=forms template=inc_ex_host_field_draggable 
                           _host_field=$_group.object 
                           ex_group_id=$_group_id 
                           _field=$_group.object->field 
                           _type=$_group.type 
                           host_type=$_group.object->host_type
                           host_object=$_host_object}}
            {{else}}
              {{mb_include module=forms template=inc_ex_message_draggable 
                           _field=$_group.object 
                           ex_group_id=$_group_id 
                           _type=$_group.type}}
            {{/if}}
          {{else}}
            &nbsp;
          {{/if}}
        </div>
      </td>
    {{/foreach}}
  </tr>
  {{/foreach}}
</table>

</div>

{{/foreach}}

</form>
