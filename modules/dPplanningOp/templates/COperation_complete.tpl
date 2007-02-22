<table class="form">

  <tr>
    <th class="title" colspan="2">
      {{$object->_view}}
    </th>
  </tr>
  
  <tr>
    <td>
      <strong>Praticien :</strong>
      {{$object->_ref_chir->_view}}
    </td>
    <td>
      <strong>Anesth�siste :</strong>
      {{$object->_ref_anesth->_view}}
    </td>
  </tr>

  <tr>
    <td>
      <strong>Date :</strong>
      le {{$object->_datetime|date_format:"%d %B %Y"}}
    </td>
    <td>
    </td>
  </tr>

  <tr>
    <td>
      <strong>Libell� :</strong>
      {{$object->libelle}}
    </td>
    <td>
      <strong>Cot�:</strong>
      {{tr}}{{$object->cote}}{{/tr}}
    </td>
  </tr>

  <tr>
    <td>
      <strong>Type d'anesth�sie :</strong>
      {{$object->_lu_type_anesth}}
    </td>
  </tr>
  
  {{if $object->materiel}}
  <tr>
    <td class="text" colspan="2">
      <strong>Mat�riel :</strong>
      {{$object->materiel|nl2br}}
    </td>
  </tr>
  {{/if}}

  {{if $object->rques}}
  <tr>
    <td class="text" colspan="2">
      <strong>Remarques :</strong>
      {{$object->rques|nl2br}}
    </td>
  </tr>
  {{/if}}

  {{if $object->examen}}
  <tr>
    <td class="text" colspan="2">
      <strong>Bilan pr�-op :</strong>
      {{$object->examen|nl2br}}
    </td>
  </tr>
  {{/if}}

  <tr>
    <th class="category" colspan="2">Actes pr�vus</th>
  </tr>
  {{foreach from=$object->_ext_codes_ccam item=currCode}}
  <tr>
    <td class="text" colspan="2">
      <strong>{{$currCode->code}}</strong> :
      {{$currCode->libelleLong}}
    </td>
  </tr>
  {{/foreach}}
  
  <tr>
    <th class="category" colspan="2">Actes cod�s</th>
  </tr>
  
  {{foreach from=$object->_ref_actes_ccam item=curr_acte}}
  <tr>
    <td>
      <strong>{{$curr_acte->_view}}</strong>
      par {{$curr_acte->_ref_executant->_view}} 
    </td>
    <td>
      {{$curr_acte->commentaire}}
    </td>
  </tr>
  {{/foreach}}
  
  <tr>
    <th class="category" colspan="2">Horodatage</th>
  </tr>
  
  <tr>
    <td>
      <strong>Entr�e salle :</strong>
      {{$object->entree_salle}} 
    </td>
    <td>
      <strong>Sortie salle :</strong>
      {{$object->sortie_salle}} 
    </td>
  </tr>

  <tr>
    <td>
      <strong>D�but intervention :</strong>
      {{$object->debut_op}} 
    </td>
    <td>
      <strong>Fin intervention :</strong>
      {{$object->fin_op}} 
    </td>
  </tr>

  <tr>
    <td>
      <strong>Pose garrot :</strong>
      {{$object->pose_garrot}} 
    </td>
    <td>
      <strong>Retrait garrot :</strong>
      {{$object->retrait_garrot}} 
    </td>
  </tr>

  <tr>
    <td>
      <strong>D�but induction :</strong>
      {{$object->induction_debut}} 
    </td>
    <td>
      <strong>Fin induction :</strong>
      {{$object->induction_fin}} 
    </td>
  </tr>

</table>