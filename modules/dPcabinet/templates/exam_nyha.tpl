<script type="text/javascript" src="modules/{{$m}}/javascript/exam_nyha.js?build={{$mb_version_build}}"></script>
<script type="text/javascript">
if (window.opener.reloadFdr) {
  window.opener.reloadFdr();
}
</script>
<form name="editFrmNyha" action="?m=dPcabinet&amp;a=exam_nyha&amp;dialog=1" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="dPcabinet" />
<input type="hidden" name="dosql" value="do_exam_nyha_aed" />
<input type="hidden" name="del" value="0" />
{{mb_field object=$exam_nyha field="examnyha_id" type="hidden" spec=""}}
{{mb_field object=$exam_nyha field="consultation_id" type="hidden" spec=""}}

<table class="form">
  <tr>
    <th class="title" colspan="3">
      Consultation de <span style="color:#f00;">{{$exam_nyha->_ref_consult->_ref_patient->_view}}</span>
      le {{$exam_nyha->_ref_consult->_date|date_format:"%A %d/%m/%Y"}}
      par le Dr. {{$exam_nyha->_ref_consult->_ref_chir->_view}}
    </th>
  </tr>
  <tr>
    <th><strong>Classe 1</strong></th>
    <td class="text" colspan="2">
      Patient porteur d'une cardiopathie snas limitation de l'activit� physique.
      Une activit� physique ordinaire n'entra�ne aucun sympt�me.
    </td>
  </tr>
  <tr>
    <th><strong>Classe 2</strong></th>
    <td class="text" colspan="2">
      Patient dont la cardiopathie entra�ne une limitation mod�r�e de l'activit� physique
      sans g�ne au repos. L'activit� quotidienne ordinaire est responsable d'une fatigue,
      d'une dyspn�e, de palpitations ou d'un angor.
    </td>
  </tr>
  <tr>
    <th><strong>Classe 3</strong></th>
    <td class="text" colspan="2">
      Patient dont la cardiopathie entra�ne une limitation marqu�e de l'activit� physique
      sans g�ne au repos.
    </td>
  </tr>
  <tr>
    <th><strong>Classe 4</strong></th>
    <td class="text" colspan="2">
      Patient dont la cardiopathie emp�che toute activit� physique. Des signes d'insufisance
      cardiaque ou un angor peuvent exister m�me au repos.
    </td>
  </tr>
  
  <tr>
    <th class="title" colspan="3">Questionnaire</th>
  </tr>
  
  <tr>
    <th>
      <label for="q1" title=""><strong>1</strong></label>
    </th>
    <td class="text">
      <label for="q1" title="">
        Le patient peut-il descendre un �tage d'escalier sans s'arr�ter ?
      </label>
    </td>
    <td>
      {{html_radios onchange="changeValue(this.name,'q2a','q3a')" name="q1" options=$exam_nyha->_enumsTrans.q1 separator="<br />" title=$exam_nyha->_props.q1 checked=$exam_nyha->q1}}
    </td>
  </tr>
  
  <tbody id="viewq2a" {{if $exam_nyha->q1==0}}style="display:none;"{{/if}}>
  <tr>
    <th>
      <label for="q2a" title=""><strong>2a</strong></label>
    </th>
    <td class="text">
      <label for="q2a" title="">
        Le patient peut-il monter un �tage d'escalier sans s'arr�ter ?<br />
        <em>ou</em><br />
        marcher d'un pas alerte sur un terrain plat<br />
        <em>ou</em><br />
        Peut-il...<br />
        jardiner, ratisser, d�sherber, danser (slow) ?
      </label>
    </td>
    <td>
      {{html_radios onchange="changeValue(this.name,'q2b','')" name="q2a" options=$exam_nyha->_enumsTrans.q2a separator="<br />" title=$exam_nyha->_props.q2a checked=$exam_nyha->q2a}}
    </td>
  </tr>
  </tbody>

  <tbody id="viewq2b" {{if $exam_nyha->q2a==0}}style="display:none;"{{/if}}>
  <tr>
    <th>
      <label for="q2b" title=""><strong>2b</strong></label>
    </th>
    <td class="text">
      <label for="q2b" title="">
        Le patient peut-il monter un �tage d'escalier en portant un enfant d'un an ou plus 
        (- 10 kg ou plus)<br />
        <em>ou</em><br />
        Peut-il porter en terrain plat une bouteille de butane pleine (35 kg) ou un objet plus lourd ?<br />
        <em>ou</em><br />
        Peut-il...<br />
        faire du jogging ? (1/2 heure), faire des travaux ext�rieurs comme b�cher la terre ? 
        S'adonner � des loisirs tels que le ski alpin, le v�lo, le football, le tennis ?
      </label>
    </td>
    <td>
      {{html_radios onchange="changeValue(this.name,'','')" name="q2b" options=$exam_nyha->_enumsTrans.q2b separator="<br />" title=$exam_nyha->_props.q2b checked=$exam_nyha->q2b}}
    </td>
  </tr>
  </tbody>

  <tbody id="viewq3a" {{if $exam_nyha->q1==1 || $exam_nyha->q1==null}}style="display:none;"{{/if}}>
  <tr>
    <th>
      <label for="q3a" title=""><strong>3a</strong></label>
    </th>
    <td class="text">
      <label for="q3a" title="">
        Le patient peut-il prendre une douche sans s'arr�ter ?<br />
        <em>ou</em><br />
        peut-il marcher d'un pas tranquille sur un terrain plat (500m)<br />
        <em>ou</em><br />
        Peut-il...<br />
        faire son lit ? passer la serpilli�re ? �tendre le linge ? laver les carreaux ?
        jouer aux boules ? (p�tanque) jouer au golf ? pousser la tondeuse � gazon ?
      </label>
    </td>
    <td>
      {{html_radios onchange="changeValue(this.name,'q3b','')" name="q3a" options=$exam_nyha->_enumsTrans.q3a separator="<br />" title=$exam_nyha->_props.q3a checked=$exam_nyha->q3a}}
    </td>
  </tr>
  </tbody>

  <tbody id="viewq3b" {{if $exam_nyha->q3a==0}}style="display:none;"{{/if}}>
  <tr>
    <th>
      <label for="q3b" title=""><strong>3b</strong></label>
    </th>
    <td class="text">
      <label for="q3b" title="">
        Le patient est-il oblig� quand il s'habille de s'arr�ter ?<br />
        <em>ou</em><br />
        A-t-il des sympt�mes<br />
        quand il mange,<br />
        quand il est debout<br />
        assis ou allong� ?
      </label>
    </td>
    <td>
      {{html_radios onchange="changeValue(this.name,'','')" name="q3b" options=$exam_nyha->_enumsTrans.q3b separator="<br />" title=$exam_nyha->_props.q3b checked=$exam_nyha->q3b}}
    </td>
  </tr>
  </tbody>
