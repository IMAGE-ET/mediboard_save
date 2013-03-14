<!-- $Id$ -->

<table class="main">
  <tr>
    <td class="halfPane">
      <table>
        <tr>
          <th>
            <a href="#" onclick="window.print()">
              Rapport 
              {{mb_include module=system template=inc_interval_date from=$filter->_date_min to=$filter->_date_max}}
            </a>
          </th>
        </tr>
        
        <!-- Praticiens concern�s -->
        {{foreach from=$listPrat item=_prat}}
        <tr>
          <td>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_prat}}</td>
        </tr>
        {{/foreach}}

        <tr>
          <td>Paiments pris en compte : {{if $filter->_mode_reglement}}{{$filter->_mode_reglement}}{{else}}tous{{/if}}</td>
        </tr>
      </table>
    </td>
    
    <td class="halfPane">
      <table class="tbl" style="text-align: center;">
        <tr>
          <th class="title" colspan="7">R�glement Patients</th>
        </tr>
        <tr>
          <th class="category">Type r�glement</th>
          <th class="category">Total</th>
          <th class="category">Ch�que</th>
          <th class="category">CB</th>
          <th class="category">Esp�ces</th>
          <th class="category">Virement</th>
          <th class="category">Autre</th>
        </tr>
        <tr>
          <th class="category">Nb r�glements</th>
          <td>{{$recapReglement.total.nb_reglement_patient}}</td>
          <td>{{$recapReglement.cheque.nb_reglement_patient}}</td>
          <td>{{$recapReglement.CB.nb_reglement_patient}}</td>
          <td>{{$recapReglement.especes.nb_reglement_patient}}</td>
          <td>{{$recapReglement.virement.nb_reglement_patient}}</td>
          <td>{{$recapReglement.autre.nb_reglement_patient}}</td>
        </tr>
        <tr>
          <th class="category">Total r�glement patient</th>
          <td>{{$recapReglement.total.du_patient|currency}}</td>
          <td>{{$recapReglement.cheque.du_patient|currency}}</td>
          <td>{{$recapReglement.CB.du_patient|currency}}</td>
          <td>{{$recapReglement.especes.du_patient|currency}}</td>
          <td>{{$recapReglement.virement.du_patient|currency}}</td>
          <td>{{$recapReglement.autre.du_patient|currency}}</td>
        </tr>
        {{if $conf.dPccam.CCodeCCAM.use_cotation_ccam == "1"}}
          <tr>
            <th class="title" colspan="7">R�glement Tiers</th>
          </tr>
          <tr>
            <th class="category">Type r�glement</th>
            <th class="category">Total</th>
            <th class="category">Ch�que</th>
            <th class="category">CB</th>
            <th class="category">Esp�ces</th>
            <th class="category">Virement</th>
            <th class="category">Autre</th>
          </tr>
          <tr>
            <th class="category">Nb r�glements</th>
            <td>{{$recapReglement.total.nb_reglement_tiers}}</td>
            <td>{{$recapReglement.cheque.nb_reglement_tiers}}</td>
            <td>{{$recapReglement.CB.nb_reglement_tiers}}</td>
            <td>{{$recapReglement.especes.nb_reglement_tiers}}</td>
            <td>{{$recapReglement.virement.nb_reglement_tiers}}</td>
            <td>{{$recapReglement.autre.nb_reglement_tiers}}</td>
          </tr>
          <tr>
            <th class="category">Total r�glement Tiers</th>
            <td>{{$recapReglement.total.du_tiers|currency}}</td>
            <td>{{$recapReglement.cheque.du_tiers|currency}}</td>
            <td>{{$recapReglement.CB.du_tiers|currency}}</td>
            <td>{{$recapReglement.especes.du_tiers|currency}}</td>
            <td>{{$recapReglement.virement.du_tiers|currency}}</td>
            <td>{{$recapReglement.autre.du_tiers|currency}}</td>
          </tr>
        {{/if}}
        <tr>
          <th class="title" colspan="7">R�capitulatif des consultations concern�es</th>
        </tr>
        <tr>
          <th class="category">Nb de consultations</th>
          <td colspan="6">{{$listConsults|@count}}</td>
        </tr>
        {{if $conf.dPccam.CCodeCCAM.use_cotation_ccam == "1"}}
          <tr>
            <th class="category">Total secteur 1</th>
            <td colspan="6">{{$recapReglement.total.secteur1|currency}}</td>
          </tr>
          <tr>
            <th class="category">Total secteur 2</th>
            <td colspan="6">{{$recapReglement.total.secteur2|currency}}</td>
          </tr>
        {{/if}}
        <tr>
          <th class="category">Total factur�</th>
          <td colspan="6">{{$recapReglement.total.secteur1+$recapReglement.total.secteur2|currency}}</td>
        </tr>
        <tr>
          <th class="category">Total r�gl�</th>
          <td colspan="6">{{$recapReglement.total.du_patient+$recapReglement.total.du_tiers|currency}}</td>
        </tr>
      </table>
    </td>
  </tr>
  {{if $filter->_type_affichage}}
  {{foreach from=$listReglements key=key_date item=_date}}
  <tr>
    <td colspan="2"><strong>R�glements du {{$key_date|date_format:$conf.longdate}}</strong></td>
  </tr>
  <tr>
    <td colspan="2">
      <table class="tbl">
        <tr>
          <th rowspan="2"  colspan="2" class="narrow text">{{tr}}CFactureCabinet{{/tr}}</th>
          <th rowspan="2" style="width: 30%;">{{mb_label class=CConsultation field=_prat_id}}</th>
          <th rowspan="2" style="width: 30%;">{{mb_label class=CConsultation field=patient_id}}</th>
          <th rowspan="2" style="width: 30%;">{{mb_label class=CConsultation field=_date}}: {{mb_label class=CConsultation field=tarif}}</th>
          
          {{if $conf.dPccam.CCodeCCAM.use_cotation_ccam == "1"}}
            <th rowspan="2" class="narrow">{{mb_title class=CFactureCabinet field=_secteur1}}</th>
            <th rowspan="2" class="narrow">{{mb_title class=CFactureCabinet field=_secteur2}}</th>
          {{/if}}
          
          {{if @$modules.tarmed->_can->read && $conf.tarmed.CCodeTarmed.use_cotation_tarmed == "1"}}
            <th rowspan="2" class="narrow">Montant</th>
            <th rowspan="2" class="narrow">Remise</th>
          {{/if}}
          
          <th rowspan="2" class="narrow">{{mb_title class=CFactureCabinet field=_montant_total}}</th>
          <th rowspan="2" class="narrow">{{mb_label class=CReglement field=mode}}</th>
          <th colspan="2" class="narrow">{{mb_title class=CReglement field=emetteur}}</th>
        </tr>
        {{if $conf.dPccam.CCodeCCAM.use_cotation_ccam == "1"}}
          <tr>
            <th class="narrow">{{tr}}CReglement.emetteur.patient{{/tr}}</th>
            <th class="narrow">{{tr}}CReglement.emetteur.tiers{{/tr}}</th>
          </tr>
        {{elseif @$modules.tarmed->_can->read && $conf.tarmed.CCodeTarmed.use_cotation_tarmed == "1"}}
          <th class="narrow" colspan="2">{{tr}}CReglement.emetteur.patient{{/tr}}</th>
        {{/if}}
        
        {{foreach from=$_date.reglements item=_reglement}}
        {{assign var=_object value=$_reglement->_ref_object}}
        {{assign var=facture value=$_reglement->_ref_facture}}
        {{assign var=prat_id value=$facture->_ref_praticien->_id}}
        
        <tr>
          {{if $facture->_id}}
          <td>
            <strong onmouseover="ObjectTooltip.createEx(this, '{{$facture->_guid}}')">
              {{$facture}}
            </strong>
          </td>
          <td>{{mb_include module=system template=inc_object_notes object=$facture}}</td>
          {{else}}
          <td colspan="2">
            <strong>{{$facture}}</strong>
          </td>
          {{/if}}
          
          <td class="text">
            {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$facture->_ref_praticien}}
          </td>

          <td class="text">
            {{assign var=patient value=$facture->_ref_patient}}
            <span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">{{$patient}}</span>
          </td>

          <td class="text">
            {{foreach from=$facture->_ref_consults item=_consult}}
            <div>
              <span onmouseover="ObjectTooltip.createEx(this, '{{$_consult->_guid}}')">
                {{mb_value object=$_consult field=_date}}: {{mb_value object=$_consult field=tarif}}
              </span>
            </div>   
            {{foreachelse}}
            <div class="empty">{{tr}}CConsultation.none{{/tr}}</div>
            {{/foreach}}
          </td>
          
          {{if $conf.dPccam.CCodeCCAM.use_cotation_ccam == "1"}}
            <td {{if $facture->_secteur1 < 0.001}} class="empty" {{/if}} style="text-align: right;">{{mb_value object=$facture field=_secteur1}}</td>
            <td {{if $facture->_secteur2 < 0.001}} class="empty" {{/if}} style="text-align: right;">{{mb_value object=$facture field=_secteur2}}</td>
            <td {{if $facture->_montant_total    < 0.001}} class="empty" {{/if}} style="text-align: right;">{{mb_value object=$facture field=_montant_total   }}</td>
          {{/if}}

          {{if @$modules.tarmed->_can->read && $conf.tarmed.CCodeTarmed.use_cotation_tarmed == "1"}}
            <td style="text-align: right;">{{mb_value object=$facture field=_montant_sans_remise}}</td>
            <td style="text-align: right;">{{mb_value object=$facture field=remise}}</td>
            <td style="text-align: right;">{{mb_value object=$facture field=_montant_avec_remise}}</td>
          {{/if}}

          <td style="text-align: center;">
            <span onmouseover="ObjectTooltip.createEx(this, '{{$_reglement->_guid}}')">{{mb_value object=$_reglement field=mode}}</span>
          </td>
          
          <td style="text-align: right;">
            {{if $_reglement->emetteur == "patient"}}
              {{mb_value object=$_reglement field=montant}}
            {{/if}}
          </td>
          
          {{if $conf.dPccam.CCodeCCAM.use_cotation_ccam == "1"}}
            <td  style="text-align: right;">
              {{if $_reglement->emetteur == "tiers"}}
                {{mb_value object=$_reglement field=montant}}
              {{/if}}
            </td>
          {{/if}}
        </tr>
        {{/foreach}}
        <tr>
          <td colspan="7" />
          <td><strong>{{tr}}Total{{/tr}}</strong></td>
          <td><strong>{{$_date.total.patient|currency}} </strong></td>
          {{if $conf.dPccam.CCodeCCAM.use_cotation_ccam == "1"}}
            <td><strong>{{$_date.total.tiers|currency}}</strong></td>
          {{/if}}
        </tr>
      </table>
    </td>
  </tr>
  {{/foreach}}
  {{/if}}
</table>
      