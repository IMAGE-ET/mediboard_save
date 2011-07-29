{{mb_script module=dPcompteRendu script=document}}

<script type="text/javascript">
Main.add(function () {
  {{if $accordDossier}}
  var tabs{{$selClass}}{{$selKey}} = Control.Tabs.create('tab-{{$selClass}}{{$selKey}}', false);
  {{else}}
  var tabs = Control.Tabs.create('tab-consult', true);
  {{/if}}
});
</script>

<ul id="tab-{{if $accordDossier}}{{$selClass}}{{$selKey}}{{else}}consult{{/if}}" class="control_tabs">
{{foreach from=$affichageFile item=_cat key=_cat_id}}
  <li>
    {{assign var=docCount value=$_cat.items|@count}}
    {{if $docCount || $conf.dPfiles.CFilesCategory.show_empty}}
  	<a href="#Category-{{$_cat_id}}" {{if !$docCount}}class="empty"{{/if}}>
  		{{$_cat.name}} 
  		<small>({{$docCount}})</small>
  	</a>
		{{/if}}
  </li>
{{/foreach}}
</ul>
<hr class="control_tabs" />

{{foreach from=$affichageFile item=_cat key=_cat_id}}
{{assign var=docCount value=$_cat.items|@count}}
{{if $docCount || $conf.dPfiles.CFilesCategory.show_empty}}
<table class="tbl" id="Category-{{$_cat_id}}" style="display: none;">
  {{if $canFile->edit && !$accordDossier}}
  <tr>
    <td colspan="2" class="text">
      <button class="new" onclick="uploadFile('{{$selClass}}', '{{$selKey}}', '{{$_cat_id}}')">
        Ajouter un fichier
      </button>
    </td>
  </tr>
  {{/if}}
  {{foreach from=$_cat.items item=_doc_item}}
  <tr>
    <td class="{{cycle name=cellicon values="dark, light"}}">
      {{assign var="elementId" value=$_doc_item->_id}}
      {{if $_doc_item->_class=="CCompteRendu"}}
        {{if $conf.dPcompteRendu.CCompteRendu.pdf_thumbnails && $app->user_prefs.pdf_and_thumbs}}
          {{assign var="nomdoc" value=$_doc_item->nom}}
          {{assign var="file_owner" value=$_doc_item->user_id}}
          {{assign var="file_id" value=$_doc_item->_ref_file->_id}}
          {{assign var="srcImg" value="?m=dPfiles&a=fileviewer&suppressHeaders=1&file_id=$file_id&phpThumb=1&w=64"}}
        {{else}}
          {{assign var="srcImg" value="images/pictures/medifile.png"}}
        {{/if}}
      {{else}}
        {{assign var="srcImg" value="?m=dPfiles&a=fileviewer&suppressHeaders=1&file_id=$elementId&phpThumb=1&wl=64&hp=64"}}
      {{/if}}
      
      <a href="#" onclick="ZoomAjax('{{$selClass}}', '{{$selKey}}', '{{$_doc_item->_class}}', '{{$elementId}}', '0');" title="Afficher l'aper�u">
        <img src="{{$srcImg}}" alt="-" width="64"/>
      </a>

    </td>
    <td class="text {{cycle name=celltxt values="dark, light"}}" style="vertical-align: middle;">
      <strong>
        {{$_doc_item}}
        {{if $_doc_item->private}}
          &mdash; <em>{{tr}}CCompteRendu-private{{/tr}}</em>
        {{/if}}
      </strong>
      <hr />
      {{include file=inc_file_toolbar.tpl notext=notext}}
    </td>
  </tr>
{{foreachelse}}
<tr>
  <td colspan="2" class="button">
    {{tr}}CDocument-none{{/tr}}            
  </td>
</tr>
{{/foreach}}
</table>
{{/if}}
{{/foreach}}