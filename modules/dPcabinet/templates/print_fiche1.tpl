    </td>
  </tr>
</table>

{{assign var="patient" value=$consult->_ref_patient}}
{{assign var="consult_anesth" value=$consult->_ref_consult_anesth}}
{{assign var="sejour" value=$consult_anesth->_ref_operation->_ref_sejour}}
{{assign var="operation" value=$consult_anesth->_ref_operation}}

{{assign var=const_med value=$patient->_ref_constantes_medicales}}
{{assign var=dossier_medical value=$patient->_ref_dossier_medical}}
{{assign var=ant value=$dossier_medical->_ref_antecedents}}
<table class="form" id="admission">
  <tr>
    <td colspan="2">
      <!-- Bordereau d'en-t�te -->
      <table width="100%">
        <tr>
          <th class="title" colspan="6">
            <a href="#" onclick="window.print()">
              Dossier d'anesth�sie de {{$patient->_view}}
            </a>
          </th>
        </tr>
        <tr>
          <th>Telephone</th>
          <td style="white-space: nowrap;">{{mb_value object=$patient field="tel"}}</td>
          <th>Age</th>
          <td style="white-space: nowrap;">{{$patient->_age}} ans</td>
          <th>C.A.</th>
          <td>
            Dr {{$consult->_ref_chir->_view}}
            - le {{mb_value object=$consult->_ref_plageconsult field="date"}}</td>
        </tr>
        <tr>
          <th>Mobile</th>
          <td style="white-space: nowrap;">{{mb_value object=$patient field="tel2"}}</td>
          <th>Taille</th>
          <td style="white-space: nowrap;">{{if $const_med->taille}}{{$const_med->taille}} cm{{else}}-{{/if}}</td>
          <th>S�jour</th>
          <td>
            {{if $sejour->_id}}
            {{mb_value object=$sejour field="type"}}
            du {{mb_value object=$sejour field="_entree"}}
            au {{mb_value object=$sejour field="_sortie"}}
            {{else}}
            -
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>Profession</th>
          <td>{{$patient->profession}}</td>
          <th>Poids</th>
          <td style="white-space: nowrap;">{{if $const_med->poids}}{{$const_med->poids}} kg{{else}}-{{/if}}</td>
          <th>Intervention</th>
          <td>
            {{if $operation->_id}}
            Dr {{$operation->_ref_chir->_view}} - le {{$operation->_datetime|date_format:"%d/%m/%Y"}}
            {{else}}
            -
            {{/if}}
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td width="50%">
      <!-- Examens compl�mentaires / Traitements / Allergies -->
      <table width="100%">
        <tr>
          <th class="category">Examens compl�mentaires</th>
        </tr>
        {{foreach from=$consult->_types_examen key=curr_type item=list_exams}}
        {{if $list_exams|@count}}
        <tr>
          <td>
            {{tr}}CExamComp.realisation.{{$curr_type}}{{/tr}}
          </td>
        </tr>
        <tr>
          <td>
            <ul>
              {{foreach from=$list_exams item=curr_examcomp}}
              <li>
                {{$curr_examcomp->examen}}
                {{if $curr_examcomp->fait}}
                  (Fait)
                {{else}}
                  (A Faire)
                {{/if}}
              </li>
              {{/foreach}}
            </ul>
          </td>
        </tr>
       {{/if}}
       {{foreachelse}}
       <tr>
        <td>
          Pas d'examen compl�mentaire
        </td>
      </tr>
      {{/foreach}}
        <tr>
          <th class="category">Traitements</th>
        </tr>
        <tr>
          <td>
            <ul>
              {{foreach from=$dossier_medical->_ref_traitements item=curr_trmt}}
              <li>
                {{if $curr_trmt->fin}}
                  Du {{$curr_trmt->debut|date_format:"%d/%m/%Y"}} au {{$curr_trmt->fin|date_format:"%d/%m/%Y"}} :
                {{elseif $curr_trmt->debut}}
                  Depuis le {{$curr_trmt->debut|date_format:"%d/%m/%Y"}} :
                {{/if}}
                <i>{{$curr_trmt->traitement}}</i>
              </li>
              {{foreachelse}}
              <li>Pas de traitements</li>
              {{/foreach}}
            </ul>
          </td>
        </tr>
        <tr>
          <th class="category">Allergies</th>
        </tr>
        <tr>
          <td style="font-weight: bold; white-space: normal; font-size:130%;">
          {{if $dossier_medical->_ref_antecedents && array_key_exists('alle', $dossier_medical->_ref_antecedents)}}
            {{foreach from=$dossier_medical->_ref_antecedents.alle item=currAnt}}
              <ul>
                <li> 
                  {{if $currAnt->date|date_format:"%d/%m/%Y"}}
                    {{$currAnt->date|date_format:"%d/%m/%Y"}} :
                  {{/if}}
                  {{$currAnt->rques}} 
                </li>
              </ul>
            {{/foreach}}
          {{else}}
            <ul>
              <li>Pas d'allergie saisie</li>
            </ul>
          {{/if}}
          </td>
        </tr>
      </table>
    </td>
    <td width="50%">
      <!-- Docs �dit�s / Intubation / Pr�m�dication -->
      <table width="100%">
        <tr>
          <th class="category">Documents �dit�s</th>
        </tr>
        <tr>
          <td>
            {{foreach from=$consult->_ref_documents item=currDoc}}
              {{$currDoc->nom}} ;
            {{foreachelse}}
            Aucun Document
            {{/foreach}}
            </ul>
          </td>
        </tr>
        <tr>
          <th class="category">Intubation</th>
        </tr>
        <tr>
          <td>
            <strong>Mallampati :</strong>
            {{tr}}CConsultAnesth.mallampati.{{$consult->_ref_consult_anesth->mallampati}}{{/tr}}
            <br />
            <strong>Ouverture de la bouche :</strong>
            {{tr}}CConsultAnesth.bouche.{{$consult->_ref_consult_anesth->bouche}}{{/tr}}
            <br />
            <strong>Distance thyro-mentoni�re :</strong>
            {{tr}}CConsultAnesth.distThyro.{{$consult->_ref_consult_anesth->distThyro}}{{/tr}}
            <br />
            <strong>Etat bucco-dentaire :</strong>
            {{$consult->_ref_consult_anesth->etatBucco|nl2br}}
            <br />
            <strong>Conclusion :</strong>
            {{$consult->_ref_consult_anesth->conclusion|nl2br}}
            <br />
            {{if $consult->_ref_consult_anesth->_intub_difficile}}
            <span style="font-weight: bold; text-align:center; color:#F00;">
              Intubation Difficile Pr�visible
            </span>
            {{else}}
            <span style="font-weight: bold; text-align:center;">
              Pas Intubation Difficile Pr�visible
            </span>        
            {{/if}}
          </td>
        </tr>
        <tr>
          <th class="category">Pr�m�dication</th>
        </tr>
        <tr>
          <td>
            {{$consult->_ref_consult_anesth->premedication|nl2br}}
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td colspan="2"><hr /></td>
  </tr>
  <tr>
    <td>
      <!-- Atcd chirurgicaux / anesth�siques -->
      <table width="100%">
        <tr>
          <th class="category">ATCD Chirurgicaux</th>
        </tr>
        <tr>
          <td>
            {{if $dossier_medical->_ref_antecedents && array_key_exists('chir', $dossier_medical->_ref_antecedents)}}
            <ul>
            {{foreach from=$dossier_medical->_ref_antecedents.chir item=currAnt}}
              <li> 
                {{if $currAnt->date|date_format:"%d/%m/%Y"}}
                  {{$currAnt->date|date_format:"%d/%m/%Y"}} :
                {{/if}}
                {{$currAnt->rques}} 
              </li>
            {{/foreach}}
            </ul>
            {{/if}}
          </td>
        </tr>
        <tr>
          <th class="category">ATCD Anesth�siques</th>
        </tr>
        <tr>
          <td>
            {{if $dossier_medical->_ref_antecedents && array_key_exists('anesth', $dossier_medical->_ref_antecedents)}}
            <ul>
            {{foreach from=$dossier_medical->_ref_antecedents.anesth item=currAnt}}
              <li> 
                {{if $currAnt->date|date_format:"%d/%m/%Y"}}
                  {{$currAnt->date|date_format:"%d/%m/%Y"}} :
                {{/if}}
                {{$currAnt->rques}} 
              </li>
            {{/foreach}}
            </ul>
            {{/if}}
          </td>
        </tr>
      </table>
    </td>
    <td>
      <!-- Atcd Cardio / Uro-Nephro / NeuroPsy / Endoc / Gyneco / Autres -->
      <table width="100%">
        <tr>
          <th class="category">Examen Cardiovasculaire</th>
        </tr>
        <tr>
          <td>
            {{$consult_anesth->examenCardio}}
            {{if $dossier_medical->_ref_antecedents && array_key_exists('cardio', $dossier_medical->_ref_antecedents)}}
            <ul>
            {{foreach from=$dossier_medical->_ref_antecedents.cardio item=currAnt}}
              <li> 
                {{if $currAnt->date|date_format:"%d/%m/%Y"}}
                  {{$currAnt->date|date_format:"%d/%m/%Y"}} :
                {{/if}}
                {{$currAnt->rques}} 
              </li>
            {{/foreach}}
            </ul>
            {{/if}}
          </td>
        </tr>
        <tr>
          <th class="category">Examen Pulmonaire</th>
        </tr>
        <tr>
          <td>
            {{$consult_anesth->examenPulmo}}
            {{if $dossier_medical->_ref_antecedents && array_key_exists('pulmo', $dossier_medical->_ref_antecedents)}}
            <ul>
            {{foreach from=$dossier_medical->_ref_antecedents.pulmo item=currAnt}}
              <li> 
                {{if $currAnt->date|date_format:"%d/%m/%Y"}}
                  {{$currAnt->date|date_format:"%d/%m/%Y"}} :
                {{/if}}
                {{$currAnt->rques}} 
              </li>
            {{/foreach}}
            </ul>
            {{/if}}
          </td>
        </tr>
        <tr>
          <th class="category">Uro-nephrologie</th>
        </tr>
        <tr>
          <td>
            {{if $dossier_medical->_ref_antecedents && array_key_exists('uro', $dossier_medical->_ref_antecedents)}}
            <ul>
            {{foreach from=$dossier_medical->_ref_antecedents.uro item=currAnt}}
              <li> 
                {{if $currAnt->date|date_format:"%d/%m/%Y"}}
                  {{$currAnt->date|date_format:"%d/%m/%Y"}} :
                {{/if}}
                {{$currAnt->rques}} 
              </li>
            {{/foreach}}
            </ul>
            {{/if}}
          </td>
        </tr>
        <tr>
          <th class="category">Neuro-psychiatrie</th>
        </tr>
        <tr>
          <td>
            {{if $dossier_medical->_ref_antecedents && array_key_exists('neuropsy', $dossier_medical->_ref_antecedents)}}
            <ul>
            {{foreach from=$dossier_medical->_ref_antecedents.neuropsy item=currAnt}}
              <li> 
                {{if $currAnt->date|date_format:"%d/%m/%Y"}}
                  {{$currAnt->date|date_format:"%d/%m/%Y"}} :
                {{/if}}
                {{$currAnt->rques}} 
              </li>
            {{/foreach}}
            </ul>
            {{/if}}
          </td>
        </tr>
        <tr>
          <th class="category">Endocrinologie</th>
        </tr>
        <tr>
          <td>
            {{if $dossier_medical->_ref_antecedents && array_key_exists('endocrino', $dossier_medical->_ref_antecedents)}}
            <ul>
            {{foreach from=$dossier_medical->_ref_antecedents.endocrino item=currAnt}}
              <li> 
                {{if $currAnt->date|date_format:"%d/%m/%Y"}}
                  {{$currAnt->date|date_format:"%d/%m/%Y"}} :
                {{/if}}
                {{$currAnt->rques}} 
              </li>
            {{/foreach}}
            </ul>
            {{/if}}
          </td>
        </tr>
        <tr>
          <th class="category">Gyn�cologie</th>
        </tr>
        <tr>
          <td>
            {{if $dossier_medical->_ref_antecedents && array_key_exists('gyn', $dossier_medical->_ref_antecedents)}}
            <ul>
            {{foreach from=$dossier_medical->_ref_antecedents.gyn item=currAnt}}
              <li> 
                {{if $currAnt->date|date_format:"%d/%m/%Y"}}
                  {{$currAnt->date|date_format:"%d/%m/%Y"}} :
                {{/if}}
                {{$currAnt->rques}} 
              </li>
            {{/foreach}}
            </ul>
            {{/if}}
          </td>
        </tr>
        <tr>
          <th class="category">Autres</th>
        </tr>
        <tr>
          <td>
            {{$consult->examen}}
            <ul>
              {{foreach from=$dossier_medical->_ref_antecedents key=type_name item=curr_type}}
              {{if $type_name != 'alle'
                && $type_name != 'chir'
                && $type_name != 'anesth'
                && $type_name != 'cardio'
                && $type_name != 'uro'
                && $type_name != 'neuropsy'
                && $type_name != 'endocrino'
                && $type_name != 'gyn'}}
                {{foreach from=$curr_type item=currAnt}}
                <li> 
                  {{if $currAnt->date|date_format:"%d/%m/%Y"}}
                    {{$currAnt->date|date_format:"%d/%m/%Y"}} :
                  {{/if}}
                  {{$currAnt->rques}} 
                </li>
                {{/foreach}}
              {{/if}}
              {{/foreach}}
            </ul>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <!-- Non impression de la biologie pour l'instant
  <tr>
    <td colspan="2">
      <!-- Biologie -->
      <table width="100%">
        <tr>
          <th class="category">Biologie</th>
        </tr>
      
      </table>
    </td>
  </tr>
  -->
</table>

<table class="main">
  <tr>
    <td>