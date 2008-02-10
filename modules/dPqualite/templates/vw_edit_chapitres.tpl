<table class="main">
  <tr>
    <td class="halfPane" rowspan="3">
      <form name="FrmTypeVue" action="?m={{$m}}" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <label for="typeVue">{{tr}}_classification{{/tr}}</label>
      <select name="typeVue" onchange="this.form.submit();">
        <option value="0" {{if $typeVue == 0}}selected="selected"{{/if}}>{{tr}}_CChapitreDoc_classification_chap{{/tr}}</option>
        <option value="1" {{if $typeVue == 1}}selected="selected"{{/if}}>{{tr}}_CThemeDoc_classification_theme{{/tr}}</option>
      </select>
      </form><br />

      <a class="buttonnew" href="?m=dPqualite&amp;tab=vw_edit_classification&amp;doc_chapitre_id=0">
        {{tr}}CChapitreDoc.create{{/tr}}
      </a>
      <table class="tbl">
        <tr>
          <th colspan="4">
            {{$nav_chapitre->_path}}
          </th>
        </tr>
        <tr>
          <th colspan="3">{{tr}}CChapitreDoc-nom{{/tr}}</th>
          <th>{{tr}}CChapitreDoc-code{{/tr}}</th>
        </tr>
        {{if $nav_chapitre->_id}}
        <tr>
          <td>
            <a href="?m=dPqualite&amp;tab=vw_edit_classification&amp;nav_chapitre_id={{$nav_chapitre->_ref_pere->_id}}" title="Retour">
              retour
            </a>
          </td>
          <td colspan="2" class="greedyPane">
            {{$nav_chapitre->nom}}
          </td>
          <td>
            {{$nav_chapitre->code}}
          </td>
        </tr>
        {{/if}}
        {{foreach from=$listChapitres item=curr_chapitre}}
        <tr>
          <td />
          <td>
            {{if $nav_chapitre->_level < $maxDeep}}
            <a href="?m=dPqualite&amp;tab=vw_edit_classification&amp;nav_chapitre_id={{$curr_chapitre->_id}}" title="Voir">
              voir
            </a>
            {{/if}}
          </td>
          <td class="text greedyPane">
            <a href="?m=dPqualite&amp;tab=vw_edit_classification&amp;doc_chapitre_id={{$curr_chapitre->doc_chapitre_id}}" title="{{tr}}CChapitreDoc.modify{{/tr}}">
              {{$curr_chapitre->nom}}
            </a>
          </td>
          <td class="text">
            <a href="?m=dPqualite&amp;tab=vw_edit_classification&amp;doc_chapitre_id={{$curr_chapitre->doc_chapitre_id}}" title="{{tr}}CChapitreDoc.modify{{/tr}}">
              {{$curr_chapitre->code}}
            </a>
          </td>
        </tr>
        {{/foreach}}        
      </table>
    </td>
    <td class="halfPane">
      <form name="editChapitre" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_chapitre_aed" />
	  <input type="hidden" name="doc_chapitre_id" value="{{$chapitre->doc_chapitre_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {{if $chapitre->doc_chapitre_id}}
          <th class="title modify" colspan="2">{{tr}}msg-CChapitreDoc-title-modify{{/tr}}: {{$chapitre->_view}}</th>
          {{else}}
          <th class="title" colspan="2">{{tr}}msg-CChapitreDoc-title-create{{/tr}}</th>
          {{/if}}
        </tr>
        <tr>
          <th>{{mb_label object=$chapitre field="pere_id"}}</th>
          <td>
            {{if $chapitre->_id}}
            {{if $chapitre->pere_id}}
              {{$chapitre->_ref_pere->_view}}
            {{else}}
              Aucun
            {{/if}}
            {{else}}
            <input type="hidden" name="pere_id" value="{{$nav_chapitre->_id}}">
            {{if $nav_chapitre->_id}}
              {{$nav_chapitre->_view}}
            {{else}}
              Aucun
            {{/if}}
            {{/if}}
          </td>
        </tr>  
        <tr>
          <th>{{mb_label object=$chapitre field="nom"}}</th>
          <td>{{mb_field object=$chapitre field="nom"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$chapitre field="code"}}</th>
          <td>{{mb_field object=$chapitre field="code"}}</td>
        </tr>
        <tr>
          <td class="button" colspan="2">            
            {{if $chapitre->doc_chapitre_id}}
              <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'{{tr escape="javascript"}}CChapitreDoc.one{{/tr}}',objName:'{{$chapitre->_view|smarty:nodefaults|JSAttribute}}'})">{{tr}}Delete{{/tr}}</button>
            {{else}}
              <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
            {{/if}}
          </td>
        </tr>        
      </table>
      </form>
    </td>
  </tr>
</table>