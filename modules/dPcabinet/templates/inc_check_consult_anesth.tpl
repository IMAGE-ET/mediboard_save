<style>
  button img{
    width:16px;
    height:16px;
  }
</style>
<table class="tbl">
  <tr>
    <th>Crit�re</th>
    {{foreach from=$consult->_ref_sejour->_ref_operations item=operation}}
      <th class="text" style="width:13em;">{{$operation->_view}}</th>
    {{foreachelse}}
      <th style="width:13em;">Pas d'intervention</th>
    {{/foreach}}
  </tr>
  <tr>
    <td onmouseover="ObjectTooltip.createDOM(this, 'DetailRank1');">
      <div class="rank">1</div><strong>Medecin anesth�siste</strong>
    </td>
    {{foreach from=$tab_op item=num_operation}}
      <td class="button"><img src="images/icons/note_green.png"/></td>
    {{/foreach}}
  </tr>
  <tr>
    {{assign var="result" value=false}}
    {{if $dm_patient->absence_traitement || $dm_patient->_ref_traitements|@count ||
    ($dm_patient->_ref_prescription && $dm_patient->_ref_prescription->_ref_prescription_lines|@count)}}
      {{assign var="result" value=true}}
    {{/if}}
    <td onmouseover="ObjectTooltip.createDOM(this, 'DetailRank2');"><div class="rank">2</div><strong>Traitement habituel</strong></td>
    {{foreach from=$tab_op item=num_operation}}
      <td class="button"><img src="images/icons/note_{{if $result}}green{{else}}red{{/if}}.png"/></td>
    {{/foreach}}
  </tr>

  <tr>
    {{assign var="result" value=false}}
    {{if $conf.dPcabinet.CConsultAnesth.show_facteurs_risque &&
      (($dm_sejour->_id &&
      ($dm_sejour->risque_antibioprophylaxie != "NR" || $dm_sejour->risque_prophylaxie != "NR" ||
      $dm_sejour->risque_MCJ_chirurgie != "NR" || $dm_sejour->risque_thrombo_chirurgie != "NR")
      || ($dm_patient->_id && ($dm_patient->facteurs_risque != "" ||
      $dm_patient->risque_thrombo_chirurgie != "NR" || $dm_patient->risque_MCJ_chirurgie != "NR"))))}}
      {{assign var="result" value=true}}
    {{/if}}

    <td onmouseover="ObjectTooltip.createDOM(this, 'DetailRank3');"><div class="rank">3</div><strong>Risque anesth�sique</strong></td>
    {{foreach from=$tab_op item=num_operation}}
      <td class="button">
        {{if $conf.dPcabinet.CConsultAnesth.show_facteurs_risque}}
          <img src="images/icons/note_{{if $result}}green{{else}}red{{/if}}.png"/>
        {{else}}
          <img src="images/icons/note_orange.png" title="N/A"/>
        {{/if}}
      </td>
    {{/foreach}}
  </tr>

  <tr>
    <td onmouseover="ObjectTooltip.createDOM(this, 'DetailRank4');"><div class="rank">4</div><strong>Type d'anesth�sie</strong></td>
    {{foreach from=$consult->_ref_sejour->_ref_operations item=operation}}
      <td class="button"><img src="images/icons/note_{{if $operation->type_anesth}}green{{else}}red{{/if}}.png"/></td>
    {{foreachelse}}
      <td class="button"><img src="images/icons/note_orange.png" title="N/A"/></td>
    {{/foreach}}
  </tr>

  <tr>
    <td onmouseover="ObjectTooltip.createDOM(this, 'DetailRank5');">
      <div class="rank">5</div><strong>Voies a�riennes sup�rieures</strong>
    </td>
    {{foreach from=$consult->_ref_sejour->_ref_operations item=operation}}
      {{assign var="result" value=false}}
      {{if ($operation->_ref_consult_anesth->mallampati && $operation->_ref_consult_anesth->bouche && $operation->_ref_consult_anesth->distThyro) || $operation->_ref_consult_anesth->conclusion}}
        {{assign var="result" value=true}}
      {{/if}}
      <td class="button"><img src="images/icons/note_{{if $result}}green{{else}}red{{/if}}.png"/></td>
    {{foreachelse}}
      {{assign var="result" value=false}}
      {{assign var="consult_anesth" value=$consult->_ref_consult_anesth}}
      {{if ($consult_anesth->mallampati && $consult_anesth->bouche && $consult_anesth->distThyro) || $consult_anesth->conclusion}}
        {{assign var="result" value=true}}
      {{/if}}
      <td class="button"><img src="images/icons/note_{{if $result}}green{{else}}red{{/if}}.png"/></td>
    {{/foreach}}
  </tr>

  <tr>
    <td onmouseover="ObjectTooltip.createDOM(this, 'DetailRank6');"><div class="rank">6</div><strong>Poids</strong></td>
    {{foreach from=$tab_op item=num_operation}}
      <td class="button"><img src="images/icons/note_{{if $constantes->poids}}green{{else}}red{{/if}}.png"/></td>
    {{/foreach}}
  </tr>

  <tr>
    <td onmouseover="ObjectTooltip.createDOM(this, 'DetailRank7');"><div class="rank">7</div><strong>Score ASA</strong></td>
    {{foreach from=$consult->_ref_sejour->_ref_operations item=operation}}
      <td class="button"><img src="images/icons/note_{{if $operation->ASA}}green{{else}}red{{/if}}.png"/></td>
    {{foreachelse}}
      <td class="button"><img src="images/icons/note_orange.png" title="N/A"/></td>
    {{/foreach}}
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
  {{mb_key   object=$consult}}
  <input type="hidden" name="chrono" value="{{$consult|const:'TERMINE'}}" />
