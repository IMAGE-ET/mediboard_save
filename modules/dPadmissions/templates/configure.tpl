<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="dosql" value="do_configure" />
<input type="hidden" name="m" value="system" />

<table class="form">
  <tr>
    <th class="title" colspan="2">Affichage</th>
  </tr>
  
  {{mb_include module=system template=inc_config_bool var=show_dh}}
  
  {{assign var="var" value="fiche_admission"}}
  <tr>
    <th style="width:50%">
      <label for="{{$m}}[fiche_admission]" title="{{tr}}config-{{$m}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <select name="{{$m}}[{{$var}}]">
        <option value="a4" {{if $conf.$m.$var == "a4"}}selected="selected"{{/if}}>
          Mod�le A4
        </option>
        <option value="a5" {{if $conf.$m.$var == "a5"}}selected="selected"{{/if}}>
          Mod�le A5
        </option>
      </select>
    </td>
  </tr>
  <tr>
    {{mb_include module=system template=inc_config_bool var=show_deficience}}
  </tr>
  <tr>
    <th style="width: 50%"></th>
    <td class="text">
      {{assign var=antecedents value=$conf.dPpatients.CAntecedent.types}}
      {{if preg_match("/deficience/", $antecedents)}}
        <div class="small-success">
          Le type d'ant�c�dent <strong>D�ficience</strong> est bien coch� dans le volet Ant�c�dents de
          <a href="?m=patients&tab=configure">l'onglet Configurer dumodule Dossier Patient</a>
        </div>
      {{else}}
        <div class="small-warning">
          Pour afficher cette ic�ne, le type d'ant�c�dent <strong>D�ficience</strong> 
          doit �tre coch� dans le volet Ant�c�dents de
          <a href="?m=patients&tab=configure">l'onglet Configurer dumodule Dossier Patient</a>
        </div>
      {{/if}}
    </td>
  </tr>
  <tr>
    <td class="button" colspan="100">
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>
</table>
</form>