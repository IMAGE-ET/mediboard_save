<table style="width: 100%">
  <tr>
    <td style="width: 50%">

    <table class="form">
      <tr>
        <th class="category" colspan="2">
          Identit� Assur� Social
        </th>
      </tr>
      <tr>
        <td colspan="2" style="text-align: center">
          <button type="button" class="cancel" onclick="delAssureValues()">
            Vider les champs de l'assur�
          </button>
          <button type="button" class="tick" onclick="copieAssureValues()">
            Recopier les champs de l'assur�
          </button>
        </td>
      </tr>
      <tr>
        <th>{{mb_label object=$patient field="assure_nom"}}</th>
        <td>{{mb_field object=$patient field="assure_nom"}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$patient field="assure_prenom"}}</th>
        <td>{{mb_field object=$patient field="assure_prenom"}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$patient field="assure_prenom_2"}}</th>
        <td>{{mb_field object=$patient field="assure_prenom_2"}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$patient field="assure_prenom_3"}}</th>
        <td>{{mb_field object=$patient field="assure_prenom_3"}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$patient field="assure_prenom_4"}}</th>
        <td>{{mb_field object=$patient field="assure_prenom_4"}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$patient field="assure_nom_jeune_fille"}}</th>
        <td>{{mb_field object=$patient field="assure_nom_jeune_fille"}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$patient field="assure_sexe"}}</th>
        <td>{{mb_field object=$patient field="assure_sexe" typeEnum=radio onchange="changeCiviliteForSexe(this, true);"}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$patient field="assure_naissance"}}</th>
        <td>{{mb_field object=$patient field="assure_naissance" onchange="changeCiviliteForDate(this, true);"}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$patient field="assure_civilite"}}</th>
        <td>
          {{assign var=civilite_locales value=$patient->_specs.assure_civilite}} 
          <select name="assure_civilite">
            <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
            {{foreach from=$civilite_locales->_locales key=key item=curr_civilite}} 
            <option value="{{$key}}" {{if $key == $patient->assure_civilite}}selected="selected"{{/if}}>
              {{tr}}CPatient.civilite.{{$key}}-long{{/tr}} - ({{$curr_civilite}})
            </option>
            {{/foreach}}
          </select>
        </td>
      </tr>
      <tr>
        <th>{{mb_label object=$patient field="assure_cp_naissance"}}</th>
        <td>{{mb_field object=$patient field="assure_cp_naissance"}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$patient field="assure_lieu_naissance"}}</th>
        <td>{{mb_field object=$patient field="assure_lieu_naissance"}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$patient field="_assure_pays_naissance_insee"}}</th>
        <td> 
          {{mb_field object=$patient field="_assure_pays_naissance_insee" class="autocomplete"}}
          <div style="display:none;" class="autocomplete" id="_assure_pays_naissance_insee_auto_complete"></div>
        </td>
      </tr>
      <tr>
        <th>{{mb_label object=$patient field="assure_profession"}}</th>
        <td>{{mb_field object=$patient field="assure_profession" form=editFrm}}</td>
      </tr>
      <tr>
       {{if $conf.ref_pays == 1}}
          <th>{{mb_label object=$patient field="assure_matricule"}}</th>
          <td>{{mb_field object=$patient field="assure_matricule"}}</td>
      {{/if}}
     </tr>
    </table>
  </td>
  <td>
    <table class="form">
      <tr>
        <th class="category" colspan="2">Coordonn�es Assur� Social</th>
      </tr>
      <tr>
        <th>{{mb_label object=$patient field="assure_adresse"}}</th>
        <td>{{mb_field object=$patient field="assure_adresse"}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$patient field="assure_cp"}}</th>
        <td>{{mb_field object=$patient field="assure_cp"}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$patient field="assure_ville"}}</th>
        <td>{{mb_field object=$patient field="assure_ville"}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$patient field="assure_pays"}}</th>
        <td>
          {{mb_field object=$patient field="assure_pays" size="31" class="autocomplete"}}
          <div style="display:none;" class="autocomplete" id="assure_pays_auto_complete"></div>
        </td>
      </tr>
      <tr>
        <th>{{mb_label object=$patient field="assure_tel"}}</th>
        <td>{{mb_field object=$patient field="assure_tel"}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$patient field="assure_tel2"}}</th>
        <td>{{mb_field object=$patient field="assure_tel2"}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$patient field="assure_rques"}}</th>
        <td>{{mb_field object=$patient field="assure_rques" onblur="tabs.changeTabAndFocus('identite', this.form.nom)"}}</td>
      </tr>
    </table>
    </td>
  </tr>
</table>