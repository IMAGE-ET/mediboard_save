{{assign var="sejour" value=$operation->_ref_sejour}}
<table class="form" id="admission">
  <tr>
    <th class="title" colspan="2">
      <span style="float:right;font-size:12px;">
        {{$sejour->_ref_group->text}}
      </span>
      <a href="#" onclick="window.print()">Fiche d'admission</a>
    </th>
  </tr>
  <tr>
    <td class="info" colspan="2">
    (Pri�re de vous munir pour la consultation d'anesth�sie de la photocopie
     de vos cartes de s�curit� sociale, de mutuelle et du r�sultat de votre
     bilan sanguin et la liste des m�dicaments que vous prennez)<br />
     {{if $sejour->_ref_group->tel}}
       Pour tout renseignement, t�l�phonez au 
       {{$sejour->_ref_group->_tel1}}
       {{$sejour->_ref_group->_tel2}}
       {{$sejour->_ref_group->_tel3}}
       {{$sejour->_ref_group->_tel4}}
       {{$sejour->_ref_group->_tel5}}
     {{/if}}
    </td>
  </tr>
  
  <tr>
    <th>Date </th>
    <td>{{$today|date_format:"%A %d/%m/%Y"}}</td>
  </tr>
  
  <tr>
    <th>Chirurgien </th>
    <td>Dr. {{$operation->_ref_chir->_view}}</td>
  </tr>
  
  <tr>
    <th class="category" colspan="2">Renseignements concernant le patient</th>
  </tr>
  
  {{assign var="patient" value=$operation->_ref_sejour->_ref_patient}}
  <tr>
    <th>Nom / Pr�nom </th>
    <td>{{$patient->_view}}</td>
  </tr>
  
  <tr>
    <th>Date de naissance / Sexe </th>
    <td>
      n�(e) le {{$patient->_naissance}}
      de sexe 
      {{if $patient->sexe == "m"}}masculin{{else}}f�minin{{/if}}
    </td>
  </tr>
  
  <tr>
    <th>Incapable majeur </th>
    <td>{{tr}}CPatient.incapable_majeur.{{$patient->incapable_majeur}}{{/tr}}</td>
  </tr>

  <tr>
    <th>T�l�phone </th>
    <td>{{$patient->_tel1}} {{$patient->_tel2}} {{$patient->_tel3}} {{$patient->_tel4}} {{$patient->_tel5}}</td>
  </tr>

  <tr>
    <th>Medecin traitant </th>
    <td>{{$patient->_ref_medecin_traitant->_view}}</td>
  </tr>
  
  <tr>
    <th>Adresse </th>
    <td>
      {{$patient->adresse}} &mdash;
      {{$patient->cp}} {{$patient->ville}}
    </td>
  </tr>
  
  <tr>
    <th class="category" colspan="2">Renseignements relatifs � l'hospitalisation</th>
  </tr>
  
  <tr>
    <th>Admission </th>
    <td>      
      le {{$sejour->entree_prevue|date_format:"%A %d/%m/%Y � %Hh%M"}} 
    </td>
  </tr>
  
  <tr>
    <th>Hospitalisation </th>
    <td>
      {{tr}}CSejour.type.{{$sejour->type}}{{/tr}}
    </td>
  </tr>
  
  <tr>
    <th>Chambre particuli�re </th>
    <td>{{tr}}CSejour.chambre_seule.{{$operation->_ref_sejour->chambre_seule}}{{/tr}}</td>
  </tr>
 
  <tr>
    <th>Date d'intervention :</th>
    <td>le {{$operation->_datetime|date_format:"%A %d/%m/%Y"}}</td>
  </tr>

  {{if $operation->libelle}}
  <tr>
    <th>Libell� </th>
    <td class="text"><em>{{$operation->libelle}}</em></td>
  </tr>
  {{/if}}

  <tr>
    <th>Actes m�dicaux </th>
    <td class="text">
      {{foreach from=$operation->_ext_codes_ccam item=ext_code_ccam}}
      {{if $ext_code_ccam->code != "-"}}
      {{$ext_code_ccam->libelleLong}}<br />
      {{/if}}
      {{/foreach}}
    </td>
  </tr>
  
  <tr>
    <th>C�t� </th>
    <td>{{tr}}COperation.cote.{{$operation->cote}}{{/tr}}</td>
  </tr>

  <tr>
    <th>Dur�e pr�vue d'hospitalisation </th>
    <td>{{$operation->_ref_sejour->_duree_prevue}} jours</td>
  </tr>
  
  {{if $operation->forfait}}
  <tr>
    <th>Forfait clinique</th>
    <td>{{$operation->forfait}} euros</td>
  </tr>
  {{/if}}
  
  {{if $operation->fournitures}}
  <tr>
    <th>Fournitures</th>
    <td>{{$operation->fournitures}} euros</td>
  </tr>
  {{/if}}
  
  <tr><th class="category" colspan="2">Rendez vous d'anesth�sie</th></tr>
  
  <tr>
    <td class="text" colspan="2">
      Veuillez prendre rendez-vous avec le cabinet d'anesth�sistes <strong>imp�rativement</strong>
      avant votre intervention. Pour cela, t�l�phonez au 05 46 00 77 08
    </td>
  <tr>
  
  <tr><td class="info" colspan="2"><b>Pour votre hospitalisation, pri�re de vous munir de :</b>
  <ul>
    <li>
      Carte Vitale ou, � d�faut, attestation de s�curit� sociale, 
      carte de mutuelle accompagn�e de la prise en charge le cas �ch�ant.
    </li>
    <li>Tous examens en votre possession (analyse, radio, carte de groupe sanguin...).</li>
    <li>Pr�voir linge et n�cessaire de toilette.</li>
    <li>Vos m�dicaments �ventuellement</li>
  </ul>
  </td></tr>
</table>