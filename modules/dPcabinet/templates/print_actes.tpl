{{if !@$offline}}
<h2>
  Consultation de {{$consult->_ref_patient}} par le Dr {{$consult->_ref_praticien}} le 
  {{mb_value object=$consult field=_date}}
</h2>
{{/if}}

<table class="main tbl">
{{mb_include module=cabinet template=inc_list_actes_ccam subject=$consult vue=complete extra=tarif}}
</table>

{{assign var=object value=$consult}}
<table class="main tbl">
  <tr>
    <th class="title" colspan="8">Codages des actes NGAP</th>
  </tr>

  <tr>
    <th class="category">{{mb_title class=CActeNGAP field=quantite}}</th>
    <th class="category">{{mb_title class=CActeNGAP field=code}}</th>
    <th class="category">{{mb_title class=CActeNGAP field=coefficient}}</th>
    <th class="category">{{mb_title class=CActeNGAP field=demi}}</th>
    <th class="category">{{mb_title class=CActeNGAP field=montant_base}}</th>
    <th class="category">{{mb_title class=CActeNGAP field=montant_depassement}}</th>
    <th class="category">{{mb_title class=CActeNGAP field=complement}}</th>
    <th class="category">{{mb_title class=CActeNGAP field=executant_id}}</th>
  </tr>
  
  {{foreach from=$object->_ref_actes_ngap item="_acte_ngap"}}
  <tr>
    <td>{{mb_value object=$_acte_ngap field="quantite"}}</td>
    <td>{{mb_value object=$_acte_ngap field="code"}}</td>
    <td>{{mb_value object=$_acte_ngap field="coefficient"}}</td>
    <td>{{mb_value object=$_acte_ngap field="demi"}}</td>
    <td style="text-align: right">{{mb_value object=$_acte_ngap field="montant_base"}}</td>
    <td style="text-align: right">{{mb_value object=$_acte_ngap field="montant_depassement"}}</td>
    <td>
      {{if $_acte_ngap->complement}}
        {{mb_value object=$_acte_ngap field="complement"}}
      {{else}}
        Aucun
      {{/if}}
    </td>

    {{assign var="executant" value=$_acte_ngap->_ref_executant}}
    <td> 
      <div class="mediuser" style="border-color: #{{$executant->_ref_function->color}};">
       {{$executant}}
      </div>
    </td>
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="8" class="empty">{{tr}}CActeNGAP.none{{/tr}}</td>
  </tr>
  {{/foreach}}
</table>