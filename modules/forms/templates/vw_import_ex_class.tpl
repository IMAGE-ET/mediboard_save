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
  Quelques remarques sur l'importation des formulaires:
  <ul>
    <li>Les valeurs par d�faut des champs de type liste ne seront pas import�s</li>
    <li>Toutes les valeurs des listes seront pr�sentes dans le formulaire m�me si seulement une partie �taient activ�es dans le formulaires d'origine</li>
    <li>Les valeurs des listes dans le formulaire n'est pas conserv�</li>
    <li>Les sous-formulaires ne seront pas import�s</li>
    <li>Les �v�nements d�clencheurs ne seront pas import�s</li>
    <li>Les tags ne seront pas import�s</li>
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