</table>
<div style="display:none;">
  <input type="radio" name="q2a" value="" {{if $exam_nyha->q2a==""}}checked="checked"{{/if}} />
  <input type="radio" name="q2b" value="" {{if $exam_nyha->q2b==""}}checked="checked"{{/if}} />
  <input type="radio" name="q3a" value="" {{if $exam_nyha->q3a==""}}checked="checked"{{/if}} />
  <input type="radio" name="q3b" value="" {{if $exam_nyha->q3b==""}}checked="checked"{{/if}} />
</div>
<table class="form">
  <tr>
    <th><strong>Classification NYHA</strong></th>
    <td class="HalfPane" id="classeNyha">{{$exam_nyha->_classeNyha}}</td>
  </tr>
  <tr>
    <th><label for="hesitation_0">R�ponses du patient sans h�sitation</label></th>
    <td>
      <input name="hesitation" title="{{$exam_nyha->_props.hesitation}}" type="radio" value="0" {{if $exam_nyha->_id && $exam_nyha->hesitation==0}}checked="checked"{{/if}}>{{tr}}CExamNyha.hesitation.0{{/tr}}
      <input name="hesitation" title="{{$exam_nyha->_props.hesitation}}" type="radio" value="1" {{if !$exam_nyha->_id || $exam_nyha->hesitation==1}}checked="checked"{{/if}}>{{tr}}CExamNyha.hesitation.1{{/tr}}
    </td>
  </tr>
  <tr>
    <td class="button" colspan="3">
      {{if $exam_nyha->examnyha_id}}
        <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
        <button class="trash" type="button" onclick="confirmDeletion(this.form, {typeName:'cet examen complementaire',target:'systemMsg'})">{{tr}}Delete{{/tr}}</button>
      {{else}}
        <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
      {{/if}}
    </td>
  </tr>
</table>
</form>