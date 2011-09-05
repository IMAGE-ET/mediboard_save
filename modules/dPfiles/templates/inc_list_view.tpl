{{mb_script module=dPcompteRendu script=document}}
{{assign var=object_class value=$object->_class}}
{{assign var=object_id value=$object->_id}}

<script type="text/javascript">
Main.add(function () {
  {{if $accordDossier}}
  var tabs{{$object_class}}{{$object_id}} = Control.Tabs.create('tab-{{$object_class}}{{$object_id}}', false);
  {{else}}
  var tabs = Control.Tabs.create('tab-consult', true);
  {{/if}}
});
</script>

<ul id="tab-{{if $accordDossier}}{{$object_class}}{{$object_id}}{{else}}consult{{/if}}" class="control_tabs">
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
      <button class="new" onclick="uploadFile('{{$object_class}}', '{{$object_id}}', '{{$_cat_id}}')">
        Ajouter un fichier
      </button>
    </td>
  </tr>
  {{/if}}
  <tbody id="Category-{{$_cat_id}}">
    {{mb_include module=dPfiles template=inc_list_files list=$_cat.items}}
  </tbody>
</table>
{{/if}}
{{/foreach}}