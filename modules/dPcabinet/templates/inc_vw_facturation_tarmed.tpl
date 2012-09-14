{{if !@$modules.tarmed->_can->read || !$conf.tarmed.CCodeTarmed.use_cotation_tarmed}}
  {{mb_return}}
{{/if}}

<tr>
  <th class="category">Date</th>
  <th class="category">Code</th>
  <th class="category">Libelle</th>
  <th class="category">Co�t</th>
  <th class="category">Qte</th>
  <th class="category">Coeff</th>        
  <th class="category">Montant</th>
</tr>

{{if $facture->cloture && isset($factures|smarty:nodefaults) && count($factures)}}
  <tr>
    <td colspan="10">
      <button class="printPDF" onclick="printFacture('{{$facture->_id}}', 0, 1);">Edition des BVR</button>
      <button class="cut" onclick="Facture.cutFacture('{{$facture->_id}}');"
        {{if $facture->_nb_factures != 1 || $facture->_reglements_total_patient}}disabled="disabled"{{/if}}> Eclatement</button>
      <button class="print" onclick="printFacture('{{$facture->_id}}', 1, 0);">Justificatif de remboursement</button>
    </td>
  </tr>
{{/if}}
<tr>
  <td colspan="2"> Type de la facture:
    <form name="type_facture" method="post" action=""> 
      {{mb_class object=$facture}}
      {{mb_key   object=$facture}}
      <input type="hidden" name="not_load_banque" value="{{if isset($factures|smarty:nodefaults) && count($factures)}}0{{else}}1{{/if}}" />
      <input type="radio" name="type_facture" value="maladie" {{if $facture->type_facture == 'maladie'}}checked{{/if}} onchange="Facture.modifCloture(this.form);" />
      <label for="maladie">Maladie</label>
      <input type="radio" name="type_facture" value="accident" {{if $facture->type_facture == 'accident'}}checked{{/if}} onchange="Facture.modifCloture(this.form);" />
      <label for="accident">Accident</label>
    </form>
  </td>
  <td colspan="8">
    <form name="cession_facture" method="post" action=""> 
      {{mb_class object=$facture}}
      {{mb_key   object=$facture}}
      <input type="hidden" name="not_load_banque" value="{{if isset($factures|smarty:nodefaults) && count($factures)}}0{{else}}1{{/if}}" />
      <input type="hidden" name="cession_creance" value="{{if $facture->cession_creance == 1}}0{{else}}1{{/if}}" />
      <input type="checkbox" name="cession_tmp" value="{{$facture->cession_creance}}" {{if $facture->cession_creance}}checked="checked"{{/if}} onclick="Facture.modifCloture(this.form);" />
      {{mb_label object=$facture field=cession_creance}}
    </form>
    <form name="npq_facture" method="post" action=""> 
      {{mb_class object=$facture}}
      {{mb_key   object=$facture}}
      <input type="hidden" name="not_load_banque" value="{{if isset($factures|smarty:nodefaults) && count($factures)}}0{{else}}1{{/if}}" />
      <input type="hidden" name="npq" value="{{if $facture->npq == 1}}0{{else}}1{{/if}}" />
      <input type="checkbox" name="npq_tmp" value="{{$facture->npq}}" {{if $facture->npq}}checked="checked"{{/if}} onclick="Facture.modifCloture(this.form);" />
      {{mb_label object=$facture field=npq}}
    </form>
    {{if count($facture->_ref_patient->_ref_correspondants_patient)}}
      <form name="assurance_patient" method="post" action="" style="margin-left:40px;"> 
        {{mb_class object=$facture}}
        {{mb_key   object=$facture}}
        <select name="assurance" style="width: 15em;" onchange="refreshAssurance();">
          <option value="" {{if !$facture->assurance}}selected="selected" {{/if}}>&mdash; Choisir une assurance</option>
          {{foreach from=$facture->_ref_patient->_ref_correspondants_patient item=_assurance}}
            {{if $_assurance->relation == "assurance"}}
            <option value="{{$_assurance->_id}}" {{if $facture->assurance == $_assurance->_id}} selected="selected" {{/if}}>
              {{$_assurance->nom}}
            </option>
            {{/if}}
          {{/foreach}}
        </select>
      </form>
    {{/if}}
  </td>
