{{assign var="patient" value=$consult->_ref_patient}}
{{assign var="consult_anesth" value=$consult->_ref_consult_anesth}}
    </td>
  </tr>
</table>
<table class="form" id="admission">
  <tr>
    <td>
      <table width="100%" style="font-size: 110%;">
        <tr>
          <th class="title" colspan="4">
            <a href="javascript:window.print()">
              Consultation pr�-anesth�sique
            </a>
          </th>
        </tr>
        <tr>
          <th>Date </th>
          <td>{{$consult->_ref_plageconsult->date|date_format:"%A %d %B %Y"}}</td>
          <th>Anesth�siste </th>
          <td>Dr. {{$consult->_ref_chir->_view}}</td>
        </tr>
      </table>
    </td>
  </tr>
</table>


<table class="form" id="admission">
  <tr>
    <td colspan="2">
      <table width="100%" style="font-size: 100%;">
        <tr>
          <th class="category" colspan="2">Intervention</th>
        </tr>
        <tr>
          <td colspan="2">
          {{if $consult_anesth->operation_id}}
          
            Intervention le <strong>{{$consult_anesth->_ref_operation->_ref_plageop->date|date_format:"%a %d %b %Y"}}</strong>
            <ul>
              {{if $consult_anesth->_ref_operation->libelle}}
                <li><em>[{{$consult_anesth->_ref_operation->libelle}}]</em></li>
              {{/if}}
              {{foreach from=$consult_anesth->_ref_operation->_ext_codes_ccam item=curr_code}}
              <li><em>{{$curr_code->libelleLong}}</em> ({{$curr_code->code}}) (cot� {{$consult_anesth->_ref_operation->cote}})</li>
              {{/foreach}}
            </ul>
          {{else}}
            Aucune Intervention
          {{/if}}
          </td>
        </tr>
        <tr>
          <td class="halfPane">
            {{if $consult_anesth->operation_id}}
            par le <strong>Dr. {{$consult_anesth->_ref_operation->_ref_chir->_view}}</strong><br />
            Position : <strong>{{tr}}{{$consult_anesth->position}}{{/tr}}</strong><br />
            Admission : {{tr}}{{$consult_anesth->_ref_operation->_ref_sejour->type}}{{/tr}}{{if $consult_anesth->_ref_operation->_ref_sejour->type=="comp"}} {{$consult_anesth->_ref_operation->_ref_sejour->_duree_prevue}} jour(s){{/if}}
            <br /><br />
            Anesth�sie pr�vue :
            <strong>{{$consult_anesth->_ref_operation->_lu_type_anesth}}</strong>
            
            {{/if}}
          </td>
          <td class="halfPane">
            <strong>Techniques Compl�mentaires</strong>
            <ul>
              {{foreach from=$consult_anesth->_ref_techniques item=curr_tech}}
              <li>
                {{$curr_tech->technique}}
              </li>
              {{foreachelse}}
              <li>Pas de technique compl�mentaire</li>
              {{/foreach}}
            </ul>
          </td>
        </tr>
      </table>    
    </td>
  </tr>

  <tr>
    <td class="halfPane">
      <table width="100%" style="font-size: 100%;">
        <tr>
          <th class="category" colspan="2">Informations sur le patient</th>
        </tr>
        <tr>
          <td colspan="2">{{$patient->_view}}</td>
        </tr>
        {{if $patient->nom_jeune_fille}}
        <tr>
          <th>Nom de jeune fille</th>
          <td>{{$patient->nom_jeune_fille}}</td>
        </tr>
        {{/if}}
        <tr>
          <td colspan="2">
            N�{{if $patient->sexe != "m"}}e{{/if}} le {{$patient->_jour}}/{{$patient->_mois}}/{{$patient->_annee}}
            ({{$patient->_age}} ans)
            - sexe {{if $patient->sexe == "m"}} masculin {{else}} f�minin {{/if}}<br />
            <strong>{{$consult->_ref_consult_anesth->poid}} kg</strong> - <strong>{{$consult->_ref_consult_anesth->taille}} cm</strong> - IMC : <strong>{{$consult->_ref_consult_anesth->_imc}}</strong>
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <table style="font-size: 100%;">
              <tr>
                <th class="NotBold">Groupe sanguin</th>
                <td class="Bold" style="white-space: nowrap;font-size:130%;">&nbsp;{{$consult->_ref_consult_anesth->groupe}} &nbsp;{{$consult->_ref_consult_anesth->rhesus}}</td>
              </tr>
              <tr>
                <th class="NotBold">RAI</th>
                <td class="Bold" style="white-space: nowrap;font-size:130%;">&nbsp;{{$consult->_ref_consult_anesth->rai}}</td>
              </tr>
              <tr>
                <th class="NotBold">ASA</th>
                <td class="Bold">{{$consult_anesth->ASA}}</td>
              </tr>
              <tr>
                <th class="NotBold">VST</th>
                <td class="Bold" style="white-space: nowrap;">{{$consult->_ref_consult_anesth->_vst}} ml</td>
              </tr>
              <tr>
                <th class="NotBold">PSA</th>
                <td class="Bold" style="white-space: nowrap;">{{$consult->_ref_consult_anesth->_psa}} ml/GR</td>
                <td colspan="2"></td>
              </tr>
              <tr>
                <th class="NotBold">Tabac</th>
                <td colspan="3" class="Bold">{{$consult->_ref_consult_anesth->tabac|nl2br}}</td>
              </tr>
              <tr>
                <th class="NotBold">Oenolisme</th>
                <td colspan="3" class="text Bold">{{$consult->_ref_consult_anesth->oenolisme|nl2br}}</td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
    <td class="halfPane">
      <table width="100%" style="font-size: 100%;">
        <tr>
          <th class="category">Ant�c�dents</th>
        </tr>
        <tr>
          <td>
            {{if $patient->_ref_antecedents}}
              {{foreach name=nameForeach from=$listAnt key=keyAnt item=currTypeAnt}}
              {{if $currTypeAnt}}
              {{if !$smarty.foreach.nameForeach.first}}
              <br />
              {{/if}}
              <strong>{{tr}}{{$keyAnt}}{{/tr}}</strong>
              {{foreach from=$currTypeAnt item=currAnt}}
              <ul>
                <li>
                {{if $currAnt->date|date_format:"%d/%m/%Y"}}
                {{$currAnt->date|date_format:"%d/%m/%Y"}} :
                {{/if}}
                {{$currAnt->rques}}
                </li>
              </ul>
              {{/foreach}}
              {{/if}}
              {{/foreach}}
            {{else}}
            <li>Pas d'ant�c�dents</li>
            {{/if}}

            {{if $patient->_codes_cim10}}
            <br />
            <strong>Diagnostics du patient</strong>
            <ul>
              {{foreach from=$patient->_codes_cim10 item=curr_code}}
              <li>
                {{$curr_code->code}}: {{$curr_code->libelle}}
              </li>
              {{foreachelse}}
              <li>Pas de diagnostic</li>
              {{/foreach}}
            </ul>
            {{/if}}
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td class="halfPane">
      <table width="100%" style="font-size: 100%;">
        <tr>
          <th class="category">Traitements</th>
        </tr>
        <tr>
          <td>
            <ul>
              {{foreach from=$patient->_ref_traitements item=curr_trmt}}
              <li>
                {{if $curr_trmt->fin}}
                  Du {{$curr_trmt->debut|date_format:"%d/%m/%Y"}} au {{$curr_trmt->fin|date_format:"%d/%m/%Y"}}
                {{else}}
                  Depuis le {{$curr_trmt->debut|date_format:"%d/%m/%Y"}}
                {{/if}}
                : <i>{{$curr_trmt->traitement}}</i>
              </li>
              {{foreachelse}}
              <li>Pas de traitements</li>
              {{/foreach}}
            </ul>
          </td>
        </tr>
      </table>
    </td>
    <td class="halfPane">
      <table width="100%" style="font-size: 100%; padding-bottom: 15px;">
        <tr>
          <th class="category" colspan="6">Examens Clinique</th>
        </tr>
        <tr>
          <th class="NotBold">Pouls</th>
          <td class="Bold" style="white-space: nowrap;">
            {{$consult->_ref_consult_anesth->pouls}} / min
          </td>
          <th class="NotBold">TA</th>
          <td class="Bold" style="white-space: nowrap;">
            {{$consult->_ref_consult_anesth->tasys}} / {{$consult->_ref_consult_anesth->tadias}} cm Hg
          </td>
          <th class="NotBold">Spo2</th>
          <td class="Bold" style="white-space: nowrap;">
            {{$consult->_ref_consult_anesth->spo2}} %
          </td>
        </tr>
        <tr>
          <th class="NotBold">Examens</th>
          <td colspan="5" class="text Bold">{{$consult->examen|nl2br}}</td>
        </tr>
      </table>
    </td>
  </tr>
