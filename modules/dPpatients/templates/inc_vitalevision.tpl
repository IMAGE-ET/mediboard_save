{{assign var=debug value="false"}}

<!-- Ne pas mettre "display: none" sinon l'applet ne se lancera pas dans Firefox -->
<applet name="resultVitaleVision" 
  code="org.yoplet.Yoplet.class" 
  archive="includes/applets/yoplet.jar?build={{$version.build}}" 
  {{if $debug=="true"}}
  width="400" height="200"
  {{else}}
  width="0" height="0"
  style="position: absolute; left: 5000px;"
  {{/if}}>
  <param name="action" value="sleep" />
  <param name="lineSeparator" value="" />
  <param name="debug" value="{{if $debug=="true"}}true{{else}}false{{/if}}" />
  <param name="filePath" value="{{$app->user_prefs.VitaleVisionDir}}/VitaleHex.xml" />
  {{if !@$keepFiles}}
  <param name="flagPath" value="{{$app->user_prefs.VitaleVisionDir}}/Vitale.csv" />
  {{/if}}
</applet>

{{mb_include_script script=vitalevision}}