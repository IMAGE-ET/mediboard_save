{{mb_default var=canFile value=0}}
{{mb_default var=canDoc  value=0}}
{{mb_default var=mozaic  value=0}}

<div id="button_toolbar">
  {{assign var=object_guid value="$object_class-$object_id"}}
  {{if $canFile && !$accordDossier}}
    <button style="float: left" class="new" type="button" onclick="uploadFile('{{$object_guid}}')">
     {{tr}}CFile-title-create{{/tr}}
    </button>

    {{if $mozaic}}
      <button style="float:left;" class="new" type="button" onclick="File.createMozaic('{{$object_guid}}', '', reloadAfterUploadFile);">{{tr}}CFile-create-mozaic{{/tr}}</button>
    {{/if}}

    {{if $app->user_prefs.directory_to_watch}}
      <button class="new yopletbutton" style="float: left" type="button" disabled="disabled"
        onclick="File.applet.modalOpen('{{$object_guid}}')">
        {{tr}}Upload{{/tr}}
      </button>
    {{/if}}
    {{if $object && ($object->_nb_cancelled_files || $object->_nb_cancelled_docs)}}
      <button class="hslip" onclick="showCancelled(this)">Voir / Masquer {{math equation=x+y x=$object->_nb_cancelled_files y=$object->_nb_cancelled_docs}} fichier(s) annul�(s)</button>
    {{/if}}
  {{/if}}
  {{if $canDoc}}
    <div style="float: left" id="document-add-{{$object_guid}}"></div>
    <script type="text/javascript">
    Main.add(function () {
      Document.register('{{$object_id}}', '{{$object_class}}', '{{$praticienId}}', "document-add-{{$object_guid}}", "hide");
    });
    </script>
  {{/if}}
</div>