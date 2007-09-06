<table class="tbl">
  <tr>
    <th style="text-align: left; font-size: 1.5em; text-align: center">
    {{$praticien->_ref_banque->_view}}
    </th>
    <th style="text-align: right; font-size: 2em;" colspan="6">
      Remise de ch�ques
    </th>
  </tr>
  
  <tr>
    <th>Date</th>
    <th>Code Banque</th>
    <th>Code Guichet</th>
    <th>N� Compte</th>
    <th>Cl� RIB</th>
    <th colspan="2">Titulaire</th>
  </tr>
  
  <tr style="text-align: center">
    <td>{{$date|date_format:"%d/%m/%Y"}}</td>
    <td>{{$compte_banque}}</td>
    <td>{{$compte_guichet}}</td>
    <td>{{$compte_numero}}</td>
    <td>{{$compte_cle}}</td>
    <td colspan="2">{{$praticien->_view}}</td>
  </tr>
  
  <tr>
    <th colspan="3" class="title">Tireur</th>
    <th colspan="3" class="title">Etablissement payeur</th>
    <th class="title">Montant</th>
  </tr>
  
  {{foreach from=$listConsult item="consult"}}
  <tr>
    <td colspan="3">{{$consult->_ref_patient->_view}}</td>
    <td colspan="3">{{$consult->_ref_banque->_view}}</td>
    <td>{{$consult->_somme}}</td>
  </tr>
  {{/foreach}}
  
  
  <tr>
    <td colspan="3"></td>
    <th>Nombre de remises</th>
    <td>{{$nbRemise}}</td>
    <th>Montant total</th>
    <td>{{$montantTotal}}</td>
  </tr>
</table>

<table class="form">
  <tr>
    <th class="category" style="width: 150px">Visa de la banque</th>
    <th class="category" style="width: 150px">Visa du client</th>
    
  </tr>
  <tr>  
    <td style="height: 50px;"> </td>
    <td> </td>
  </tr>
</table>






