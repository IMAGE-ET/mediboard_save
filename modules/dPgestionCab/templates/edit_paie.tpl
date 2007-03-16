<!-- $Id: form_print_planning.tpl 23 2006-05-04 15:05:35Z MyttO $ -->

<script type="text/javascript">

function printFiche() {
  var iFiche_id = document.forms.editFrm.fiche_paie_id.value;
  var url = new Url();
  url.setModuleAction("dPgestionCab", "print_fiche");
  url.addParam("fiche_paie_id", iFiche_id);
  url.popup(700, 550, "Fiche");
}

function pageMain() {
  regFieldCalendar("editFrm", "debut");
  regFieldCalendar("editFrm", "fin");
}

</script>

<table class="main">
  <tr>
    <td colspan="2">
      <form name="userSelector" action="index.php" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <label for="employecab_id" title="Veuillez s�lectionner l'employ� concern�">Eploy� Concern�</label>
      <select name="employecab_id" onchange="this.form.submit()">
      {{foreach from=$listEmployes item=curr_emp}}
        <option value="{{$curr_emp->employecab_id}}" {{if $curr_emp->employecab_id == $employe->employecab_id}}selected="selected"{{/if}}>
          {{$curr_emp->_view}}
        </option>
      {{/foreach}}
      </select>
      </form>
      {{if $fichePaie->fiche_paie_id}}
      <br />
      <a class="buttonnew" href="index.php?m={{$m}}&amp;tab=edit_paie&amp;fiche_paie_id=0" title="Cr�er une nouvelle fiche de paie">
        Cr�er une nouvelle fiche de paie
      </a>
      {{/if}}
    </td>
  </tr>
  <tr>
    <td class="halfPane">
      <form name="editFrm" action="./index.php?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_fichePaie_aed" />
      <input type="hidden" name="m" value="dPgestionCab" />
      <input type="hidden" name="fiche_paie_id" value="{{$fichePaie->fiche_paie_id}}" />
      <input type="hidden" name="params_paie_id" value="{{$paramsPaie->params_paie_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        {{if $fichePaie->fiche_paie_id}}
        <tr>
          <th class="title modify" colspan="4" colspan="2">Modifier la {{$fichePaie->_view}}</th>
        </tr>
        {{else}}
        <tr>
          <th class="title" colspan="2">Cr�er une fiche de paie</th>
        </tr>
        {{/if}}
        <tr>
          <th>
            <label for="debut" title="Debut de la p�riode de la fiche de paie">D�but de la p�riode</label>
          </th>
          <td class="date">
            <div id="editFrm_debut_da">{{$fichePaie->debut|date_format:"%d/%m/%Y"}}</div>
            <input type="hidden" name="debut" class="{{$fichePaie->_props.debut}}" value="{{$fichePaie->debut}}" />
            <img id="editFrm_debut_trigger" src="./images/icons/calendar.gif" alt="calendar" title="Choisir une date de d�but"/>
          </td>
        </tr>
        <tr>
          <th>
            <label for="fin" title="Fin de la p�riode de la fiche de paie">Fin de la p�riode</label>
          </th>
          <td class="date">
            <div id="editFrm_fin_da">{{$fichePaie->fin|date_format:"%d/%m/%Y"}}</div>
            <input type="hidden" name="fin" class="{{$fichePaie->_props.fin}}" value="{{$fichePaie->fin}}" />
            <img id="editFrm_fin_trigger" src="./images/icons/calendar.gif" alt="calendar" title="Choisir une date de d�but"/>
          </td>
        </tr>
        <tr>
          <th>
            <label for="salaire" title="Salaire horaireen euros">Salaire horaire</label>
          </th>
          <td>
            <input type="text" name="salaire" size="5" class="{{$fichePaie->_props.salaire}}" value="{{$fichePaie->salaire}}" />
            �
          </td>
        </tr>
        <tr>
          <th>
            <label for="heures" title="Nombre d'heures travaill�es dans la p�riode">Nombre d'heures travaill�es</label>
          </th>
          <td>
            <input type="text" size="4" name="heures" class="{{$fichePaie->_props.heures}}" value="{{$fichePaie->heures}}" />
            h
          </td>
        </tr>
        <tr>
          <th>
            <label for="heures_sup" title="Nombre d'heures supl�mentaires travaill�es dans la p�riode">Nombre d'heures supl�mentaires</label>
          </th>
          <td>
            <input type="text" size="4" name="heures_sup" class="{{$fichePaie->_props.heures_sup}}" value="{{$fichePaie->heures_sup}}" />
            h
          </td>
        </tr>
        <tr>
          <th>
            <label for="mutuelle" title="Valeur de la cotisation pour la mutuelle">Mutuelle</label>
          </th>
          <td>
            <input type="text" name="mutuelle" size="4" class="{{$fichePaie->_props.mutuelle}}" value="{{$fichePaie->mutuelle}}" />
            �
          </td>
        </tr>
        <tr>
          <th>
            <label for="precarite" title="Prime de pr�carit� exprim�e en pourcentage">Prime de pr�carit�</label>
          </th>
          <td>
            <input type="text" size="4" name="precarite" class="{{$fichePaie->_props.precarite}}" value="{{$fichePaie->precarite}}" />
            %
          </td>
        </tr>
        <tr>
          <th>
            <label for="anciennete" title="Prime d'anciennet� exprim�e en pourcentage">Prime d'anciennet�</label>
          </th>
          <td>
            <input type="text" size="4" name="anciennete" class="{{$fichePaie->_props.anciennete}}" value="{{$fichePaie->anciennete}}" />
            %
          </td>
        </tr>
        <tr>
          <th>
            <label for="conges_payes" title="Cong�s pay�s exprim�s en pourcentage">Cong�s pay�s</label>
          </th>
          <td>
            <input type="text" size="4" name="conges_payes" class="{{$fichePaie->_props.conges_payes}}" value="{{$fichePaie->conges_payes}}" />
            %
          </td>
        </tr>
        <tr>
          <th>
            <label for="prime_speciale" title="Prime sp�ciale en euros">Prime sp�ciale</label>
          </th>
          <td>
            <input type="text" size="4" name="prime_speciale" class="{{$fichePaie->_props.prime_speciale}}" value="{{$fichePaie->prime_speciale}}" />
            �
          </td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button class="submit" type="submit">Sauver</button>
            {{if $fichePaie->fiche_paie_id}}
            <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la ',objName:'{{$fichePaie->_view|smarty:nodefaults|JSAttribute}}'})">
              Supprimer
            </button>
            <button class="print" type="button" onclick="printFiche()">
              Imprimer
            </button>
            {{/if}}
          </td>
        </tr>
      </table>
      </form>
    </td>
    <td class="halfPane">
      <table class="form">
        <tr>
          <th class="title">Anciennes Fiches de paie</th>
        </tr>
        {{foreach from=$listFiches item=curr_fiche}}
        <tr>
          <td>
            <form name="editFrm{{$curr_fiche->fiche_paie_id}}" action="./index.php?m={{$m}}" method="post" onsubmit="return checkForm(this)">
            <input type="hidden" name="dosql" value="do_fichePaie_aed" />
            <input type="hidden" name="m" value="dPgestionCab" />
            <input type="hidden" name="fiche_paie_id" value="{{$curr_fiche->fiche_paie_id}}" />
            <input type="hidden" name="del" value="0" />
            <button class="trash" type="button" style="float:right;" onclick="confirmDeletion(this.form,{typeName:'la ',objName:'{{$curr_fiche->_view|smarty:nodefaults|JSAttribute}}'})">
              Supprimer
            </button>
            </form>
            <a href="index.php?m=dPgestionCab&amp;tab=edit_paie&amp;fiche_paie_id={{$curr_fiche->fiche_paie_id}}" title="Editer cette fiche" >
              {{$curr_fiche->_view}}
            </a>
          </td>
        </tr>
        {{foreachelse}}
        <tr>
          <td>
            Liste vide
          </td>
        </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
</table>