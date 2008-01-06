{{assign var="file" value=$object}}

<table class="tbl tooltip">
  <tr>
    <th>
      {{$file->_view}}
    </th>
  </tr>
  <tr>
    <td>
      <strong>Date de cr�ation :</strong>
	    {{$file->file_date|date_format:"%d %B %Y � %H:%M:%S"}}
	  </td>
	</tr>
	<tr>
	  <td>
      <strong>Propri�taire :</strong>
	    {{$file->_ref_file_owner->_view}}
	  </td>
	</tr>
	<tr>
	  <td>
      <strong>Poids :</strong>
	    {{$file->_file_size}}
	  </td>
	</tr>
	<tr>
	  <td class="text">
      <strong>Associ� � :</strong>
	    {{$file->_ref_object->_view}}
    </td>
  </tr>
</table>