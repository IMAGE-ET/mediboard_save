<table class="form">
  <tr>
    <th><label for="regime_sante" title="Regime d'assurance sant�">R�gime d'assurance sant�</label></th>
    <td>
      {{mb_field object=$patient field="regime_sante" tabindex="201" size="40" maxlength="40"}}
    </td>
    <th>
      <label for="medecin_traitant" title="Choisir un m�decin traitant">Medecin traitant</label>
      {{mb_field object=$patient field="medecin_traitant" type="hidden"}}
    </th>
    <td class="readonly">
      <input type="text" name="_medecin_traitant_name" size="30" value="Dr. {{$patient->_ref_medecin_traitant->_view}}" ondblclick="popMed('_traitant')" readonly="readonly" />
      <button class="cancel notext" type="button" onclick="delMed('_traitant')"></button>
    </td>
    <td class="button"><button class="search" tabindex="251" type="button" onclick="popMed('_traitant')">Choisir un m�decin</button></td>
  </tr>
  
  <tr>
    <th><label for="cmu" title="Couverture Mutuelle Universelle">CMU</label></th>
    <td class="date">
      <div id="editFrm_cmu_da">{{$patient->cmu|date_format:"%d/%m/%Y"}}</div>
      {{mb_field object=$patient field="cmu" type="hidden" spec="date"}}
      <img tabindex="202" id="editFrm_cmu_trigger" src="./images/icons/calendar.gif" alt="calendar"/>
      <button class="cancel notext" type="button" onclick="delCmu()"></button>
    </td>
    <th>
      <label for="medecin1" title="Choisir un m�decin correspondant">M�decin correspondant 1</label>
      {{mb_field object=$patient field="medecin1" type="hidden"}}
    </th>
    <td class="readonly">
      <input type="text" name="_medecin1_name" size="30" value="Dr. {{$patient->_ref_medecin1->_view}}" ondblclick="popMed('1')" readonly="readonly" />
      <button class="cancel notext" type="button" onclick="delMed('1')"></button>
    </td>
    <td class="button"><button class="search" tabindex="252" type="button" onclick="popMed('1')">Choisir un m�decin</button></td>
  </tr>
  
  <tr>
    <th rowspan="2"><label for="ald" title="Affection longue Dur�e">ALD</label></th>
    <td rowspan="2">
      {{mb_field object=$patient field="ald" tabindex="203"}}
    </td>
    <th>
      {{mb_field object=$patient field="medecin2" type="hidden"}}
      <label for="medecin2" title="Choisir un second m�decin correspondant">M�decin correspondant 2</label>
    </th>
    <td class="readonly">
      <input type="text" name="_medecin2_name" size="30" value="{{if ($patient->_ref_medecin2)}}Dr. {{$patient->_ref_medecin2->_view}}{{/if}}" ondblclick="popMed('2')" readonly="readonly" />
      <button class="cancel notext" type="button" onclick="delMed('2')"></button>
    </td>
    <td class="button"><button class="search" tabindex="253" type="button" onclick="popMed('2')">Choisir un m�decin</button></td>
  </tr>
  
  <tr>
    <th>
      {{mb_field object=$patient field="medecin3" type="hidden"}}
      <label for="medecin3" title="Choisir un troisi�me m�decin correspondant">M�decin correspondant 3</label>
    </th>
    <td class="readonly">
      <input type="text" name="_medecin3_name" size="30" value="{{if ($patient->_ref_medecin3)}}Dr. {{$patient->_ref_medecin3->_view}}{{/if}}" ondblclick="popMed('3')" readonly="readonly" />
      <button class="cancel notext" type="button" onclick="delMed('3')"></button>
    </td>
    <td class="button"><button class="search" tabindex="254" type="button" onclick="popMed('3')">Choisir un m�decin</button></td>
  </tr>
  
  <tr>
    <th><label for="incapable_majeur" title="Patient reconnu incapable majeur">Incapable majeur</label></th>
    <td>
      {{mb_field object=$patient field="incapable_majeur" tabindex="204"}}
    </td>
    <th><label for="matricule" title="Matricule valide d'assur� social (13 chiffres + 2 pour la cl�)">Num�ro d'assur� social</label></th>
    <td colspan="2">
      {{mb_field object=$patient field="matricule" tabindex="255" size="17" maxlength="15"}}
    </td>
  </tr>
  
  <tr>
    <th><label for="ATNC" title="Patient pr�sentant un risque ATNC">ATNC </label></th>
    <td>
      {{mb_field object=$patient field="ATNC" tabindex="206"}}
    </td>
    <th><label for="SHS" title="Code Administratif SHS">Code administratif</label></th>
    <td colspan="2">
      {{mb_field object=$patient field="SHS" tabindex="256" size="8" maxlength="8" onblur="oAccord.changeTabAndFocus(2, this.form.prevenir_nom);"}}
    </td>
  </tr>

</table>