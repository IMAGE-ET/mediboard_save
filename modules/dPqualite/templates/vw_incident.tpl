<script type="text/javascript">

function choixSuiteEven(){
  var oForm = document.FrmEI;
  if(oForm.suite_even.value=="autre"){
    $('suiteEvenAutre').show();
    oForm.suite_even_descr.title="{{$fiche->_props.suite_even_descr}}|notNull";
  }else{
    $('suiteEvenAutre').hide();
    oForm.suite_even_descr.title="{{$fiche->_props.suite_even_descr}}";
  }
}

function viewItems(iCategorie){
  var oForm = document.FrmEI;
  $('Items' + oForm._elemOpen.value).hide();
  $('Items' + iCategorie).show();
  oForm._elemOpen.value = iCategorie;
}

function checkCode(oElement){
  if(oElement.checked == true){
    putCode(oElement.name);
  }else{
    delCode(oElement.name);
  }
}
function delCode(iCode){
  var oForm = document.FrmEI;
  var aEvenements = oForm.evenements.value.split("|");
  // Si la chaine est vide, il cr�e un tableau � un �l�ment vide donc :
  aEvenements.removeByValue("");
  aEvenements.removeByValue(iCode, true);
  oForm.evenements.value = aEvenements.join("|");
  
  var oElement = eval("oForm._ItemsSel_cat_" + oForm._elemOpen.value);
  var aEvenements = oElement.value.split("|");
  aEvenements.removeByValue("");
  aEvenements.removeByValue(iCode, true);
  oElement.value = aEvenements.join("|");
  
  refreshListChoix();
}

function putCode(iCode){
  var oForm = document.FrmEI;
  var aEvenements = oForm.evenements.value.split("|");
  // Si la chaine est vide, il cr�e un tableau � un �l�ment vide donc :
  aEvenements.removeByValue("");
  aEvenements.push(iCode);
  oForm.evenements.value = aEvenements.join("|");
  
  var oElement = eval("oForm._ItemsSel_cat_" + oForm._elemOpen.value);
  var aEvenements = oElement.value.split("|");
  aEvenements.removeByValue("");
  aEvenements.push(iCode);
  oElement.value = aEvenements.join("|");
  
  refreshListChoix();
}

function refreshListChoix(){
  var oForm = document.FrmEI;
  var oCategorie = oForm._cat_evenement.options;
  var sListeChoix = "";
  for(i=0; i< oCategorie.length; i++){
    var oElement = eval("oForm._ItemsSel_cat_" + oCategorie[i].value);
    if(oElement.value){
      sListeChoix += "<strong>" + oCategorie[i].text + "</strong><ul>";
      var aItems = oElement.value.split("|");
      aItems.removeByValue("");
      iCode = 0;
      while (sCode = aItems[iCode++]) {
        sListeChoix += "<li>" + $('titleItem' + sCode).title + "</li>";
      }
      sListeChoix += "</ul>";
    }
  }
  $('listChoix').innerHTML = sListeChoix;
}

