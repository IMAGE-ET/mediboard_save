<script type="text/javascript">

var submitPatient = function() {
  oForm = getForm("testDHEExterne");
  url = new Url("dPplanningOp", "dhe_externe");
  url.addParam("praticien_id"                 , $V(oForm.praticien_id));
  url.addParam("patient_nom"                  , $V(oForm.nom));
  url.addParam("patient_prenom"               , $V(oForm.prenom));
  url.addParam("patient_date_naissance"       , $V(oForm.naissance));
  url.addParam("patient_sexe"                 , $V(oForm.sexe));
  url.addParam("patient_adresse"              , $V(oForm.adresse));
  url.addParam("patient_code_postal"          , $V(oForm.cp));
  url.addParam("patient_ville"                , $V(oForm.ville));
  url.addParam("patient_telephone"            , $V(oForm.tel));
  url.addParam("patient_mobile"               , $V(oForm.tel2));
  url.addParam("sejour_libelle"               , $V(oForm.libelle));
  url.addParam("sejour_type"                  , $V(oForm.type));
  url.addParam("sejour_entree_prevue"         , $V(oForm.entree_prevue));
  url.addParam("sejour_sortie_prevue"         , $V(oForm.sortie_prevue));
  url.addParam("sejour_remarques"             , $V(oForm.rques));
  url.addParam("sejour_intervention"          , $V(oForm.sejour_intervention));
  url.addParam("intervention_date"            , $V(oForm._datetime));
  url.addParam("intervention_duree"           , $V(oForm.temp_operation));
  url.addParam("intervention_cote"            , $V(oForm.cote));
  url.addParam("intervention_horaire_souhaite", $V(oForm.horaire_voulu));
  url.addParam("intervention_codes_ccam"      , $V(oForm.codes_ccam));
  url.addParam("intervention_materiel"        , $V(oForm.materiel));
  url.redirect();
  return false;
}

Main.add(function() {
  oForm = getForm("testDHEExterne");
  Calendar.regField(oForm.entree_prevue);
  Calendar.regField(oForm.sortie_prevue);
  Calendar.regField(oForm._datetime);
  Calendar.regField(oForm.temp_operation);
  Calendar.regField(oForm.horaire_voulu);
});

</script>

<form name="testDHEExterne" action="?" method="get" onsubmit="return submitPatient();">
<input type="hidden" name="m" value="dPplanningOp" />
<input type="hidden" name="a" value="dhe_externe" />
<table class="form">
  <tr>
    <th colspan="3" class="title">Test de la Demande d'hospitalisation �lectronique externe</th>
  </tr>
  <tr>
    <th colspan="3" class="category">Patient</th>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field=nom}}</th>
    <td>{{mb_field object=$patient field=nom}}</td>
    <td class="text">Obligatoire</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field=prenom}}</th>
    <td>{{mb_field object=$patient field=prenom}}</td>
    <td class="text">Obligatoire</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field=naissance}}</th>
    <td><input type="text" name="naissance" /></td>
    <td class="text">Obligatoire</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field=sexe}}</th>
    <td>{{mb_field object=$patient field=sexe}}</td>
    <td class="text">Obligatoire</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field=adresse}}</th>
    <td>{{mb_field object=$patient field=adresse}}</td>
    <td></td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field=cp}}</th>
    <td>{{mb_field object=$patient field=cp}}</td>
    <td></td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field=ville}}</th>
    <td>{{mb_field object=$patient field=ville}}</td>
    <td></td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field=tel}}</th>
    <td>{{mb_field object=$patient field=tel}}</td>
    <td></td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field=tel2}}</th>
    <td>{{mb_field object=$patient field=tel2}}</td>
    <td></td>
  </tr>
  <tr>
    <th colspan="3" class="category">
      Sejour
    </th>
  </tr>
    <th>{{mb_label object=$sejour field=praticien_id}}</th>
    <td>
      <select name="praticien_id">
      {{foreach from=$praticiens item=_praticien}}
        <option value="{{$_praticien->_id}}">{{$_praticien->_view}}</option>
      {{/foreach}}
      </select>
    </td>
    <td>Obligatoire</td>
  </tr>
  </tr>
    <th>{{mb_label object=$sejour field=libelle}}</th>
    <td>{{mb_field object=$sejour field=libelle}}</td>
    <td class="text">Obligatoire dans le cas d'un s�jour</td>
  </tr>
  <tr>
    <th>{{mb_label object=$sejour field=type}}</th>
    <td>{{mb_field object=$sejour field=type}}</td>
    <td class="text">Obligatoire dans le cas d'un s�jour</td>
  </tr>
  <tr>
    <th>{{mb_label object=$sejour field=entree_prevue}}</th>
    <td>{{mb_field object=$sejour field=entree_prevue}}</td>
    <td class="text">Obligatoire dans le cas d'un s�jour</td>
  </tr>
  <tr>
    <th>{{mb_label object=$sejour field=sortie_prevue}}</th>
    <td>{{mb_field object=$sejour field=sortie_prevue}}</td>
    <td class="text">Obligatoire dans le cas d'un s�jour</td>
  </tr>
  <tr>
    <th>{{mb_label object=$sejour field=rques}}</th>
    <td>{{mb_field object=$sejour field=rques}}</td>
    <td></td>
  </tr>
  <tr>
    <th colspan="3" class="category">
      Intervention
      <input name="sejour_intervention" type="radio" value="1" /> oui /
      <input name="sejour_intervention" type="radio" value="0" checked="checked"/> non
    </th>
  </tr>
  <tr>
    <th>{{mb_label object=$intervention field=_datetime}}</th>
    <td>{{mb_field object=$intervention field=_datetime}}</td>
    <td class="text">Obligatoire dans le cas d'une intervention - seule la date est prise en compte</td>
  </tr>
  <tr>
    <th>{{mb_label object=$intervention field=temp_operation}}</th>
    <td>{{mb_field object=$intervention field=temp_operation}}</td>
    <td class="text">Obligatoire dans le cas d'une intervention</td>
  </tr>
  <tr>
    <th>{{mb_label object=$intervention field=cote}}</th>
    <td>{{mb_field object=$intervention field=cote}}</td>
    <td class="text">Obligatoire dans le cas d'une intervention</td>
  </tr>
  <tr>
    <th>{{mb_label object=$intervention field=horaire_voulu}}</th>
    <td>{{mb_field object=$intervention field=horaire_voulu}}</td>
    <td></td>
  </tr>
  <tr>
    <th>{{mb_label object=$intervention field=codes_ccam}}</th>
    <td>{{mb_field object=$intervention field=codes_ccam}}</td>
    <td></td>
  </tr>
  <tr>
    <th>{{mb_label object=$intervention field=materiel}}</th>
    <td>{{mb_field object=$intervention field=materiel}}</td>
    <td></td>
  </tr>
  <tr>
    <td colspan="2" class="button">
      <button type="button" class="submit" onclick="this.form.onsubmit();">{{tr}}Submit{{/tr}}</button>
    </td>
  </tr>
</table>

</form>
