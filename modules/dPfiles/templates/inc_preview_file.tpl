{{if $fileSel && $fileSel->_id}}
  <strong>{{$fileSel->_view}}</strong><br />
  {{if $fileSel->_class_name=="CFile"}}
    {{$fileSel->file_date|date_format:"%d/%m/%Y � %Hh%M"}}<br />
  {{/if}}
  
  {{if $fileSel->_class_name == "CFile" && $fileSel->_nb_pages && !$acces_denied}}
  <!-- D�placement dans les pages -->
    {{if $page_prev !== null}}
    <a class="button" href="#" onclick="ZoomAjax('{{$objectClass}}', '{{$objectId}}', '{{$elementClass}}', '{{$elementId}}', '{{$page_prev}}');">
    	<img align="top" src="images/icons/prev.png" alt="Page pr�c�dente" /> 
    	Page pr�c�dente
    </a>
    {{/if}}
    
    {{if $fileSel->_nb_pages && $fileSel->_nb_pages>=2}}
      <select name="_num_page" onchange="ZoomAjax('{{$objectClass}}', '{{$objectId}}', '{{$elementClass}}', '{{$elementId}}', this.value);">
      {{foreach from=$arrNumPages item=currPage}}
      <option value="{{$currPage-1}}" {{if $currPage-1==$sfn}}selected="selected" {{/if}}>
      Page {{$currPage}} / {{$fileSel->_nb_pages}}
      </option>
      {{/foreach}}
      </select>
    {{elseif $fileSel->_nb_pages}}
      Page {{$sfn+1}} / {{$fileSel->_nb_pages}}
    {{/if}}
            
    {{if $page_next}}
    <a class="button" href="#" onclick="ZoomAjax('{{$objectClass}}', '{{$objectId}}', '{{$elementClass}}', '{{$elementId}}', '{{$page_next}}');">
    	Page suivante 
    	<img align="top" src="images/icons/next.png" alt="Page suivante" />
    </a>
    {{/if}}
  {{/if}}
  
  <hr />
    <a href="#popFile" onclick="popFile('{{$objectClass}}', '{{$objectId}}', '{{$elementClass}}', '{{$elementId}}',{{if $sfn}}{{$sfn}}{{else}}0{{/if}})">
	    {{if $includeInfosFile}}
	      {{include file="../../dPfiles/templates/inc_preview_contenu_file.tpl"}}
	    {{else}}
	      <img class="preview" src="?m=dPfiles&amp;a=fileviewer&amp;suppressHeaders=1&amp;file_id={{$fileSel->file_id}}&amp;phpThumb=1&amp;hp=450&amp;wl=450{{if $sfn}}&amp;sfn={{$sfn}}{{/if}}" title="Afficher le grand aper�u" border="0" />
	    {{/if}}
    </a>  

{{else}}
  <div class="big-info">
  S�lectionnez un document pour en avoir un aper�u.
  </div>
{{/if}}