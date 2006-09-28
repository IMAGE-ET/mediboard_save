<script type="text/javascript">

function setField(oField, sValue) {
  oField.value = sValue;
}

function setChecked(oField, sValue) {
  for (i=0; i < oField.length; i++){
    if (oField[i].value == sValue)
      oField[i].checked = true;
  }
}

</script>

<h2 class="module {{$m}}">Fusion de patients</h2>


<form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="dosql" value="do_patients_fusion" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="patient1_id" value="{{$patient1->patient_id}}" />
<input type="hidden" name="patient2_id" value="{{$patient2->patient_id}}" />

<table class="form">
  <tr>
    <th class="category">Champ</th>
    <th width="30%" class="category">1er patient</th>
    <th width="30%" class="category">2�me patient</th>
    <th width="30%" class="category">R�sultat</th>
  </tr>
  <tr>
    <th><label for="nom" title="Nom du patient. Obligatoire">Nom</label></th>
    <td>
      <input type="radio" name="_choix_nom" value="{{$patient1->nom}}" checked="checked" onclick="setField(this.form.nom, '{{$patient1->nom|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->nom}}
    </td>
    <td>
      <input type="radio" name="_choix_nom" value="{{$patient2->nom}}" onclick="setField(this.form.nom, '{{$patient2->nom|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->nom}}
    </td>
    <td>
      <input tabindex="1" type="text" name="nom" value="{{$finalPatient->nom}}" title="{{$finalPatient->_props.nom}}" />
     </td>
  </tr>
  <tr>
    <th><label for="prenom" title="Pr�nom du patient. Obligatoire">Pr�nom</label></th>
    <td>
      <input type="radio" name="_choix_prenom" value="{{$patient1->prenom}}" checked="checked" onclick="setField(this.form.prenom, '{{$patient1->prenom|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->prenom}}
    </td>
    <td>
      <input type="radio" name="_choix_prenom" value="{{$patient2->prenom}}" onclick="setField(this.form.prenom, '{{$patient2->prenom|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->prenom}}
    </td>
    <td>
      <input tabindex="2" type="text" name="prenom" value="{{$finalPatient->prenom}}" title="{{$finalPatient->_props.prenom}}" /></td>
  </tr>
  <tr>
    <th><label for="nom_jeune_fille" title="Nom de jeune fille d'une femme mari�e">Nom de jeune fille</label></th>
    <td>
      <input type="radio" name="_choix_nom_jeune_fille" value="{{$patient1->nom_jeune_fille}}" checked="checked" onclick="setField(this.form.nom_jeune_fille, '{{$patient1->nom_jeune_fille|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->nom_jeune_fille}}
    </td>
    <td>
      <input type="radio" name="_choix_nom_jeune_fille" value="{{$patient2->nom_jeune_fille}}" onclick="setField(this.form.nom_jeune_fille, '{{$patient2->nom_jeune_fille|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->nom_jeune_fille}}
    </td>
    <td>
      <input tabindex="3" type="text" name="nom_jeune_fille" title="{{$finalPatient->_props.nom_jeune_fille}}" value="{{$finalPatient->nom_jeune_fille}}" />
    </td>
  </tr>
  <tr>
    <th><label for="_jour" title="Date de naissance du patient, au format JJ-MM-AAAA">Date de naissance</label></th>
    <td>
      <input type="radio" name="_choix_naissance" value="{{$patient1->naissance}}" checked="checked"
      onclick="setField(this.form._jour, '{{$patient1->_jour}}'); setField(this.form._mois, '{{$patient1->_mois}}'); setField(this.form._annee, '{{$patient1->_annee}}');" />
      {{$patient1->_naissance}}
    </td>
    <td>
      <input type="radio" name="_choix_naissance" value="{{$patient2->naissance}}"
      onclick="setField(this.form._jour, '{{$patient2->_jour}}'); setField(this.form._mois, '{{$patient2->_mois}}'); setField(this.form._annee, '{{$patient2->_annee}}');" />
      {{$patient2->_naissance}}
    </td>
    <td>
      <input tabindex="4" type="text" name="_jour"  size="2" maxlength="2" value="{{if $finalPatient->_jour != "00"}}{{$finalPatient->_jour}}{{/if}}" onkeyup="followUp(this, '_mois', 2)" /> -
      <input tabindex="5" type="text" name="_mois"  size="2" maxlength="2" value="{{if $finalPatient->_mois != "00"}}{{$finalPatient->_mois}}{{/if}}" onkeyup="followUp(this, '_annee', 2)" /> -
      <input tabindex="6" type="text" name="_annee" size="4" maxlength="4" value="{{if $finalPatient->_annee != "0000"}}{{$finalPatient->_annee}}{{/if}}" />
    </td>
  </tr>
  <tr>
    <th><label for="sexe" title="Sexe du patient">Sexe</label></th>
    <td>
      <input type="radio" name="_choix_sexe" value="{{$patient1->sexe}}" checked="checked" onclick="setField(this.form.sexe, '{{$patient1->sexe}}')" />
      {{if $patient1->sexe == "m"}}
        masculin
      {{elseif $patient1->sexe == "f"}}
        f�minin
      {{elseif $patient1->sexe == "j"}}
        femme c�libataire
      {{/if}}
    </td>
    <td>
      <input type="radio" name="_choix_sexe" value="{{$patient2->sexe}}" onclick="setField(this.form.sexe, '{{$patient2->sexe}}')" />
      {{if $patient2->sexe == "m"}}
        masculin
      {{elseif $patient2->sexe == "f"}}
        f�minin
      {{elseif $patient2->sexe == "j"}}
        femme c�libataire
      {{/if}}
    </td>
    <td>
      <select tabindex="7" name="sexe" title="{{$finalPatient->_props.sexe}}">
        <option value="m" {{if $finalPatient->sexe == "m"}} selected="selected" {{/if}}>masculin</option>
        <option value="f" {{if $finalPatient->sexe == "f"}} selected="selected" {{/if}}>f�minin</option>
        <option value="j" {{if $finalPatient->sexe == "j"}} selected="selected" {{/if}}>femme c�libataire</option>
      </select>
    </td>
  </tr>
  <tr>
    <th><label for="adresse" title="Adresse du patient">Adresse</label></th>
    <td>
      <input type="radio" name="_choix_adresse" value="{{$patient1->adresse}}" checked="checked" onclick="setField(this.form.adresse, '{{$patient1->adresse|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->adresse}}
    </td>
    <td>
      <input type="radio" name="_choix_adresse" value="{{$patient2->adresse}}" onclick="setField(this.form.adresse, '{{$patient2->adresse|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->adresse}}
    </td>
    <td>
      <textarea tabindex="8" name="adresse" title="{{$finalPatient->_props.adresse}}" rows="1">{{$finalPatient->adresse}}</textarea>
    </td>
  </tr>
  <tr>
    <th><label for="cp" title="Code postal">Code Postal</label></th>
    <td>
      <input type="radio" name="_choix_cp" value="{{$patient1->cp}}" checked="checked" onclick="setField(this.form.cp, '{{$patient1->cp|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->cp}}
    </td>
    <td>
      <input type="radio" name="_choix_cp" value="{{$patient2->cp}}" onclick="setField(this.form.cp, '{{$patient2->cp|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->cp}}
    </td>
    <td>
      <input tabindex="9" type="text" name="cp" value="{{$finalPatient->cp}}" title="{{$finalPatient->_props.cp}}" onclick="setField(this.form.cp, '{{$patient2->cp|smarty:nodefaults|JSAttribute}}')" />
    </td>
  </tr>
  <tr>
    <th><label for="ville" title="Ville du patient">Ville</label></th>
    <td>
      <input type="radio" name="_choix_ville" value="{{$patient1->ville}}" checked="checked" onclick="setField(this.form.ville, '{{$patient1->ville|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->ville}}
    </td>
    <td>
      <input type="radio" name="_choix_ville" value="{{$patient2->ville}}" onclick="setField(this.form.ville, '{{$patient2->ville|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->ville}}
    </td>
    <td>
      <input tabindex="10" type="text" name="ville" value="{{$finalPatient->ville}}" title="{{$finalPatient->_props.ville}}" />
    </td>
  </tr>
  <tr>
    <th><label for="_tel1" title="Num�ro de t�l�phone filaire">T�l�phone</label></th>
    <td>
      <input type="radio" name="_choix_tel" value="{{$patient1->tel}}" checked="checked"
      onclick="setField(this.form._tel1, '{{$patient1->_tel1}}'); setField(this.form._tel2, '{{$patient1->_tel2}}');
      setField(this.form._tel3, '{{$patient1->_tel3}}'); setField(this.form._tel4, '{{$patient1->_tel4}}'); setField(this.form._tel5, '{{$patient1->_tel5}}');" />
      {{$patient1->tel}}
    </td>
    <td>
      <input type="radio" name="_choix_tel" value="{{$patient2->tel}}"
      onclick="setField(this.form._tel1, '{{$patient2->_tel1}}'); setField(this.form._tel2, '{{$patient2->_tel2}}');
      setField(this.form._tel3, '{{$patient2->_tel3}}'); setField(this.form._tel4, '{{$patient2->_tel4}}'); setField(this.form._tel5, '{{$patient2->_tel5}}');" />
      {{$patient2->tel}}
    </td>
    <td>
      <input tabindex="11" type="text" name="_tel1" size="2" maxlength="2" value="{{$finalPatient->_tel1}}" title="num|length|2" onkeyup="followUp(this, '_tel2', 2)" /> - 
      <input tabindex="12" type="text" name="_tel2" size="2" maxlength="2" value="{{$finalPatient->_tel2}}" title="num|length|2" onkeyup="followUp(this, '_tel3', 2)" /> -
      <input tabindex="13" type="text" name="_tel3" size="2" maxlength="2" value="{{$finalPatient->_tel3}}" title="num|length|2" onkeyup="followUp(this, '_tel4', 2)" /> -
      <input tabindex="14" type="text" name="_tel4" size="2" maxlength="2" value="{{$finalPatient->_tel4}}" title="num|length|2" onkeyup="followUp(this, '_tel5', 2)" /> -
      <input tabindex="15" type="text" name="_tel5" size="2" maxlength="2" value="{{$finalPatient->_tel5}}" title="num|length|2" onkeyup="followUp(this, '_tel21', 2)" />
    </td>
  </tr>
  <tr>
    <th><label for="_tel21" title="Num�ro de t�l�phone portable">Portable</label></th>
    <td>
      <input type="radio" name="_choix_tel2" value="{{$patient1->tel2}}" checked="checked"
      onclick="setField(this.form._tel21, '{{$patient1->_tel21}}'); setField(this.form._tel22, '{{$patient1->_tel22}}');
      setField(this.form._tel23, '{{$patient1->_tel23}}'); setField(this.form._tel24, '{{$patient1->_tel24}}'); setField(this.form._tel25, '{{$patient1->_tel25}}');" />
      {{$patient1->tel2}}
    </td>
    <td>
      <input type="radio" name="_choix_tel2" value="{{$patient2->tel2}}"
      onclick="setField(this.form._tel21, '{{$patient2->_tel21}}'); setField(this.form._tel22, '{{$patient2->_tel22}}');
      setField(this.form._tel23, '{{$patient2->_tel23}}'); setField(this.form._tel24, '{{$patient2->_tel24}}'); setField(this.form._tel25, '{{$patient2->_tel25}}');" />
      {{$patient2->tel2}}
    </td>
    <td>
      <input tabindex="16" type="text" name="_tel21" size="2" maxlength="2" value="{{$finalPatient->_tel21}}" title="num|length|2" onkeyup="followUp(this, '_tel22', 2)" /> - 
      <input tabindex="17" type="text" name="_tel22" size="2" maxlength="2" value="{{$finalPatient->_tel22}}" title="num|length|2" onkeyup="followUp(this, '_tel23', 2)" /> -
      <input tabindex="18" type="text" name="_tel23" size="2" maxlength="2" value="{{$finalPatient->_tel23}}" title="num|length|2" onkeyup="followUp(this, '_tel24', 2)" /> -
      <input tabindex="19" type="text" name="_tel24" size="2" maxlength="2" value="{{$finalPatient->_tel24}}" title="num|length|2" onkeyup="followUp(this, '_tel25', 2)" /> -
      <input tabindex="20" type="text" name="_tel25" size="2" maxlength="2" value="{{$finalPatient->_tel25}}" title="num|length|2" />
    </td>
  </tr>
  <tr>
    <th><label for="rques" title="Remarques g�n�rales concernant le patient">Remarques</label></th>
    <td class="text">
      <input type="radio" name="_choix_rques" value="{{$patient1->rques}}" checked="checked" onclick="setField(this.form.rques, '{{$patient1->rques|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->rques|nl2br}}
    </td>
    <td class="text">
      <input type="radio" name="_choix_rques" value="{{$patient2->rques}}" onclick="setField(this.form.rques, '{{$patient2->rques|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->rques|nl2br}}
    </td>
    <td class="text">
      <textarea tabindex="21" rows="3" title="{{$finalPatient->_props.rques}}" name="rques">{{$finalPatient->rques}}</textarea>
    </td>
  </tr>
  <tr>
    <th>
      <label for="medecin_traitant" title="Choisir un m�decin traitant">Medecin traitant</label>
    </th>
    <td>
      <input type="radio" name="_choix_medecin_traitant" value="{{$patient1->medecin_traitant}}" checked="checked"
      onclick="setField(this.form.medecin_traitant, '{{$patient1->medecin_traitant}}'); setField(this.form._medecin_traitant_view, '{{$patient1->_ref_medecin_traitant->_view}}')" />
      {{$patient1->_ref_medecin_traitant->_view}}
    </td>
    <td>
      <input type="radio" name="_choix_medecin_traitant" value="{{$patient2->medecin_traitant}}"
      onclick="setField(this.form.medecin_traitant, '{{$patient2->medecin_traitant}}'); setField(this.form._medecin_traitant_view, '{{$patient2->_ref_medecin_traitant->_view}}')" />
      {{$patient2->_ref_medecin_traitant->_view}}
    </td>
    <td>
      <input type="hidden" name="medecin_traitant" value="{{$finalPatient->medecin_traitant}}" />
      <input type="text" readonly="readonly" name="_medecin_traitant_view" value="{{$finalPatient->_ref_medecin_traitant->_view}}" />
    </td>
  </tr>
  <tr>
    <th>
      <label for="medecin1" title="Choisir un m�decin correspondant">Correspondant 1</label>
    </th>
    <td>
      <input type="radio" name="_choix_medecin1" value="{{$patient1->medecin1}}" checked="checked"
      onclick="setField(this.form.medecin1, '{{$patient1->medecin1}}'); setField(this.form._medecin1_view, '{{$patient1->_ref_medecin1->_view}}')" />
      {{$patient1->_ref_medecin1->_view}}
    </td>
    <td>
      <input type="radio" name="_choix_medecin1" value="{{$patient2->medecin1}}"
      onclick="setField(this.form.medecin1, '{{$patient2->medecin1}}'); setField(this.form._medecin1_view, '{{$patient2->_ref_medecin1->_view}}')" />
      {{$patient2->_ref_medecin1->_view}}
    </td>
    <td>
      <input type="hidden" name="medecin1" value="{{$finalPatient->medecin1}}" />
      <input type="text" readonly="readonly" name="_medecin1_view" value="{{$finalPatient->_ref_medecin1->_view}}" />
    </td>
  </tr>
  <tr>
    <th>
      <label for="medecin2" title="Choisir un m�decin correspondant">Correspondant 2</label>
    </th>
    <td>
      <input type="radio" name="_choix_medecin2" value="{{$patient1->medecin2}}" checked="checked"
      onclick="setField(this.form.medecin2, '{{$patient1->medecin2}}'); setField(this.form._medecin2_view, '{{$patient1->_ref_medecin2->_view}}')" />
      {{$patient1->_ref_medecin2->_view}}
    </td>
    <td>
      <input type="radio" name="_choix_medecin2" value="{{$patient2->medecin2}}"
      onclick="setField(this.form.medecin2, '{{$patient2->medecin2}}'); setField(this.form._medecin2_view, '{{$patient2->_ref_medecin2->_view}}')" />
      {{$patient2->_ref_medecin2->_view}}
    </td>
    <td>
      <input type="hidden" name="medecin2" value="{{$finalPatient->medecin2}}" />
      <input type="text" readonly="readonly" name="_medecin2_view" value="{{$finalPatient->_ref_medecin2->_view}}" />
    </td>
  </tr>
  <tr>
    <th>
      <label for="medecin3" title="Choisir un m�decin correspondant">Correspondant 3</label>
    </th>
    <td>
      <input type="radio" name="_choix_medecin3" value="{{$patient1->medecin3}}" checked="checked"
      onclick="setField(this.form.medecin3, '{{$patient1->medecin3}}'); setField(this.form._medecin3_view, '{{$patient1->_ref_medecin3->_view}}')" />
      {{$patient1->_ref_medecin3->_view}}
    </td>
    <td>
      <input type="radio" name="_choix_medecin3" value="{{$patient2->medecin3}}"
      onclick="setField(this.form.medecin3, '{{$patient2->medecin3}}'); setField(this.form._medecin3_view, '{{$patient2->_ref_medecin3->_view}}')" />
      {{$patient2->_ref_medecin3->_view}}
    </td>
    <td>
      <input type="hidden" name="medecin3" value="{{$finalPatient->medecin3}}" />
      <input type="text" readonly="readonly" name="_medecin3_view" value="{{$finalPatient->_ref_medecin3->_view}}" />
    </td>
  </tr>
  <tr>
    <th><label for="incapable_majeur" title="Patient reconnu incapable majeur">Incapable majeur</label></th>
    <td>
      <input type="radio" name="_choix_incapable majeur" value="{{$patient1->incapable_majeur}}" checked="checked" onclick="setChecked(this.form.incapable_majeur, '{{$patient1->incapable_majeur|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->incapable_majeur}}
    </td>
    <td>
      <input type="radio" name="_choix_incapable majeur" value="{{$patient2->incapable_majeur}}" onclick="setChecked(this.form.incapable_majeur, '{{$patient2->incapable_majeur|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->incapable_majeur}}
    </td>
    <td>
      <input tabindex="22" type="radio" name="incapable_majeur" value="o" {{if $finalPatient->incapable_majeur == "o"}} checked="checked" {{/if}} />oui
      <input tabindex="23" type="radio" name="incapable_majeur" value="n" {{if $finalPatient->incapable_majeur == "n"}} checked="checked" {{/if}} />non
    </td>
  </tr>
  <tr>
    <th><label for="ATNC" title="Patient pr�sentant un risque ATNC">ATNC</label></th>
    <td>
      <input type="radio" name="_choix_ATNC" value="{{$patient1->ATNC}}" checked="checked" onclick="setChecked(this.form.ATNC, '{{$patient1->ATNC|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->ATNC}}
    </td>
    <td>
      <input type="radio" name="_choix_ATNC" value="{{$patient2->ATNC}}" onclick="setChecked(this.form.ATNC, '{{$patient2->ATNC|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->ATNC}}
    </td>
    <td>
      <input tabindex="24" type="radio" name="ATNC" value="o" {{if $finalPatient->ATNC == "o"}} checked="checked" {{/if}} />oui
      <input tabindex="25" type="radio" name="ATNC" value="n" {{if $finalPatient->ATNC == "n"}} checked="checked" {{/if}} />non
    </td>
  </tr>
  <tr>
    <th><label for="matricule" title="Matricule valide d'assur� social (13 chiffres + 2 pour la cl�)">Num�ro d'assur� social</label></th>
    <td>
      <input type="radio" name="_choix_matricule" value="{{$patient1->matricule}}" checked="checked" onclick="setField(this.form.matricule, '{{$patient1->matricule|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->matricule}}
    </td>
    <td>
      <input type="radio" name="_choix_matricule" value="{{$patient2->matricule}}" onclick="setField(this.form.matricule, '{{$patient2->matricule|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->matricule}}
    </td>
    <td>
      <input tabindex="26" type="text" size="15" maxlength="15" name="matricule" title="{{$finalPatient->_props.matricule}}" value="{{$finalPatient->matricule}}" />
    </td>
  </tr>
  <tr>
    <th><label for="SHS" title="Code Administratif SHS">Code administratif</label></th>
    <td>
      <input type="radio" name="_choix_SHS" value="{{$patient1->SHS}}" checked="checked" onclick="setField(this.form.SHS, '{{$patient1->SHS|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->SHS}}
    </td>
    <td>
      <input type="radio" name="_choix_SHS" value="{{$patient2->SHS}}" onclick="setField(this.form.SHS, '{{$patient2->SHS|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->SHS}}
    </td>
    <td><input tabindex="27" type="text" size="10" maxlength="10" name="SHS" title="{{$finalPatient->_props.SHS}}" value="{{$finalPatient->SHS}}" /></td>
  </tr>
  <tr>
    <th>
      <label for="cmu" title="Choisir la date de fin de cmu">cmu</label>
    </th>
    <td>
      <input type="radio" name="_choix_cmu" value="{{$patient1->cmu}}" checked="checked"
      onclick="setField(this.form.cmu, '{{$patient1->cmu}}'); setField(this.form._cmu_view, '{{$patient1->cmu|date_format:"%d/%m/%Y"}}')" />
      {{$patient1->cmu|date_format:"%d/%m/%Y"}}
    </td>
    <td>
      <input type="radio" name="_choix_cmu" value="{{$patient2->cmu}}"
      onclick="setField(this.form.cmu, '{{$patient2->cmu}}'); setField(this.form._cmu_view, '{{$patient2->cmu|date_format:"%d/%m/%Y"}}')" />
      {{$patient2->cmu|date_format:"%d/%m/%Y"}}
    </td>
    <td>
      <input type="hidden" name="cmu" value="{{$finalPatient->cmu}}" />
      <input type="text" readonly="readonly" name="_cmu_view" value="{{$finalPatient->cmu|date_format:"%d/%m/%Y"}}" />
    </td>
  </tr>
  <tr>
    <th><label for="ald" title="Information sur une affection longue duree">ald</label></th>
    <td class="text">
      <input type="radio" name="_choix_ald" value="{{$patient1->ald}}" checked="checked" onclick="setField(this.form.ald, '{{$patient1->ald|smarty:nodefaults|JSAttribute}}')" />
      {{$patient1->ald|nl2br}}
    </td>
    <td class="text">
      <input type="radio" name="_choix_ald" value="{{$patient2->ald}}" onclick="setField(this.form.ald, '{{$patient2->ald|smarty:nodefaults|JSAttribute}}')" />
      {{$patient2->ald|nl2br}}
    </td>
    <td class="text">
      <textarea tabindex="21" rows="3" title="{{$finalPatient->_props.ald}}" name="ald">{{$finalPatient->ald}}</textarea>
    </td>
  </tr>
  <tr>
    <td class="button" colspan="4"><button type="submit" class="submit">Fusionner</button></td>
  </tr>
</table>

</form>