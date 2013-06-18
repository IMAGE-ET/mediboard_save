{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: 6287 $
 * @author SARL OpenXtrem
 * @license GNU GPL
*}}

<h2>{{tr}}CCorrespondant-import-modale-title{{/tr}}</h2>

<div class="small-info">
  Veuillez indiquez les champs suivants dans un fichier CSV (<strong>au format ISO</strong>) dont les champs sont s�par�s par
  <strong>;</strong> et les textes par <strong>"</strong>, la premi�re ligne �tant saut�e :
  <ul>
    <li>0 Code CDM </li>
    <li>1 {{tr}}CCorrespondantPatient-nom{{/tr}} *</li>
    <li>2 {{tr}}CCorrespondantPatient-adresse{{/tr}}</li>
    <li>3 {{tr}}CCorrespondantPatient-adresse{{/tr}} 2</li>
    <li>4 CP {{tr}}CCorrespondantPatient-ville{{/tr}}</li>
    <li>5 Type de Prise en Charge</li>
    <li>9 {{tr}}CCorrespondantPatient-ean-desc{{/tr}} *</li>
    <li>10 Surnom</li>
  </ul>
  <em>* : {{tr}}CCorrespondantPatient-import-required{{/tr}}</em>
</div>

<form method="post" action="?m={{$m}}&amp;{{$actionType}}={{$action}}&amp;dialog=1&amp;" name="import" enctype="multipart/form-data">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="{{$actionType}}" value="{{$action}}" />

  <input type="hidden" name="MAX_FILE_SIZE" value="4096000" />
  <input type="file" name="import" />

  <button type="submit" class="submit">{{tr}}Save{{/tr}}</button>
</form>

{{if $results|@count}}
<table class="tbl">
  <tr>
    <th class="title" colspan="16">{{$results|@count}} assurances trouv�s</th>
  </tr>
  <tr>
    <th>Etat</th>
    <th>{{tr}}CCorrespondantPatient-nom{{/tr}}</th>
    <th>{{tr}}CCorrespondantPatient-adresse{{/tr}}</th>
    <th>{{tr}}CCorrespondantPatient-adresse{{/tr}} 2</th>
    <th>{{tr}}CCorrespondantPatient-cp{{/tr}}</th>
    <th>{{tr}}CCorrespondantPatient-ville{{/tr}}</th>
    <th>{{tr}}CCorrespondantPatient-ean-desc{{/tr}}</th>

  </tr>
  {{foreach from=$results item=_corres}}
  <tr>
    <td class="text">
      {{if $_corres.error}}
        {{$_corres.error}}
      {{else}}
        OK
      {{/if}}
    </td>
    <td>{{$_corres.nom}}</td>
    <td>{{$_corres.adress}}</td>
    <td>{{$_corres.rue}}</td>
    <td>{{$_corres.cp}}</td>
    <td>{{$_corres.localite}}</td>
    <td>{{$_corres.ean}}</td>
  </tr>
  {{/foreach}}
</table>
{{/if}}

