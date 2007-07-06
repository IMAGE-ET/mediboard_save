{{if !$tab}}
<form name="changeuser" action="?m=admin&amp;a={{$a}}" method="post" onsubmit="return checkForm(this)">
{{else}}
<form name="changeuser" action="?m=admin&amp;tab={{$tab}}" method="post" onsubmit="return checkForm(this)">
{{/if}}
<input type="hidden" name="dosql" value="do_preference_aed" />
<input type="hidden" name="pref_user" value="{{$user_id}}" />
<input type="hidden" name="del" value="0" />

{{if $tab && $can->edit && $user_id}}
<a href="index.php?m={{$m}}&amp;tab=edit_prefs&amp;user_id=0" class="buttonedit">
  Editer les Pr�f�rences par D�faut
</a>
{{/if}}
<table class="form">
  <tr>
    <th colspan="2" class="title">
      {{if $user_id}}
      {{tr}}User Preferences{{/tr}} : {{$user->_view}}
      {{else}}
      {{tr}}User Preferences{{/tr}} : {{tr}}Default{{/tr}}
      {{/if}}
    </th>
  </tr>

  <!-- Tous modules confondus -->
  {{assign var="module" value="GENERALE"}}

  {{assign var="var" value="LOCALE"}}
  <tr>
    <th>
      <label for="pref_name[{{$var}}]" title="Veuillez choisir le langage que vous souhaiter utiliser">{{tr}}Langage{{/tr}}</label>
    </th>
    <td>
      <select name="pref_name[{{$var}}]" class="text" size="1">
        {{foreach from=$locales|smarty:nodefaults item=currLocale key=keyLocale}}
        <option value="{{$keyLocale}}" {{if $keyLocale==$prefsUser.$module.$var}}selected="selected"{{/if}}>
          {{tr}}{{$currLocale}}{{/tr}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  
  {{assign var="var" value="UISTYLE"}}
  <tr>
    <th>
      <label for="pref_name[{{$var}}]" title="Veuillez choisir l'apparence que vous souhaiter utiliser">
        {{tr}}User Interface Style{{/tr}}
      </label>
    </th>
    <td>
      <select name="pref_name[{{$var}}]" class="text" size="1">
        {{foreach from=$styles|smarty:nodefaults item=currStyles key=keyStyles}}
        <option value="{{$keyStyles}}" {{if $keyStyles==$prefsUser.$module.$var}}selected="selected"{{/if}}>
          {{tr}}{{$currStyles}}{{/tr}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  
  {{assign var="var" value="MenuPosition"}}
  <tr>
    <th>
      <label for="pref_name[{{$var}}]" title="{{tr}}pref-{{$var}}-desc{{/tr}}">
        {{tr}}pref-{{$var}}{{/tr}}
      </label>
    </th>
    <td>
      <select name="pref_name[{{$var}}]">
        <option value="top"  {{if $prefsUser.$module.$var == "top"  }}selected="selected"{{/if}}>{{tr}}pref-{{$var}}-top{{/tr}}</option>
        <option value="left" {{if $prefsUser.$module.$var == "left" }}selected="selected"{{/if}}>{{tr}}pref-{{$var}}-left{{/tr}}</option>
      </select>
    </td>
  </tr>
    
  {{assign var="var" value="DEFMODULE"}}
  <tr>
    <th>
      <label for="pref_name[{{$var}}]" title="Veuillez choisir le module par d�faut � afficher">{{tr}}Module par d�faut{{/tr}}</label>
    </th>
    <td>
      <select name="pref_name[{{$var}}]" class="text" size="1">
        {{foreach from=$modules|smarty:nodefaults item=currModule key=keyModule}}
        <option value="{{$currModule->mod_name}}" {{if $currModule->mod_name==$prefsUser.$module.$var}}selected="selected"{{/if}}>
          {{tr}}module-{{$currModule->mod_name}}-court{{/tr}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>  
  
  {{assign var="module" value="dPpatients"}}
  <!-- Pr�f�rences pour le module {{$module}} -->
  {{if $prefsUser.$module}}  

  {{assign var="var" value="DEPARTEMENT"}}
  <tr>
    <th class="category" colspan="2">{{tr}}module-{{$module}}-long{{/tr}}</th>
  </tr>
  <tr>
    <th>
      <label for="pref_name[{{$var}}]" title="Veuillez choisir le num�ro du d�partement par d�faut � utiliser">{{tr}}N� du d�partement par d�faut{{/tr}}</label>
    </th>
    <td>
      <input type="text" name="pref_name[{{$var}}]" value="{{$prefsUser.$module.$var}}" maxlength="3" size="4" class="num minMax|0|999"/>
    </td>
  </tr>
  {{/if}}
  
  {{assign var="module" value="dPcabinet"}}
  <!-- Pr�f�rences pour le module {{$module}} -->
  {{if $prefsUser.$module}}  
  
  {{assign var="var" value="AFFCONSULT"}}
  <tr>
    <th class="category" colspan="2">{{tr}}module-{{$module}}-long{{/tr}}</th>
  </tr>

  <tr>
    <th>
      <label for="pref_name[{{$var}}]" title="Type de vue par d�faut des consultations">{{tr}}Vue des Consultations par d�faut{{/tr}}</label>
    </th>
    <td>
      <select name="pref_name[{{$var}}]">
        <option value="0"{{if $prefsUser.$module.$var == "0"}}selected="selected"{{/if}}>Tout afficher</option>
        <option value="1"{{if $prefsUser.$module.$var == "1"}}selected="selected"{{/if}}>Cacher les Termin�es</option>
      </select>
    </td>
  </tr>

  {{assign var="var" value="MODCONSULT"}}
  <tr>
    <th>
      <label for="pref_name[{{$var}}]" title="Mode d'affichage des consultations">{{tr}}Mode d'affichage des Consultations{{/tr}}</label>
    </th>
    <td>
      <select name="pref_name[{{$var}}]">
        <option value="0"{{if $prefsUser.$module.$var == "0"}}selected="selected"{{/if}}>Classique</option>
        <option value="1"{{if $prefsUser.$module.$var == "1"}}selected="selected"{{/if}}>Avanc�e</option>
      </select>
    </td>
  </tr>

  {{assign var="var" value="GestionFSE"}}
  <tr>
    <th>
      <label for="pref_name[{{$var}}]" title="{{tr}}pref-{{$var}}-desc{{/tr}}">{{tr}}pref-{{$var}}{{/tr}}</label>
    </th>
    <td>
      <select name="pref_name[{{$var}}]">
        <option value="0"{{if $prefsUser.$module.$var == "0"}}selected="selected"{{/if}}>{{tr}}bool.0{{/tr}}</option>
        <option value="1"{{if $prefsUser.$module.$var == "1"}}selected="selected"{{/if}}>{{tr}}bool.1{{/tr}}</option>
      </select>
    </td>
  </tr>

  {{assign var="var" value="DossierCabinet"}}
  <tr>
    <th>
      <label for="pref_name[{{$var}}]" title="{{tr}}pref-{{$var}}-desc{{/tr}}">{{tr}}pref-{{$var}}{{/tr}}</label>
    </th>
    <td>
      <select name="pref_name[{{$var}}]">
        <option value="dPcabinet" {{if $prefsUser.$module.$var == "dPcabinet" }}selected="selected"{{/if}}>{{tr}}module-dPcabinet-court {{/tr}}</option>
        <option value="dPpatients"{{if $prefsUser.$module.$var == "dPpatients"}}selected="selected"{{/if}}>{{tr}}module-dPpatients-court{{/tr}}</option>
      </select>
    </td>
  </tr>

  {{assign var="var" value="InterMaxDir"}}
  <tr>
    <th>
      <label for="pref_name[{{$var}}]" title="{{tr}}pref-{{$var}}-desc{{/tr}}">
        {{tr}}pref-{{$var}}{{/tr}}
      </label>
    </th>
    <td>
      <input class="str" type="text" size="40" name="pref_name[{{$var}}]" value="{{$prefsUser.$module.$var}}" />
    </td>
  </tr>

  {{assign var="var" value="DefaultPeriod"}}
  <tr>
    <th>
      <label for="pref_name[{{$var}}]" title="{{tr}}pref-{{$var}}-desc{{/tr}}">{{tr}}pref-{{$var}}{{/tr}}</label>
    </th>
    <td>
      <select name="pref_name[{{$var}}]">
        <option value="day"   {{if $prefsUser.$module.$var == "day"  }}selected="selected"{{/if}}>{{tr}}Period.day{{/tr}}</option>
        <option value="week"  {{if $prefsUser.$module.$var == "week" }}selected="selected"{{/if}}>{{tr}}Period.week{{/tr}}</option>
        <option value="month" {{if $prefsUser.$module.$var == "month"}}selected="selected"{{/if}}>{{tr}}Period.month{{/tr}}</option>
      </select>
    </td>
  </tr>

  {{assign var="var" value="simpleCabinet"}}
  <tr>
    <th>
      <label for="pref_name[{{$var}}]" title="{{tr}}pref-{{$var}}-desc{{/tr}}">{{tr}}pref-{{$var}}{{/tr}}</label>
    </th>
    <td>
      <select name="pref_name[{{$var}}]">
        <option value="0" {{if $prefsUser.$module.$var == 0 }}selected="selected"{{/if}}>avec hospitalisation</option>
        <option value="1" {{if $prefsUser.$module.$var == 1 }}selected="selected"{{/if}}>sans hospitalisation</option>
      </select>
    </td>
  </tr>

  {{assign var="var" value="ccam"}}
  <tr>
    <th>
      <label for="pref_name[{{$var}}]" title="{{tr}}pref-{{$var}}-desc{{/tr}}">{{tr}}pref-{{$var}}{{/tr}}</label>
    </th>
    <td>
      <select name="pref_name[{{$var}}]">
        <option value="0" {{if $prefsUser.$module.$var == 0 }}selected="selected"{{/if}}>Cacher</option>
        <option value="1" {{if $prefsUser.$module.$var == 1 }}selected="selected"{{/if}}>Visible</option>
      </select>
    </td>
  </tr>

  <tr>
    <th class="category" colspan="2">{{tr}}module-{{$module}}-long{{/tr}} - Anesth</th>
  </tr>

  {{assign var="var" value="AUTOADDSIGN"}}
  <tr>
    <th>
      <label for="pref_name[{{$var}}]" title="Ajout automatique des �l�ments significatifs">{{tr}}Ajout automatique des �l�ments significatifs{{/tr}}</label>
    </th>
    <td>
      <select name="pref_name[{{$var}}]">
        <option value="0"{{if $prefsUser.$module.$var == "0"}}selected="selected"{{/if}}>Non</option>
        <option value="1"{{if $prefsUser.$module.$var == "1"}}selected="selected"{{/if}}>Oui</option>
      </select>
    </td>
  </tr>
  {{/if}}

  {{assign var="module" value="system"}}
  <!-- Pr�f�rences pour le module {{$module}} -->
  {{if $prefsUser.$module}}  
  <tr>
    <th class="category" colspan="2">{{tr}}module-{{$module}}-long{{/tr}}</th>
  </tr>

  {{assign var="var" value="INFOSYSTEM"}}
  <tr>
    <th>
      <label for="pref_name[{{$var}}]" title="Afficher les informations syst�me">{{tr}}Afficher les informations syst�me{{/tr}}</label>
    </th>
    <td>
      <select name="pref_name[{{$var}}]">
        <option value="0"{{if $prefsUser.$module.$var == "0"}}selected="selected"{{/if}}>Cacher</option>
        <option value="1"{{if $prefsUser.$module.$var == "1"}}selected="selected"{{/if}}>Visible</option>
      </select>
    </td>
  </tr>
  {{/if}}
  
  {{assign var="module" value="dPplanningOp"}}
  <!-- Pr�f�rences pour le module {{$module}} -->
  {{if $prefsUser.$module}}  
  <tr>
    <th class="category" colspan="2">{{tr}}module-{{$module}}-long{{/tr}}</th>
  </tr>

  {{assign var="var" value="mode_dhe"}}
  <tr>
    <th>
      <label for="pref_name[{{$var}}]" title="{{tr}}pref-{{$var}}-desc{{/tr}}">{{tr}}pref-{{$var}}{{/tr}}</label>
    </th>
    <td>
      <select name="pref_name[{{$var}}]">
        <option value="0"{{if $prefsUser.$module.$var == "0"}}selected="selected"{{/if}}>{{tr}}pref-{{$var}}-0{{/tr}}</option>
        <option value="1"{{if $prefsUser.$module.$var == "1"}}selected="selected"{{/if}}>{{tr}}pref-{{$var}}-1{{/tr}}</option>
      </select>
    </td>
  </tr>
  {{/if}}
  
  <tr>
    <td class="button" colspan="2">
      <button type="submit" class="submit">
        {{tr}}submit{{/tr}}
      </button>
    </td>
  </tr>
</table>
</form>