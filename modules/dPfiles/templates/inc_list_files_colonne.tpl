{{if isset($category_id|smarty:nodefaults)}}
  <script type="text/javascript">
    // Mise � jour des compteurs des documents
    Main.add(function() {
      var countDocItemTotal = {{math equation="x+y" x=$object->_ref_documents|@count y=$object->_ref_files|@count}};
      var countDocItemCat = {{$list|@count}};
      var button = $("docItem_{{$object->_guid}}");
      var tab = $("tab_category_{{$category_id}}");
      var countTab = tab.down("small");
      var tabPatient = $("listViewPatient");
      
      if (button) {
        button.update(countDocItemTotal);
        if (!countDocItemTotal) {
          button.addClassName("right-disabled");
          button.removeClassName("right");
        }
        else {
          button.removeClassName("right-disabled");
          button.addClassName("right");
        }
      }
      if (tab) {
        countTab.update("("+countDocItemCat+")");
        if (!countDocItemCat) {
          tab.addClassName("empty");
        }
        else {
          tab.removeClassName("empty");
        }
      }
      if (tabPatient) {
        tabPatient.down("span").update(countDocItemTotal);
        if (!countDocItemCat) {
          tabPatient.addClassName("empty");
        }
        else {
          tabPatient.removeClassName("empty");
        }
      }
    });
  </script>
{{/if}}

{{foreach from=$list item=_doc_item}}
  <div style="float: left; width: 220px;">
    <table class="tbl">
      <tbody class="hoverable">
        <tr>
          <td rowspan="2" style="width: 70px; height: 112px; text-align: center">
            <div></div>
            {{assign var="elementId" value=$_doc_item->_id}}
            {{if $_doc_item->_class=="CCompteRendu"}}
              {{if $conf.dPcompteRendu.CCompteRendu.pdf_thumbnails && $app->user_prefs.pdf_and_thumbs}}
                {{assign var="file_id"  value=$_doc_item->_ref_file->_id}}
                {{assign var="srcImg"   value="?m=dPfiles&a=fileviewer&suppressHeaders=1&file_id=$file_id&phpThumb=1&w=64&h=92"}}
              {{else}}
                {{assign var="srcImg" value="images/pictures/medifile.png"}}
              {{/if}}
            {{else}}
              {{assign var="srcImg" value="?m=dPfiles&a=fileviewer&suppressHeaders=1&file_id=$elementId&phpThumb=1&w=64&h=92"}}
            {{/if}}

            <a href="#" onclick="popFile('{{$object->_class}}', '{{$object->_id}}', '{{$_doc_item->_class}}', '{{$elementId}}', '0');">
              <img class="thumbnail" src="{{$srcImg}}" />
            </a>
          </td>
          
          <!-- Tooltip -->
          <td class="text" style="height: 35px; overflow: auto">
            <span onmouseover="ObjectTooltip.createEx(this, '{{$_doc_item->_guid}}');">
              {{$_doc_item->_view|truncate:60}}
              {{if $_doc_item->private}}
                &mdash; <em>{{tr}}CCompteRendu-private{{/tr}}</em>
              {{/if}}
            </span>
          </td>
        </tr>
        <tr>
          <!-- Toolbar -->
          <td class="button" style="height: 1px;">
            {{include file=inc_file_toolbar.tpl notext=notext}}
          </td>
        </tr>
      </tbody>
    </table>
  </div>
{{foreachelse}}
  <div class="empty">Aucun document</div>
{{/foreach}}