{{mb_default var=hide_save_button value=0}}
{{mb_default var=callback_administration value=0}}
{{mb_default var=display_graph value=1}}
{{mb_default var=tri value=""}}

{{mb_default var=show_cat_tabs value="CConstantesMedicales::getConfig"|static_call:"show_cat_tabs"}}

{{mb_default var=show_enable_all_button value="CConstantesMedicales::getConfig"|static_call:"show_enable_all_button"}}

<script type="text/javascript">
submitConstantesMedicales = function(oForm) {
  return onSubmitFormAjax(oForm, {
    onComplete: function () {
      {{if $display_graph}}
        refreshConstantesMedicales($V(oForm.context_class)+'-'+$V(oForm.context_id), 1);
      {{/if}}
      {{if $conf.ref_pays == 2}}
        refreshConstantesMedicalesTri($V(oForm.context_class)+'-'+$V(oForm.context_id), 1);
      {{/if}}
    }
  });
}

calculImcVst = function(form) {
  var imcInfo, imc, vst,
      poids  = parseFloat($V(form.poids)),
      taille = parseFloat($V(form.taille));
  
  if (poids && !isNaN(poids) && poids > 0) {
    vst = {{if $constantes->_ref_patient->sexe=="m"}}70{{else}}65{{/if}} * poids;
  
    if (taille && !isNaN(taille) && taille > 0) {
      imc = Math.round(100 * 100 * 100 * poids / (taille * taille))/100; // Math.round(x*100)/100 == round(x, 2)
      
           if (imc < 15)   imcInfo = "Inanition";
      else if (imc < 18.5) imcInfo = "Maigreur";
      else if (imc > 40)   imcInfo = "Ob�sit� morbide";
      else if (imc > 35)   imcInfo = "Ob�sit� s�v�re";
      else if (imc > 30)   imcInfo = "Ob�sit� mod�r�e";
      else if (imc > 25)   imcInfo = "Surpoids";
    }
  }
  
  $V(form._vst, vst);
  $V(form._imc, imc);
  
  var element = $('constantes_medicales_imc');
  if (element) {
    element.update(imcInfo);
  }
  
  if(typeof(calculPSA) == 'function' && typeof(calculClairance) == 'function') {
    calculPSA(); 
    calculClairance();
  }
}

emptyAndSubmit = function(const_name) {
  var form = getForm("edit-constantes-medicales");
  const_name.each(function(elem) {$V(form[elem], '');});
  return submitConstantesMedicales(form);
}

Main.add(function () {
  var oForm = getForm('edit-constantes-medicales{{$tri}}');
  calculImcVst(oForm);
  if (window.toggleAllGraphs) {
    toggleAllGraphs();
  }
  
  {{if $show_cat_tabs}}
    Control.Tabs.create("constantes-by-type{{$tri}}");
  {{/if}}
});
</script>

{{if ($constantes->_ref_context && $context_guid == $constantes->_ref_context->_guid && !$readonly) || isset($real_context|smarty:nodefaults)}}
  {{assign var=real_context value=1}}
{{else}}
  {{assign var=real_context value=0}}
{{/if}}

