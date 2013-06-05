{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage hprim21
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

var Sejour = {
  select: function(Numdos, IPP) {
		window.opener.SejourHprimSelector.set(Numdos, IPP);
    window.close();
  }
}

</script>

<div class="big-info">
  <strong>L'identifiant patient permanent (IPP)</strong> et/ou <strong>le num�ro de dossier</strong>
  sont manquant, veuillez les indiquer en choisissant un s�jour parmi la liste propos�e ou bien
  le rentrer manuellement dans le formulaire en bas.
</div>

<!-- Formulaire de recherche -->
<form action="?" name="patientSearch" method="get">

<input type="hidden" name="m" value="hprim21" />
<input type="hidden" name="a" value="sejour_hprim_selector" />
<input type="hidden" name="dialog" value="1" />

<table class="form">

<tr>
  <th class="category" colspan="6">Crit�res de s�lection</th>
</tr>

<tr>
  <th><label for="name" title="Nom du patient � rechercher, au moins les premi�res lettres">Nom</label></th>
  <td><input name="name" value="{{$name|stripslashes}}" size="30" tabindex="1" /></td>
  
  <th><label for="nomjf" title="Nom de naissance">Nom de naissance</label></th>
  <td><input name="nomjf" value="{{$nomjf|stripslashes}}" size="30" tabindex="3" /></td>
  
  <td>
  </td>
</tr>

<tr>
  <th><label for="firstName" title="Pr�nom du patient � rechercher, au moins les premi�res lettres">Pr�nom</label></th>
  <td><input name="firstName" value="{{$firstName|stripslashes}}" size="30" tabindex="2" /></td>
  
  <th><label for="naissance" title="Date de naissance">Date de naissance</label></th>
  <td>
    {{mb_include module=patients template=inc_select_date date=$datePat tabindex=4}}
  </td>
  <td><button class="search" type="submit">Rechercher</button></td>
</tr>
</table>

</form>

<!-- Liste de patients -->
<table class="tbl">
  <tr>
    <th class="category" colspan="5">Choisissez un patient dans la liste</th>
  </tr>
  <tr>
    <th>Patient</th>
    <th>Date de naissance</th>
    <th>T�l�phone</th>
    <th>Mobile</th>
    <th>Actions</th>
  </tr>

  <!-- Recherche exacte -->
  {{foreach from=$patients item=_patient}}
    {{include file="inc_line_sejour_hprim_selector.tpl"}}
  {{foreachelse}}
  {{if $name || $firstName}}
  <tr>
    <td class="button empty" colspan="5">
      Aucun r�sultat exact
    </td>
  </tr>
  {{/if}}
  {{/foreach}}
  <tr>
    <td class="button" colspan="5">
      <button class="cancel" type="button" onclick="window.close()">Annuler</button>
    </td>
  </tr>

  <!-- Recherche phon�tique -->
  {{if $patientsSoundex|@count}}
  <tr>
    <th colspan="5">
      <em>R�sultats proches</em>
    </th>
  </tr>
  {{/if}}

  {{foreach from=$patientsSoundex item=_patient}}
    {{include file="inc_line_sejour_hprim_selector.tpl"}}
  {{/foreach}}
</table>

{{if $IPP}}
<form action="?" onsubmit="if(checkForm(this)) {Sejour.select(this.numdos.value, null);} return false;">
{{else}}
<form action="?" onsubmit="if(checkForm(this)) {Sejour.select(this.numdos.value, this.IPP.value);} return false;">
{{/if}}
<table class="form">  
  <tr>
    <th colspan="2" class="category">Saisie manuelle</th>
  </tr>
  <tr>
    <th><label for="IPP">IPP</label></th>
    <td><input class="notNull" name="IPP" type="text" value="{{$IPP}}" tabindex="7" /></td>
  </tr>
  <tr>
    <th><label for="numdos">Num�ro de dossier</label></th>
    <td><input class="notNull" name="numdos" type="text" value="" tabindex="8" /></td>
  </tr>
  <tr>
    <td colspan="2" class="button">
      <button class="submit" type="submit">
        {{tr}}Save{{/tr}}
      </button>
    </td>
  </tr>
</table>
</form>