</form>

<div  style="display: none;">
  <table class="tbl" id="DetailRank1">
    <tr><th class="title" colspan="2">Medecin anesth�siste</th></tr>
    <tr>
      <th>Crit�re</th>
      <td style="white-space: pre-wrap;">Identification du m�decin anesth�siste sur le document tra�ant la phase pr�-anesth�sique</td>
    </tr>
    <tr>
      <th>Elem�nt requis <br/>pour le valider</th>
      <td style="white-space: pre-wrap;">Nom du m�decin anesth�siste indiqu� sur le document tra�ant la phase pr�-anesth�sique</td>
    </tr>
  </table>

  <table class="tbl" id="DetailRank2">
    <tr><th class="title" colspan="2">Traitement habituel</th></tr>
    <tr>
      <th>Crit�re</th>
      <td style="white-space: pre-wrap;">Mention du traitement habituel ou de l'absence de traitement dans le document tra�ant la CPA (si applicable)</td>
    </tr>
    <tr>
      <th>Elem�nt requis <br/>pour le valider</th>
      <td style="white-space: pre-wrap;">Le document tra�ant la CPA indique formellement :
        - Soit l'existence et la mention du traitement habituel,
        - Soit l'absence de traitement.</td>
    </tr>
  </table>

  <table class="tbl" id="DetailRank3">
    <tr><th class="title" colspan="2">Risque anesth�sique</th></tr>
    <tr>
      <th>Crit�re</th>
      <td style="white-space: pre-wrap;">Mention de l'�valuation du risque anesth�sique dans le document tra�ant la CPA</td>
    </tr>
    <tr>
      <th>Elem�nt requis <br/>pour le valider</th>
      <td style="white-space: pre-wrap;">La mention de l'�valuation du risque anesth�sique est retrouv�e dans le document tra�ant la CPA</td>
    </tr>
  </table>

  <table class="tbl" id="DetailRank4">
    <tr><th class="title" colspan="2">Type d'anesth�sie</th></tr>
    <tr>
      <th>Crit�re</th>
      <td style="white-space: pre-wrap;">Mention du type d'anesth�sie propos� au patient dans le document tra�ant la CPA</td>
    </tr>
    <tr>
      <th>Elem�nt requis <br/>pour le valider</th>
      <td style="white-space: pre-wrap;">La mention du type d'anesth�sie propos� au patient est retrouv�e dans le document tra�ant la CPA</td>
    </tr>
  </table>

  <table class="tbl" id="DetailRank5">
    <tr><th class="title" colspan="2">Voies a�riennes sup�rieures</th></tr>
    <tr>
      <th>Crit�re</th>
      <td style="white-space: pre-wrap;">Mention de l'�valuation des conditions d'abord des <strong>voies a�riennes sup�rieures</strong> en phase pr�-anesth�sique dans le document tra�ant la CPA</td>
    </tr>
    <tr>
      <th>Elem�nt requis <br/>pour le valider</th>
      <td style="white-space: pre-wrap;">Le score de Mallampati, la distance thyro-mentonni�re ET l'ouverture de bouche sont retrouv�s dans le document tra�ant la CPA
        OU<br/>Une conclusion explicite est retrouv�e dans le document tra�ant la CPA</td>
    </tr>
  </table>

  <table class="tbl" id="DetailRank6">
    <tr><th class="title" colspan="2">Poids</th></tr>
    <tr>
      <th>Elem�nt requis <br/>pour le valider</th>
      <td style="white-space: pre-wrap;">Le poids est renseign� dans les constantes du patient</td>
    </tr>
  </table>

  <table class="tbl" id="DetailRank7">
    <tr><th class="title" colspan="2">Score ASA</th></tr>
    <tr>
      <th>Elem�nt requis <br/>pour le valider</th>
      <td style="white-space: pre-wrap;">Le score ASA est renseign� dans l'intervention pr�vue</td>
    </tr>
  </table>
</div>