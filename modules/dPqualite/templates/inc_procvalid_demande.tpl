{{assign var="info_proc" value=$docGed->_lastentry->date|date_format:"%d %b %Y � %Hh%M"}}
<table class="form">
  <tr>          
    <th class="title modify" colspan="2">              
      <input type="hidden" name="suivi[etat]" value="{{$docGed|const:'VALID'}}" />
      {{tr}}_CDocGed_DEMANDE{{/tr}}
    </th>
  </tr>
  <tr>
    <th>{{tr}}Date{{/tr}}</th>
    <td>{{$docGed->_lastentry->date|date_format:"%A %d %B %Y � %Hh%M"}}</td>
  </tr>
  <tr>
    <th>{{tr}}CDocGedSuivi-doc_ged_suivi_id-court{{/tr}}</th>
    <td>
      {{if $docGed->doc_ged_id && $docGed->_lastactif->doc_ged_suivi_id}}
      {{tr}}_CDocGed_revision{{/tr}} {{$docGed->_reference_doc}}<br />
      {{tr}}CDocGed-doc_theme_id{{/tr}} : {{$docGed->_ref_theme->nom}}
      {{else}}
      {{tr}}_CDocGed_new{{/tr}}
      {{/if}}
    </td>
  </tr>
  <tr>
    <th>{{tr}}CDocGed-group_id{{/tr}}</th>
    <td class="text">
      {{$docGed->_ref_group->text}}
    </td>
  </tr>
  <tr>
    <th>{{tr}}CDocGedSuivi-user_id-court{{/tr}}</th>
    <td class="text">{{$docGed->_lastentry->_ref_user->_view}}</td>
  </tr>
  <tr>
    <th>{{tr}}CDocGedSuivi-remarques{{/tr}}</th>
    <td class="text">
      {{$docGed->_lastentry->remarques|nl2br}}
    </td>
  </tr>
  <tr>
    <td colspan="2" class="button">
      <button class="cancel" type="button" onclick="refuseDoc(this.form);">
        {{tr}}button-CDocGed-refusdemande{{/tr}}
      </button>
    </td>
  </tr>
  <tr>
    <th>
      <label for="ged[titre]" title="{{tr}}CDocGed-titre-desc{{/tr}}">
        {{tr}}CDocGed-titre{{/tr}}
      </label>
    </th>
    <td>
      <input type="text" name="ged[titre]" value="{{$docGed->titre}}" title="notNull {{$docGed->_props.titre}}" />
    </td>
  </tr>
  <tr>
    <th>
      <label for="ged[doc_theme_id]" title="{{tr}}CDocGed-doc_theme_id-desc{{/tr}}">
        {{tr}}CDocGed-doc_theme_id{{/tr}}
      </label>
    </th>
    <td>
      <select name="ged[doc_theme_id]" title="notNull {{$docGed->_props.doc_theme_id}}">
        <option value="">&mdash; {{tr}}CDocGed-doc_theme_id-desc{{/tr}}</option>
        {{foreach from=$listThemes item=curr_theme}}
        <option value="{{$curr_theme->doc_theme_id}}" {{if $docGed->doc_theme_id == $curr_theme->doc_theme_id}} selected="selected" {{/if}} >
          {{$curr_theme->nom}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  {{if !$docGed->_lastactif->doc_ged_suivi_id}}            
  <tr>
    <th>
      <label for="ged[doc_chapitre_id]" title="{{tr}}CDocGed-doc_chapitre_id-desc{{/tr}}">
        {{tr}}CDocGed-doc_chapitre_id{{/tr}}
      </label>
    </th>
    <td>
      <select name="ged[doc_chapitre_id]" title="notNull {{$docGed->_props.doc_chapitre_id}}">
        <option value="">&mdash; {{tr}}CDocGed-doc_chapitre_id-desc{{/tr}}</option>
        {{foreach from=$listChapitres item=curr_chapitre}}
        <option value="{{$curr_chapitre->doc_chapitre_id}}" {{if $docGed->doc_chapitre_id == $curr_chapitre->doc_chapitre_id}} selected="selected" {{/if}} >
          {{$curr_chapitre->_view}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  <tr>
    <th>
      <label for="ged[doc_categorie_id]" title="{{tr}}CDocGed-doc_categorie_id-desc{{/tr}}">
        {{tr}}CDocGed-doc_categorie_id{{/tr}}
      </label>
    </th>
    <td>
      <select name="ged[doc_categorie_id]" title="notNull {{$docGed->_props.doc_categorie_id}}">
        <option value="">&mdash; {{tr}}CDocGed-doc_categorie_id-desc{{/tr}}</option>
        {{foreach from=$listCategories item=curr_category}}
        <option value="{{$curr_category->doc_categorie_id}}" {{if $docGed->doc_categorie_id == $curr_category->doc_categorie_id}} selected="selected" {{/if}} >
          {{$curr_category->_view}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  {{/if}}
  <tr>
    <th><label for="suivi[remarques]" title="{{tr}}CDocGed-remarques-desc{{/tr}}">{{tr}}CDocGed-remarques{{/tr}}</label></th>
    <td>
      <textarea name="suivi[remarques]" title="{{$docGed->_lastentry->_props.remarques}}"></textarea>
    </td>
  </tr>
  <tr>
    <td colspan="2" class="button">
      <button class="tick" type="button" onclick="redactionDoc(this.form);">
        {{tr}}button-CDocGed-accept{{/tr}}
      </button>
      <button class="trash" type="button" onclick="confirmDeletion(this.form, {typeName:'{{tr escape="javascript"}}CDocGed.one{{/tr}}',objName:'{{$info_proc|smarty:nodefaults|JSAttribute}}'})">
        {{tr}}Delete{{/tr}}
      </button>
    </td>
  </tr>
</table>