{{assign var="operation" value=$object}}

<table class="form">

  <tr>
    <th class="title" colspan="2">
      {{mb_include module=system template=inc_object_idsante400}}
      {{mb_include module=system template=inc_object_history}}
      {{mb_include module=system template=inc_object_notes}}
      {{$operation}}
    </th>
  </tr>
  
  {{if $operation->annulee == 1}}
  <tr>
    <th class="category cancelled" colspan="4">
    {{tr}}COperation-annulee{{/tr}}
    </th>
  </tr>
  {{/if}}

  <tr>
    <td>
      <strong>{{tr}}COperation-chir_id-court{{/tr}} :</strong>
      {{$operation->_ref_chir->_view}}
    </td>
    <td>
      <strong>{{tr}}COperation-anesth_id-court{{/tr}} :</strong>
      {{$operation->_ref_anesth->_view}}
    </td>
  </tr>

  <tr>
    <td>
      <strong>{{tr}}COperation-date-court{{/tr}} :</strong>
      {{$operation->_datetime|date_format:"%d %B %Y"}}
    </td>
    <td>
    </td>
  </tr>

  <tr>
    <td>
      <strong>{{tr}}COperation-libelle{{/tr}} :</strong>
      {{$operation->libelle}}
    </td>
    <td>
      <strong>{{tr}}COperation-cote{{/tr}} :</strong>
      {{tr}}{{$operation->cote}}{{/tr}}
    </td>
  </tr>

  <tr>
    <td>
      <strong>{{tr}}COperation-_lu_type_anesth{{/tr}} :</strong>
      {{$operation->_lu_type_anesth}}
    </td>
  </tr>
  
  {{if $operation->materiel}}
  <tr>
    <td class="text" colspan="2">
      <strong>{{tr}}COperation-materiel-court{{/tr}} :</strong>
      {{$operation->materiel|nl2br}}
    </td>
  </tr>
  {{/if}}

  {{if $operation->exam_per_op}}
  <tr>
    <td class="text" colspan="2">
      <strong>{{tr}}COperation-exam_per_op-court{{/tr}} :</strong>
      {{$operation->exam_per_op|nl2br}}
    </td>
  </tr>
  {{/if}}

  {{if $operation->rques}}
  <tr>
    <td class="text" colspan="2">
      <strong>{{tr}}COperation-rques-court{{/tr}} :</strong>
      {{$operation->rques|nl2br}}
    </td>
  </tr>
  {{/if}}

  {{if $operation->examen}}
  <tr>
    <td class="text" colspan="2">
      <strong>{{tr}}COperation-examen{{/tr}} :</strong>
      {{$operation->examen|nl2br}}
    </td>
  </tr>
  {{/if}}
</table>

<table class="tbl">
  {{mb_include module=cabinet template=inc_list_actes_ccam subject=$object vue=complete}}
</table>

<table class="form">
  <tr>
    <th class="category" colspan="2">{{tr}}COperation-msg-horodatage{{/tr}}</th>
  </tr>
  
  <tr>
    <td>
      <strong>{{tr}}COperation-entree_salle{{/tr}} :</strong>
      {{$operation->entree_salle}} 
    </td>
    <td>
      <strong>{{tr}}COperation-sortie_salle{{/tr}} :</strong>
      {{$operation->sortie_salle}} 
    </td>
  </tr>

  <tr>
    <td>
      <strong>{{tr}}COperation-debut_op{{/tr}} :</strong>
      {{$operation->debut_op}} 
    </td>
    <td>
      <strong>{{tr}}COperation-fin_op{{/tr}} :</strong>
      {{$operation->fin_op}} 
    </td>
  </tr>

  <tr>
    <td>
      <strong>{{tr}}COperation-pose_garrot{{/tr}} :</strong>
      {{$operation->pose_garrot}} 
    </td>
    <td>
      <strong>{{tr}}COperation-retrait_garrot{{/tr}} :</strong>
      {{$operation->retrait_garrot}} 
    </td>
  </tr>

  <tr>
    <td>
      <strong>{{tr}}COperation-induction_debut{{/tr}} :</strong>
      {{$operation->induction_debut}} 
    </td>
    <td>
      <strong>{{tr}}COperation-induction_fin{{/tr}} :</strong>
      {{$operation->induction_fin}} 
    </td>
  </tr>

  <tr>
    <td>
      <strong>{{tr}}COperation-suture_fin{{/tr}} :</strong>
      {{$operation->suture_fin}}
    </td>
  </tr>

</table>