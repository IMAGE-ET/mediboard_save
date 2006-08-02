<table class="form">
  <tr>
    <td>
    {{if $filePrev}}
    <a class="button" href="index.php?m=dPfiles&amp;a=preview_files&amp;popup=1&amp;file_id={{$filePrev}}&amp;dialog=1&amp;navig=1"><img align="top" src="modules/{{$m}}/images/prev.png" alt="Fichier pr�c�dent" /> Fichier pr�c�dent</a>
    {{/if}}
    </td>
    <th>
    {{if $fileNext}}
    <a class="button" href="index.php?m=dPfiles&amp;a=preview_files&amp;popup=1&amp;file_id={{$fileNext}}&amp;dialog=1&amp;navig=1">Fichier suivant <img align="top" src="modules/{{$m}}/images/next.png" alt="Fichier suivant" /></a>
    {{/if}}
    </th>
  </tr>
  <tr>
    <td class="button" colspan="2">      
      {{if $file->file_id}}
        {{$file->_view}} (dans {{if $listCat->nom}}{{$listCat->nom}}{{else}}Aucune Cat�gorie{{/if}})<br />
        {{$file->file_date|date_format:"%d/%m/%Y � %Hh%M"}}<br />
        {{if $file->_nb_pages}}
            {{if $page_prev !== null}}
            <a class="button" href="index.php?m=dPfiles&amp;a=preview_files&amp;popup=1&amp;file_id={{$file->file_id}}&amp;dialog=1&amp;navig=1&amp;sfn={{$page_prev}}"><img align="top" src="modules/{{$m}}/images/prev.png" alt="Page pr�c�dente" /> Page pr�c�dente</a>
            {{/if}}
            {{if $page_next}}
            <a class="button" href="index.php?m=dPfiles&amp;a=preview_files&amp;popup=1&amp;file_id={{$file->file_id}}&amp;dialog=1&amp;navig=1&amp;sfn={{$page_next}}">Page suivante <img align="top" src="modules/{{$m}}/images/next.png" alt="Page suivante" /></a>
            {{/if}}
        {{/if}}<br />
        <a href="mbfileviewer.php?file_id={{$file->file_id}}"><img src="mbfileviewer.php?file_id={{$file->file_id}}&amp;phpThumb=1&amp;w=600{{if $sfn}}&amp;sfn={{$sfn}}{{/if}}" alt="-" border="0" /></a>
      {{/if}}
    </td>
  </tr>
</table>