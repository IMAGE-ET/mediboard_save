<table class="tbl" style="width: auto !important;">
  <tr>
    <th>
      Ouvert
    </th>
    <td class="text">
      % du nombre de lits ouverts sur le nombre de lits total du service.<br />
      Un lit n'est pas ouvert s'il y a un blocage � minuit.
    </td>
  </tr>
  <tr>
    <th>
      Pr�vu
    </th>
    <td class="text">
      % du nombre de lits occup�s (p/r au nombre de lits ouverts) de mani�re pr�visionnelle � minuit.<br />
      On consid�re pr�vu un s�jour sectoris� dans un service.
    </td>
  </tr>
  <tr>
    <th>
      R�el
    </th>
    <td class="text">
      % du nombre de lits r�ellement occup�s (p/r au nombre de lits ouverts),<br />
      c'est � dire ayant un placement dans un lit ou dans le couloir du service � minuit
    </td>
  </tr>
  <tr>
    <th>
      Entr�es
    </th>
    <td>
      % du nombre d'entr�e dans le journ�e (p/r au nombre de lits ouverts). <br />
      Utile pour connaitre le taux d'activit� pour les services d'ambulatoire.
    </td>
  </tr>
  <tr>
    <td class="button" colspan="2">
      <button type="button" class="cancel" onclick="Control.Modal.close();">{{tr}}Close{{/tr}}</button>
    </td>
  </tr>
</table>