<!--  $Id$ -->

<script type="text/javascript">

var aTraduction = new Array();
{{foreach from=$listTraductions key=key item=currClass}}
aTraduction["{{$key}}"] = "{{$currClass}}";
{{/foreach}}

var classes = {{$classes|@json}};

function loadClasses(value) {
  var form = document.editFrm;
  var select = form.elements['class'];
  var options = classes;

  // delete all former options except first
  while (select.length > 1) {
    select.options[1] = null;
  }
  
  // insert new ones
  for (var elm in options) {
    var option = elm;
    if (typeof(options[option]) != "function") { // to filter prototype functions
      select.options[select.length] = new Option(aTraduction[option], option, option == value);
    }
  }
  loadFields();
}

function loadFields(value) {
  var form = document.editFrm;
  var select = form.elements['field'];
  var className  = form.elements['class'].value;
  var options = classes[className];

  // delete all former options except first
  while (select.length > 1) {
    select.options[1] = null;
  }

  // insert new ones
  for (var elm in options) {
    var option = elm;
    if (typeof(options[option]) != "function") { // to filter prototype functions
      select.options[select.length] = new Option(option, option, option == value);
    }
  }
  loadDependances();
}

function loadDependances(value){
  var form = document.editFrm;
  var select = form.elements['depend_value'];
  var className  = form.elements['class'].value;
  var fieldName  = form.elements['field'].value;
  var options = classes[className];

  // delete all former options except first
  while (select.length > 1) {
    select.options[1] = null;
  }
  
  if(!options){
   return;
  }
  options = classes[className][fieldName];
  
  // insert new ones
  for (var elm in options) {
    var option = options[elm];
    if (typeof(option) != "function") { // to filter prototype functions
      select.options[select.length] = new Option(aTraduction[option], elm, elm == value);
    }
  }
}

Main.add(function () {
  loadClasses('{{$aide->class}}');
  loadFields('{{$aide->field}}');
  loadDependances('{{$aide->depend_value}}');
});

</script>

<table class="main">

