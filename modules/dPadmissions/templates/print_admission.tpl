<table class="form" id="admission">
  <tr>
    <th class="title" colspan="2">
      <a href="javascript:window.print()">R�capitulatif admission</a>
    </th>
  </tr>

  <tr>
    <th>Chirurgien : </th>
    <td>Dr. {$admission->_ref_chir->_view}</td>
  </tr>
  
  {assign var=patient value=$admission->_ref_pat}
  <tr>
    <th class="category" colspan="2">Informations sur le patient</th>
  </tr>
  
  <tr>
    <th>Nom / Pr�nom :</th>
    <td>{$patient->_view}</td>
  </tr>

  <tr>
    <th>Date de naissance / Sexe : </th>
    <td>n�(e) le {$patient->_jour}/{$patient->_mois}/{$patient->_annee} de sexe {$patient->sexe}</td>
  </tr>

  <tr>
    <th>Incapable majeur :</th>
    <td>{$patient->incapable_majeur}</td>
  </tr>

  <tr>
    <th>T�l�phone: </th>
    <td>{$patient->tel}</td>
  </tr>

  <tr>
    <th>Portable :</th>
    <td>{$patient->tel2}</td>
  </tr>

  <tr>
    <th>Adresse :</th>
    <td>{$patient->adresse} &mdash; {$patient->cp} {$patient->ville}</td>
  </tr>

  <tr>
    <th>Numero d'assur� social :</th>
    <td>{$patient->matricule}</td>
  </tr>

  <tr>
    <th>Remarques :</th>
    <td>{$patient->rques|nl2br:php}</td>
  </tr>

  {if $patient->_ref_medecin_traitant->medecin_id}
  <tr>
    <th>Medecin traitant :</th>
    <td>{$patient->_ref_medecin_traitant->_view}</td>
  </tr>

  <tr>
    <th />
    <td>
      {$patient->_ref_medecin_traitant->adresse|nl2br}<br />
      {$patient->_ref_medecin_traitant->cp} {$patient->_ref_medecin_traitant->ville}
    </td>
  </tr>
  {/if}

  {if $patient->_ref_medecin1->medecin_id}
  <tr>
    <th>Medecin correspondant 1: </th>
    <td>{$patient->_ref_medecin1->_view}</td>
  </tr>
  
  <tr>
    <th />
    <td>
      {$patient->_ref_medecin1->adresse|nl2br}<br />
      {$patient->_ref_medecin1->cp} {$patient->_ref_medecin1->ville}
    </td>
  </tr>
  {/if}

  {if $patient->_ref_medecin1->medecin_id}
  <tr>
    <th>Medecin correspondant 2: </th>
    <td>{$patient->_ref_medecin2->_view}</td>
  </tr>
  
  <tr>
    <th />
    <td>
      {$patient->_ref_medecin2->adresse|nl2br}<br />
      {$patient->_ref_medecin2->cp} {$patient->_ref_medecin2->ville}
    </td>
  </tr>
  {/if}

  {if $patient->_ref_medecin3->medecin_id}
  <tr>
    <th>Medecin correspondant 3: </th>
    <td>{$patient->_ref_medecin3->_view}</td>
  </tr>
  
  <tr>
    <th />
    <td>
      {$patient->_ref_medecin3->adresse|nl2br}<br />
      {$patient->_ref_medecin3->cp} {$patient->_ref_medecin3->ville}
    </td>
  </tr>
  {/if}

  
  <tr>
    <th class="category" colspan="2">Informations sur l'admission</th>
  </tr>
  
  <tr>
    <th>Date d'admission :</th>
    <td>{$admission->date_adm|date_format:"%d/%m/%Y"} � {$admission->_hour_adm}h{$admission->_min_adm}</td>
  </tr>

  <tr>
    <th>Dur�e d'hospitalisation :</th>
    <td>{$admission->duree_hospi} jour(s)</td>
  </tr>

  <tr>
    <th>Bilan pr�-op�ratoire :</th>
    <td class="text">{$admission->examen}</td>
  </tr>

  <tr>
    <th>Admission en :</th>
    <td>{$admission->type_adm}</td>
  </tr>

  <tr>
    <th>Chambre particuli�re :</th>
    <td>{$admission->chambre}</td>
  </tr>

  <tr>
    <th>Remarques :</th>
    <td>{$admission->rques|nl2br}</td>
  </tr>
  
  {if $admission->plageop_id}
  <tr>
    <th class="category" colspan="2">Informations sur l'intervention</th>
  </tr>

  <tr>
    <th>Date d'intervention :</th>
    <td>{$admission->_ref_plageop->_day}/{$admission->_ref_plageop->_month}/{$admission->_ref_plageop->_year}</td>
  </tr>

  {foreach from=$admission->_ext_codes_ccam item=curr_code}
  <tr>
    <th>Acte m�dical :</th>
    <td class="text">{$curr_code->libelleLong} <em>({$curr_code->code})</em></td>
  </tr>
  {/foreach}

  <tr>
    <th>C�t� :</th>
    <td>{$admission->cote}</td>
  </tr>

  <!-- Pas d'affichage des d�passements pour l'instant 
  {if $admission->depassement}
  <tr>
    <th>D�passement d'honoraires :</th>
    <td>{$admission->depassement} �</td>
  </tr> 
  {/if}
  -->
  
  {else}
  <tr>
    <th class="category" colspan="2">Pas d'intervention</th>
  </tr>
  {/if}

</table>