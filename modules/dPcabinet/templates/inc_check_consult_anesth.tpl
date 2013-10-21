<table class="tbl">
  <tr>
    <th class="category" style="width:350px;">Crit�re</th>
    <th class="category" style="width:350px;">Elem�nt requis pour le valider</th>
    <th class="category" style="width:50px;">Renseign�</th>
  </tr>
  <tr>
    <td style="white-space: pre-wrap;">Identification du m�decin anesth�siste sur le document tra�ant la phase pr�-anesth�sique</td>
    <td style="white-space: pre-wrap;">Nom du m�decin anesth�siste indiqu� sur le document tra�ant la phase pr�-anesth�sique</td>
    <td class="ok">Oui</td>
  </tr>

  <tr>
    {{assign var="result" value=false}}
    {{if $dm_patient->absence_traitement || $dm_patient->_ref_traitements|@count ||
        ($dm_patient->_ref_prescription && $dm_patient->_ref_prescription->_ref_prescription_lines|@count)}}
      {{assign var="result" value=true}}
    {{/if}}
    <td style="white-space: pre-wrap;">Mention du <strong>traitement habituel</strong> ou de l'absence de traitement dans le document tra�ant la CPA (si applicable)</td>
    <td style="white-space: pre-wrap;">Le document tra�ant la CPA indique formellement :
      - Soit l'existence et la mention du traitement habituel,
      - Soit l'absence de traitement.</td>
    <td class="{{if $result}}ok{{else}}error{{/if}}">{{if $result}}Oui{{else}}Non{{/if}}</td>
  </tr>

  <tr>
    {{assign var="result" value=false}}
    {{if $conf.dPcabinet.CConsultAnesth.show_facteurs_risque &&
      (($dm_sejour->_id &&
        ($dm_sejour->risque_antibioprophylaxie != "NR" || $dm_sejour->risque_prophylaxie != "NR" ||
        $dm_sejour->risque_MCJ_chirurgie != "NR" || $dm_sejour->risque_thrombo_chirurgie != "NR")
      || ($dm_patient->_id && ($dm_patient->facteurs_risque != "" ||
        $dm_patient->risque_thrombo_chirurgie != "NR" || $dm_patient->risque_MCJ_chirurgie != "NR"))))
    }}
      {{assign var="result" value=true}}
    {{/if}}

    <td style="white-space: pre-wrap;">Mention de l'�valuation du <strong>risque anesth�sique</strong> dans le document tra�ant la CPA</td>
    <td style="white-space: pre-wrap;">La mention de l'�valuation du risque anesth�sique est retrouv�e dans le document tra�ant la CPA</td>
    {{if $conf.dPcabinet.CConsultAnesth.show_facteurs_risque}}
      <td class="{{if $result}}ok{{else}}error{{/if}}">{{if $result}}Oui{{else}}Non{{/if}}</td>
    {{else}}
      <td style="background-color: yellow;">N/A</td>
    {{/if}}
  </tr>

  <tr>
    <td style="white-space: pre-wrap;">Mention du <strong>type d'anesth�sie</strong> propos� au patient dans le document tra�ant la CPA</td>
    <td style="white-space: pre-wrap;">La mention du type d'anesth�sie propos� au patient est retrouv�e dans le document tra�ant la CPA</td>
    <td class="{{if $consult_anesth->_ref_operation->_id && $consult_anesth->_ref_operation->type_anesth}}ok{{else}}error{{/if}}">
      {{if $consult_anesth->_ref_operation->_id && $consult_anesth->_ref_operation->type_anesth}}Oui{{else}}Non{{/if}}
    </td>
  </tr>

  <tr>
    {{assign var="result" value=false}}
    {{if ($consult_anesth->mallampati && $consult_anesth->bouche && $consult_anesth->distThyro) || $consult_anesth->conclusion}}
      {{assign var="result" value=true}}
    {{/if}}
    <td style="white-space: pre-wrap;">Mention de l'�valuation des conditions d'abord des <strong>voies a�riennes sup�rieures</strong> en phase pr�-anesth�sique dans le document tra�ant la CPA</td>
    <td style="white-space: pre-wrap;">Le score de Mallampati, la distance thyro-mentonni�re ET l'ouverture de bouche sont retrouv�s dans le document tra�ant la CPA
      OU<br/>Une conclusion explicite est retrouv�e dans le document tra�ant la CPA</td>
    <td class="{{if $result}}ok{{else}}error{{/if}}">{{if $result}}Oui{{else}}Non{{/if}}</td>
  </tr>

  <tr>
    <td><strong>Poids</strong></td>
    <td>Le poids est renseign� dans les constantes du patient</td>
    <td class="{{if $constantes->poids}}ok{{else}}error{{/if}}">
      {{if $constantes->poids}}Oui{{else}}Non{{/if}}
    </td>
  </tr>
  <tr>
    <td><strong>Score ASA</strong></td>
    <td>Le score ASA est renseign� dans l'intervention pr�vue</td>
    <td class="{{if $consult_anesth->_ref_operation->_id && $consult_anesth->_ref_operation->ASA}}ok{{else}}error{{/if}}">
      {{if $consult_anesth->_ref_operation->_id && $consult_anesth->_ref_operation->ASA}}Oui{{else}}Non{{/if}}
    </td>
  </tr>
  <tr>
    <td colspan="3" class="button">
      <button type="button" class="left" onclick="Control.Modal.close();">Continuer la consultation</button>
      <button type="button" class="save" onclick="document.formCheckConsultAnesth.submit();">Terminer la consultation</button>
    </td>
  </tr>
</table>

<form class="watch" name="formCheckConsultAnesth" action="?m={{$m}}" method="post">
  <input type="hidden" name="m" value="dPcabinet" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="dosql" value="do_consultation_aed" />
  {{mb_key   object=$consultation}}
  <input type="hidden" name="chrono" value="{{$consultation|const:'TERMINE'}}" />
</form>