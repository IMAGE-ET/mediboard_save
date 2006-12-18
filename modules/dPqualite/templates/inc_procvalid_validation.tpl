<table class="form">
  <tr>
    <th class="title" colspan="2">
      <input type="hidden" name="suivi[etat]" value="{{$docGed|const:'VALID'}}" />
      {{tr}}_CDocGed_VALID{{/tr}}
    </th>
  </tr>
  <tr>
    <th>{{tr}}CDocGedSuivi-doc_ged_suivi_id-court{{/tr}}</th>
    <td>{{$docGed->_reference_doc}}</td>
  </tr>
  <tr>
    <th>{{tr}}CDocGedSuivi-user_id-court{{/tr}}</th>
    <td class="text">{{$docGed->_lastentry->_ref_user->_view}}</td>
  </tr>
  <tr>
    <th>{{tr}}Date{{/tr}}</th>
    <td>{{$docGed->_lastentry->date|date_format:"%d %B %Y - %Hh%M"}}</td>
  </tr>
  <tr>
    <th>{{tr}}CDocGedSuivi-remarques{{/tr}}</th>
    <td class="text">{{$docGed->_lastentry->remarques|nl2br}}</td>
  </tr>
  <tr>
    <td colspan="2" class="button">
      <a href="#" onclick="popFile('{{$docGed->_class_name}}','{{$docGed->_id}}','CFile','{{$docGed->_lastentry->file_id}}')" title="{{tr}}msg-CFile-viewfile{{/tr}}">
        <img src="index.php?m=dPfiles&amp;a=fileviewer&amp;suppressHeaders=1&amp;file_id={{$docGed->_lastentry->file_id}}&amp;phpThumb=1&amp;wl=64&amp;hp=64" alt="-" />
      </a>
    </td>
  </tr>
  <tr>
    <th>
      <label for="ged[titre]" title="{{tr}}CDocGed-titre-desc{{/tr}}">
        {{tr}}CDocGed-titre{{/tr}}
      </label>
    </th>
    <td>
      <input type="text" name="ged[titre]" value="{{$docGed->titre}}" title="{{$docGed->_props.titre}}|notNull" />
    </td>
  </tr>
  <tr>
    <th>
      <label for="ged[doc_theme_id]" title="{{tr}}CDocGed-doc_theme_id-desc{{/tr}}">
        {{tr}}CDocGed-doc_theme_id{{/tr}}
      </label>
    </th>
    <td>
      <select name="ged[doc_theme_id]" title="{{$docGed->_props.doc_theme_id}}|notNull">
        <option value="">&mdash; {{tr}}CDocGed-doc_theme_id-desc{{/tr}}</option>
        {{foreach from=$listThemes item=curr_theme}}
        <option value="{{$curr_theme->doc_theme_id}}" {{if $docGed->doc_theme_id == $curr_theme->doc_theme_id}} selected="selected" {{/if}} >
          {{$curr_theme->nom}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  <tr>
    <th><label for="suivi[remarques]" title="{{tr}}CDocGedSuivi-remarques-desc{{/tr}}">{{tr}}CDocGedSuivi-remarques{{/tr}}</label></th>
    <td>
      <textarea name="suivi[remarques]" title="{{$docGed->_lastentry->_props.remarques}}"></textarea>
    </td>
  </tr>
  {{if $docGed->version}}
  <tr>
    <th><label for="ged[version]">{{tr}}CDocGed-version{{/tr}}</label></th>
    <td>
      <select name="ged[version]" title="currency|notNull">
        {{foreach from=$versionDoc|smarty:nodefaults item=currVersion}}
        <option value="{{$currVersion}}">{{$currVersion}}</option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  {{else}}
  <input type="hidden" name="ged[version]" value="1">
  {{/if}}
  <tr>
    <td colspan="2" class="button">
      <button class="tick" type="button" onclick="validDoc(this.form);">
        {{tr}}button-CDocGed-valid{{/tr}}
      </button>
      <button class="cancel" type="button" onclick="redactionDoc(this.form);">
        {{tr}}button-CDocGed-renvoi{{/tr}}
      </button>
      <button class="trash" type="button" onclick="confirmDeletion(this.form, {typeName:'{{tr escape="javascript"}}CDocGed.one{{/tr}}',objName:'{{$docGed->_reference_doc|smarty:nodefaults|JSAttribute}}'})" title="{{tr}}Delete{{/tr}}">
        {{tr}}Delete{{/tr}}
      </button>
    </td>
  </tr>
</table>