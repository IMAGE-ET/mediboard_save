{{mb_script module=dPcabinet script=yoplet}}

{{if $app->user_prefs.directory_to_watch}}
  <script type="text/javascript">
    File.applet.extensions = '{{$conf.dPfiles.extensions_yoplet}}';
    File.appletDirectory = "{{$app->user_prefs.directory_to_watch|addslashes}}";
  </script>
  
  <!-- Modale pour l'applet --> 
  {{mb_include module=dPfiles template=yoplet_modal object=$object}}

  <applet id="uploader" name="yopletuploader" width="{{if $app->user_prefs.debug_yoplet == 1}}400{{else}}1{{/if}}"
          height="{{if $app->user_prefs.debug_yoplet == 1}}400{{else}}1{{/if}}"
          code="org.yoplet.Yoplet.class" archive="includes/applets/yoplet2.jar">
    <param name="debug" value="true" />
    <param name="action" value="" />
    <param name="url" value="{{$base_url}}/index.php?m=dPfiles&a=ajax_yoplet_upload" />
    <param name="content" value="a" />
    <param name="cookies" value="{{$session_name}}" />
  </applet>
  
  {{if $app->user_prefs.debug_yoplet}}
    <div id="yoplet-debug-console" style="border: 1px solid grey;">
      Directory watched: "{{$app->user_prefs.directory_to_watch}}"<br />
    </div>
  {{/if}}
{{/if}}