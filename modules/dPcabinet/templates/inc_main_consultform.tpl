<form class="watch" name="editFrmExams" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
<input type="hidden" name="m" value="dPcabinet" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="dosql" value="do_consultation_aed" />
{{mb_field object=$consult field="consultation_id" hidden=1 prop=""}}

<table class="form">
  <tr>
    <th class="category">
      {{mb_label object=$consult field="motif"}}
    </th>
    <th>
      <select name="_helpers_motif" size="1" onchange="pasteHelperContent(this);this.form.motif.onchange();">
        <option value="">&mdash; Choisir une aide</option>
        {{html_options options=$consult->_aides.motif.no_enum}}
      </select>
      <button class="new notext" title="Ajouter une aide � la saisie" type="button" onclick="addHelp('CConsultation', this.form.motif)">
        Nouveau
      </button>
    </th>
    <th class="category">
      {{mb_label object=$consult field="rques"}}
    </th>
    <th>
      <select name="_helpers_rques" size="1" onchange="pasteHelperContent(this);this.form.rques.onchange();">
        <option value="">&mdash; Choisir une aide</option>
        {{html_options options=$consult->_aides.rques.no_enum}}
      </select>
      <button class="new notext" title="Ajouter une aide � la saisie" type="button" onclick="addHelp('CConsultation', this.form.rques)">
        Nouveau
      </button>
    </th>
  </tr>
  <tr>
    <td class="text" colspan="2">{{mb_field object=$consult field="motif" rows="5" onchange="submitFormAjax(this.form, 'systemMsg');"}}</td>
    <td class="text" colspan="2">{{mb_field object=$consult field="rques" rows="5" onchange="submitFormAjax(this.form, 'systemMsg');"}}</td>
  </tr>
  <tr>
    <th class="category">
      {{mb_label object=$consult field="examen"}}
    </th>
    <th {{if !$app->user_prefs.view_traitement}}colspan="3"{{/if}}>
      <select name="_helpers_examen" size="1" onchange="pasteHelperContent(this);this.form.examen.onchange();">
        <option value="">&mdash; Choisir une aide</option>
        {{html_options options=$consult->_aides.examen.no_enum}}
      </select>
      <button class="new notext" title="Ajouter une aide � la saisie" type="button" onclick="addHelp('CConsultation', this.form.examen)">
        Nouveau
      </button>
    </th>
    
    {{if $app->user_prefs.view_traitement}}
    <th class="category">
      {{mb_label object=$consult field="traitement"}}
    </th>
    <th>
      <select name="_helpers_traitement" size="1" onchange="pasteHelperContent(this);this.form.traitement.onchange();">
        <option value="">&mdash; Choisir une aide</option>
        {{html_options options=$consult->_aides.traitement.no_enum}}
      </select>
      <button class="new notext" title="Ajouter une aide � la saisie" type="button" onclick="addHelp('CConsultation', this.form.traitement)">
        Nouveau
      </button>
    </th>
  </tr>
  {{/if}}
  
  <tr>
    <td class="text" {{if $app->user_prefs.view_traitement}}colspan="2"{{else}}colspan="4"{{/if}}>{{mb_field object=$consult field="examen" rows="5" onchange="submitFormAjax(this.form, 'systemMsg');"}}</td>
    {{if $app->user_prefs.view_traitement}}
    <td class="text" colspan="2">{{mb_field object=$consult field="traitement" rows="5" onchange="submitFormAjax(this.form, 'systemMsg');"}}</td>
    {{/if}}
  </tr>
  
  <tr>
    <td class="button" colspan="4">
      <button class="modify" type="button" onclick="submitFormAjax(this.form, 'systemMsg')">
        sauver
      </button>
    </td>
  </tr>
</table>
</form>