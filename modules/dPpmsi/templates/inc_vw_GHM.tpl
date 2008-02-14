{{assign var="GHM" value=$sejour->_ref_GHM}}
{{if $sejour->_ref_GHM->_CM}}
  <strong>Cat�gorie majeure CM{{$GHM->_CM}}</strong> : {{$GHM->_CM_nom}}
  <br />
  <strong>GHM</strong> : {{$GHM->_GHM}} ({{$GHM->_tarif_2006}} &euro;)
  <br />
  {{$GHM->_GHM_nom}}
  <br />
  <em>Appartenance aux groupes {{$GHM->_GHM_groupe}}</em>
  <br />
  <strong>Bornes d'hospitalisation</strong> :
  de {{$GHM->_borne_basse}}
  � {{$GHM->_borne_haute}} jours
  {{if $GHM->_notes|@count}}
    <br />
    <strong>Notes</strong> :
    <ul>
    {{foreach from=$GHM->_notes item="curr_note"}}
      <li>{{$curr_note}}</li>
    {{/foreach}}
    </ul>
  {{/if}}
  <br />
  <button class="tick" onclick="exporterHPRIM({{$sejour->_id}}, 'sej')">Exporter vers S@nt�.com</button>
{{else}}
  <strong>{{$GHM->_GHM}}</strong>
{{/if}}