</table>

<br style="page-break-after: always;" />
<table class="form" id="admission">
  <tr>
    <td>
      <table width="100%" style="font-size: 110%;">
        <tr>
          <th class="title" colspan="4">
            <a href="javascript:window.print()">
              Consultation pr�-anesth�sique
            </a>
          </th>
        </tr>
        <tr>
          <th>Date </th>
          <td>{{$consult->_ref_plageconsult->date|date_format:"%A %d %B %Y"}}</td>
          <th>Anesth�siste </th>
          <td>Dr. {{$consult->_ref_chir->_view}}</td>
        </tr>
        <tr>
          <th>Patient </th>
          <td>{{$patient->_view}}</td>
          <td colspan="2"></td>
        </tr>
      </table>
    </td>
  </tr>
</table>
        
<table class="form" id="admission">
  <tr>
    <td>
      <table width="100%" style="font-size: 100%;">
        <tr>
          <th colspan="3" class="category">Conditions d'intubation</th>
        </tr>
        <tr>
          {{if $consult->_ref_consult_anesth->mallampati}}
          <td rowspan="4" class="button" style="white-space: nowrap;">
            <img src="modules/{{$m}}/images/mallampati/{{$consult->_ref_consult_anesth->mallampati}}.gif" alt="{{tr}}{{$consult->_ref_consult_anesth->mallampati}}{{/tr}}" />
            <br />Mallampati<br />de {{tr}}{{$consult->_ref_consult_anesth->mallampati}}{{/tr}}
          </td>
          {{/if}}
          <th class="NotBold">Ouverture de la bouche</th>
          <td class="Bold">
            {{tr}}{{$consult->_ref_consult_anesth->bouche}}{{/tr}}
          </td>
        </tr>
        <tr>
          <th class="NotBold">Distance thyro-mentonni�re</th>
          <td class="Bold">{{tr}}{{$consult->_ref_consult_anesth->distThyro}}{{/tr}}</td>
        </tr>
        <tr>
          <th class="NotBold">Etat bucco-dentaire</th>
          <td class="text Bold">{{$consult->_ref_consult_anesth->etatBucco}}</td>
        </tr>
        <tr>
          <th class="NotBold">Conclusion</th>
          <td class="text Bold">{{$consult->_ref_consult_anesth->conclusion}}</td>
        </tr>
        
        <tr>
        {{if $consult->_ref_consult_anesth->_intub_difficile}}
          <td colspan="3" class="Bold" style="text-align:center;color:#F00;">
            Intubation Difficile Pr�visible
          </td>
        {{else}}
          <td colspan="3" class="Bold" style="text-align:center;">
            Pas Intubation Difficile Pr�visible
          </td>        
        {{/if}}
        </tr>
      </table>    

      <table width="100%" style="font-size: 100%;">
        <tr>
          <th class="category" colspan="6">
            Examens Compl�mentaires
          </th>
        </tr>
        <tr>
          <th class="NotBold">Hb</th>
          <td class="Bold" style="white-space: nowrap;">
            {{$consult->_ref_consult_anesth->hb}} g/dl
          </td>
          <th class="NotBold">Cr�atinine</th>
          <td class="Bold" style="white-space: nowrap;">
            {{$consult->_ref_consult_anesth->creatinine}} mg/l
          </td>
          <th class="NotBold">TP</th>
          <td class="Bold" style="white-space: nowrap;">
            {{$consult->_ref_consult_anesth->tp}} %
          </td>
        </tr>
        <tr>
          <th class="NotBold">Ht</th>
          <td class="Bold">
            {{$consult->_ref_consult_anesth->ht}} %
          </td>
          <th class="NotBold">
            Clairance de Cr�atinine
          </th>
          <td class="Bold" style="white-space: nowrap;">
            {{$consult->_ref_consult_anesth->_clairance}} ml/min
          </td>
          <th class="NotBold">TCA</th>
          <td class="Bold" style="white-space: nowrap;">
            {{$consult->_ref_consult_anesth->tca_temoin}} s / {{$consult->_ref_consult_anesth->tca}} s
          </td>
        </tr>
        <tr>
          <th class="NotBold">Ht final</th>
          <td class="Bold">
            {{$consult->_ref_consult_anesth->ht_final}} %
          </td>
          <th class="NotBold">Na+</th>
          <td class="Bold" style="white-space: nowrap;">
            {{$consult->_ref_consult_anesth->na}} mmol/l
          </td>
          <th class="NotBold">TS Ivy</th>
          <td class="Bold" style="white-space: nowrap;">
            {{$consult->_ref_consult_anesth->tsivy|date_format:"%Mm%Ss"}}
          </td>
        </tr>
        <tr>
          <th class="NotBold">Plaquettes</th>
          <td class="Bold" style="white-space: nowrap;">
            {{$consult->_ref_consult_anesth->plaquettes}}
          </td>
          <th class="NotBold">K+</th>
          <td class="Bold" style="white-space: nowrap;">
            {{$consult->_ref_consult_anesth->k}} mmol/l
          </td>
          <th class="NotBold">ECBU</th>
          <td class="Bold">
            {{$consult->_ref_consult_anesth->ecbu}}
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td>
      <table width="100%" style="font-size: 100%;">
        <tr>
          <th>
            Examens Compl�mentaires
          </th>
          <td>
            <ul>
              {{foreach from=$consult->_ref_examcomp item=curr_examcomp}}
              <li>
                {{$curr_examcomp->examen}} 
                {{if $curr_examcomp->fait}}
                  (Fait)
                {{else}}
                  (A Faire)
                {{/if}}
              </li>
              {{foreachelse}}
              <li>Pas d'examen compl�mentaire</li>
              {{/foreach}}
            </ul>
          </td>
        </tr>
      </table>
      <table width="100%" style="font-size: 100%;padding-bottom: 10px;">
        <tr>
          <th class="category">
            Liste des Documents Edit�s
          </th>
        </tr>
        <tr>
          <td>
            <ul>
            {{foreach from=$consult->_ref_documents item=currDoc}}
              <li>{{$currDoc->nom}}<br />
            {{foreachelse}}
            Aucun Document
            {{/foreach}}
            </ul>
          </td>
        </tr>
      </table>
      <table width="100%" style="font-size: 100%;padding-bottom: 10px;">
        <tr>
          <th class="category">
            Pr�m�dication
          </th>
        </tr>
        <tr>
          <td>
            {{$consult->_ref_consult_anesth->premedication|nl2br}}
          </td>
        </tr>
      </table>
      <table width="100%" style="font-size: 100%;">
        <tr>
          <th class="category">
            Pr�paration pr�-op�ratoire
          </th>
        </tr>
        <tr>
          <td>
            {{$consult->_ref_consult_anesth->prepa_preop|nl2br}}
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>

<br style="page-break-after: always;" />
<table class="form" id="admission">
  <tr>
    <td>
      <table width="100%" style="font-size: 110%;">
        <tr>
          <th class="title" colspan="4">
            <a href="javascript:window.print()">
              Visite pr�-anesth�sique
            </a>
          </th>
        </tr>
        <tr>
          <th>Date </th>
          <td>{{$consult->_ref_plageconsult->date|date_format:"%A %d %B %Y"}}</td>
          <th>Anesth�siste </th>
          <td>Dr. {{$consult->_ref_chir->_view}}</td>
        </tr>
        <tr>
          <th>Patient </th>
          <td>{{$patient->_view}}</td>
          <td colspan="2"></td>
        </tr>
      </table>
    </td>
  </tr>
</table>

<table class="form" id="admission">
  <tr>
    <td>
      <table width="100%" style="font-size: 100%;">
        <tr>
          <td class="text">
            {{$consult->rques|nl2br}}
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<table class="main">
  <tr>
    <td>