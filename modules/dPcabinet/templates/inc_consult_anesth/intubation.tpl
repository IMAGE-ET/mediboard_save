<script language="Javascript" type="text/javascript">
function verifIntubDifficileAndSave(oForm){
  if(oForm.mallampati[2].checked || oForm.mallampati[3].checked
    || oForm.bouche[0].checked || oForm.bouche[1].checked
    || oForm.distThyro[0].checked){
  
    // Avertissement d'intubatino difficile
    $('divAlertIntubDiff').style.visibility = "visible";
  }else{
    $('divAlertIntubDiff').style.visibility = "hidden";
  }
  submitFormAjax(oForm, 'systemMsg')
}
</script>
<form name="editFrmIntubation" action="?m=dPcabinet" method="post">
<input type="hidden" name="m" value="dPcabinet" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="dosql" value="do_consult_anesth_aed" />
{{mb_field object=$consult_anesth field="consultation_anesth_id" hidden=1 prop=""}}
<table class="form">
  <tr>
    <th colspan="6" class="category">Condition d'intubation</th>
  </tr>
  <tr>
    {{foreach from=$consult_anesth->_enumsTrans.mallampati|smarty:nodefaults key=curr_mallampati item=trans_mallampati}}
    <td rowspan="2" class="button">
      <label for="mallampati_{{$curr_mallampati}}" title="Mallampati de {{$trans_mallampati}}">
        <img src="images/pictures/{{$curr_mallampati}}.gif" alt="{{$trans_mallampati}}" />
        <br />
        <input type="radio" name="mallampati" value="{{$curr_mallampati}}" {{if $consult_anesth->mallampati == $curr_mallampati}}checked="checked"{{/if}} onclick="verifIntubDifficileAndSave(this.form);" />
        {{$trans_mallampati}}
      </label>
    </td>
    {{/foreach}}

    <th>{{mb_label object=$consult_anesth field="bouche" defaultFor="bouche_m20"}}</th>
    <td>
      {{mb_field object=$consult_anesth field="bouche" typeEnum="radio" separator="<br />" onclick="verifIntubDifficileAndSave(this.form);"}}
    </td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$consult_anesth field="distThyro" defaultFor="distThyro_m65"}}</th>
    <td>
      {{mb_field object=$consult_anesth field="distThyro" typeEnum="radio" separator="<br />" onclick="verifIntubDifficileAndSave(this.form);"}}
    </td>
  </tr>

  <tr>
    <td colspan="4" rowspan="2">Sch�ma des dents</td>
    <th>{{mb_label object=$consult_anesth field="etatBucco"}}</th>
    <td>
      <select name="_helpers_etatBucco" size="1" onchange="pasteHelperContent(this);this.form.etatBucco.onchange();">
        <option value="">&mdash; Choisir une aide</option>
        {{html_options options=$consult_anesth->_aides.etatBucco.no_enum}}
      </select>
      <button class="new notext" title="Ajouter une aide � la saisie" type="button" onclick="addHelp('CConsultAnesth', this.form.etatBucco)">{{tr}}New{{/tr}}</button><br />
      {{mb_field object=$consult_anesth field="etatBucco" onchange="submitFormAjax(this.form, 'systemMsg')"}}
    </td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$consult_anesth field="conclusion"}}</th>
    <td>
      <select name="_helpers_conclusion" size="1" onchange="pasteHelperContent(this);this.form.conclusion.onchange();">
        <option value="">&mdash; Choisir une aide</option>
        {{html_options options=$consult_anesth->_aides.conclusion.no_enum}}
      </select>
      <button class="new notext" title="Ajouter une aide � la saisie" type="button" onclick="addHelp('CConsultAnesth', this.form.conclusion)">{{tr}}New{{/tr}}</button><br />
      {{mb_field object=$consult_anesth field="conclusion" onchange="submitFormAjax(this.form, 'systemMsg')"}}
    </td>
  </tr>
  <tr>
    <td colspan="6" class="button">
      <div id="divAlertIntubDiff" style="float:right;color:#F00;{{if !$consult_anesth->_intub_difficile}}visibility:hidden;{{/if}}"><strong>Intubation Difficile Pr�visible</strong></div>
    </td>
  </tr>
</table>
</form>