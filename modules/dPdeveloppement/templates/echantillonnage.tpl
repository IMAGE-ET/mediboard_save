<form action="index.php?m={{$m}}" name="echantillonage" method="post" onsubmit="return checkForm(this)">
<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="dosql" value="do_echantillonnage" />
<input type="hidden" name="tab" value="{{$tab}}" />

<table class="form">
  <tr>
    <th class="title" colspan="8">
      Echantillonnage
    </th>
  </tr>
  
  <tr>
    <th colspan="4">
      <label for="etablissement" title="Veuillez saisir un titre pour l'etablissement">Titre de l'etablissement</label>
    </th>
    <td colspan="4">
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
    <th>
      <label for="_nb_services" title="Nombre de services � cr�er">Nombre de services</label>
    </th>
    <td>
      {{html_options name="_nb_services" options=$list_5}}
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
    <th>
      <label for="_nb_chambre" title="Nombre de chambre � cr�er par service">Nombre de chambre</label>
    </th>
    <td>
      {{html_options name="_nb_chambre" options=$list_20}}
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
    <th>
      <label for="_nb_lit" title="Nombre maximal de lit � cr�er par chambre">Nombre de lit</label>
    </th>
    <td>
      {{html_options name="_nb_lit" options=$list_5}}
    </td>
  </tr>

  <tr>
    <td colspan="8" class="button">
      <button type="submit" class="submit">Cr�er</button>
    </td>
  </tr>
</table>
</form>