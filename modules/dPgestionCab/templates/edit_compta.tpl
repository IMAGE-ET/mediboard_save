<script type="text/javascript">
function printRapport() {
  var form = document.selectFrm;

  var url = new Url;
  url.setModuleAction("dPgestionCab", "print_rapport");
  url.addElement(form._date_min);
  url.addElement(form._date_max);
  url.addElement(form.libelle);
  url.addElement(form.rubrique_id);
  url.addElement(form.mode_paiement_id);
  url.popup(700, 550, "Rapport");
}

function pageMain() {
  regFieldCalendar("editFrm", "date");
  regFieldCalendar("selectFrm", "_date_min");
  regFieldCalendar("selectFrm", "_date_max");
}

</script>

<table class="main">
  <tr>
  
    <!-- Modification d'une fiche -->
    <td class="halfpane">
      <form name="editFrm" action="index.php?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_gestioncab_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="gestioncab_id" value="{{$gestioncab->gestioncab_id}}" />
      <input type="hidden" name="function_id" value="{{$gestioncab->function_id}}" />
      {{if $gestioncab->gestioncab_id}}
      <a class="buttonnew" href="index.php?m={{$m}}&gestioncab_id=0">Cr�er une nouvelle fiche</a>
      {{/if}}
      <table class="form">
        <tr>
          {{if $gestioncab->gestioncab_id}}
          <th class="title modify" colspan="4">
            <a style="float:right;" href="#" onclick="view_log('CGestionCab',{{$gestioncab->gestioncab_id}})">
              <img src="images/icons/history.gif" alt="historique" />
            </a>
            Modification de la fiche {{$gestioncab->_view}}
          </th>
          {{else}}
          <th class="title" colspan="2">Cr�ation d'une nouvelle fiche</th>
          {{/if}}
        </tr>
        <tr>
          <th>{{mb_label object=$gestioncab field="libelle"}}</th>
          <td>{{mb_field object=$gestioncab field="libelle"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$gestioncab field="date"}}</th>
          <td class="date">{{mb_field object=$gestioncab field="date" form="editFrm"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$gestioncab field="rubrique_id"}}</th>
          <td>
            <select name="rubrique_id">
             <optgroup label="{{$etablissement}}">
            {{foreach from=$listRubriques item=rubrique}}
              <option value="{{$rubrique->rubrique_id}}" {{if $rubrique->rubrique_id == $gestioncab->rubrique_id}}selected="selected"{{/if}}>
                {{$rubrique->nom}}
              </option>
            {{/foreach}}
            </optgroup>
            <optgroup label="{{$fonction}}">
            {{foreach from=$listRubriquesFonction item=rubrique}}
              <option value="{{$rubrique->rubrique_id}}" {{if $rubrique->rubrique_id == $gestioncab->rubrique_id}}selected="selected"{{/if}}>
                {{$rubrique->nom}}
              </option>
            {{/foreach}}
            </optgroup>
            </select>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$gestioncab field="montant"}}</th>
          <td>{{mb_field object=$gestioncab field="montant"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$gestioncab field="mode_paiement_id"}}</th>
          <td>
            <select name="mode_paiement_id">
            <optgroup label="{{$etablissement}}">
            {{foreach from=$listModesPaiement item=mode}}
              <option value="{{$mode->mode_paiement_id}}" {{if $mode->mode_paiement_id == $gestioncab->mode_paiement_id}}selected="selected"{{/if}}>
                {{$mode->nom}}
              </option>
            {{/foreach}}
            </optgroup>
            <optgroup label="{{$fonction}}">
            {{foreach from=$listModePaiementFonction item=mode}}
              <option value="{{$mode->mode_paiement_id}}" {{if $mode->mode_paiement_id == $gestioncab->mode_paiement_id}}selected="selected"{{/if}}>
                {{$mode->nom}}
              </option>
            {{/foreach}}
            </optgroup>
            </select>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$gestioncab field="rques"}}</th>
          <td>{{mb_field object=$gestioncab field="rques"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$gestioncab field="num_facture"}}</th>
          <td>{{mb_field object=$gestioncab field="num_facture"}}</td>
        </tr>
        <tr>
          <td class="button" colspan="5">
            {{if $gestioncab->gestioncab_id}}
            <button class="modify" type="submit">Modifier</button>
            <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la fiche',objName:'{{$gestioncab->_view|smarty:nodefaults|JSAttribute}}'})">
              Supprimer
            </button>
            {{else}}
            <button class="submit" type="submit">Cr�er</button>
            {{/if}}
          </td>
        </tr>
      </table>
      </form>
    </td>
    
    <!-- Recherche de fiches -->
    <td class="halfpane">
      <form name="selectFrm" action="index.php" method="get" onSubmit="return checkForm(this)">
      <input type="hidden" name="m" value="{{$m}}" />
      <table class="tbl">
        <tr>
          <th class="title" colspan="5">Recherche de fiches</th>
        </tr>
        <tr>
          <td>{{mb_label object=$filter field="_date_min"}}</td>
          <td class="date">{{mb_field object=$filter field="_date_min" form="selectFrm" canNull="false"}}</td>
          <td class="button" colspan="3">
            <button type="submit" class="print" onclick="printRapport()">Imprimer</button>
          </td>
        </tr>
        <tr>
          <td>{{mb_label object=$filter field="_date_max"}}</td>
          <td class="date" >{{mb_field object=$filter field="_date_max" form="selectFrm"  canNull="false"}}</td>
          <td class="button" colspan="3">
            <button type="submit" class="search">Afficher</button>
          </td>
        </tr>
        <tr>
          <th class="category">Date</th>
          <th>{{mb_label object=$filter field="libelle"}}
		  <br />
            {{mb_field object=$filter field="libelle" canNull="true"}}
          </th>
          <th class="category"> {{mb_label object=$filter field="rubrique_id"}}
            <br />
            <select name="rubrique_id">
             <optgroup label="{{$etablissement}}">
            {{foreach from=$listRubriques item=rubrique}}
              <option value="{{$rubrique->rubrique_id}}" {{if $rubrique->rubrique_id == $gestioncab->rubrique_id}}selected="selected"{{/if}}>
                {{$rubrique->nom}}
              </option>
            {{/foreach}}
            </optgroup>
            <optgroup label="{{$fonction}}">
            {{foreach from=$listRubriquesFonction item=rubrique}}
              <option value="{{$rubrique->rubrique_id}}" {{if $rubrique->rubrique_id == $gestioncab->rubrique_id}}selected="selected"{{/if}}>
                {{$rubrique->nom}}
              </option>
            {{/foreach}}
            </optgroup>
            </select>
          </th>
          <th class="category">{{mb_label object=$filter field="rubrique_id"}}
            <br />
            <select name="mode_paiement_id">
            <optgroup label="{{$etablissement}}">
            {{foreach from=$listModesPaiement item=mode}}
              <option value="{{$mode->mode_paiement_id}}" {{if $mode->mode_paiement_id == $gestioncab->mode_paiement_id}}selected="selected"{{/if}}>
                {{$mode->nom}}
              </option>
            {{/foreach}}
            </optgroup>
            <optgroup label="{{$fonction}}">
            {{foreach from=$listModePaiementFonction item=mode}}
              <option value="{{$mode->mode_paiement_id}}" {{if $mode->mode_paiement_id == $gestioncab->mode_paiement_id}}selected="selected"{{/if}}>
                {{$mode->nom}}
              </option>
            {{/foreach}}
            </optgroup>
            </select>
          </th>
          <th class="category">Montant</th>
        </tr>
        {{foreach from=$listGestionCab item=fiche}}
        <tr>
          <td>
            <a href="index.php?m={{$m}}&gestioncab_id={{$fiche->gestioncab_id}}">
            {{$fiche->date|date_format:"%d/%m/%Y"}}
            </a>
          </td>
          <td>
            <a href="index.php?m={{$m}}&gestioncab_id={{$fiche->gestioncab_id}}">
            {{$fiche->libelle}}
            </a>
          </td>
          <td>
            <a href="index.php?m={{$m}}&gestioncab_id={{$fiche->gestioncab_id}}">
            {{$fiche->_ref_rubrique->nom}}
            </a>
          </td>
          <td>
            <a href="index.php?m={{$m}}&gestioncab_id={{$fiche->gestioncab_id}}">
            {{$fiche->_ref_mode_paiement->nom}}
            </a>
          </td>
          <td>
            <a href="index.php?m={{$m}}&gestioncab_id={{$fiche->gestioncab_id}}">
            {{$fiche->montant}} �
            </a>
          </td>
        </tr>
        {{/foreach}}
      </table>
      </form>
    </td>
  </tr>
</table>