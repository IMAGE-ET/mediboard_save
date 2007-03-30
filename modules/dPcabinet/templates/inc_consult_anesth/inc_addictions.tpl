<script type="text/javascript">
function submitAddiction(oForm){
  submitFormAjax(oForm, 'systemMsg', { onComplete : reloadAntecedents });
}
function finAddiction(oForm){
  oForm._hidden_addiction.value = oForm.addiction.value;
  oForm.addiction.value = "";
  oForm._helpers_addiction.value = "";
}
{{if $_is_anesth}}
  function copyAddiction(addiction_id){
   var oForm = document.frmCopyAddiction;
   oForm.addiction_id.value = addiction_id;
   oForm.object_class.value  = "CConsultAnesth";
   oForm.object_id.value     = "{{$consult_anesth->consultation_anesth_id}}";
   submitFormAjax(oForm, 'systemMsg', { waitingText : null, onComplete : reloadAntecedentsAnesth });
  }
{{/if}}
</script>

<hr />
<form name="editTabacFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="dosql" value="do_consult_anesth_aed" />
{{if $_is_anesth}}
  {{mb_field object=$consult_anesth field="consultation_anesth_id" hidden=1 prop=""}}
  {{mb_field object=$consult_anesth field="listCim10" hidden=1 prop=""}}
{{/if}}
</form>

<form name="editAddictFrm" action="?m=dPcabinet" method="post">
<input type="hidden" name="m" value="dPpatients" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="dosql" value="do_addiction_aed" />
<input type="hidden" name="object_id" value="{{$patient->_id}}" />
<input type="hidden" name="object_class" value="CPatient" />
{{if $_is_anesth}}
{{mb_field object=$consult_anesth field="consultation_anesth_id" hidden=1 prop=""}}
{{/if}}
<table class="form">

  <tr>
    <td colspan="2"><strong>Addiction</strong></td>
    <td>
      {{mb_label object=$addiction field="addiction"}}
      {{* Tout sur une ligne pour �viter les espaces qui s'affichent sous IE *}}
      {{foreach from=$addiction->_aides.addiction item=curr_list key=keyEnum}}{{if $keyEnum == "no_enum"}}{{assign var=dependOn value=""}}{{assign var=styleSelect value=""}}{{else}}{{assign var=dependOn value=$keyEnum}}{{assign var=styleSelect value="style='display:none;'"}}{{/if}}<select name="_helpers_addiction{{if $dependOn}}-{{$dependOn}}{{/if}}" size="1" onchange="pasteHelperContent(this)" {{$styleSelect|smarty:nodefaults}}>
        <option value="">&mdash; Choisir une aide</option>
        {{foreach from=$curr_list item=list_aides key=sTitleOpt}}
          <optgroup label="{{$sTitleOpt}}">
            {{html_options options=$list_aides}}
          </optgroup>
        {{/foreach}}
      </select>{{/foreach}}
      <input type="hidden" name="_hidden_addiction" value="" />
      <button class="new notext" title="Ajouter une aide � la saisie" type="button" onclick="addHelp('CAddiction', this.form._hidden_addiction, 'addiction')">
        Nouveau
      </button>
    </td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$addiction field="type"}}</th>
    <td>
      {{html_options name="type" options=$addiction->_enumsTrans.type onchange="putHelperContent(this,'addiction')"}}
    </td>
    <td>
      <textarea name="addiction" onblur="if(verifNonEmpty(this)){submitAddiction(this.form);finAddiction(this.form);}"></textarea>
    </td>
  </tr>
  <tr>
    <td class="button" colspan="3">
      <button class="submit" type="button" onclick="if(verifNonEmpty(this.form.addiction)){submitAddiction(this.form);finAddiction(this.form);}">Ajouter</button>
    </td>
  </tr>
</table>
</form>