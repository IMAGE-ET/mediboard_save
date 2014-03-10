{{if $nb_modeles == 0}}
  <div class="small-info">Aucun mod�le disponible</div>
  {{mb_return}}
{{/if}}

<form name="Add-CPackToModele" method="post" onsubmit="return Pack.onSubmitModele(this);">
  {{mb_class object=$link}}
  {{mb_key   object=$link}}
  {{mb_field object=$link field=pack_id hidden=1}}

  <table class="form">
    <tr>
      <th style="width: 40%;">{{mb_label object=$link field=modele_id}}</th>
      <td style="width: 60%;">
        <select name="modele_id" class="notNull ref" onchange="this.form.onsubmit()" style="width: 20em;">
          <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
          {{if $modeles.prat|@count}}
            <optgroup label="{{tr}}CCompteRendu-owned-by-user{{/tr}}">
              {{foreach from=$modeles.prat item=_modele}}
              <option value="{{$_modele->_id}}">{{$_modele->nom}}</option>
              {{/foreach}}
            </optgroup>
          {{/if}}
          {{if $access_function}}
          {{if $modeles.func|@count}}
            <optgroup label="{{tr}}CCompteRendu-owned-by-function{{/tr}}">
              {{foreach from=$modeles.func item=_modele}}
              <option value="{{$_modele->_id}}">{{$_modele->nom}}</option>
              {{/foreach}}
            </optgroup>
          {{/if}}
          {{/if}}
          {{if $access_group}}
          {{if $modeles.etab|@count}}
            <optgroup label="{{tr}}CCompteRendu-owned-by-etablissment{{/tr}}">
              {{foreach from=$modeles.etab item=_modele}}
              <option value="{{$_modele->_id}}">{{$_modele->nom}}</option>
              {{/foreach}}
            </optgroup>
          {{/if}}
          {{/if}}
        </select>        
      </td>
    </tr>
  </table>
  
  
</form>