<h2>Contr�le d'int�grit� des documents</h2>

<table class="main">

<tr>
  <td>
  
<table class="tbl">
  <tr>
    <th class="title" colspan="5">Bilan d'int�grit�</th>
  </tr>
  <tr>
    <th>Nombre de documents</th>
    <th>Nombre de documents sans fichiers</th>
    <th>Nombre de fichiers</th>
    <th>Nombre de fichiers sans document</th>
    <th>Nombre de fichiers avec document erron�</th>
  </tr>
  <tr>
    <td>{{$docsCount}}</td>
    <td>{{$docsWithoutFileCount}}</td>
    <td>{{$filesCount}}</td>
    <td>{{$filesWithoutDocCount}}</td>
    <td>{{$filesWithBadDocCount}}</td>
  </tr>
</table>

  </td>
</tr>
<tr>
  <td>

<table class="tbl">
  <tr>
    <th class="title" colspan="4">
      D�tail des documents sans fichiers
      {{if $docsWithoutFileCount > $show}}
      ({{$show}} premiers documents sur {{$docsWithoutFileCount}})
      {{/if}}
    </th>
  </tr>
  <tr>
    <th>Classe d'objet</th>
    <th>Id d'object</th>
    <th>Nom r�el du fichier</th>
    <th>Chemin du fichier</th>
  </tr>
  {{foreach from=$docsWithoutFileTruncated item=curr_doc}}
  
  <tr>
    <td>{{$curr_doc->object_class}}</td>
    <td>{{$curr_doc->object_id}}</td>
    <td>{{$curr_doc->file_name}}</td>
    <td>{{$curr_doc->_file_path}}</td>
  </tr>
  {{/foreach}}
</table>

  </td>
</tr>
<tr>
  <td>

<table class="tbl">
  <tr>
    <th class="title" colspan="4">
      D�tail des fichiers sans documents
      {{if $filesWithoutDocCount > $show}}
      ({{$show}} premiers fichiers sur {{$filesWithoutDocCount}})
      {{/if}}
    </th>
  </tr>
  <tr>
    <th>Classe d'objet</th>
    <th>Id d'object</th>
    <th>Nom r�el du fichier</th>
    <th>Chemin du fichier</th>
  </tr>
  {{foreach from=$filesWithoutDocTruncated item=curr_file}}
  <tr>
    <td>{{$curr_file.fileObjectClass}}</td>
    <td>{{$curr_file.fileObjectId}}</td>
    <td>{{$curr_file.fileName}}</td>
    <td>{{$curr_file.filePath}}</td>
  </tr>
  {{/foreach}}
</table>

  </td>
</tr>
<tr>
  <td>

<table class="tbl">
  <tr>
    <th class="title" colspan="4">
      D�tail des fichiers avec un document erron�
      {{if $filesWithoutDocCount > $show}}
      ({{$show}} premiers fichiers sur {{$filesWithBadDocCount}})
      {{/if}}
    </th>
  </tr>
  <tr>
    <th>Classe d'objet</th>
    <th>Id d'object</th>
    <th>Nom r�el du fichier</th>
    <th>Chemin du fichier</th>
  </tr>
  {{foreach from=$filesWithBadDocTruncated item=curr_file}}
  <tr>
    <td>{{$curr_file.fileObjectClass}}</td>
    <td>{{$curr_file.fileObjectId}}</td>
    <td>{{$curr_file.fileName}}</td>
    <td>{{$curr_file.filePath}}</td>
  </tr>
  {{/foreach}}
</table>

  </td>
</tr>

</table>
