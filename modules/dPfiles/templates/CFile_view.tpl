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
	  <br />
      <strong>Propri�taire :</strong>
	  {{$file->_ref_file_owner->_view}}
	  <br />
      <strong>Poids :</strong>
	  {{$file->_file_size}}
	  <br />
      <strong>Associ� � :</strong>
	  {{$file->_ref_object->_view}}
	  <br />
    </td>
  </tr>
</table>