<tr>
  <td class="greedyPane">

    <form name="filterFrm" action="?" method="get">
    
    <input type="hidden" name="m" value="{{$m}}" />
    <a href="?m={{$m}}&amp;tab={{$tab}}&amp;aide_id=0" class="buttonnew"><strong>Cr�er une aide � la saisie</strong></a>
    <table class="form">
      <tr>
        <th class="category" colspan="10">Filtrer les aides</th>
      </tr>

      <tr>
        <th><label for="filter_user_id" title="Filtrer les aides pour cet utilisateur">Utilisateur</label></th>
        <td>
          <select name="filter_user_id" onchange="this.form.submit()">
            <option value="">&mdash; Choisir un utilisateur</option>
            {{foreach from=$listPrat item=curr_user}}
            <option class="mediuser" style="border-color: #{{$curr_user->_ref_function->color}};" value="{{$curr_user->user_id}}" {{if $curr_user->user_id == $filter_user_id}} selected="selected" {{/if}}>
              {{$curr_user->_view}}
            </option>
            {{/foreach}}
          </select>
        </td>
        <th><label for="filter_class" title="Filtrer les aides pour ce type d'objet">Type d'objet</label></th>
        <td>
          <select name="filter_class" onchange="this.form.submit()">
            <option value="0">&mdash; Tous les types d'objets</option>
            {{foreach from=$classes|smarty:nodefaults key=class_name item=fields}}
            <option value="{{$class_name}}" {{if $class_name == $filter_class}} selected="selected" {{/if}}>
              {{tr}}{{$class_name}}{{/tr}}
            </option>
            {{/foreach}}
          </select>
        </td>
      </tr>
    </table>

    </form>
    
    <table class="tbl">
    
    <tr>
      <th colspan="6" class="title"><strong>Liste des aides � la saisie</strong></th>
    </tr>
    
    <tr>
      <th>Type d'objet</th>
      <th>Champ de l'objet</th>
      <th>D�pendance</th>
      <th>Nom de l'aide</th>
      <th>Texte de remplacement</th>
    </tr>

    <tr>
      <th colspan="6">Aides du praticien '{{$userSel->_view}}'</th>
    </tr>
    {{foreach from=$aidesPrat item=curr_aide}}
    <tr {{if $curr_aide->_id == $aide->_id}}class="selected"{{/if}}>
      {{assign var="aide_id" value=$curr_aide->aide_id}}
      {{assign var="href" value="?m=$m&tab=$tab&aide_id=$aide_id"}}
      {{assign var="className" value=$curr_aide->class}}
      {{assign var="field" value=$curr_aide->field}}
      <td><a href="{{$href}}">{{tr}}{{$className}}{{/tr}}</a></td>
      <td><a href="{{$href}}">{{$field}}</a></td>
      <td>
        <a href="{{$href}}">
        {{if $curr_aide->_ref_abstat_object->_helped_fields.$field}}
           <!-- 
          {{$curr_aide->_ref_abstat_object->_helped_fields.$field}} :
           -->
          {{if $curr_aide->depend_value}}
            {{tr}}{{$className}}.{{$curr_aide->_ref_abstat_object->_helped_fields.$field}}.{{$curr_aide->depend_value}}{{/tr}}
          {{else}}
            Aucune
          {{/if}}
        {{else}}&mdash;{{/if}}
        </a>
      </td>
      <td><a href="{{$href}}">{{$curr_aide->name}}</a></td>
      <td class="text">{{$curr_aide->text|nl2br}}</td>
    </tr>
    {{/foreach}}
    
    <tr>
      <th colspan="6">Aides du cabinet '{{$userSel->_ref_function->_view}}'</th>
    </tr>
    {{foreach from=$aidesFunc item=curr_aide}}
    <tr {{if $curr_aide->_id == $aide->_id}}class="selected"{{/if}}>
      {{assign var="aide_id" value=$curr_aide->aide_id}}
      {{assign var="href" value="?m=$m&tab=$tab&aide_id=$aide_id"}}
      {{assign var="className" value=$curr_aide->class}}
      {{assign var="field" value=$curr_aide->field}}
      <td><a href="{{$href}}">{{tr}}{{$className}}{{/tr}}</a></td>
      <td><a href="{{$href}}">{{$field}}</a></td>
      <td>
        <a href="{{$href}}">
        {{if $curr_aide->_ref_abstat_object->_helped_fields.$field}}
          {{$curr_aide->_ref_abstat_object->_helped_fields.$field}}
          {{if $curr_aide->depend_value}}
          : {{tr}}{{$className}}.{{$curr_aide->_ref_abstat_object->_helped_fields.$field}}.{{$curr_aide->depend_value}}{{/tr}}
          {{/if}}
        {{else}}&mdash;{{/if}}
        </a>
      </td>
      <td><a href="{{$href}}">{{$curr_aide->name}}</a></td>
      <td class="text">{{$curr_aide->text|nl2br}}</td>
    </tr>
    {{/foreach}}
      
    </table>

  </td>
  
  <td class="pane">

    <form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

    <input type="hidden" name="dosql" value="do_aide_aed" />
    {{mb_field object=$aide field="aide_id" hidden=1 prop=""}}
    <input type="hidden" name="del" value="0" />

    <table class="form">

    <tr>
      <th class="category" colspan="2">
      {{if $aide->aide_id}}
        Modification d'une aide
      {{else}}
        Cr�ation d'une aide
      {{/if}}
      </th>
    </tr>
  
    <tr>
      <th>{{mb_label object=$aide field="function_id"}}</th>
      <td>
        <select name="function_id" class="{{$aide->_props.function_id}}" onchange="this.form.user_id.value = ''">
          <option value="">&mdash; Associer � une fonction &mdash;</option>
          {{foreach from=$listFunc item=curr_func}}
            <option class="mediuser" style="border-color: #{{$curr_func->color}};" value="{{$curr_func->function_id}}" {{if $curr_func->function_id == $aide->function_id}} selected="selected" {{/if}}>
              {{$curr_func->_view}}
            </option>
          {{/foreach}}
        </select>
      </td>
    </tr>

    <tr>
      <th>{{mb_label object=$aide field="user_id"}}</th>
      <td>
        <select name="user_id" class="{{$aide->_props.user_id}}" onchange="this.form.function_id.value = ''">
          <option value="">&mdash; Associer � un praticien &mdash;</option>
          {{foreach from=$listPrat item=curr_prat}}
            <option class="mediuser" style="border-color: #{{$curr_prat->_ref_function->color}};" value="{{$curr_prat->user_id}}" {{if $curr_prat->user_id == $aide->user_id}} selected="selected" {{/if}}>
              {{$curr_prat->_view}}
            </option>
          {{/foreach}}
        </select>
      </td>
    </tr>

    <tr>
      <th>{{mb_label object=$aide field="class"}}</th>
      <td>
        <select name="class" class="{{$aide->_props.class}}" onchange="loadFields()">
          <option value="">&mdash; Choisir un objet</option>
        </select>
      </td>
    </tr>

    <tr>
      <th>{{mb_label object=$aide field="field"}}</th>
      <td>
        <select name="field" class="{{$aide->_props.field}}" onchange="loadDependances()">
          <option value="">&mdash; Choisir un champ</option>
        </select>
      </td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$aide field="depend_value"}}</th>
      <td>
        <select name="depend_value" class="{{$aide->_props.depend_value}}">
          <option value="">&mdash; Tous</option>
        </select>
      </td>
    </tr>

    <tr>
      <th>{{mb_label object=$aide field="name"}}</th>
      <td>{{mb_field object=$aide field="name"}}</td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$aide field=text"}}</th>
      <td>
        {{mb_field object=$aide field="text" style="width: 200px" rows="4"}}
      </td>
    </tr>

    <tr>
      <td class="button" colspan="2">
        {{if $aide->aide_id}}
        <button class="modify" type="submit">
          {{tr}}Modify{{/tr}}
        </button>
        <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'l\'aide',objName:'{{$aide->name|smarty:nodefaults|JSAttribute}}'})">
          {{tr}}Delete{{/tr}}
        </button>
        {{else}}
        <button class="submit" type="submit">
          {{tr}}Create{{/tr}}
        </button>
        {{/if}}
      </td>
    </tr>
    </table>
    </form>
  </td>
</tr>

</table>
