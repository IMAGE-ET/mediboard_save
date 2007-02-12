// Mediboard Combo configuration
var aMbCombos = new Array();
var bAutoSelectSpans = {{if $templateManager->valueMode}} false {{else}} true {{/if}};


// FCK editor general configuration
sMbPath = "../../../";
sMbPluginsPath = sMbPath + "modules/dPcompteRendu/fcke_plugins/" ;
FCKConfig.EditorAreaCSS = sMbPath + "style/mediboard/htmlarea.css?build={{$mb_version_build}}";
FCKConfig.Debug = false;

// Toolbar Configuration
FCKConfig.ToolbarSets["Default"] = [
	['Save','Preview'],
	['Cut','Copy','Paste','PasteText','PasteWord','-','mbPrint'],
	['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
	['Table','Rule','Image', 'Smiley','SpecialChar','mbPageBreak'],
	['FitWindow','Source','About'],
	'/',
	['FontFormat','FontName','FontSize'],
	['Bold','Italic','Underline','StrikeThrough','-','Subscript','Superscript'],
	['JustifyLeft','JustifyCenter','JustifyRight','JustifyFull'],
	['OrderedList','UnorderedList','-','Outdent','Indent'],
	['TextColor','BGColor']
] ;

// Add properties Combo
var oMbCombo = new Object();
oMbCombo.commandName = "MbField";
oMbCombo.spanClass = {{if $templateManager->valueMode}} "value" {{else}} "field" {{/if}};
oMbCombo.commandLabel = "Champs";

var aOptions = new Array();
oMbCombo.options = aOptions;
aMbCombos.push(oMbCombo);

{{foreach from=$templateManager->properties item=property}}
  aOptions.push( {
    view: "{{$property.field|smarty:nodefaults|escape:"htmlall"|escape:"javascript"}}" ,
    item: 
      {{if $templateManager->valueMode}} 
        "{{$property.value|smarty:nodefaults|escape:"htmlall"|nl2br|escape:"javascript"}}" 
      {{else}} 
        "[{{$property.field|smarty:nodefaults|escape:"htmlall"|escape:"javascript"}}]" 
      {{/if}}
    });
{{/foreach}}

{{if !$templateManager->valueMode}}
// Add name lists Combo
var oMbCombo = new Object();
oMbCombo.commandName = "MbNames";
oMbCombo.spanClass = "name";
oMbCombo.commandLabel = "Liste de choix";

var aOptions = new Array();
oMbCombo.options = aOptions;
aMbCombos.push(oMbCombo);

{{foreach from=$templateManager->lists item=list}}
  aOptions.push( { 
    view: "{{$list.name|smarty:nodefaults|escape:"htmlall"|escape:"javascript"}}" ,
    item: "[Liste - {{$list.name|smarty:nodefaults|escape:"htmlall"|escape:"javascript"}}]"
    });
{{/foreach}}
{{/if}}

// Add helpers Combo
var oMbCombo = new Object();
oMbCombo.commandName = "MbHelpers";
oMbCombo.spanClass = "helper";
oMbCombo.commandLabel = "Aides &agrave; la saisie";

var aOptions = new Array();
oMbCombo.options = aOptions;
aMbCombos.push(oMbCombo);

{{foreach from=$templateManager->helpers key=helperName item=helperText}}
  aOptions.push( { 
    view: "{{$helperName|smarty:nodefaults|escape:"htmlall"|escape:"javascript"}}" ,
    item: "{{$helperText|smarty:nodefaults|escape:"htmlall"|nl2br|escape:"javascript"}}"
    });
{{/foreach}}


aToolbarSet = FCKConfig.ToolbarSets["Default"];

// Add MbCombos toolbar
aMbToolbar = new Array();
for (var i = 0; i < aMbCombos.length; i++) {
  aMbToolbar.push(aMbCombos[i].commandName);
}
aToolbarSet.push(aMbToolbar);

FCKConfig.Plugins.Add( 'mbpagebreak', 'en,fr', sMbPluginsPath );
FCKConfig.Plugins.Add( 'mbcombo2.3.2', 'en,fr', sMbPluginsPath );
FCKConfig.Plugins.Add( 'mbprint', 'en,fr', sMbPluginsPath );