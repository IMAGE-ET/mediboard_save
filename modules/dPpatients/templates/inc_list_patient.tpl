{{mb_include_script path="includes/javascript/intermax.js"}}

<script type="text/javascript">
Intermax.ResultHandler["Lire Vitale"] = function() {
  var url = new Url;
  url.setModuleTab("dPpatients", "vw_idx_patients");
  url.addParam("useVitale", 1);
  url.redirect();
}
</script>

{{if $app->user_prefs.GestionFSE}}
{{assign var="debug" value="false"}}
{{include file="../../dPpatients/templates/inc_intermax.tpl"}}
{{/if}}

<form name="find" action="./index.php" method="get">

<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="tab" value="{{$tab}}" />
<input type="hidden" name="new" value="1" />

<table class="form">
  <tr>
    <th class="category" colspan="4">Recherche d'un dossier patient</th>
  </tr>

  <tr>
    <th><label for="nom" title="Nom du patient � rechercher, au moins les premi�res lettres">Nom</label></th>
    <td><input tabindex="1" type="text" name="nom" value="{{$nom|stripslashes}}" /></td>
    <th><label for="cp" title="Code postal du patient � rechercher">Code postal</label></th>
    <td><input tabindex="4" type="text" name="cp" value="{{$cp|stripslashes}}" /></td>
  </tr>
  
  <tr>
    <th><label for="prenom" title="Pr�nom du patient � rechercher, au moins les premi�res lettres">Pr�nom</label></th>
    <td><input tabindex="2" type="text" name="prenom" value="{{$prenom|stripslashes}}" /></td>
    <th><label for="ville" title="Ville du patient � rechercher">Ville</label></th>
    <td><input tabindex="5" type="text" name="ville" value="{{$ville|stripslashes}}" /></td>
  </tr>
  
  <tr>
    <th><label for="jeuneFille" title="Nom de naissance">Nom de naissance</label></th>
    <td><input tabindex="3" type="text" name="jeuneFille" value="{{$jeuneFille|stripslashes}}" /></td>
    <td colspan="2" />
  </tr>
  
  
  <tr>
    <th colspan="2">
      <label for="Date_Day" title="Date de naissance du patient � rechercher">
        Date de naissance
      </label>
      <input type="hidden" name="naissance" {{if $naissance == "on"}}value="on"{{else}}value="off"{{/if}} />
    </th>
    <td colspan="2">
         {{html_select_date
           time=0000-00-00
           start_year=1900
           field_order=DMY
           day_empty="Jour"
           month_empty="Mois"
           year_empty="Ann�e"
           day_extra="tabindex='6'"
           month_extra="tabindex='7'"
           year_extra="tabindex='8'"
           all_extra="style='display:inline;'"}}
           
    </td>
  </tr>
  
  <tr>
    <td class="button" colspan="4">
      {{if $board}}
        <button class="search" type="button" onclick="updateListPatients()">Rechercher</button>
      {{else}}
        <button class="search" type="submit">Rechercher</button>
   {{if $app->user_prefs.GestionFSE}}
     <button class="search" type="button" onclick="Intermax.trigger('Lire Vitale');">
       Lire Vitale
     </button>
     <button class="tick" type="button" onclick="Intermax.result();">
       R�sultat Vitale
     </button>
   {{/if}}
      {{/if}}
    </td>
  </tr>
</table>
</form>

<form name="fusion" action="index.php" method="get">
<input type="hidden" name="m" value="dPpatients" />
<input type="hidden" name="a" value="fusion_pat" />
<table class="tbl">
  <tr>
    <th><button type="submit" class="search">{{tr}}Merge{{/tr}}</button></th>
    <th>
      Patient
      ({{$patientsCount}} {{tr}}found{{/tr}})
    </th>
    <th>Date de naissance</th>
    <th>Adresse</th>
  </tr>

  {{mb_ternary var="tabPatient" test=$board 
     value="vw_full_patients&patient_id=" 
     other="vw_idx_patients&patient_id="}}
  
  {{foreach from=$patients item=curr_patient}}
  <tr {{if $patient->_id == $curr_patient->_id}}class="selected"{{/if}}>
    <td><input type="checkbox" name="fusion_{{$curr_patient->_id}}" /></td>
    <td class="text">
      <a href="?m=dPpatients&amp;tab={{$tabPatient}}{{$curr_patient->_id}}">
        {{mb_value object=$curr_patient field="_view"}}
      </a>
    </td>
    <td class="text">
      <a href="?m=dPpatients&amp;tab={{$tabPatient}}{{$curr_patient->_id}}">
        {{mb_value object=$curr_patient field="naissance"}}
      </a>
    </td>
    <td class="text">
      <a href="?m=dPpatients&amp;tab={{$tabPatient}}{{$curr_patient->_id}}">
        {{mb_value object=$curr_patient field="adresse"}}
        {{mb_value object=$curr_patient field="cp"}}
        {{mb_value object=$curr_patient field="ville"}}
      </a>
    </td>
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="100"><em>Aucun r�sultat exact</em></td>
  </tr>
  {{/foreach}}
  {{if $patientsSoundex|@count}}
  <tr>
    <th colspan="4">
      R�sultats proches
      ({{$patientsSoundexCount}} {{tr}}found{{/tr}})
      
    </th>
  </tr>
  {{/if}}
  {{foreach from=$patientsSoundex item=curr_patient}}
  <tr {{if $patient->_id == $curr_patient->_id}}class="selected"{{/if}}>
    <td><input type="checkbox" name="fusion_{{$curr_patient->_id}}" /></td>
    <td class="text">
      <a href="?m=dPpatients&amp;tab={{$tabPatient}}{{$curr_patient->_id}}">
        {{mb_value object=$curr_patient field="_view"}}
      </a>
    </td>
    <td class="text">
      <a href="?m=dPpatients&amp;tab={{$tabPatient}}{{$curr_patient->_id}}">
        {{mb_value object=$curr_patient field="naissance"}}
      </a>
    </td>
    <td class="text">
      <a href="?m=dPpatients&amp;tab={{$tabPatient}}{{$curr_patient->_id}}">
        {{mb_value object=$curr_patient field="adresse"}}
        {{mb_value object=$curr_patient field="cp"}}
        {{mb_value object=$curr_patient field="ville"}}
      </a>
    </td>
  </tr>
  {{/foreach}}
  
</table>
</form>
      