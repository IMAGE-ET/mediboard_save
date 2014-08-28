{{mb_script module=forms script=ex_class_editor}}

<script>
function displayListItems(select, key) {
  var list_id = $V(select).split(/-/)[1];
  var items = $("list-items-"+key);
  
  if (!list_id || list_id == "__create__") {
    items.update("");
    return;
  }
  
  var url = new Url("forms", "ajax_ex_list_info");
  url.addParam("list_id", list_id);
  url.requestUpdate(items);
}
</script>

<div class="small-info">
  Ne seront pas import�s:
  <ul>
    <li>Les valeurs par d�faut des champs de type liste</li>
    <li>Les valeurs d�coch�es des listes dans les champs (si la liste provient d'un concept)</li>
    <li>Les sous-formulaires</li>
    <li>Les �v�nements d�clencheurs</li>
  </ul>
</div>

<table class="main layout">
  <tr>
    <td style="width: 50%">
      <fieldset>
        <legend>1. T�l�versement</legend>
        <iframe name="upload-import-file" id="upload-import-file" style="width: 1px; height: 1px;"></iframe>

        <form method="post" name="upload-import-file-form" enctype="multipart/form-data" target="upload-import-file">
          <input type="hidden" name="m" value="forms" />
          <input type="hidden" name="dosql" value="do_upload_import_ex_class" />

          <input type="hidden" name="MAX_FILE_SIZE" value="4096000" />
          <input type="file" name="import" style="width: 400px;" onchange="ExClass.uploadReset()" />

          <button type="submit" class="submit">{{tr}}Upload{{/tr}}</button>
          <span class="upload-ok" style="display: none;">
            <img src="./images/icons/tick.png" />
            Le fichier est pr�t � �tre import�
          </span>
          <span class="upload-error" style="display: none;">
            <img src="./images/icons/cancel.png" />
            Le fichier n'est pas valide, ce doit �tre un fichier XML export� depuis Mediboard
          <span>
        </form>
      </fieldset>

      <div id="import-steps"></div>
    </td>
    <td>
      <fieldset>
        <legend>Rapport d'importation</legend>
        <div id="ex_class-import-report"><span class="empty">Aucune importation r�alis�e</span></div>
      </fieldset>
    </td>
  </tr>
</table>
