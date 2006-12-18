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
      
      <form name="FrmThemeVue" action="?m={{$m}}" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <label for="selTheme" title="{{tr}}CDocGed-doc_theme_id-desc{{/tr}}">{{tr}}CDocGed-doc_theme_id{{/tr}}</label>
      <select name="selTheme" onchange="this.form.submit();">
        <option value="0">&mdash; {{tr}}_CThemeDoc_alltheme{{/tr}}</option>
        {{foreach from=$listThemes item=currTheme}}
        <option value="{{$currTheme->doc_theme_id}}" {{if $selTheme==$currTheme->doc_theme_id}}selected="selected"{{/if}}>{{$currTheme->nom}}</option>
        {{/foreach}}
      </select>
      </form><br />
      
      <table class="tbl">
        <tr>
          {{if $canEdit}}
          <th>-</th>
          {{/if}}
          <th>{{tr}}CDocGed-_reference_doc{{/tr}}</th>
          <th>{{tr}}CDocGed-titre{{/tr}}</th>
          <th>{{tr}}Date{{/tr}}</th>          
        </tr>
        {{foreach from=$procedures item=currProc}}
        <tr>
          {{if $canEdit}}
          <td>
            {{if $canAdmin}}
            <form name="ProcEditFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
            <input type="hidden" name="dosql" value="do_docged_aed" />
            <input type="hidden" name="m" value="{{$m}}" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="_validation" value="1" />
            <input type="hidden" name="ged[doc_ged_id]" value="{{$currProc->doc_ged_id}}" />  
            <input type="hidden" name="ged[user_id]" value="{{$currProc->user_id}}" />
            <input type="hidden" name="ged[group_id]" value="{{$currProc->group_id}}" />
            <input type="hidden" name="ged[annule]" value="1" />
            <input type="hidden" name="ged[etat]" value="{{$currProc->etat}}" />
            <button class="cancel notext" style="float: left;" type="submit" title="{{tr}}button-CDocGed-cancel{{/tr}}">
            </button>
            </form>
            {{/if}}
            {{if $currProc->etat==$docGed|const:'TERMINE'}}
            <a class="buttonedit notext" style="float: left;" href="index.php?m={{$m}}&amp;tab=vw_procencours&amp;doc_ged_id={{$currProc->doc_ged_id}}" title="{{tr}}button-CDocGed-edit{{/tr}}"></a>
            {{/if}}
          </td>
          {{/if}}
          <td>
            <a href="#" onclick="ZoomAjax('{{$currProc->_class_name}}','{{$currProc->_id}}','CFile','{{$currProc->_lastactif->file_id}}', 0);">
              {{$currProc->_reference_doc}} ({{tr}}CDocGed-version-court{{/tr}} : {{$currProc->version}})
            </a>
          </td>
          <td class="text">
            <a href="#" onclick="ZoomAjax('{{$currProc->_class_name}}','{{$currProc->_id}}','CFile','{{$currProc->_lastactif->file_id}}', 0);">
              {{$currProc->titre}}
            </a>
          </td>
          <td>
            {{$currProc->_lastactif->date|date_format:"%d %b %Y � %Hh%M"}}
          </td>
        </tr>
        {{foreachelse}}
        <tr>
          {{if $canEdit}}
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