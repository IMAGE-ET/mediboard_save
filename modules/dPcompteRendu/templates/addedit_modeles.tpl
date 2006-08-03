<script language="JavaScript" type="text/javascript">

function nouveau() {
  var url = new Url;
  url.setModuleTab("dPcompteRendu", "addedit_modeles");
  url.addParam("compte_rendu_id", "0");
  url.redirect();
}

function supprimer() {
  var form = document.editFrm;
  form.del.value = 1;
  form.submit();
}

</script>

<form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="dosql" value="do_modele_aed" />
<input type="hidden" name="compte_rendu_id" value="{{$compte_rendu->compte_rendu_id}}" />

<table class="main">

<tr>
  <td>
    {{if $compte_rendu->compte_rendu_id}}
    <button class="new" type="button" onclick="nouveau()">
      Cr�er un mod�le
    </button>
    {{/if}}  
<table class="form">
  <tr>
    <th class="category" colspan="2">
      {{if $compte_rendu->compte_rendu_id}}
      <a style="float:right;" href="javascript:view_log('CCompteRendu',{{$compte_rendu->compte_rendu_id}})">
        <img src="images/history.gif" alt="historique" />
      </a>
      {{/if}}
      Informations sur le mod�le
    </th>
  </tr>
  
  <tr>
    <th><label for="nom" title="Intitul� du mod�le. Obligatoire">Nom</label></th>
    <td><input type="text" name="nom" value="{{$compte_rendu->nom}}" title="{{$compte_rendu->_props.nom}}" /></td>
  </tr>
  
  <tr>
    <th><label for="function_id" title="Fonction � laquelle le mod�le est associ�">Fonction</label></th>
    <td>
      <select name="function_id" title="{{$compte_rendu->_props.function_id}}">
        <option value="">&mdash; Associer � une fonction &mdash;</option>
        {{foreach from=$listFunc item=curr_func}}
          <option value="{{$curr_func->function_id}}" {{if $curr_func->function_id == $compte_rendu->function_id}} selected="selected" {{/if}}>
            {{$curr_func->_view}}
          </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  
  <tr>
    <th><label for="chir_id" title="Praticien auquel le mod�le est associ�">Praticien</label></th>
    <td>
      <select name="chir_id" title="{{$compte_rendu->_props.chir_id}}">
        <option value="">&mdash; Associer � un praticien &mdash;</option>
        {{foreach from=$listPrat item=curr_prat}}
          <option value="{{$curr_prat->user_id}}" {{if $curr_prat->user_id == $prat_id}} selected="selected" {{/if}}>
            {{$curr_prat->_view}}
          </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  
  <tr>
    <th><label for="type" title="Contexte dans lequel est utilis� le mod�le">Type de mod�le: </label></th>
    <td>
      <select name="type">
        {{foreach from=$compte_rendu->_enums.type item=curr_type}}
        <option value="{{$curr_type}}" {{if $curr_type == $compte_rendu->type}} selected="selected" {{/if}}>
          {{$curr_type}}
        </option>
        {{/foreach}}
    </td>
  </tr>
  
  <tr>
    <td class="button" colspan="2">
    {{if $compte_rendu->compte_rendu_id}}
      <button class="modify" type="submit">Modifier</button>
      <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le mod�le',objName:'{{$compte_rendu->nom|escape:javascript}}'})">
        Supprimer
      </button>
    {{else}}
      <button class="submit" type="submit">Cr�er</button>
    {{/if}}
    </td>
  </tr>
</table>

  </td>
  <td class="greedyPane" style="height: 500px">
  {{if $compte_rendu->compte_rendu_id}}
    <textarea id="htmlarea" name="source">
    {{$compte_rendu->source}}
    </textarea>
  {{/if}}
  </td>
</tr>

</table>

</form>