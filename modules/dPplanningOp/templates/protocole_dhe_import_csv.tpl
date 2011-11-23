{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: 6287 $
 * @author SARL OpenXtrem
 * @license GNU GPL
*}}

<h2>Import de lits Mediboard.</h2>

<div class="small-info">
  Veuillez indiquez les champs suivants dans un fichier CSV (<strong>au format ISO</strong>) dont les champs sont s�par�s par
  <strong>;</strong> et les textes par <strong>"</strong>, la premi�re ligne �tant saut�e :
  <ul>
    <li>Nom de la fonction *</li>
    <li>Motif d'hospitalisation *</li>
    <li>Dur�e d'intervention (HH:MM) *</li>
    <li>Acte(s) CCAM (s�par�s par des |)</li>
    <li>Type d'hospitalisation (comp, ambu, exte, seances, ssr, psy, urg ou consult) *</li>
    <li>Dur�e d'hospitalisation en nuits *</li>
  </ul>
  <em>* : champs obligatoires</em>
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
    <th class="title" colspan="7">{{$results|@count}} protocoles trouv�s</th>
  </tr>
  <tr>
    <th>Etat</th>
    <th>Fonction</th>
    <th>Motif d'hospitalisation</th>
    <th>Dur�e d'intervention</th>
    <th>Actes</th>
    <th>Type d'hospi</th>
    <th>Dur�e d'hospi</th>
  </tr>
  {{foreach from=$results item=_protocole}}
  <tr>
    <td class="text">
      {{if $_protocole.error}}
        {{$_protocole.error}}
      {{else}}
        OK
      {{/if}}
    </td>
    <td>{{$_protocole.function}}</td>
    <td>{{$_protocole.motif}}</td>
    <td>{{$_protocole.temps_op}}</td>
    <td>{{$_protocole.actes}}</td>
    <td>{{$_protocole.type_hospi}}</td>
    <td>{{$_protocole.duree_hospi}}</td>
  </tr>
  {{/foreach}}
</table>
{{/if}}

