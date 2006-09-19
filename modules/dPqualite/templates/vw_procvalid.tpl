{{if $docGed->doc_ged_id}}
<script language="Javascript" type="text/javascript">

function popFile(file_id){
  var url = new Url;
  url.addParam("nonavig", 1);
  url.ViewFilePopup(file_id, 0);
}

function annuleDoc(oForm,annulation){
  oForm.elements["ged[annule]"].value = annulation;
  oForm._validation.value = 1;  
  oForm.elements["ged[etat]"].value = {{$docGed->etat}};
  oForm.submit();
}

function validDoc(oForm){
  if(oForm.elements["suivi[remarques]"].value == ""){
    alert("Veuillez saisir vos remarques dans la zone 'Remarques'.");
    oForm.elements["suivi[remarques]"].focus();
  }else{
    oForm.elements["suivi[doc_ged_suivi_id]"].value = "";
    oForm.elements["ged[etat]"].value = {{$smarty.const.CDOC_TERMINE}};
    oForm.elements["suivi[actif]"].value = 1;
    oForm.submit();
  }
}

function refuseDoc(oForm){
  if(oForm.elements["suivi[remarques]"].value == ""){
    alert("Veuillez saisir un motif de refus dans la zone 'Remarques'.");
    oForm.elements["suivi[remarques]"].focus();
  }else{
    oForm.elements["suivi[doc_ged_suivi_id]"].value = "";
    oForm.elements["ged[doc_theme_id]"].value="";
    oForm.elements["ged[doc_categorie_id]"].value="";
    oForm.elements["ged[doc_chapitre_id]"].value="";
    oForm.elements["ged[titre]"].value="";  
    oForm.elements["ged[etat]"].value = {{$smarty.const.CDOC_TERMINE}};      
    oForm.submit();
  }
}

