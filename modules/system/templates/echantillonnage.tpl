<form action="index.php?m={{$m}}" name="echantillonage" method="post" onsubmit="return checkForm(this)">
<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="dosql" value="do_echantillonnage" />
<input type="hidden" name="tab" value="{{$tab}}" />

<table class="form">
  <tr>
    <th class="title" colspan="4">
      Echantillonnage
    </th>
  </tr>
  
  <tr>
    <th>
      <label for="etablissement" title="Veuillez saisir un titre pour l'etablissement">Titre de l'etablissement</label>
    </th>
    <td>
      <input type="text" name="etablissement" value="" title="str|notNull" />
    </td>
    <th>
      <label for="_nb_pat" title="Nombre de patients � cr�er">Nombre de patients � cr�er</label>
    </th>
    <td>
      {{html_options name="_nb_pat" options=$list_50}}
    </td>
  </tr>
  
  <tr>
    <th>
      <label for="_nb_plages" title="Nombre de plages � cr�er par praticien">Nombre de plages � cr�er</label>
    </th>
    <td>
      {{html_options name="_nb_plages" options=$list_13}}
    </td>
    <th>
      <label for="_nb_prat" title="Nombre de praticiens � cr�er">Nombre de praticiens � cr�er</label>
    </th>
    <td>
      {{html_options name="_nb_prat" options=$list_10}}
    </td>
  </tr>
  
  <tr>
    <th>
      <label for="_nb_consult" title="Nombre de consultation � cr�er par praticien">Nombre de consultation</label>
    </th>
    <td>
      {{html_options name="_nb_consult" options=$list_20}}
    </td>
    <th>
      <label for="_nb_fct" title="Nombre de fonctions � cr�er">Nombre de fonctions � cr�er</label>
    </th>
    <td>
      {{html_options name="_nb_fct" options=$list_5}}
    </td>
  </tr>



  <tr>
    <td colspan="4" class="button">
      <button type="submit" class="submit">Cr�er</button>
    </td>
  </tr>
</table>
</form>