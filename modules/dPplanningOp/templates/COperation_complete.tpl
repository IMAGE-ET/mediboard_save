{{assign var="operation" value=$object}}

<table class="form">

  <tr>
    <th class="title" colspan="2">
      {{$operation->_view}}
    </th>
  </tr>
  
  {{if $operation->annulee == 1}}
  <tr>
    <th class="category cancelled" colspan="4">
    OPERATION ANNULEE
    </th>
  </tr>
  {{/if}}

  <tr>
    <td>
      <strong>Praticien :</strong>
      {{$operation->_ref_chir->_view}}
    </td>
    <td>
      <strong>Anesth�siste :</strong>
      {{$operation->_ref_anesth->_view}}
    </td>
  </tr>

  <tr>
    <td>
      <strong>Date :</strong>
      le {{$operation->_datetime|date_format:"%d %B %Y"}}
    </td>
    <td>
    </td>
  </tr>

  <tr>
    <td>
      <strong>Libell� :</strong>
      {{$operation->libelle}}
    </td>
    <td>
      <strong>Cot� :</strong>
      {{tr}}{{$operation->cote}}{{/tr}}
    </td>
  </tr>

  <tr>
    <td>
      <strong>Type d'anesth�sie :</strong>
      {{$operation->_lu_type_anesth}}
    </td>
  </tr>
  
  {{if $operation->materiel}}
  <tr>
    <td class="text" colspan="2">
      <strong>Mat�riel :</strong>
      {{$operation->materiel|nl2br}}
    </td>
  </tr>
  {{/if}}

  {{if $operation->rques}}
  <tr>
    <td class="text" colspan="2">
      <strong>Remarques :</strong>
      {{$operation->rques|nl2br}}
    </td>
  </tr>
  {{/if}}

  {{if $operation->examen}}
  <tr>
    <td class="text" colspan="2">
      <strong>Bilan pr�-op :</strong>
      {{$operation->examen|nl2br}}
    </td>
  </tr>
  {{/if}}

  <tr>
    <th class="category" colspan="2">Actes pr�vus</th>
  </tr>
  {{foreach from=$operation->_ext_codes_ccam item=currCode}}
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
  
  {{foreach from=$operation->_ref_actes_ccam item=curr_acte}}
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
      {{$operation->entree_salle}} 
    </td>
    <td>
      <strong>Sortie salle :</strong>
      {{$operation->sortie_salle}} 
    </td>
  </tr>

  <tr>
    <td>
      <strong>D�but intervention :</strong>
      {{$operation->debut_op}} 
    </td>
    <td>
      <strong>Fin intervention :</strong>
      {{$operation->fin_op}} 
    </td>
  </tr>

  <tr>
    <td>
      <strong>Pose garrot :</strong>
      {{$operation->pose_garrot}} 
    </td>
    <td>
      <strong>Retrait garrot :</strong>
      {{$operation->retrait_garrot}} 
    </td>
  </tr>

  <tr>
    <td>
      <strong>D�but induction :</strong>
      {{$operation->induction_debut}} 
    </td>
    <td>
      <strong>Fin induction :</strong>
      {{$operation->induction_fin}} 
    </td>
  </tr>

</table>