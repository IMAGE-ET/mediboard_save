{{if $templateManager->editor == "fckeditor2.3.2" }}

{{mb_include_script path="lib/fckeditor/fckeditor.js"}}

<script type="text/javascript">

function initFCKEditor() {
	var field = document.getElementById("htmlarea");
	var oFCKeditor = new FCKeditor(field.name, '100%', '100%') ;
	
	// Be carefull: after that all Js code is executed in lib/fckeditor/
	oFCKeditor.BasePath	= 'lib/fckeditor/';
	
	oFCKeditor.Config['CustomConfigurationsPath'] = '../../../modules/dPcompteRendu/mb_fckeditor2.3.2.php' ;
	
	oFCKeditor.ReplaceTextarea() ;
}

</script>
{{/if}}