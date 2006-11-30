<form action="index.php?m={{$m}}" name="echantillonage" method="post" onsubmit="return checkForm(this)">
<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="dosql" value="do_echantillonnage" />
<input type="hidden" name="tab" value="{{$tab}}" />

<table class="form">
  <tr>
    <th class="title" colspan="6">
      Echantillonnage
    </th>
  </tr>
  
  <tr>
    <th colspan="3">
      <label for="etablissement" title="Veuillez saisir un titre pour l'etablissement">Titre de l'etablissement</label>
    </th>
    <td colspan="3">
      <input type="text" name="etablissement" value="" title="str|notNull" />
    </td>
  </tr>
  
  <tr>
     <th>
      <label for="_nb_pat" title="Nombre de patients � cr�er">Nombre de patients</label>
    </th>
    <td>
      {{html_options name="_nb_pat" options=$list_50}}
    </td>
    <th>
      <label for="_nb_plages" title="Nombre de plages de consultations � cr�er par praticien">Nombre de plages de consultation</label>
    </th>
    <td>
      {{html_options name="_nb_plages" options=$list_14}}
    </td>
    <th>
      <label for="_nb_salles" title="Nombre de salles op�ratoire � cr�er">Nombre de salles</label>
    </th>
    <td>
      {{html_options name="_nb_salles" options=$list_5}}
    </td>
  </tr> 
  
  <tr>
    <th>
      <label for="_nb_prat" title="Nombre de praticiens � cr�er">Nombre de praticiens</label>
    </th>
    <td>
      {{html_options name="_nb_prat" options=$list_10}}
    </td>
    <th>
      <label for="_nb_consult" title="Nombre de consultation � cr�er par praticien">Nombre de consultation</label>
    </th>
    <td>
      {{html_options name="_nb_consult" options=$list_20}}
    </td>
    <th>
      <label for="_nb_plagesop" title="Nombre de plages op�ratoire � cr�er par salle et par semaine">Nombre de plage op�ratoire</label>
    </th>
    <td>
      {{html_options name="_nb_plagesop" options=$list_14}}
    </td>
  </tr>
  
  <tr>
    <th>
      <label for="_nb_fct" title="Nombre de fonctions � cr�er">Nombre de fonctions</label>
    </th>
    <td>
      {{html_options name="_nb_fct" options=$list_5}}
    </td>
    <td colspan="2"></td>
    <th>
      <label for="_nb_interv" title="Nombre d'intervention � cr�er par plage op�ratoire">Nombre d'intervention</label>
    </th>
    <td>
      {{html_options name="_nb_interv" options=$list_5}}
    </td>
  </tr>

  <tr>
    <td colspan="6" class="button">
      <button type="submit" class="submit">Cr�er</button>
    </td>
  </tr>
</table>
</form>