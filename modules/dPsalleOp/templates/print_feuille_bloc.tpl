<table class="form" id="admission">
  <tr>
    <td>
      <table width="100%" style="font-size: 110%;">
        <tr>
          <th class="title" colspan="4">
            <a href="#" onclick="window.print()">
              Feuille de Bloc
            </a>
          </th>
        </tr>
      </table>
    </td>
  </tr>
</table>

<table class="form" id="admission">
  <tr>
    <td class="halfPane">
      <table width="100%" style="font-size: 100%;">
        <tr>
          <th class="category" colspan="2">Informations sur le patient</th>
        </tr>
        <tr>
          <td colspan="2">{{$patient->_view}}</td>
        </tr>
        <tr>
          <td colspan="2">
            N�{{if $patient->sexe != "m"}}e{{/if}} le {{$patient->_jour}}/{{$patient->_mois}}/{{$patient->_annee}}
            ({{$patient->_age}} ans)
            - sexe {{if $patient->sexe == "m"}} masculin {{else}} f�minin {{/if}}
          </td>
        </tr>
      </table>
      
      <table width="100%" style="font-size: 100%;">
        <tr>
          <th class="category" colspan="2">Intervention</th>
        </tr>

        <tr>
          <th>Date</th>
          <td class="greedyPane">
            {{$operation->_ref_plageop->date|date_format:"%A %d %B %Y"}}
          </td>
        </tr>
        <tr>
          <th>Libell�</th>
          <td class="text">
            {{if $operation->libelle}}
            {{$operation->libelle}}
            {{else}}
            &mdash;
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>C�t�</th>
          <td>
            {{tr}}COperation.cote.{{$operation->cote}}{{/tr}}
          </td>
        </tr>
        <tr>
          <th>Chirurgien</th>
          <td class="text">
            Dr. {{$operation->_ref_chir->_view}}
          </td>
        </tr>
        
        <tr>
          <th>Anesth�siste</th>
          <td class="text">
            {{if $operation->_ref_anesth->user_id}}
            Dr. {{$operation->_ref_anesth->_view}}
            {{else}}
            &mdash;
            {{/if}}
          </td>
        </tr>
        
        <tr>
          <th>Type d'anesth�sie</th>
          <td class="text">
            {{if $operation->type_anesth}}
            {{$operation->_lu_type_anesth}}
            {{else}}
            &mdash;
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>Personnel</th>
          <td class="text">
            Non Disponible
          </td>
        </tr>
        <tr>
          <th>Mat�riel</th>
          <td class="text">
            {{if $operation->materiel}}
            {{$operation->materiel|nl2br}}
            {{else}}
            &mdash;
            {{/if}}
          </td>
        </tr>       
        <tr>
          <th>Remarques</th>
          <td class="text">
            {{if $operation->rques}}
            {{$operation->rques|nl2br}}
            {{else}}
            &mdash;
            {{/if}}
          </td>
        </tr>

      </table>
      
    </td>
    <td class="halfPane">
      <table width="100%" style="font-size: 100%;">
        <tr>
          <th class="category" colspan="2">Horaires</th>
        </tr>
        
        <tr>
          <th>Entr�e en salle</th>
          <td class="halfPane">
            {{if $operation->entree_salle}}
              {{$operation->entree_salle|date_format:"%Hh%M"}}
            {{else}}
            &mdash;
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>Debut d'induction</th>
          <td class="halfPane">
            {{if $operation->induction_debut}}
              {{$operation->induction_debut|date_format:"%Hh%M"}}
            {{else}}
            &mdash;
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>Fin d'induction</th>
          <td class="halfPane">
            {{if $operation->induction_fin}}
              {{$operation->induction_fin|date_format:"%Hh%M"}}
            {{else}}
            &mdash;
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>Pose du garrot</th>
          <td class="halfPane">
            {{if $operation->pose_garrot}}
              {{$operation->pose_garrot|date_format:"%Hh%M"}}
            {{else}}
            &mdash;
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>D�but de l'intervention</th>
          <td class="halfPane">
            {{if $operation->debut_op}}
              {{$operation->debut_op|date_format:"%Hh%M"}}
            {{else}}
            &mdash;
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>Fin de l'intervention</th>
          <td class="halfPane">
            {{if $operation->fin_op}}
              {{$operation->fin_op|date_format:"%Hh%M"}}
            {{else}}
            &mdash;
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>Retrait du garrot</th>
          <td class="halfPane">
            {{if $operation->retrait_garrot}}
              {{$operation->retrait_garrot|date_format:"%Hh%M"}}
            {{else}}
            &mdash;
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>Sortie de salle</th>
          <td class="halfPane">
            {{if $operation->sortie_salle}}
              {{$operation->sortie_salle|date_format:"%Hh%M"}}
            {{else}}
            &mdash;
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>Entr�e salle r�veil</th>
          <td class="halfPane">
            {{if $operation->entree_reveil}}
              {{$operation->entree_reveil|date_format:"%Hh%M"}}
            {{else}}
            &mdash;
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>Sortie salle de r�veil</th>
          <td class="halfPane">
            {{if $operation->sortie_reveil}}
              {{$operation->sortie_reveil|date_format:"%Hh%M"}}
            {{else}}
            &mdash;
            {{/if}}
          </td>
        </tr>
      </table>    
      
      <table width="100%" style="font-size: 100%;">
        <tr>
          <th class="category" colspan="2">Dur�es</th>
        </tr>
        <tr>
          <th>Pr�sence en salle</th>
          <td class="halfPane">
            {{if $operation->_presence_salle}}
              {{$operation->_presence_salle|date_format:"%Hh%M"}}
            {{else}}
            &mdash;
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>Dur�e d'intervention</th>
          <td class="halfPane">
            {{if $operation->_duree_interv}}
              {{$operation->_duree_interv|date_format:"%Hh%M"}}
            {{else}}
            &mdash;
            {{/if}}
          </td>
        </tr>
      </table>      
    </td>
  </tr>
  
  <tr>
    <td colspan="2">
      <table width="100%" style="border-spacing: 0px;font-size: 100%;">
        <tr>
          <th class="category" colspan="5">Actes CCAM</th>
        </tr>
        {{assign var="styleBorder" value="border: solid #aaa 1px;"}}
        <tr>
          <th style="{{$styleBorder}}text-align:left;">Code</th>
          <th style="{{$styleBorder}}text-align:left;">Ex�cutant</th>
          <th style="{{$styleBorder}}text-align:left;">Activit�</th>
          <th style="{{$styleBorder}}text-align:left;">Phase &mdash; Modifs.</th>
          <th style="{{$styleBorder}}text-align:left;">Association</th>
        </tr>
        {{foreach from=$operation->_ref_actes_ccam item=currActe}}
        <tr>
          <td class="text" style="{{$styleBorder}}">
            <strong>{{$currActe->code_acte}}</strong><br />
            {{$currActe->_ref_code_ccam->libelleLong}}
          </td>
          <td class="text" style="{{$styleBorder}}">{{$currActe->_ref_executant->_view}}</td>
          <td style="{{$styleBorder}}">{{$currActe->code_activite}}</td>
          <td style="{{$styleBorder}}">
            {{$currActe->code_phase}}
            {{if $currActe->modificateurs}}
            &mdash; {{$currActe->modificateurs}}
            {{/if}}
          </td>
          <td style="{{$styleBorder}}">{{$currActe->_guess_association}}</td>
        </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
</table>