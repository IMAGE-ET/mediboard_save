{{assign var=pdf_thumbnails value=$dPconfig.dPcompteRendu.CCompteRendu.pdf_thumbnails}}
{{assign var="document" value=$object}}

{{if !$document->object_id}}
<table class="tbl">
  <tr>
    <th class="title">{{tr}}CCompteRendu-modele-one{{/tr}}</th>
  </tr>
</table>
{{/if}}

<table class="tbl">
  <tr>
  {{if $document->object_id && $pdf_thumbnails == 1 && isset($document->_ref_file|smarty:nodefaults) && $document->_ref_file->_id}}
    <td id="thumbnail-{{$document->_id}}" style="text-align: center; background: #fff url(style/mediboard/images/icons/loading.gif) center center no-repeat;">
      <img style="border: 1px solid #000; margin: auto; width: 64px; height: 91px;"
           src="?m=dPfiles&amp;a=fileviewer&amp;suppressHeaders=1&amp;file_id={{$document->_ref_file->_id}}&amp;phpThumb=1&amp;w=64" />
  </td>
  {{else}}
    <td>
      <img src="images/pictures/medifile.png"/>
    </td>
  {{/if}}
    <td>
      {{include file=CMbObject_view.tpl}}
      <hr />
      <table class="tbl">
        <tr>
          <td>
            <strong>{{mb_label object=$document field=compte_rendu_id}}:</strong>
              {{mb_value object=$document field=compte_rendu_id}}
          </td>
        </tr>

        <tr>
          <td>
            <strong>{{tr}}CCompteRendu-created-by{{/tr}} :</strong>
            {{$document->_ref_chir->_view}}
          </td>
        </tr>

        <tr>
          <td class="text">
            <strong>{{tr}}CCompteRendu-linked-object{{/tr}} :</strong>
	          {{$document->_ref_object}}
	        </td>
        </tr>

        <tr>
          <td>
            <strong>{{tr}}CCompteRendu-count-words{{/tr}} :</strong>
              {{$document->_source|count_words}} {{tr}}CCompteRendu-words{{/tr}}
	        </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