</tr>

{{if $facture->type_facture == "accident"}}
  <tr>
    <td colspan="2">
      <form name="ref_accident" method="post" action="" onsubmit="return onSubmitFormAjax(this);" style="max-width:100px;">
        {{mb_class object=$facture}}
        {{mb_key   object=$facture}}
        <b>{{mb_label object=$facture field="ref_accident"}}:</b>
        {{if $facture->cloture}}
          {{mb_value object=$facture field="ref_accident"}} 
        {{else}}
          {{mb_field object=$facture field="ref_accident" onchange="return onSubmitFormAjax(this.form);"}} 
        </text
        {{/if}}
      </form>
    </td>
    <td colspan="9"></td>
  </tr>
{{/if}}

{{foreach from=$facture->_ref_consults item=_consultation}}
{{foreach from=$_consultation->_ref_actes_tarmed item=_acte_tarmed}}
  <tr>
    <td style="text-align:center;width:100px;">
      {{if $_acte_tarmed->date}}
        {{mb_value object=$_acte_tarmed field="date"}} 
      {{else}}
        {{$_consultation->_date}}
      {{/if}}
    </td>
    {{if $_acte_tarmed->code}} 
    <td style="background-color:#BA55D3; width:140px;">
       {{mb_value object=$_acte_tarmed field="code"}}
    </td>
    {{else}}
    <td>
    </td>
    {{/if}}
    <td style="white-space: pre-wrap;">
      {{if $_acte_tarmed->libelle}}
        {{$_acte_tarmed->libelle}}
      {{else}}
        {{$_acte_tarmed->_ref_tarmed->libelle}}
      {{/if}}
    </td>
    <td style="text-align:right;">
      {{if $_acte_tarmed->quantite}}
        {{$_acte_tarmed->montant_base/$_acte_tarmed->quantite|string_format:"%0.2f"}}
      {{else}}
        {{$_acte_tarmed->montant_base|string_format:"%0.2f"}}
      {{/if}}
    </td>
    <td style="text-align:right;">{{mb_value object=$_acte_tarmed field="quantite"}}</td>
    <td style="text-align:right;">{{$facture->_coeff}}</td>
    <td style="text-align:right;">{{$_acte_tarmed->montant_base*$facture->_coeff|string_format:"%0.2f"}}</td>
  </tr>
{{/foreach}}

