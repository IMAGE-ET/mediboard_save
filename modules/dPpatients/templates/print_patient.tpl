<script type="text/javascript">
//Main.add(window.print);

var lists = {
  sejour: {
    labels: ["Dernier s�jour", "S�jours"],
    all: true
  },
  consultation: {
    labels: ["Derni�re consultation", "Consultations"],
    all: true
  }
};

function toggleList(list, button) {
  var lines = $$('.'+list),
      data = lists[list];
      
  lines.invoke('toggle');
  lines.first().show();
  data.all = !data.all;
  button.up().select('span')[0].update(data.labels[data.all ? 1 : 0]);
}
</script>

<table class="print">
  <tr>
    <th class="title" colspan="4"><a href="#" onclick="window.print()">Fiche Patient &mdash; le {{$today}}</a></th>
  </tr>
  <tr>
    <td colspan="2" style="width: 50%;"></td>
    <td colspan="2" style="width: 50%;"></td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field=nom}} / {{mb_label object=$patient field=prenom}}</th>
    <td colspan="3"><strong>{{$patient->_view}}</strong> {{mb_include module=patients template=inc_vw_ipp ipp=$patient->_IPP hide_empty=true}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field=naissance}} / {{mb_label object=$patient field=sexe}}</th>
    <td>
      n�(e) le {{mb_value object=$patient field=naissance}} <br />
      de sexe {{if $patient->sexe == "m"}} masculin {{else}} f�minin {{/if}}
    </td>
    <th>{{mb_label object=$patient field=lieu_naissance}}</th>
    <td>
      {{mb_value object=$patient field=cp_naissance}}
      {{mb_value object=$patient field=lieu_naissance}} <br />
      {{mb_value object=$patient field=_pays_naissance_insee}}
    </td>
  </tr>
  <tr>
    {{if $conf.ref_pays == 1}}
      <th>{{mb_label object=$patient field=matricule}}</th>
      <td>{{mb_value object=$patient field=matricule}}</td>
    {{else}}
      <th>{{mb_label object=$patient field=avs}}</th>
      <td>{{mb_value object=$patient field=avs}}</td>
    {{/if}}
    <th>{{mb_label object=$patient field=profession}}</th>
    <td>{{mb_value object=$patient field=profession}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field=tel}}</th>
    <td>{{mb_value object=$patient field=tel}}</td>
    <th>{{mb_label object=$patient field=tel2}}</th>
    <td>{{mb_value object=$patient field=tel2}}</td>
  </tr>
  <tr>
  </tr>
  {{if $patient->tel_autre}}
  <tr>
    <th>{{mb_label object=$patient field=tel_autre}}</th>
    <td>{{mb_value object=$patient field=tel_autre}}</td>
  </tr>
  </tr>
  {{/if}}
  <tr>
    <th>{{mb_label object=$patient field=adresse}}</th>
    <td>
      {{$patient->adresse|nl2br}} <br />
      {{$patient->cp}} {{$patient->ville}}
    </td>
    <th>{{mb_label object=$patient field=incapable_majeur}}</th>
    <td>{{mb_value object=$patient field=incapable_majeur}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field=rques}}</th>
    <td colspan="3">{{$patient->rques|nl2br}}</td>
  </tr>
  <tr>
    <th class="category" colspan="4">Correspondants</th>
  </tr>
  <tr>
    <td colspan="2" class="halfPane">
      <table>
        <tr>
          <th>M�decin traitant</th>
          <td>
            {{if $patient->_ref_medecin_traitant->_id}}
              {{$patient->_ref_medecin_traitant->_view}}<br />
              {{$patient->_ref_medecin_traitant->adresse|nl2br}}<br />
              {{$patient->_ref_medecin_traitant->cp}} {{$patient->_ref_medecin_traitant->ville}}
            {{else}}
              Non renseign�
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>Corresp. m�dicaux</th>
          <td>
            {{foreach from=$patient->_ref_medecins_correspondants item=curr_corresp}}
            <div style="float: left; margin-right: 1em; margin-bottom: 0.5em; margin-top: 0.4em; width: 15em;">
              {{$curr_corresp->_ref_medecin->_view}}<br />
              {{$curr_corresp->_ref_medecin->adresse|nl2br}}<br />
              {{$curr_corresp->_ref_medecin->cp}} {{$curr_corresp->_ref_medecin->ville}}
            </div>
            {{foreachelse}}
              Non renseign�
            {{/foreach}}
          </td>
        </tr>
      </table>
    </td>
    <td colspan="2">
      <table>
        {{foreach from=$patient->_ref_correspondants_patient item=curr_corresp}}
        <tr>
          <th>{{mb_value object=$curr_corresp field=relation}}</th>
          <td>
            {{$curr_corresp->nom}} {{$curr_corresp->prenom}}<br />
            {{$curr_corresp->adresse|nl2br}}<br />
            {{$curr_corresp->cp}} {{$curr_corresp->ville}}
          </td>
        </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
  {{if $patient->_ref_sejours|@count}}
  <tr>
    <th class="category" colspan="4">
      <button class="change not-printable" style="float:right;" onclick="toggleList('sejour', this)">Seulement le dernier</button>
      <span>S�jours</span>
    </th>
  </tr>
  {{foreach from=$patient->_ref_sejours item=curr_sejour}}
  <tr class="sejour">
    <th class="text">{{$curr_sejour->_ref_praticien->_view}}</th>
    <td colspan="3">
      {{mb_include module=planningOp template=inc_vw_numdos nda_obj=$curr_sejour}}
      Du {{$curr_sejour->entree_prevue|date_format:"%d/%m/%Y"}}
      au {{$curr_sejour->sortie_prevue|date_format:"%d/%m/%Y"}}
      - ({{mb_value object=$curr_sejour field=type}})
      <ul>
      {{foreach from=$curr_sejour->_ref_operations item="curr_op"}}
        <li>
          Intervention le {{$curr_op->_datetime|date_format:"%d/%m/%Y"}}
          ({{$curr_op->_ref_chir->_view}})
        </li>
      {{foreachelse}}
        <li class="empty">Pas d'interventions</li>
      {{/foreach}}
      </ul>
    </td>
  </tr>
  {{/foreach}}
  {{/if}}
  {{if $patient->_ref_consultations|@count}}
  <tr>
    <th class="category" colspan="4">
      <button class="change not-printable" style="float:right;" onclick="toggleList('consultation', this)">Seulement la derni�re</button>
      <span>Consultations</span>
    </th>
  </tr>
  {{foreach from=$patient->_ref_consultations item=curr_consult}}
  <tr class="consultation">
    <th class="text">{{$curr_consult->_ref_plageconsult->_ref_chir->_view}}</th>
    <td colspan="3">le {{mb_value object=$curr_consult->_ref_plageconsult field=date}} � {{mb_value object=$curr_consult field=heure}}</td>
  </tr>
  {{/foreach}}
  {{/if}}
</table>