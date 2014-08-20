
<script>
function diplayListItems(select, key) {
  var list_id = $V(select);
  var items = $("list-items-"+key);
  
  if (!list_id || list_id == "__create__") {
    items.update("");
    return;
  }
  
  var url = new Url("forms", "ajax_ex_list_info");
  url.addParam("list_id", list_id);
  url.requestUpdate(items);
}
</script>

<div class="small-info">
  Ne seront pas import�s:
  <ul>
    <li>Les valeurs par d�faut des champs de type liste</li>
    <li>Les valeurs d�coch�es des listes dans les champs (si la liste provient d'un concept)</li>
    <li>Les sous-formulaires</li>
    <li>Les �v�nements d�clencheurs</li>
  </ul>
</div>

<form name="import-ex_class" method="post">
  <fieldset>
    <legend>Options</legend>
    
    <table class="main form">
      <tr>
        <th>Nom du formulaire</th>
        <td><input type="text" name="options[ex_class_name]" value="{{$ex_class_name}}" size="50" /></td>
      </tr>
      <tr>
        <th></th>
        <td>
          <label>
            <input type="checkbox" name="options[ignore_disabled_fields]" checked /> Ignorer les champs d�sactiv�s
          </label>
        </td>
      </tr>
    </table>
  </fieldset>

  <fieldset>
    <legend>{{tr}}CExList{{/tr}}s</legend>
    <table class="main tbl">
      <colgroup style="width: 50%;">
        <col style="width: 25%;"/>
        <col style="width: 25%;"/>
      </colgroup>
      
      <colgroup style="width: 50%;">
        <col style="width: 25%;"/>
        <col style="width: 25%;"/>
      </colgroup>
      
      <tr>
        <th class="category" colspan="2">Pr�sent dans le fichier</th>
        <th class="category" colspan="2">Pr�sent en base</th>
      </tr>
      {{foreach from=$lists item=_list key=_key}}
        <tr>
          <td>
            {{$_list.values.name}}
            
            <br />
            {{if $_list.values.coded}}
              Cod�e
            {{else}}
              <em>Non cod�e</em>
            {{/if}}
          </td>
          <td>
            <ul>
            {{foreach from=$_list.elements item=_values}}
              <li>{{$_values.name}} &mdash; {{if $_values.code !== ""}}{{$_values.code}}{{else}}<span class="empty">Aucune code</span>{{/if}}</li>
            {{/foreach}}
            </ul>
          </td>
          <td>
            <script>
              Main.add(function(){
                var form = getForm("import-ex_class");
                diplayListItems(form.elements["fromdb[{{$_key}}]"], '{{$_key}}'); 
              })
            </script>
            <select name="fromdb[{{$_key}}]" style="width: 30em;" onchange="diplayListItems(this, '{{$_key}}')">
              {{foreach from=$_list.similar item=_similar}}
                <option value="{{$_similar->_id}}">{{$_similar}}</option>
                <option value="__create__"> &ndash; Cr�er (renomm� si d�j� pr�sent) &ndash; </option>
              {{/foreach}}
              <optgroup label="Autre">
                {{foreach from=$all_lists item=_list_object}}
                  <option value="{{$_list_object->_id}}">{{$_list_object}}</option>
                {{/foreach}}
              </optgroup>
            </select>
          </td>
          <td id="list-items-{{$_key}}"></td>
        </tr>
      {{/foreach}}
    </table>
  </fieldset>
  
  <fieldset>
    <legend>{{tr}}CExConcept{{/tr}}s</legend>
    <table class="main tbl">
      <tr>
        <th class="category" style="width: 50%;">Pr�sent dans le fichier</th>
        <th class="category">Pr�sent en base</th>
      </tr>
      {{foreach from=$concepts item=_concept key=_key}}
        <tr>
          <td>{{$_concept.values.name}}</td>
          <td>
            <select name="fromdb[{{$_key}}]" style="width: 30em;">
              {{foreach from=$_concept.similar item=_similar}}
                {{assign var=_concept_spec value=$_similar->loadConceptSpec()}}
                <option value="{{$_similar->_id}}">
                  {{$_similar}} {{if $_concept_spec->getSpecType() != $_concept.spec_type}} (ATTENTION: type diff�rent){{/if}}
                </option>
              {{/foreach}}
              <option value="__create__">Cr�er (renomm� si d�j� pr�sent)</option>
              
              <optgroup label="Autre de m�me type">
                {{foreach from=$all_concepts item=_concept_object}}
                  {{assign var=_concept_spec value=$_concept_object->loadConceptSpec()}}
                  {{if $_concept_spec->getSpecType() == $_concept.spec_type}}
                    <option value="{{$_concept_object->_id}}">{{$_concept_object}}</option>
                  {{/if}}
                {{/foreach}}
              </optgroup>
              
              <optgroup label="Autre de type diff�rent (d�conseill�)">
                {{foreach from=$all_concepts item=_concept_object}}
                  {{assign var=_concept_spec value=$_concept_object->loadConceptSpec()}}
                  {{if $_concept_spec->getSpecType() != $_concept.spec_type}}
                    <option value="{{$_concept_object->_id}}">{{$_concept_object}}</option>
                  {{/if}}
                {{/foreach}}
              </optgroup>
            </select>
          </td>
        </tr>
      {{/foreach}}
    </table>
  </fieldset>
  
  <table class="main tbl">
    <tr>
      <td style="width: 50%;"></td>
      <td><button class="save">{{tr}}Import{{/tr}}</button></td>
    </tr>
  </table>
  
</form>