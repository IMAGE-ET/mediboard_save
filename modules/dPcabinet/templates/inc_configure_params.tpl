<script type="text/javascript">
  function showCreateSejour(input) {
    $(input).up('tr').next().setVisible($V(input) == 1);
    if ($V(input) == 0) {
      $V(input.form.elements["dPcabinet[CConsultation][create_consult_sejour]"] , "0");
    }
  }
  
  Main.add(function () {
    var nodeList = getForm('editConfig').elements["dPcabinet[CConsultation][attach_consult_sejour]"];
    showCreateSejour(nodeList[1-$V(nodeList)]);
    
    getForm("editConfig")["dPcabinet[CConsultation][minutes_before_consult_sejour]"].addSpinner({min:1, max:360});
    getForm("editConfig")["dPcabinet[CConsultation][hours_after_changing_prat]"].addSpinner({min:0, max:48});
  });
</script>



<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="dosql" value="do_configure" />

<table class="form">
  <colgroup>
    <col style="width: 30%;" />
  </colgroup>

  <!-- Prise de rendez-vous -->  
  <tr>
    <th class="category" colspan="2">Prise de rendez-vous</th>
    <td rowspan="100">
      <div class="big-info">
        <b>Format des champs auto :</b>
        <ul>
          <li><tt>%N</tt> - Nom praticien interv</li>
          <li><tt>%P</tt> - Pr�nom praticien interv</li>
          <li><tt>%S</tt> - Initiales praticien interv</li>
          <li><tt>%L</tt> - Libell� intervention</li>
          <li><tt>%I</tt> - Jour intervention</li>
          <li><tt>%i</tt> - Heure intervention</li>
          <li><tt>%E</tt> - Jour d'entr�e</li>
          <li><tt>%e</tt> - Heure d'entr�e</li>
          <li><tt>%T</tt> - Type de s�jour (A, O, E...)</li>
        </ul>
      </div>
    </td>
  </tr>
  
  {{mb_include module=system template=inc_config_bool var=keepchir}}
  
  {{mb_include module=system template=inc_config_enum var=display_nb_consult values="none|cab|etab"}}
  
  <!-- Champs de l'onglet examen -->
  {{assign var="class" value="CConsultation"}}
    
  <tr>
    <th class="category" colspan="2">{{tr}}{{$class}}{{/tr}}</th>
  </tr>
  
  {{mb_include module=system template=inc_config_bool var=use_last_consult}}
  
  {{mb_include module=system template=inc_config_bool var=show_examen}}
  
  {{mb_include module=system template=inc_config_bool var=show_histoire_maladie}}
  
  {{mb_include module=system template=inc_config_bool var=show_conclusion}}
  
  {{mb_include module=system template=inc_config_bool var=attach_consult_sejour onchange="showCreateSejour(this)"}}
  
  {{mb_include module=system template=inc_config_bool var=search_sejour_all_groups}}

  {{mb_include module=system template=inc_config_bool var=create_consult_sejour}}
  
  {{mb_include module=system template=inc_config_str var=minutes_before_consult_sejour size="3" suffix="min"}}
  
  {{mb_include module=system template=inc_config_str var=hours_after_changing_prat size="3" suffix="h"}}
  
  {{mb_include module=system template=inc_config_bool var=consult_readonly}}
  
  {{mb_include module=system template=inc_config_bool var=fix_doc_edit}}
  
  {{mb_include module=system template=inc_config_bool var=consult_facture}}
  
  {{mb_include module=system template=inc_config_bool var=surbooking_readonly}}
  
  <!-- CConsultAnesth -->  
  {{assign var="class" value="CConsultAnesth"}}
    
  <tr>
    <th class="category" colspan="2">{{tr}}{{$class}}{{/tr}}</th>
  </tr>
  
  <tr>
    {{assign var="var" value="feuille_anesthesie"}}
    <th>
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <select class="str" name="{{$m}}[{{$class}}][{{$var}}]">
        <option value="print_fiche"  {{if "print_fiche" == $conf.$m.$class.$var}} selected="selected" {{/if}}>{{tr}}config-{{$m}}-{{$class}}-{{$var}}-print_fiche{{/tr}}</option>
        <option value="print_fiche1" {{if "print_fiche1" == $conf.$m.$class.$var}} selected="selected" {{/if}}>{{tr}}config-{{$m}}-{{$class}}-{{$var}}-print_fiche1{{/tr}}</option>
      </select>
    </td>
  </tr>
  {{assign var="var" value="format_auto_motif"}}
  <tr>
    <th>
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input type="text" name="{{$m}}[{{$class}}][{{$var}}]" value="{{$conf.$m.$class.$var}}" />
    </td>
  </tr>
  
  {{assign var="var" value="format_auto_rques"}}
  <tr>
    <th>
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input type="text" name="{{$m}}[{{$class}}][{{$var}}]" value="{{$conf.$m.$class.$var}}" />
    </td>
  </tr>
  
  {{mb_include module=system template=inc_config_bool var=show_mallampati}}
  
  
  {{mb_include module=system template=inc_config_bool var=view_premedication}}
  {{mb_include module=system template=inc_config_bool var=show_facteurs_risque}}
  
  <!-- CPlageconsult -->  
  {{assign var="class" value="CPlageconsult"}}
    
  <tr>
    <th class="category" colspan="2">{{tr}}{{$class}}{{/tr}}</th>
  </tr>
  
  {{assign var="var" value="hours_start"}}
  <tr>
    <th>
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <select class="num" name="{{$m}}[{{$class}}][{{$var}}]">
      {{foreach from=$hours item=_hour}}
        <option value="{{$_hour}}" {{if $_hour == $conf.$m.$class.$var}} selected="selected" {{/if}}>
          {{$_hour|string_format:"%02d"}}
        </option>
      {{/foreach}}
      </select>
    </td>
  </tr>
  
  {{assign var="var" value="hours_stop"}}
  <tr>
    <th>
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <select class="num" name="{{$m}}[{{$class}}][{{$var}}]">
      {{foreach from=$hours item=_hour}}
        <option value="{{$_hour}}" {{if $_hour == $conf.$m.$class.$var}} selected="selected" {{/if}}>
          {{$_hour|string_format:"%02d"}}
        </option>
      {{/foreach}}
      </select>
    </td>
  </tr>
  
  {{assign var="var" value="minutes_interval"}}
  <tr>
    <th>
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <select class="num" name="{{$m}}[{{$class}}][{{$var}}]">
      {{foreach from=$intervals item=_interval}}
        <option value="{{$_interval}}" {{if $_interval == $conf.$m.$class.$var}} selected="selected" {{/if}}>
          {{$_interval}}
        </option>
      {{/foreach}}
      </select>
    </td>
  </tr>
  
  
  <!-- CPrescription -->  
  {{assign var="class" value="CPrescription"}}
  <tr>
    <th class="category" colspan="2">Prescriptions</th>
  </tr>

  {{mb_include module=system template=inc_config_bool var=view_prescription}}
  
  {{if $user->_user_username == "admin"}}
    <!-- Comptabilit� affich�e uniquement pour le comtpe "admin"-->
    {{assign var="class" value="Comptabilite"}}
    <tr>
      <th class="category" colspan="2">Comptabilit�</th>
    </tr>
    {{mb_include module=system template=inc_config_bool var=show_compta_tiers}}
  {{/if}}
  
  <tr>
    <td class="button" colspan="6">
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>
</table>

</form>