function pageMain() {
  regFieldCalendar("FrmEI", "_incident_date");
  refreshListChoix();
}
</script>
<table class="main">
  <tr>
    <td>
      <form name="FrmEI" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_ficheEi_aed" />
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="del" value="0" />       
      <input type="hidden" name="fiche_ei_id" value="{{$fiche->fiche_ei_id}}" />
    
      <table class="form">
        {{if $canAdmin}}
        <tr>
          <th colspan="2">
            <label for="user_id" title="Auteur de la fiche">Auteur de la fiche</label>
          </th>
          <td colspan="2">
            <select name="user_id" title="{{$fiche->_props.user_id}}">
              {{foreach from=$listFct item=currFct key=keyFct}}
              {{if $aUsers.$keyFct|@count}}
              <optgroup label="{{$currFct->_view}}">
                {{foreach from=$aUsers.$keyFct item=currUser}}
                <option class="mediuser" style="border-color: #{{$currFct->color}};" value="{{$currUser->user_id}}" 
                {{if ($fiche->fiche_ei_id && $fiche->user_id==$currUser->user_id)
                      || (!$fiche->fiche_ei_id && $user_id==$currUser->user_id)}}
                  selected="selected"
                {{/if}}
                >
                  {{$currUser->_view}}
                </option>
                {{/foreach}}
              </optgroup>
              {{/if}}
              {{/foreach}}
            </select>
          </td>
        </tr>
        {{else}}
        <input type="hidden" name="user_id" value="{{if $fiche->fiche_ei_id}}{{$fiche->user_id}}{{else}}{{$user_id}}{{/if}}" />
        {{/if}}
        
        <tr>
          {{if $fiche->fiche_ei_id}}
          <th colspan="4" class="title" style="color:#f00;">
          <input type="hidden" name="_validation" value="1" />
          {{else}}
          <th colspan="4" class="title">
          {{/if}}
            Fiche d'Incident Pr�vention - Gestion des Risques
          </th>
        </tr>
        
        <tr>
          <th>
            <label for="type_incident" title="Veuillez S�lectionner un type de signalement">Type de Signalement</label>
          </th>
          <td>
            <select name="type_incident" title="{{$fiche->_props.type_incident}}">
            <option value="" {{if $fiche->type_incident===null}}selected="selected"{{/if}}>&mdash;Veuillez Choisir &mdash;</option>
            {{html_options options=$fiche->_enumsTrans.type_incident selected=$fiche->type_incident}}
            </select>
          </td>
          <th><label for="_incident_date" title="Date de l'�v�nement">Date de l'�v�nement</label></th>
          <td class="date">
            <div id="FrmEI__incident_date_da">{{if $fiche->fiche_ei_id}}{{$fiche->_incident_date|date_format:"%d/%m/%Y"}}{{else}}{{$datenow|date_format:"%d/%m/%Y"}}{{/if}}</div>
            <input type="hidden" name="_incident_date" title="date|notNull" value="{{if $fiche->fiche_ei_id}}{{$fiche->_incident_date}}{{else}}{{$datenow}}{{/if}}" />
            <img id="FrmEI__incident_date_trigger" src="./images/calendar.gif" alt="calendar" title="Choisir une date pour l'�v�nement"/>
         </td>
        </tr>
        <tr>
          <th>
            <label for="elem_concerne" title="Veuillez choisir � qui ou � quoi se r�f�re cette fiche">Cette Fiche concerne</label>
          </th>
          <td>
            <select name="elem_concerne" title="{{$fiche->_props.elem_concerne}}">
            <option value=""{{if $fiche->elem_concerne==null}} selected="selected"{{/if}}>&mdash;Veuillez Choisir &mdash;</option>
            {{html_options options=$fiche->_enumsTrans.elem_concerne selected=$fiche->elem_concerne}}
            </select>            
          </td>
          <th><label for="_incident_heure" title="Heure de l'�v�nement">Heure de l'�v�nement</label></th>
          <td>
            <select name="_incident_heure">
            {{foreach from=$heures|smarty:nodefaults item=curr_heure}}
              <option value="{{$curr_heure}}"{{if (!$fiche->fiche_ei_id && $curr_heure==$heurenow) || ($fiche->fiche_ei_id && $curr_heure==$fiche->_incident_heure)}}selected="selected"{{/if}}>{{$curr_heure}}</option>
            {{/foreach}}
            </select> h
            <select name="_incident_min">
            {{foreach from=$mins|smarty:nodefaults item=minute}}
              <option value="{{$minute}}"{{if (!$fiche->fiche_ei_id && $minute==$minnow) || ($fiche->fiche_ei_id && $minute==$fiche->_incident_min)}}selected="selected"{{/if}}>{{$minute}}</option>
            {{/foreach}}
            </select> min
          </td>
        </tr>
        <tr>
          <th>
            <label for="elem_concerne_detail" title="Pr�cision concernant l'objet ou la personne concern�">
              Pr�cisez
            </label>
          </th>
          <td>
            <textarea name="elem_concerne_detail" title="{{$fiche->_props.elem_concerne_detail}}">{{$fiche->elem_concerne_detail}}</textarea>
          </td>
          <th><label for="lieu" title="Veuillez saisir le service ou � eu lieu l'�v�nement">Service</label></th>
          <td>
            <input type="text" name="lieu" title="{{$fiche->_props.lieu}}" value="{{$fiche->lieu}}" />
          </td>
        </tr>
        <tr>
          <th colspan="4" class="category"><label for="evenements" title="Veuillez choisir ce qui d�crit le mieux l'�v�nement">Description de l'�v�nement</label></th>
        </tr>

        <tr>
          <td colspan="2"rowspan="2" class="halfPane" id="listChoix"></td>
          <th>
            <label for="_cat_evenement" title="Veuillez S�lectionner un cat�gorie d'�v�nement">Cat�gorie d'�v�nement</label>
          </th>
          <td style="height:1%;">
            <input type="hidden" name="evenements" title="{{$fiche->_props.evenements}}" value="{{$fiche->evenements}}"/>
            <input type="hidden" name="_elemOpen" value="{{$firstdiv}}" />
            <select name="_cat_evenement" onchange="javascript:viewItems(this.value);">
            {{foreach from=$listCategories item=curr_evenement}}
            <option value="{{$curr_evenement->ei_categorie_id}}"{{if $curr_evenement->ei_categorie_id==$firstdiv}} selected="selected"{{/if}}>
              {{$curr_evenement->nom}}
            </option>
            {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
         <td colspan="2">
           {{foreach from=$listCategories item=curr_evenement}}
           <input type="hidden" name="_ItemsSel_cat_{{$curr_evenement->ei_categorie_id}}" value="{{$curr_evenement->checked}}" />
           <table class="tbl" id="Items{{$curr_evenement->ei_categorie_id}}" {{if $curr_evenement->ei_categorie_id!=$firstdiv}}style="display:none;"{{/if}}>
             {{counter start=0 skip=1 assign=curr_data}}
             {{foreach name=itemEvenement from=$curr_evenement->_ref_items item=curr_item}}
             {{if $curr_data is div by 3 || $curr_data==0}}
             <tr>
             {{/if}}
               <td class="text">
                 <input type="checkbox" name="{{$curr_item->ei_item_id}}" onclick="javascript:checkCode(this);" {{if $curr_item->checked}}checked="checked"{{/if}}/><label for="{{$curr_item->ei_item_id}}" id="titleItem{{$curr_item->ei_item_id}}" title="{{$curr_item->nom}}">{{$curr_item->nom}}</label>
               </td>
             {{if (($curr_data+1) is div by 3 || $smarty.foreach.itemEvenement.last)}}
             </tr>
             {{/if}}
             {{counter}}
             {{foreachelse}}
             <tr>
               <td>
                 Pas d'Item dans cette cat�gorie
               </td>
             </tr>
             {{/foreach}}
           </table>
           {{foreachelse}}
           Aucun Item disponible
           {{/foreach}}
         </td>
       </tr>

        <tr>
          <th colspan="4" class="category">Informations compl�mentaires</th>
        </tr>
        <tr>
          <th>
            <label for="autre" title="Veuillez saisir les �v�nements non list�s ci-dessous">Autre</label>
          </th>
          <td>
            <textarea name="autre" title="{{$fiche->_props.autre}}">{{$fiche->autre}}</textarea>
          </td>
          <th><label for="gravite" title="Veuillez S�lectionner la gravit�e estim�e de l'�v�nement">Gravit�e Estim�e</label></th>
          <td>
            <select name="gravite" title="{{$fiche->_props.gravite}}">
              <option value=""{{if $fiche->gravite===null}} selected="selected"{{/if}}>&mdash;Veuillez Choisir &mdash;</option>
              {{html_options options=$fiche->_enumsTrans.gravite selected=$fiche->gravite}}
            </select>
          </td>
        </tr>
        <tr>
          <th>
            <label for="descr_faits" title="Veuillez d�crire les faits">Description des faits</label>
          </th>
          <td>
            <textarea name="descr_faits" title="{{$fiche->_props.descr_faits}}">{{$fiche->descr_faits}}</textarea>
          </td>
          <th><label for="plainte" title="Une plainte est-elle pr�visible pour cet �v�nement">Plainte pr�visible</label></th>
          <td>
            <select name="plainte" title="{{$fiche->_props.plainte}}">
              <option value=""{{if $fiche->plainte===null}} selected="selected"{{/if}}>&mdash;Veuillez Choisir &mdash;</option>
              {{html_options options=$fiche->_enumsTrans.plainte selected=$fiche->plainte}}
            </select>
          </td>
        </tr>
        <tr>
          <th>
            <label for="mesures" title="Veuillez d�crire les mesures prises">Mesures Prises</label>
          </th>
          <td>
            <textarea name="mesures" title="{{$fiche->_props.mesures}}">{{$fiche->mesures}}</textarea>
          </td>
          <th><label for="commission" title="Y aura t'il une Commission de conciliation">Commission conciliation</label></th>
          <td>
            <select name="commission" title="{{$fiche->_props.commission}}">
              <option value=""{{if $fiche->commission===null}} selected="selected"{{/if}}>&mdash;Veuillez Choisir &mdash;</option>
              {{html_options options=$fiche->_enumsTrans.commission selected=$fiche->commission}}
            </select>
          </td>
        </tr>
        <tr>
          <th>
            <label for="descr_consequences" title="Veuillez d�crire les cons�quences">Description des cons�quences</label>
          </th>
          <td>
            <textarea name="descr_consequences" title="{{$fiche->_props.descr_consequences}}">{{$fiche->descr_consequences}}</textarea>
          </td>
          <th><label for="suite_even" title="Veuillez choisir la suite de l'�v�nement">Suite de l'�v�nement</label></th>
          <td>
            <select name="suite_even" title="{{$fiche->_props.suite_even}}" onchange="javascript:choixSuiteEven();">
              <option value=""{{if $fiche->suite_even===null}} selected="selected"{{/if}}>&mdash;Veuillez Choisir &mdash;</option>
              {{html_options options=$fiche->_enumsTrans.suite_even selected=$fiche->suite_even}}
            </select>
            <table id="suiteEvenAutre" style="width:100%;{{if $fiche->suite_even!="autre"}}display:none;{{/if}}">
            <tr>
              <td><label for="suite_even_descr" title="Pr�cisez les suite de l'�v�nement">Pr�cisez :</label></td>
            </tr>
            <tr>
              <td>
                <textarea name="suite_even_descr" title="{{$fiche->_props.suite_even_descr}}{{if $fiche->suite_even=="autre"}}|notNull{{/if}}">{{$fiche->suite_even_descr}}</textarea>
              </td>
            </tr>
            </table>
          </td>
        </tr>
        <tr>
          <th colspan="2">
            <label for="deja_survenu" title="Avez-vous d�j� eu connaissance d'un evenement similaire">Ev�nement d�j� survenu � votre connaissance</label>
          </th>
          <td colspan="2">
            <select name="deja_survenu" title="{{$fiche->_props.deja_survenu}}">
              <option value=""{{if $fiche->deja_survenu===null}} selected="selected"{{/if}}>{{tr}}CFicheEi.deja_survenu.{{/tr}}</option>
              {{html_options options=$fiche->_enumsTrans.deja_survenu selected=$fiche->deja_survenu}}
            </select>
          </td>
        </tr>
        <tr>
          <td colspan="4" class="button">
            <button class="submit" type="submit">
              {{if $fiche->fiche_ei_id}}
              Modifier la Fiche
              {{else}}
              Envoyer la Fiche
              {{/if}}
            </button>
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
</table>