<table class="form">
  <tr>
    <th class="title" colspan="2">
      <input type="hidden" name="suivi[etat]" value="{{$smarty.const.CDOC_REDAC}}" />
      Proc�dure en cours de r�daction ({{$docGed->_reference_doc}})
    </th>
  </tr>
  <tr>
    <td class="button" colspan="2">
      <br />La proc�dure est en cours de r�daction.
      <br />Vous ne pouvez pas y apporter de modification.
    </td>
  </tr>
  <tr>
    <th>Vis� par</th>
    <td>{{$docGed->_lastentry->_ref_user->_view}}</td>
  </tr>
  {{if $docGed->_lastentry->file_id}}
  <tr>
    <th>Dernier Fichier li�</th>
    <td>
      <a href="javascript:popFile({{$docGed->_lastentry->file_id}})" title="Voir le Fichier">
        <img src="index.php?m=dPfiles&amp;a=fileviewer&amp;suppressHeaders=1&amp;file_id={{$docGed->_lastentry->file_id}}&amp;phpThumb=1&amp;wl=64&amp;hp=64" alt="-" />
      </a>
    </td>
  </tr>
  {{/if}}
  <tr>
    <th>
      <strong>Dernier commentaire :</strong><br />
      {{$docGed->_lastentry->date|date_format:"%d %B %Y - %Hh%M"}}
    </th>
    <td>
      {{$docGed->_lastentry->remarques|nl2br}}
    </td>
  </tr>
  <tr>
    <td colspan="2" class="button">
      <button class="trash" type="button" onclick="confirmDeletion(this.form, {typeName:'La proc�dure ',objName:'{{$docGed->_reference_doc|escape:"javascript"}}'})" title="Supprimer la Proc�dure">
        Supprimer
      </button>
    </td>
  </tr>
</table>