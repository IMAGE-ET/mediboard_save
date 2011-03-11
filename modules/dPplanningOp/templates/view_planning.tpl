{{if !$sejour->_id}}
  {{assign var="sejour" value=$operation->_ref_sejour}}
{{/if}}
<table class="print">
  <tr>
    <th class="title" colspan="2">
      <span style="float:left;font-size:12px;">
        [{{$sejour->_num_dossier}}]
      </span>
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
       {{mb_value object=$sejour->_ref_group field=tel}}
     {{/if}}
    </td>
  </tr>
  
  <tr>
    <th>Date </th>
    <td>{{$today|date_format:"%A %d/%m/%Y"}}</td>
  </tr>
  
  <tr>
    <th>Praticien </th>
    <td>
    {{if $operation->_id}}
	    {{if $operation->_ref_chir}}
	      Dr {{$operation->_ref_chir->_view}}
	    {{/if}}
    {{else}}
      {{if $sejour->_ref_praticien}}
        Dr {{$sejour->_ref_praticien->_view}}
      {{/if}}
    {{/if}}
    </td>
  </tr>
  
  <tr>
    <th class="category" colspan="2">Renseignements concernant le patient</th>
  </tr>
  
  {{assign var="patient" value=$sejour->_ref_patient}}
  
  <tr>
    <th>Nom / Pr�nom </th>
    <td>{{$patient->_view}}</td>
  </tr>
  
  <tr>
    <th>Date de naissance / Sexe</th>
    <td>
      n�(e) le {{mb_value object=$patient field="naissance"}}
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
    <td>{{mb_value object=$patient field=tel}}</td>
  </tr>

  <tr>
    <th>Medecin traitant </th>
    <td>
    {{if $patient->_ref_medecin_traitant}}
      {{$patient->_ref_medecin_traitant->_view}}
    {{/if}}
    </td>
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
  
  {{if $sejour->_num_dossier}}
  <tr>
    <th>{{tr}}CSejour-_num_dossier{{/tr}}</th>
    <td>
      [{{$sejour->_num_dossier}}]
    </td>
  </tr>
  {{/if}}
  
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
    <td>{{tr}}CSejour.chambre_seule.{{$sejour->chambre_seule}}{{/tr}}</td>
  </tr>
  
  {{if $operation->_id}}
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
    <th>Actes</th>
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
  {{/if}}
	
  <tr>
    <th>Dur�e pr�vue d'hospitalisation </th>
    <td>{{$sejour->_duree_prevue}} nuits</td>
  </tr>
 
  <tr>
    <th>Adresse</th>
    <td>
      {{$sejour->_ref_group->text}}<br />
      {{$sejour->_ref_group->adresse}}<br />
      {{$sejour->_ref_group->cp}}
      {{$sejour->_ref_group->ville}}
		</td>
	</tr>
	
  {{if $operation->_id}}
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

  <tr>
    <th class="category" colspan="2">Rendez vous d'anesth�sie</th>
  </tr>
	  
  <tr>
    <td class="text" colspan="2">
      Veuillez prendre rendez-vous avec le cabinet d'anesth�sistes <strong>imp�rativement</strong>
      avant votre intervention.
     {{if $sejour->_ref_group->tel_anesth}}
       Pour cela, t�l�phonez au {{mb_value object=$sejour->_ref_group field=tel_anesth}}
     {{/if}}
    </td>
  <tr>
  {{/if}}
  
  <tr>
    <td class="info" colspan="2">
      <b>Pour votre hospitalisation, pri�re de vous munir de :</b>
      <ul>
        <li>
          Carte Vitale et attestation de s�curit� sociale, 
          carte de mutuelle accompagn�e de la prise en charge le cas �ch�ant.
        </li>
        <li>Tous examens en votre possession (analyse, radio, carte de groupe sanguin...).</li>
        <li>Pr�voir linge et n�cessaire de toilette.</li>
        <li>Vos m�dicaments �ventuellement</li>
      </ul>
    </td>
  </tr>
</table>