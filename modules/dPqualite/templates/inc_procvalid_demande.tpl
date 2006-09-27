{{assign var="info_proc" value=$docGed->_lastentry->date|date_format:"%d %b %Y � %Hh%M"}}
<table class="form">
  <tr>          
    <th class="title" colspan="2" style="color: #f00;">              
      <input type="hidden" name="suivi[etat]" value="{{$smarty.const.CDOC_VALID}}" />
      Proc�dure Demand�e
    </th>
  </tr>
  <tr>
    <th>Date</th>
    <td>{{$docGed->_lastentry->date|date_format:"%A %d %B %Y � %Hh%M"}}</td>
  </tr>
  <tr>
    <th>Proc�dure Associ�e</th>
    <td>
      {{if $docGed->doc_ged_id && $docGed->_lastactif->doc_ged_suivi_id}}
      R�vision de la proc�dure {{$docGed->_reference_doc}}<br />
      Th�me : {{$docGed->_ref_theme->nom}}
      {{else}}
      Nouvelle Proc�dure
      {{/if}}
    </td>
  </tr>
  <tr>
    <th>Etablissement</th>
    <td class="text">
      {{$docGed->_ref_group->text}}
    </td>
  </tr>
  <tr>
    <th>Formul�e par</th>
    <td class="text">{{$docGed->_lastentry->_ref_user->_view}}</td>
  </tr>
  <tr>
    <th>Remarques</th>
    <td class="text">
      {{$docGed->_lastentry->remarques|nl2br}}
    </td>
  </tr>
  <tr>
    <td colspan="2" class="button">
      <button class="cancel" type="button" onclick="refuseDoc(this.form);">
        Refuser la demande 
      </button>
    </td>
  </tr>
  <tr>
    <th>
      <label for="ged[titre]" title="Veuillez saisir un titre pour cette proc�dure">
        Titre
      </label>
    </th>
    <td>
      <input type="text" name="ged[titre]" value="{{$docGed->titre}}" title="{{$docGed->_props.titre}}|notNull" />
    </td>
  </tr>
  <tr>
    <th>
      <label for="ged[doc_theme_id]" title="Veuillez S�lectionner un th�me de classement">
        Th�me
      </label>
    </th>
    <td>
      <select name="ged[doc_theme_id]" title="{{$docGed->_props.doc_theme_id}}|notNull">
        <option value="">&mdash; Veuillez s�lectionner un Th�me &mdash;</option>
        {{foreach from=$listThemes item=curr_theme}}
        <option value="{{$curr_theme->doc_theme_id}}" {{if $docGed->doc_theme_id == $curr_theme->doc_theme_id}} selected="selected" {{/if}} >
          {{$curr_theme->nom}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  {{if !$docGed->_lastactif->doc_ged_suivi_id}}            
  <tr>
    <th>
      <label for="ged[doc_chapitre_id]" title="Veuillez S�lectionner un chapitre de classement">
        Chapitre
      </label>
    </th>
    <td>
      <select name="ged[doc_chapitre_id]" title="{{$docGed->_props.doc_chapitre_id}}|notNull">
        <option value="">&mdash; Veuillez s�lectionner un Chapitre &mdash;</option>
        {{foreach from=$listChapitres item=curr_chapitre}}
        <option value="{{$curr_chapitre->doc_chapitre_id}}" {{if $docGed->doc_chapitre_id == $curr_chapitre->doc_chapitre_id}} selected="selected" {{/if}} >
          {{$curr_chapitre->_view}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  <tr>
    <th>
      <label for="ged[doc_categorie_id]" title="Veuillez S�lectionner une cat�gorie de classement">
        Cat�gorie
      </label>
    </th>
    <td>
      <select name="ged[doc_categorie_id]" title="{{$docGed->_props.doc_categorie_id}}|notNull">
        <option value="">&mdash; Veuillez s�lectionner une Cat�gorie &mdash;</option>
        {{foreach from=$listCategories item=curr_category}}
        <option value="{{$curr_category->doc_categorie_id}}" {{if $docGed->doc_categorie_id == $curr_category->doc_categorie_id}} selected="selected" {{/if}} >
          {{$curr_category->_view}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  {{/if}}
  <tr>
    <th><label for="suivi[remarques]" title="Veuillez saisir vos remarques">Remarques</label></th>
    <td>
      <textarea name="suivi[remarques]" title="{{$docGed->_lastentry->_props.remarques}}"></textarea>
    </td>
  </tr>
  <tr>
    <td colspan="2" class="button">
      <button class="tick" type="button" onclick="redactionDoc(this.form);">
        Accepter la demande 
      </button>
      <button class="trash" type="button" onclick="confirmDeletion(this.form, {typeName:'La proc�dure ',objName:'{{$info_proc|escape:javascript}}'})" title="Supprimer la Proc�dure">
        Supprimer
      </button>
    </td>
  </tr>
</table>