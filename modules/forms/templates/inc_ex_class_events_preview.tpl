<table class="main tbl">
  <tr>
    <th>Veuillez choisir un �v�nement pour avoir un aper�u</th>
  </tr>
  {{foreach from=$ex_class->_ref_events item=_event}}
  <tr>
    <td>
      <button class="search" onclick="ExObject.preview('{{$_event->ex_class_id}}', '{{$_event->host_class}}-0')">
        {{$_event}}
      </button>
    </td>
  </tr>
  {{foreachelse}}
  <tr>
    <td class="empty">Il faut param�trer au moins un �v�nement</td>
  </tr>
  {{/foreach}}
</table>
