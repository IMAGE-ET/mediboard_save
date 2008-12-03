{{* $Id: $ *}}

{{if $dmi_id!="0" && $dmi_id != $dmi->_id}}
<div class="big-warning">
  Le DMI voulu (#{{$dmi_id}}) n'a pas pu �tre charg�. Deux raisons sont possibles :
  <ul>
    <li>Soit le DMI a �t� <strong>supprim� du catalogue</strong>,</li>
    <li>Soit le DMI est dans <strong>un catalogue d'un autre �tablissement</strong>.</li>
  </ul>
</div>
{{/if}}

<table class="main">
  <tr>
    <td colspan="10">{{include file=inc_check_dPstock.tpl}}</td>
  </tr>
  <tr>
    <td>{{include file=inc_list_dmis.tpl}}</td>
    <td>{{include file=inc_form_dmis.tpl}}</td>
  </tr>
</table>



