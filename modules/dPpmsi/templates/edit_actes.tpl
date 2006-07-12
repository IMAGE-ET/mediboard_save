{literal}
<script type="text/javascript">

function pageMain() {
  initGroups("acte");
}

</script>
{/literal}

<table class="tbl">
  <tr>
    <th class="title" colspan="2">
      {$selOp->_ref_sejour->_ref_patient->_view} &mdash; {$selOp->_datetime|date_format:"%A %d %B %Y"}<br />
      Chirurgien : Dr. {$selOp->_ref_chir->_view}<br />
      Anesth�siste probable : Dr. {$selOp->_ref_anesth->_view} 
      
    </th>
  </tr>
  <tr>
    <th>Patient</th>
    <td>{$selOp->_ref_sejour->_ref_patient->_view} &mdash; {$selOp->_ref_sejour->_ref_patient->_age} ans</td>
  </tr>
  <tr>
    <th>Actes</th>
    <td class="text">
    {include file="../../dPsalleOp/templates/inc_manage_codes.tpl"}
    </td>
  </tr>
  <tr>
    <th>
      Intervention
      <br />
      C�t� {$selOp->cote}
      <br />
      ({$selOp->temp_operation|date_format:"%Hh%M"})
    </th>
    <td class="text">
    {include file="../../dPsalleOp/templates/inc_codage_actes.tpl"}
    </td>
  </tr>
</table>