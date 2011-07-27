{{**
  * Show a file/document edit/delete/move/send toolbar
  *}}

{{assign var=pdf_thumbnails value=$conf.dPcompteRendu.CCompteRendu.pdf_thumbnails}}

{{if $canFile->edit && !$accordDossier}}

 {{if $_doc_item->_class_name=="CCompteRendu" && $_doc_item->_is_editable}}
 	<!-- Modification -->
   <button class="edit {{$notext}}" type="button" onclick="Document.edit({{$elementId}})">
     {{tr}}Edit{{/tr}}
   </button>
 {{/if}}
  
  <!-- T�l�chargement du fichier -->
  {{if $_doc_item->_class_name=="CFile"}}
    <a class="button new notext"
      href="?m=dPfiles&amp;a=fileviewer&amp;suppressHeaders=1&amp;file_id={{$_doc_item->_id}}"
      target="_blank" title="{{tr}}CFile.download{{/tr}}"></a>
  {{/if}}


  <!-- Impression -->
  {{if $_doc_item->_class_name=="CCompteRendu"}}
    <button type="button" class="print notext"
      onclick="
      {{if $pdf_thumbnails && $app->user_prefs.pdf_and_thumbs}}
        Document.printPDF('{{$_doc_item->_id}}');
      {{else}}
        Document.print('{{$_doc_item->_id}}')
      {{/if}}">
      {{tr}}Print{{/tr}}
    </button>
  {{/if}}
  
	<!-- Deletion -->
 {{if $_doc_item->_class_name=="CCompteRendu"}}
   <form name="editDoc{{$_doc_item->_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
   <input type="hidden" name="m" value="dPcompteRendu" />
   <input type="hidden" name="dosql" value="do_modele_aed" />
   <input type="hidden" name="compte_rendu_id" value="{{$_doc_item->_id}}" />
   <input type="hidden" name="del" value="0" />
   {{assign var="confirmDeleteType" value="le document"}}
   {{assign var="confirmDeleteName" value=$_doc_item->nom|smarty:nodefaults|JSAttribute}}
 {{/if}}
   
 {{if $_doc_item->_class_name=="CFile"}}
   <form name="editFile{{$_doc_item->_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
   <input type="hidden" name="m" value="dPfiles" />
   <input type="hidden" name="dosql" value="do_file_aed" />
   <input type="hidden" name="file_id" value="{{$_doc_item->_id}}" />
   <input type="hidden" name="del" value="0" />
   {{assign var="confirmDeleteType" value="le fichier"}}
   {{assign var="confirmDeleteName" value=$_doc_item->file_name|smarty:nodefaults|JSAttribute}}
 {{/if}}
 
	<!-- Deletion -->
  <button type="button" class="trash  {{$notext}}" onclick="file_deleted={{$elementId}};confirmDeletion(
    this.form, {
      typeName:'{{$confirmDeleteType}}',
      objName:'{{$confirmDeleteName}}',
      ajax:1,
      target:'systemMsg'
    },{
      onComplete:reloadAfterDeleteFile
    } );">
    {{tr}}Delete{{/tr}}
  </button>
  
  <!-- Send File -->
  {{assign var=doc_class value=$_doc_item->_class_name}}
  {{assign var=doc_id    value=$_doc_item->_id        }}
	{{mb_include template=inc_file_send_button onComplete="Document.refreshList('$doc_class','$doc_id')"}}
  
	<!-- Move -->
	<button type="button" class="hslip  {{$notext}}" onclick="this.form.file_category_id.setVisibility(true)">
	  {{tr}}Move{{/tr}}
	</button>
	<br />
  <select style="visibility: hidden; width: 12em;" name="file_category_id" onchange="submitFileChangt(this.form)">
    <option value="" {{if !$_doc_item->file_category_id}}selected="selected"{{/if}}>&mdash; Aucune cat�gorie</option>
    {{foreach from=$listCategory item=curr_cat}}
    <option value="{{$curr_cat->file_category_id}}" {{if $curr_cat->file_category_id == $_doc_item->file_category_id}}selected="selected"{{/if}} >
      {{$curr_cat->nom}}
    </option>
    {{/foreach}}
  </select>
    
 </form>
 {{/if}}