function redactionDoc(oForm){
  oForm.elements["suivi[doc_ged_suivi_id]"].value = "";
  oForm.elements["ged[etat]"].value = {{$smarty.const.CDOC_REDAC}};
  if(oForm.onsubmit()){
    oForm.submit();
  }
}
</script>
{{/if}}
<table class="main">
  <tr>
    <td class="halfPane">
      {{if $procDemande|@count}}
      <table class="form">
        <tr>
          <th class="category" colspan="4">
            Prod�cure demand�e en Attente
          </th>
        </tr>
        <tr>
          <th class="category">Demande formul�e</th>
          <th class="category">Etablissement</th>
          <th class="category">Date</th>
          <th class="category">Remarques</th>
        </tr>
        {{foreach from=$procDemande item=currProc}}
        <tr>
          <td class="text">
            <a href="index.php?m=dPqualite&amp;tab=vw_procvalid&amp;doc_ged_id={{$currProc->doc_ged_id}}">
              {{if $currProc->_lastactif->doc_ged_suivi_id}}
                R�vision de la proc�dure {{$currProc->_reference_doc}}
              {{else}}
                Nouvelle Proc�dure
              {{/if}}
            </a>
          </td>
          <td class="text">
            <a href="index.php?m=dPqualite&amp;tab=vw_procvalid&amp;doc_ged_id={{$currProc->doc_ged_id}}">
              {{$currProc->_ref_group->text}}
            </a>
          </td>
          <td class="text">
            <a href="index.php?m=dPqualite&amp;tab=vw_procvalid&amp;doc_ged_id={{$currProc->doc_ged_id}}">
              {{$currProc->_lastentry->date|date_format:"%A %d %B %Y � %Hh%M"}}
            </a>
          </td>
          {{if $currProc->annule}}
          <td class="text" style="background-color:#f00;">[ANNULE] {{$currProc->_lastentry->remarques|nl2br}}</td>
          {{else}}
          <td>{{$currProc->_lastentry->remarques|nl2br}}</td>
          {{/if}}
        </tr>
        {{/foreach}}
      </table><br /><br />      
      {{/if}}
      
      <table class="form">
        <tr>
          <th class="category" colspan="5">Proc�dure En attente</th>
        </tr>
        <tr>
          <th class="category">Titre</th>
          <th class="category">R�f�rence</th>
          <th class="category">Etablissement</th>
          <th class="category">Th�me</th>
          <th class="category">Etat</th>
        </tr>
        {{foreach from=$procEnCours item=currProc}}
        <tr>
          <td class="text">
            <a href="index.php?m=dPqualite&amp;tab=vw_procvalid&amp;doc_ged_id={{$currProc->doc_ged_id}}">
              {{$currProc->titre}}
            </a>
          </td>
          <td>
            <a href="index.php?m=dPqualite&amp;tab=vw_procvalid&amp;doc_ged_id={{$currProc->doc_ged_id}}">
              {{$currProc->_reference_doc}}
            </a>
          </td>
          <td class="text">
            <a href="index.php?m=dPqualite&amp;tab=vw_procvalid&amp;doc_ged_id={{$currProc->doc_ged_id}}">
              {{$currProc->_ref_group->text}}
            </a>
          </td>
          <td class="text">{{$currProc->_ref_theme->nom}}</td>
          {{if $currProc->annule}}
          <td class="text" style="background-color:#f00;">[ANNULE] {{$currProc->_etat_actuel}}</td>
          {{else}}
          <td>{{$currProc->_etat_actuel}}</td>
          {{/if}}
        </tr>
        {{foreachelse}}
        <tr>
          <td colspan="5">
            Aucune Proc�dure En Cours
          </td>
        </tr>
        {{/foreach}}
      </table>


    </td>
    <td class="halfPane">      
      {{if $docGed->doc_ged_id}}
      <form name="ProcEditFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_docged_aed" />
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="_validation" value="0" />
      
      <input type="hidden" name="ged[doc_ged_id]" value="{{$docGed->doc_ged_id}}" />  
      <input type="hidden" name="ged[user_id]" value="{{$docGed->user_id}}" />
      <input type="hidden" name="ged[group_id]" value="{{$docGed->group_id}}" />
      <input type="hidden" name="ged[annule]" value="{{$docGed->annule}}" />
      <input type="hidden" name="ged[etat]" value="" />
      
      <input type="hidden" name="suivi[doc_ged_suivi_id]" value="{{$docGed->_lastentry->doc_ged_suivi_id}}" />
      <input type="hidden" name="suivi[user_id]" value="{{$user_id}}" />  
      <input type="hidden" name="suivi[actif]" value="{{$docGed->_lastentry->actif}}" /> 
      <input type="hidden" name="suivi[file_id]" value="{{$docGed->_lastentry->file_id}}" />    
      
      <table class="form">
        <tr>          
          {{if $docGed->etat==CDOC_DEMANDE}}
            <th class="title" colspan="2" style="color: #f00;">              
              <input type="hidden" name="suivi[etat]" value="{{$smarty.const.CDOC_VALID}}" />
              Proc�dure Demand�e
            </th>
          {{elseif $docGed->etat==CDOC_REDAC}}
            <th class="title" colspan="2">
              <input type="hidden" name="suivi[etat]" value="{{$smarty.const.CDOC_REDAC}}" />
              Proc�dure en cours de r�daction ({{$docGed->_reference_doc}})
            </th>
          {{elseif $docGed->etat==CDOC_VALID}}
            <th class="title" colspan="2">
              <input type="hidden" name="suivi[etat]" value="{{$smarty.const.CDOC_VALID}}" />
              Validation du Document
            </th>
          {{/if}}                    
        </tr>
        {{assign var="info_proc" value=$docGed->_reference_doc}}
        {{if $docGed->etat==CDOC_TERMINE}}

        {{else}}
          {{if $docGed->etat==CDOC_DEMANDE}}
            {{assign var="info_proc" value=$docGed->_lastentry->date|date_format:"%d %b %Y � %Hh%M"}}
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
              <td>
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
            {{if !$docGed->annule}}
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
            {{/if}}
          {{elseif $docGed->etat==CDOC_REDAC}}
            <tr>
              <td class="button" colspan="2">
                <br />La proc�dure est en cours de r�daction.
                <br />Vous ne pouvez pas y apporter de modification.
              </td>
            </tr>
            <tr>
              <th>Vis� par</th>
              <td>{{$docGed->_lastentry->_ref_user->_view}}</td>
            </tr>
            {{if $docGed->_lastentry->file_id}}
            <tr>
              <th>Dernier Fichier li�</th>
              <td>
                <a href="javascript:popFile({{$docGed->_lastentry->file_id}})" title="Voir le Fichier">
                  <img src="index.php?m=dPfiles&amp;a=fileviewer&amp;suppressHeaders=1&amp;file_id={{$docGed->_lastentry->file_id}}&amp;phpThumb=1&amp;wl=64&amp;hp=64" alt="-" />
                </a>
              </td>
            </tr>
            {{/if}}
            <tr>
              <th>
                <strong>Dernier commentaire :</strong><br />
                {{$docGed->_lastentry->date|date_format:"%d %B %Y - %Hh%M"}}
              </th>
              <td>
                {{$docGed->_lastentry->remarques|nl2br}}
              </td>
            </tr>
          {{elseif $docGed->etat==CDOC_VALID}}
            <tr>
              <th>Proc�dure Associ�e</th>
              <td>
                {{$docGed->_reference_doc}}
              </td>
            </tr>
            <tr>
              <th>Propos� par</th>
              <td>{{$docGed->_lastentry->_ref_user->_view}}</td>
            </tr>
            <tr>
              <th>Le</th>
              <td>{{$docGed->_lastentry->date|date_format:"%d %B %Y - %Hh%M"}}</td>
            </tr>
            <tr>
              <th>Remarques</th>
              <td class="text">{{$docGed->_lastentry->remarques|nl2br}}</td>
            </tr>
            <tr>
              <td colspan="2" class="button">
                <a href="javascript:popFile({{$docGed->_lastentry->file_id}})" title="Voir le Fichier">
                  <img src="index.php?m=dPfiles&amp;a=fileviewer&amp;suppressHeaders=1&amp;file_id={{$docGed->_lastentry->file_id}}&amp;phpThumb=1&amp;wl=64&amp;hp=64" alt="-" />
                </a>
              </td>
            </tr>
            {{if !$docGed->annule}}
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
            <tr>
              <th><label for="suivi[remarques]" title="Veuillez saisir vos remarques">Vos Remarques</label></th>
              <td>
                <textarea name="suivi[remarques]" title="{{$docGed->_lastentry->_props.remarques}}"></textarea>
                {{$docGed->version}}
              </td>
            </tr>
            {{/if}}
            {{if $docGed->version}}
            <tr>
              <th><label for="ged[version]">Valider pour la version</label></th>
              <td>
                <select name="ged[version]" title="currency|notNull">
                  {{foreach from=$versionDoc item=currVersion}}
                  <option value="{{$currVersion}}">{{$currVersion}}</option>
                  {{/foreach}}
                </select>
              </td>
            </tr>
            {{else}}
            <input type="hidden" name="ged[version]" value="1">
            {{/if}}
          {{/if}}
          <tr>
            <td colspan="2" class="button">
              {{if $docGed->etat==CDOC_DEMANDE && !$docGed->annule}}
              <button class="tick" type="button" onclick="redactionDoc(this.form);">
                Accepter la demande 
              </button>                          
              {{elseif $docGed->etat!=CDOC_REDAC && !$docGed->annule}}
              <button class="tick" type="button" onclick="validDoc(this.form);">
                Valider le document 
              </button>
              <button class="cancel" type="button" onclick="redactionDoc(this.form);">
                Renvoyer le document
              </button>
              {{/if}}
              <button class="trash" type="button" onclick="confirmDeletion(this.form, {typeName:'La proc�dure ',objName:'{{$info_proc|escape:javascript}}'})" title="Supprimer la Proc�dure">
                Supprimer
              </button>
              
              {{if $docGed->annule}}
              <button class="change" type="button" onclick="annuleDoc(this.form,0);">
                R�tablir
              </button>
              {{else}}
              <button class="cancel" type="button" onclick="annuleDoc(this.form,1);">
                Annuler
              </button>
              {{/if}}
            </td>
          </tr>
        {{/if}}
      </table>
      </form>
      {{/if}}
    </td>
  </tr>
</table>