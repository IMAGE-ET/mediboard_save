<script language="Javascript" type="text/javascript">

function popFile(objectClass, objectId, elementClass, elementId, sfn){
  var url = new Url;
  url.addParam("nonavig", 1);
  url.ViewFilePopup(objectClass, objectId, elementClass, elementId, sfn);
}

function ZoomAjax(objectClass, objectId, elementClass, elementId, sfn){
  file_preview = elementId;
  var url = new Url;
  url.setModuleAction("dPfiles", "preview_files");
  url.addParam("objectClass", objectClass);
  url.addParam("objectId", objectId);
  url.addParam("elementClass", elementClass);
  url.addParam("elementId", elementId);
  if(sfn && sfn!=0){
    url.addParam("sfn", sfn);
  }
  url.requestUpdate('bigView', { waitingText : "{{tr}}msg-CFile-loadimgmini{{/tr}}" });
}

</script>
<table class="main">
  <tr>
    <td class="halfPane">
      
      <form name="FrmClassifVue" action="?m={{$m}}" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <label for="selTheme" title="{{tr}}CDocGed-doc_theme_id-desc{{/tr}}">{{tr}}CDocGed-doc_theme_id{{/tr}}</label>
      <select name="selTheme" onchange="this.form.submit();">
        <option value="0">&mdash; {{tr}}_CThemeDoc_alltheme{{/tr}}</option>
        {{foreach from=$listThemes item=currTheme}}
        <option value="{{$currTheme->doc_theme_id}}" {{if $selTheme == $currTheme->doc_theme_id}}selected="selected"{{/if}}>
          {{$currTheme->nom}}
        </option>
        {{/foreach}}
      </select>
      <br />
      <label for="selChapitre" title="{{tr}}CDocGed-doc_chapitre_id-desc{{/tr}}">{{tr}}CDocGed-doc_chapitre_id{{/tr}}</label>
      <select name="selChapitre" onchange="this.form.submit();">
        <option value="0">&mdash; {{tr}}_CChapitreDoc_allchapitres{{/tr}}</option>
        {{*1er niveau*}}
        {{foreach from=$listChapitres item=curr_chapitre}}
        <option value="{{$curr_chapitre->doc_chapitre_id}}" {{if $selChapitre == $curr_chapitre->doc_chapitre_id}}selected="selected"{{/if}} >
          {{$curr_chapitre->_view}}
        </option>
        {{*2�me niveau*}}
        {{foreach from=$curr_chapitre->_ref_chapitres_doc item=curr_chapitre2}}
        <option value="{{$curr_chapitre2->doc_chapitre_id}}" {{if $selChapitre == $curr_chapitre2->doc_chapitre_id}}selected="selected"{{/if}} >
          |&mdash;{{$curr_chapitre2->_view}}
        </option>
        {{*3�me niveau*}}
        {{foreach from=$curr_chapitre2->_ref_chapitres_doc item=curr_chapitre3}}
        <option value="{{$curr_chapitre3->doc_chapitre_id}}" {{if $selChapitre == $curr_chapitre3->doc_chapitre_id}}selected="selected"{{/if}} >
          |&mdash;|&mdash;{{$curr_chapitre3->_view}}
        </option>
        {{*4�me niveau*}}
        {{foreach from=$curr_chapitre3->_ref_chapitres_doc item=curr_chapitre4}}
        <option value="{{$curr_chapitre4->doc_chapitre_id}}" {{if $selChapitre == $curr_chapitre4->doc_chapitre_id}}selected="selected"{{/if}} >
          |&mdash;|&mdash;|&mdash;{{$curr_chapitre4->_view}}
        </option>
        {{*5�me niveau*}}
        {{foreach from=$curr_chapitre4->_ref_chapitres_doc item=curr_chapitre5}}
        <option value="{{$curr_chapitre5->doc_chapitre_id}}" {{if $selChapitre == $curr_chapitre5->doc_chapitre_id}}selected="selected"{{/if}} >
          |&mdash;|&mdash;|&mdash;|&mdash;{{$curr_chapitre5->_view}}
        </option>
        {{/foreach}}
        {{/foreach}}
        {{/foreach}}
        {{/foreach}}
        {{/foreach}}
      </select>
      </form>
      
      <table class="tbl">
        <tr>
          {{if $can->edit}}
          <th />
          {{/if}}
          <th>{{tr}}CDocGed-_reference_doc{{/tr}}</th>
          <th>{{tr}}CDocGed-titre{{/tr}}</th>
          <th>{{tr}}Date{{/tr}}</th>          
        </tr>
        {{foreach from=$procedures item=currProc}}
        <tr>
          {{if $can->edit}}
          <td>
            {{if $can->admin}}
            <form name="ProcEditFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
              <input type="hidden" name="dosql" value="do_docged_aed" />
              <input type="hidden" name="m" value="{{$m}}" />
              <input type="hidden" name="del" value="0" />
              <input type="hidden" name="_validation" value="1" />
              <input type="hidden" name="ged[doc_ged_id]" value="{{$currProc->doc_ged_id}}" />  
              <input type="hidden" name="ged[user_id]" value="{{$app->user_id}}" />
              <input type="hidden" name="ged[group_id]" value="{{$currProc->group_id}}" />
              <input type="hidden" name="ged[annule]" value="1" />
              <input type="hidden" name="ged[etat]" value="{{$currProc->etat}}" />
              <button class="cancel notext" style="float: left;" type="submit" title="{{tr}}button-CDocGed-cancel{{/tr}}">
                {{tr}}button-CDocGed-cancel{{/tr}}
              </button>
            </form>
            {{/if}}
            {{if $currProc->etat==$docGed|const:'TERMINE'}}
            <button type="button" class="edit notext" style="float: left;" onclick="document.location = '?m={{$m}}&amp;tab=vw_procencours&amp;doc_ged_id={{$currProc->doc_ged_id}}'" title="{{tr}}button-CDocGed-edit{{/tr}}">
              {{tr}}button-CDocGed-edit{{/tr}}
            </button>
            {{/if}}
          </td>
          {{/if}}
          <td class="text">
            <a href="#" onclick="ZoomAjax('{{$currProc->_class_name}}','{{$currProc->_id}}','CFile','{{$currProc->_lastactif->file_id}}', 0);">
              {{$currProc->_reference_doc}}
              <br />
              {{tr}}CDocGed-version-court{{/tr}} {{$currProc->version}}
            </a>
          </td>
          <td class="text">
            <a href="#" onclick="ZoomAjax('{{$currProc->_class_name}}','{{$currProc->_id}}','CFile','{{$currProc->_lastactif->file_id}}', 0);">
              {{$currProc->titre}}
            </a>
          </td>
          <td>
            {{$currProc->_lastactif->date|date_format:"%d/%m/%Y"}}
          </td>
        </tr>
        {{foreachelse}}
        <tr>
          {{if $can->edit}}
          <td colspan="4">
          {{else}}
          <td colspan="3">
          {{/if}}
            {{tr}}CDocGed.none{{/tr}}
          </td>
        </tr>
        {{/foreach}}
      </table>    
    </td>
    <td class="halfPane" id="bigView" style="text-align: center;">
      {{include file="../../dPfiles/templates/inc_preview_file.tpl"}}
    </td>
  </tr>
</table>