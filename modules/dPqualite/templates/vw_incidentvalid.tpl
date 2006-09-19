<table class="main">
  <tr>
    <td class="halfPane">
      {{if $listFichesEnCours|@count}}
      <table class="form">
        <tr>
          <th class="category" colspan="4">
            Fiche d'EI en Attente
          </th>
        </tr>
        <tr>
          <th class="category">Date de l'�v�nement</th>
          <th class="category">Auteur de la fiche</th>
        </tr>
        {{foreach from=$listFichesEnCours item=currFiche}}
        <tr>
          <td class="text">
            <a href="index.php?m=dPqualite&amp;tab=vw_incidentvalid&amp;fiche_ei_id={{$currFiche->fiche_ei_id}}">
              {{$currFiche->date_incident|date_format:"%A %d %B %Y � %Hh%M"}}
            </a>
          </td>
          <td class="text">
            <a href="index.php?m=dPqualite&amp;tab=vw_incidentvalid&amp;fiche_ei_id={{$currFiche->fiche_ei_id}}">
              {{$currFiche->_ref_user->_view}}
            </a>
          </td>
        </tr>
        {{/foreach}}
      </table><br /><br />
      {{/if}}
        
      <table class="form">
        <tr>
          <th class="category" colspan="4">Fiches d'EI trait�e</th>
        </tr>
        <tr>
          <th class="category">Date de l'�v�nement</th>
          <th class="category">Auteur</th>
          <th class="category">Validation</th>
          <th class="category">Degr� d'Urgence</th>
        </tr>        
        {{foreach from=$listFichesTermine item=currFiche}}
        <tr>
          <td class="text">
            <a href="index.php?m=dPqualite&amp;tab=vw_incidentvalid&amp;fiche_ei_id={{$currFiche->fiche_ei_id}}">
              {{$currFiche->date_incident|date_format:"%d %b %Y � %Hh%M"}}
            </a>
          </td>
          <td class="text">
            <a href="index.php?m=dPqualite&amp;tab=vw_incidentvalid&amp;fiche_ei_id={{$currFiche->fiche_ei_id}}">
              {{$currFiche->_ref_user->_view}}
            </a>
          </td>
          <td class="text">
            <a href="index.php?m=dPqualite&amp;tab=vw_incidentvalid&amp;fiche_ei_id={{$currFiche->fiche_ei_id}}">
              {{$currFiche->_ref_user_valid->_view}}
            </a>
          </td>
          <td class="text">
            <a href="index.php?m=dPqualite&amp;tab=vw_incidentvalid&amp;fiche_ei_id={{$currFiche->fiche_ei_id}}">
              {{$currFiche->degre_urgence}}
            </a>
          </td>
        </tr>
        {{foreachelse}}
        <tr>
          <td colspan="4">
            Aucune Fiche trait�e actuellement
          </td>
        </tr>
        {{/foreach}}
      </table>
    </td>
    <td class="halfPane">
      {{if $fiche->fiche_ei_id}}
      {{if !$fiche->valid_user_id}}
      <form name="ProcEditFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_ficheEi_aed" />
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="fiche_ei_id" value="{{$fiche->fiche_ei_id}}" />
      <input type="hidden" name="valid_user_id" value="{{$user_id}}" />
      {{/if}}
      <table class="form">
        <tr>
          {{if !$fiche->valid_user_id}}
          <th colspan="2" class="title" style="color:#f00;">
            Validation d'une fiche
          {{else}}
          <th colspan="2" class="title">
            Visualisation d'une fiche
          {{/if}}
          </th>
        </tr>
        <tr>
          <th>Evenement</th>
          <td>
            <strong>Signalement d'un
            {{if $fiche->type_incident}}
            risque d'incident
            {{else}}
            incident
            {{/if}}</strong>
            <br /> le {{$fiche->date_incident|date_format:"%A %d %B %Y � %Hh%M"}}
          </td>
        </tr>        
        <tr>
          <th>Auteur de la Fiche</th>
          <td class="text">
            {{$fiche->_ref_user->_view}}
            <br />{{$fiche->_ref_user->_ref_function->_view}}
          </td>
        </tr>
        <tr>
          <th>Concernant</th>
          <td class="text">
            {{if $fiche->elem_concerne==4}}    un mat�riel
            {{elseif $fiche->elem_concerne==3}}un m�dicament
            {{elseif $fiche->elem_concerne==2}}un membre du personnel
            {{elseif $fiche->elem_concerne==1}}un visiteur
            {{else}}                           un Patient
            {{/if}}<br />
            {{$fiche->elem_concerne_detail|nl2br}}
          </td>
        </tr>
        <tr>
          <th>Lieu</th>
          <td class="text">
            {{$fiche->lieu}}
          </td>
        </tr>
        <tr>
          <th colspan="2" class="category">Description de l'�v�nement</th>
        </tr>
        {{foreach from=$catFiche item=currEven key=keyEven}}
        <tr>
          <th><strong>{{$keyEven}}</strong></th>
          <td>
            <ul>
              {{foreach from=$currEven item=currItem}}
              <li>{{$currItem->nom}}</li>
              {{/foreach}}
            </ul>
          </td>
        </tr>  
        {{/foreach}}
        <tr>
          <th colspan="2" class="category">Informations compl�mentaires</th>
        </tr>
        
        {{if $fiche->autre}}
        <tr>
          <th>Autre</th>
          <td>{{$fiche->autre|nl2br}}</td>
        </tr>
        {{/if}}
        {{if $fiche->descr_faits}}
        <tr>
          <th>Description des faits</th>
          <td>{{$fiche->descr_faits|nl2br}}</td>
        </tr>
        {{/if}}
        {{if $fiche->mesures}}
        <tr>
          <th>Mesures Prises</th>
          <td>{{$fiche->mesures|nl2br}}</td>
        </tr>
        {{/if}}
        {{if $fiche->descr_consequences}}
        <tr>
          <th>Description des cons�quences</th>
          <td>{{$fiche->descr_consequences|nl2br}}</td>
        </tr>
        {{/if}}
        <tr>
          <th>Gravit� estim�e</th>
          <td>
            {{if $fiche->gravite==2}}    Importante
            {{elseif $fiche->gravite==1}}Mod�r�e
            {{else}}                     Nulle
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>Plainte pr�visible</th>
          <td>
            {{if $fiche->plainte}} Oui
            {{else}}               Non
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>Commission concialition</th>
          <td>
            {{if $fiche->commission}} Oui
            {{else}}               Non
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>Ev�nement d�j� survenu �<br /> la connaissance de l'auteur</th>
          <td>
            {{if $fiche->deja_survenu!==null}}
              {{if $fiche->deja_survenu}} Oui
              {{else}}                    Non
              {{/if}}
            {{else}}                      Ne sais pas
            {{/if}}
          </td>
        </tr>
        
        <tr>
          <th colspan="2" class="category">Validation de la Fiche</th>
        </tr>
        {{if $fiche->valid_user_id}}
        <tr>
          <th>Degr� d'Urgence</th>
          <td>{{$fiche->degre_urgence}}</td>
        </tr>
        <tr>
          <th>Valid�e par</th>
          <td>
            {{$fiche->_ref_user_valid->_view}}
            <br />le {{$currFiche->date_validation|date_format:"%d %b %Y � %Hh%M"}}
          </td>
        </tr>
      </table>
        {{else}}
        <tr>
          <th><label for="degre_urgence" title="Veuillez s�lectionenr le degr� d'urgence">Degr� d'Urgence</label></th>
          <td>
            <select name="degre_urgence" title="{{$fiche->_props.degre_urgence}}|notNull">
            <option value="">&mdash; Veuillez Choisir &mdash;</option>
            {{html_options values=$fiche->_enums.degre_urgence output=$fiche->_enums.degre_urgence}}
            </select>
          </td>
        </tr>
        <tr>
          <td colspan="2" class="button">
            <button class="modify" type="submit">
              Valider la Fiche
            </button>
          </td>
        </tr>
      </table>
      </form>
        {{/if}}
      
      {{/if}}
    </td>
  </tr>
</table>