{{foreach from=$_consultation->_ref_actes_caisse item=_acte_caisse}}
  {{assign var="caisse" value=$_acte_caisse->_ref_caisse_maladie}}
  {{if $facture->type_facture == "accident"}}
    {{assign var="coeff_caisse" value=$_acte_caisse->_ref_caisse_maladie->coeff_accident}}
  {{else}}
    {{assign var="coeff_caisse" value=$_acte_caisse->_ref_caisse_maladie->coeff_maladie}}
  {{/if}}
  <tr>
    <td style="text-align:center;width:100px;">{{$_consultation->_date|date_format:"%d/%m/%Y"}}</td>
    <td  {{if $_acte_caisse->code}} style="background-color:#DA70D6; width:140px;">{{mb_value object=$_acte_caisse field="code"}}{{else}}>{{/if}}</td>
    <td style="white-space: pre-wrap;">{{$_acte_caisse->_ref_prestation_caisse->libelle}}</td>
    <td style="text-align:right;">
      {{if $_acte_caisse->quantite}}
        {{$_acte_caisse->montant_base/$_acte_caisse->quantite|string_format:"%0.2f"}}
      {{else}}
        {{$_acte_caisse->montant_base|string_format:"%0.2f"}}
      {{/if}}
    </td>
    <td style="text-align:right;">{{mb_value object=$_acte_caisse field="quantite"}}</td>
    <td style="text-align:right;">{{$coeff_caisse}}
    </td>
    <td style="text-align:right;">{{$_acte_caisse->montant_base*$coeff_caisse|string_format:"%0.2f"}}</td>
  </tr>
{{/foreach}}

  <tbody class="hoverable">
    {{assign var="nb_montants" value=$facture->_montant_factures|@count }}
    {{if $nb_montants > 1}}
      {{foreach from=$facture->_montant_factures item=_montant key=key }}
        <tr>
          {{if $key == 0}}
          <td colspan="4" rowspan="{{$nb_montants+2}}"></td>
          {{/if}}
          <td colspan="2">Montant n�{{$key+1}}</td>
          <td style="text-align:right;">{{$_montant|string_format:"%0.2f"}}</td>
        </tr>
      {{/foreach}}
    {{/if}}
    
    <tr>
      <td colspan="2"><b>{{mb_label object=$facture field="remise"}}</b></td>
      <td style="text-align: right;"> 
        <form name="modif_remise" method="post" onsubmit="Facture.modifCloture(this.form);">
          {{mb_class object=$facture}}
          {{mb_key   object=$facture}}
          <input type="hidden" name="patient_id" value="{{$facture->patient_id}}" />
          <input type="hidden" name="not_load_banque" value="{{if isset($factures|smarty:nodefaults) && count($factures)}}0{{else}}1{{/if}}" />                
          
          {{if $facture->cloture}}
            {{mb_value object=$facture field="remise"}} 
          {{else}}
            <input name="remise" type="text" value="{{$facture->remise}}" onchange="Facture.modifCloture(this.form);" size="4" />
          {{/if}}
          
          <br/>soit 
          {{if $facture->_montant_sans_remise!=0 && $facture->remise}}
            <strong>{{math equation="(y/x)*100" x=$facture->_montant_sans_remise y=$facture->remise format="%.2f"}} %</strong>
          {{else}}
            <strong>0 %</strong>
          {{/if}}
        </form>
      </td>
    </tr>
    
    <tr>
      <td colspan="3"></td>
      <td colspan="2"><b>Montant Total</b></td>
      <td style="text-align:right;"><b>{{mb_value object=$facture field="_montant_avec_remise"}}</b></td>
    <tr>

  </tbody>

  {{if !$facture->_reglements_total_patient}}
    <tr>
      <td colspan="7">
        {{if $facture->_nb_factures == 1}}
          <form name="change_type_facture" method="post">
            {{mb_class object=$facture}}
            {{mb_key   object=$facture}}
            <input type="hidden" name="cloture" value="{{if !$facture->cloture}}{{$date}}{{/if}}" />
            <input type="hidden" name="not_load_banque" value="{{if isset($factures|smarty:nodefaults) && count($factures)}}0{{else}}1{{/if}}" />
            {{if !$facture->cloture}}
              <button class="submit" type="button" onclick="Facture.modifCloture(this.form);" >Cloturer la facture</button>
            {{elseif !isset($reglement|smarty:nodefaults) || ($facture->_ref_reglements|@count == 0)}}
              <button class="submit" type="button" onclick="Facture.modifCloture(this.form);" >R�ouvrir la facture</button>
            {{/if}}
          </form>
        {{else}}
          <form name="fusionner_eclatements" method="post">
            <input type="hidden" name="dosql" value="do_fusion_facture_aed" />
            <input type="hidden" name="m" value="dPcabinet" />
            <input type="hidden" name="del" value="0" />
            {{mb_key   object=$facture}}
            <input type="hidden" name="not_load_banque" value="{{if isset($factures|smarty:nodefaults) && count($factures)}}0{{else}}1{{/if}}" />
            <button class="submit" type="button" onclick="Facture.modifCloture(this.form);" > Fusionner les �clats de facture </button>
          </form>
        {{/if}}
      </td>
    </tr>
  {{/if}}

{{foreachelse}}
  <tr><td colspan="10" class="empty">{{tr}}CConsultation.none{{/tr}}</td></tr>
{{/foreach}}
