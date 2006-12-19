<form class="watch" name="editFrmExams" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="dosql" value="do_consultation_aed" />
<input type="hidden" name="consultation_id" value="{{$consult->consultation_id}}" />
<input type="hidden" name="_check_premiere" value="{{$consult->_check_premiere}}" />
<table class="form">
  <tr>
    <th class="category">
      <label for="motif" title="Motif de la consultation">Motif</label>
    </th>
    <th>
      <select name="_helpers_motif" size="1" onchange="pasteHelperContent(this);this.form.motif.onchange();">
        <option value="">&mdash; Choisir une aide</option>
        {{html_options options=$consult->_aides.motif}}
      </select>
      <button class="new notext" title="Ajouter une aide � la saisie" type="button" onclick="addHelp('CConsultation', this.form.motif)"/>
    </th>
    <th class="category">
      <label for="rques" title="Remarques concernant la consultation">Remarques</label>
    </th>
    <th>
      <select name="_helpers_rques" size="1" onchange="pasteHelperContent(this);this.form.rques.onchange();">
        <option value="">&mdash; Choisir une aide</option>
        {{html_options options=$consult->_aides.rques}}
      </select>
      <button class="new notext" title="Ajouter une aide � la saisie" type="button" onclick="addHelp('CConsultation', this.form.rques)"/>
    </th>
  </tr>
  <tr>
    <td class="text" colspan="2"><textarea name="motif" rows="5" onchange="submitFormAjax(this.form, 'systemMsg');">{{$consult->motif}}</textarea></td>
    <td class="text" colspan="2"><textarea name="rques" rows="5" onchange="submitFormAjax(this.form, 'systemMsg');">{{$consult->rques}}</textarea></td>
  </tr>
  <tr>
    <th class="category">
      <label for="examen" title="Bilan de l'examen clinique">Examens</label>
    </th>
    <th>
      <select name="_helpers_examen" size="1" onchange="pasteHelperContent(this);this.form.examen.onchange();">
        <option value="">&mdash; Choisir une aide</option>
        {{html_options options=$consult->_aides.examen}}
      </select>
      <button class="new notext" title="Ajouter une aide � la saisie" type="button" onclick="addHelp('CConsultation', this.form.examen)"/>
    </th>
    <th class="category">
      <label for="traitement" title="title">Traitements</label>
    </th>
    <th>
      <select name="_helpers_traitement" size="1" onchange="pasteHelperContent(this);this.form.traitement.onchange();">
        <option value="">&mdash; Choisir une aide</option>
        {{html_options options=$consult->_aides.traitement}}
      </select>
      <button class="new notext" title="Ajouter une aide � la saisie" type="button" onclick="addHelp('CConsultation', this.form.traitement)"/>
    </th>
  </tr>
  <tr>
    <td class="text" colspan="2"><textarea name="examen" rows="5" onchange="submitFormAjax(this.form, 'systemMsg');">{{$consult->examen}}</textarea></td>
    <td class="text" colspan="2"><textarea name="traitement" rows="5" onchange="submitFormAjax(this.form, 'systemMsg');">{{$consult->traitement}}</textarea></td>
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