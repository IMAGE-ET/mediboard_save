{{assign var="document" value=$object}}

<table class="tbl tooltip">
  <tr>
    <th>
      {{$document->_view}}
    </th>
  </tr>
  <tr>
    <td>
      <strong>Produit par :</strong>
	  {{$document->_ref_chir->_view}}
	  <br />
      <strong>Associ� � :</strong>
	  {{$document->_ref_object->_view}}
	  <br />
      <strong>Taille :</strong>
	  {{$document->source|count_words}}
	  mots
	  <br />
<!--
      <strong>Valid� :</strong>
	  {{mb_value object=$document field="valide"}}
-->
    </td>
  </tr>
</table>