<form name="edit-constantes-medicales{{$tri}}" action="?" method="post" onsubmit="return {{if $real_context}}checkForm(this){{else}}false{{/if}}">
  <input type="hidden" name="m" value="dPpatients" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="dosql" value="do_constantes_medicales_aed" />
  {{if !$constantes->datetime}}
  <input type="hidden" name="datetime" value="now" />
  <input type="hidden" name="_new_constantes_medicales" value="1" />
  {{else}}
  <input type="hidden" name="constantes_medicales_id" value="{{$constantes->_id}}" />
  <input type="hidden" name="_new_constantes_medicales" value="0" />
  {{/if}}
  {{mb_field object=$constantes field=context_class hidden=1}}
  {{mb_field object=$constantes field=context_id hidden=1}}
  {{mb_field object=$constantes field=patient_id hidden=1}}
  {{if $callback_administration}}
    <input type="hidden" name="callback" value="submitAdmission" />
  {{/if}}
  {{assign var=const value=$latest_constantes.0}}
  {{assign var=dates value=$latest_constantes.1}}
  {{assign var=all_constantes value="CConstantesMedicales"|static:"list_constantes_type"}}
  
  <input type="hidden" name="_poids" value="{{$const->poids}}" />
  
  {{if $show_cat_tabs}}
  <ul id="constantes-by-type{{$tri}}" class="control_tabs small" style="min-width: 200px;">
    {{foreach from=$all_constantes key=_type item=_list}}
      {{if array_key_exists($_type, $selection)}}
        <li>
          <a href="#type{{$tri}}-{{$_type}}">{{tr}}CConstantesMedicales.type.{{$_type}}{{/tr}}</a>
        </li>
      {{/if}}
    {{/foreach}}
  </ul>
  {{/if}}
  
  <table class="main form constantes">
    <tr>
      <th class="category"></th>
      {{if $real_context}}<th class="category">Saisie</th>{{/if}}
      <th class="category" colspan="{{if $display_graph}}2{{else}}1{{/if}}">Derni�res</th>
      <th class="category">
        {{if $constantes->_id}}
          {{mb_include module=system template=inc_object_history object=$constantes}}
        {{/if}}
      </th>
    </tr>
    
    {{assign var=at_least_one_hidden value=false}}
    {{assign var=constants_list value="CConstantesMedicales"|static:"list_constantes"}}

    {{foreach from=$selection key=_type item=_ranks}}
      <tbody id="type{{$tri}}-{{$_type}}" {{if $show_cat_tabs}} {{if $_type != "vital"}} style="display: none;" {{/if}} {{/if}}>
        {{foreach from=$_ranks key=_rank item=_constants}}
          {{foreach from=$_constants item=_constant}}
            <tr {{if $_rank == "hidden" && ($const->$_constant == "" || !$display_graph)}}
              style="display: none;" class="secondary"
              {{assign var=at_least_one_hidden value=true}}
              {{/if}}>
              <th style="text-align: left;">
                <label for="{{$_constant}}" title="{{tr}}CConstantesMedicales-{{$_constant}}-desc{{/tr}}">
                  {{tr}}CConstantesMedicales-{{$_constant}}-court{{/tr}}

                  {{assign var=_params value=$constants_list.$_constant}}
                  {{if $_params.unit}}
                    <small class="opacity-50">
                      {{if in_array($_constant, " "|explode:"ta ta_gauche ta_droit")}}
                        ({{$conf.dPpatients.CConstantesMedicales.unite_ta}})
                      {{else}}
                        ({{$_params.unit}})
                      {{/if}}
                    </small>
                  {{/if}}
                </label>
              </th>

              {{assign var=_readonly value=null}}
              {{if array_key_exists("formfields", $_params)}}
                {{if $real_context}}
                  <td>
                    {{foreach from=$_params.formfields item=_formfield_name key=_key name=_formfield}}
                      {{assign var=_style value="width:1.7em;"}}
                      {{assign var=_size value=2}}
                      {{if $_params.formfields|@count == 1}}
                        {{assign var=_style value=""}}
                        {{assign var=_size value=3}}
                      {{/if}}

                      {{if !$smarty.foreach._formfield.first}}/{{/if}}
                      {{mb_field object=$constantes field=$_params.formfields.$_key size=$_size style=$_style}}
                    {{/foreach}}
                  </td>
                {{/if}}
                <td style="text-align: center" title="{{$dates.$_constant|date_format:$conf.datetime}}">
                  {{if $const->$_constant}}
                    {{foreach from=$_params.formfields item=_formfield_name key=_key name=_formfield}}
                      {{if !$smarty.foreach._formfield.first}}/{{/if}}
                      {{mb_value object=$const field=$_params.formfields.$_key}}
                    {{/foreach}}
                  {{/if}}
                </td>
              {{else}}
                {{assign var=_hidden value=false}}

                {{if $_constant.0 == "_"}}
                  {{assign var=_readonly value="readonly"}}

                  {{if array_key_exists("formula", $_params)}}
                    {{assign var=_hidden value=true}}
                  {{/if}}
                {{/if}}

                {{if $real_context}}
                  <td>
                    {{if array_key_exists("callback", $_params)}}
                      {{assign var=_callback value=$_params.callback}}
                    {{else}}
                      {{assign var=_callback value=null}}
                    {{/if}}

                    {{mb_field object=$constantes field=$_constant size="3" onchange=$_callback|ternary:"$_callback(this.form)":null readonly=$_readonly hidden=$_hidden}}

                    {{if $_constant == "_imc"}}
                      <div id="constantes_medicales_imc" style="color:#F00;"></div>
                    {{/if}}
                  </td>
                {{/if}}
                <td style="text-align: center" title="{{$dates.$_constant|date_format:$conf.datetime}}">
                  {{mb_value object=$const field=$_constant}}
                  <input type="hidden" name="_last_{{$_constant}}" value="{{$const->$_constant}}" />
                </td>
              {{/if}}

              {{if $display_graph}}
                <td class="narrow">
                  {{if $_constant.0 != "_" || !empty($_params.plot|smarty:nodefaults)}}
                    <input type="checkbox" name="checkbox-constantes-medicales-{{$_constant}}" onclick="toggleGraph('{{$_constant}}', this.checked)" tabIndex="100" />
                  {{/if}}
                </td>
              {{/if}}
              <td>
                {{if $_readonly !="readonly" && $real_context == 1 && $constantes->$_constant != ""}}
                  {{if array_key_exists("formfields", $_params)}}
                    <button type="button" class="cancel notext compact" onclick="emptyAndSubmit({{$_params.formfields|@json|smarty:nodefaults|JSAttribute}});"></button>
                  {{else}}
                    <button type="button" class="cancel notext compact" onclick="emptyAndSubmit(['{{$_constant}}']);"></button>
                  {{/if}}
                {{/if}}
              </td>
            </tr>
          {{/foreach}}
        {{/foreach}}
      </tbody>
    {{/foreach}}

    {{if $real_context}}
      {{if $constantes->datetime}}
      <tr>
        <th>{{mb_title object=$constantes field=datetime}}</th>
        <td colspan="4">{{mb_field object=$constantes field=datetime form="edit-constantes-medicales" register=true}}</td>
      </tr>
      {{/if}}
      <tr>
        <td colspan="5" style="text-align: center;">{{mb_label object=$constantes field=comment}}</td>
      </tr>
      <tr>
        <td colspan="5">{{mb_field object=$constantes field=comment}}</td>
      </tr>
      <tr>
        {{if !$hide_save_button}}
          <td colspan="5" class="button">
            <button class="modify singleclick" onclick="return submitConstantesMedicales(this.form);">
              {{tr}}Save{{/tr}}
            </button>
            {{if $constantes->datetime}}
              <button class="new singleclick" type="button" onclick="$V(this.form.constantes_medicales_id, ''); $V(this.form._new_constantes_medicales, 1); return submitConstantesMedicales(this.form);">
                {{tr}}Create{{/tr}}
              </button>
              <br />
              <button class="trash" type="button" onclick="if (confirm('Etes-vous s�r de vouloir supprimer ce relev� ?')) {$V(this.form.del, 1); return submitConstantesMedicales(this.form);}">
                {{tr}}CConstantesMedicales.delete_all{{/tr}}
              </button>
            {{/if}}
          {{/if}}
        </td>
      </tr>
    {{/if}}
    
    {{if $show_enable_all_button && $at_least_one_hidden}}
    <tr>
      <td colspan="5" class="button">
        <button class="down" type="button" onclick="$$('.constantes .secondary').invoke('toggle')">Afficher toutes les valeurs</button>
      </td>
    </tr>
    {{/if}}
  </table>
</form>