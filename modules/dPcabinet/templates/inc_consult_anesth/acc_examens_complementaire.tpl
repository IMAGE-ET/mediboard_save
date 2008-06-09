<script language="Javascript" type="text/javascript">
function calculClairance () {
  var oFormExam  = document.forms["editExamCompFrm"];
  var oFormConst = document.forms["edit-constantes-medicales"];
  
  var poids      = parseFloat($V(oFormConst.poids) ? $V(oFormConst.poids): $V(oFormConst._poids));
  var creatinine = parseFloat($V(oFormExam.creatinine));
  
   if({{if $patient->_age && $patient->_age!="??" && $patient->_age>=18 && $patient->_age<=110}}1{{else}}0{{/if}} && 
     poids && !isNaN(poids) && poids >= 35 && poids <= 120 && 
     creatinine && !isNaN(creatinine) && creatinine >= 6 && creatinine <= 70) {
     
     $V(oFormExam._clairance, Math.round(({{if $patient->sexe!="m"}}0.85 * {{/if}}poids * (140-{{if $patient->_age!="??"}}{{$patient->_age}}{{else}}0{{/if}})/(creatinine*7.2))*100)/100);
   }
   else{
     $V(oFormExam._clairance, "");
   }
}

function calculPSA () {
  var oFormExam  = document.forms["editExamCompFrm"];
  var oFormConst = document.forms["edit-constantes-medicales"];
  
  var vst = parseFloat($V(oFormConst._vst));
  var ht = parseFloat($V(oFormExam.ht));
  var ht_final = parseFloat($V(oFormExam.ht_final));
  
  if (vst && !isNaN(vst) && 
    ht && !isNaN(ht) && ht > 0 &&
    ht_final && !isNaN(ht_final) && ht_final > 0) {
    $V(oFormExam._psa, Math.round(vst * (ht - ht_final))/100);
  }
  else {
    $V(oFormExam._psa, "");
  }
}

function delExamComp(oForm){
  oForm.del.value = "1";
  submitExamComp(oForm);
}

function modifEtatExamComp(oForm){
  oForm.fait.value = (oForm.fait.value == 1) ? 0 : 1;
  submitExamComp(oForm);
}

function submitExamComp(oForm) {
  if (oForm.examen) {
    var examen = oForm.examen.value;
    var realisation = oForm.realisation.value;
  }
  submitFormAjax(oForm, 'systemMsg', { onComplete : reloadListExamComp});
  oForm.reset();
  if(oForm.examen){
    oForm._hidden_examen.value = examen;
    oForm.realisation.value = realisation;
  }
}

function reloadListExamComp() {
  var UrllistExamComp= new Url;
  UrllistExamComp.setModuleAction("dPcabinet", "httpreq_vw_list_exam_comp");
  UrllistExamComp.addParam("selConsult", document.editFrmFinish.consultation_id.value);
  UrllistExamComp.requestUpdate('listExamComp', { waitingText : null});
}
</script>

<table class="form">
  <tr>
    <td class="text">
      <form name="addExamCompFrm" action="?m=dPcabinet" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="m" value="dPcabinet" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_examcomp_aed" />
      {{mb_field object=$consult field="consultation_id" hidden=1 prop=""}}
      <table class="form">
        <tr>
          <td><strong>Ajouter un examen compl�mentaire</strong></td>
          <td>
            {{mb_label object=$examComp field="examen"}}
            <select name="_helpers_examen" size="1" onchange="pasteHelperContent(this)">
              <option value="">&mdash; Choisir une aide</option>
                {{html_options options=$examComp->_aides.examen.no_enum}}
              </select>
              <button class="new notext" title="Ajouter une aide � la saisie" type="button" onclick="addHelp('CExamComp', this.form._hidden_examen, 'examen')">{{tr}}New{{/tr}}</button>
          </td>
        </tr>
        <tr>
          <td>
            {{html_options name="realisation" options=$examComp->_enumsTrans.realisation}}
          </td>
          <td>
            <input type="hidden" name="_hidden_examen" value="" />
            <textarea name="examen" onblur="if(verifNonEmpty(this)){submitExamComp(this.form);}"></textarea>
          </td>
        </tr>
        <tr>
          <td class="button" colspan="3">
            <button class="submit" type="button" onclick="if(verifNonEmpty(this.form.examen)){submitExamComp(this.form);}">Ajouter</button>
          </td>
        </tr>
      </table>      
      </form>
    </td>
    <td class="text" rowspan="2">
      <div id="listExamComp">
      {{include file="../../dPcabinet/templates/exam_comp.tpl"}}
      </div>
      
      
      <table class="form">
			  <!-- Documents ExamComp -->
			  <tr>
			    <th class="category">Documents</th>
			  </tr>
			  <tr>
			    <td>
			      {{mb_ternary var=object test=$consult->_is_anesth value=$consult->_ref_consult_anesth other=$consult}}
			      <!-- Documents -->
			      <div id="documents-exam">
			      </div>
			      <script type="text/javascript">
			         Document.register('{{$object->_id}}','{{$object->_class_name}}','{{$consult->_praticien_id}}','exam');
            </script>
			    </td>
			  </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td>
      <form name="editExamCompFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
      <input type="hidden" name="m" value="dPcabinet" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
      {{mb_field object=$consult_anesth field="consultation_anesth_id" hidden=1 prop=""}}
      <table class="form">
        <tr>
          <th colspan="4" class="category">R�sultats d'analyse</th>
        </tr>
        <tr>
          <th>{{mb_label object=$consult_anesth field="groupe"}}</th>
          <td>
            {{mb_field object=$consult_anesth field="groupe" tabindex="101" onchange="submitForm(this.form)"}}
            /
            {{mb_field object=$consult_anesth field="rhesus" tabindex="102" onchange="submitForm(this.form)"}}
          </td>
          <th>{{mb_label object=$consult_anesth field="creatinine"}}</th>
          <td>
            {{mb_field object=$consult_anesth field="creatinine" tabindex="108" size="4" onchange="calculClairance();submitForm(this.form);"}}
            mg/l
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$consult_anesth field="rai"}}</th>
          <td>
            {{mb_field object=$consult_anesth field="rai" tabindex="103" onchange="submitForm(this.form)"}}
          </td>
          <th>{{mb_label object=$consult_anesth field="_clairance"}}</th>
          <td class="readonly">
            {{mb_field object=$consult_anesth field="_clairance"  size="4" readonly="readonly"}}
            ml/min
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$consult_anesth field="hb"}}</th>
          <td>
            {{mb_field object=$consult_anesth field="hb" tabindex="104" size="4" onchange="submitForm(this.form)"}}
            g/dl
          </td>
          <th>{{mb_label object=$consult_anesth field="na"}}</th>
          <td>
            {{mb_field object=$consult_anesth field="na" tabindex="109" size="4" onchange="submitForm(this.form)"}}
            mmol/l
          </td>
        </tr>
        <tr> 
          <th>{{mb_label object=$consult_anesth field="ht"}}</th>
          <td>
            {{mb_field object=$consult_anesth field="ht" tabindex="105" size="4" onchange="calculPSA();submitForm(this.form);"}}
            %
          </td>
          <th>{{mb_label object=$consult_anesth field="k"}}</th>
          <td>
            {{mb_field object=$consult_anesth field="k" tabindex="110" size="4" onchange="submitForm(this.form)"}}
            mmol/l
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$consult_anesth field="ht_final"}}</th>
          <td>
            {{mb_field object=$consult_anesth field="ht_final" tabindex="106" size="4" onchange="calculPSA();submitForm(this.form);"}}
            %
          </td>
          <th>{{mb_label object=$consult_anesth field="tp"}}</th>
          <td>
            {{mb_field object=$consult_anesth field="tp" tabindex="111" size="4" onchange="submitForm(this.form)"}}
            %
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$consult_anesth field="_psa"}}</th>
          <td class="readonly">
            {{mb_field object=$consult_anesth field="_psa"  size="4" readonly="readonly"}}
            ml/GR
          </td>
          <th>{{mb_label object=$consult_anesth field="tca" defaultFor="tca_temoin"}}</th>
          <td>
            {{mb_field object=$consult_anesth field="tca_temoin" tabindex="112" maxlength="2" size="2" onchange="submitForm(this.form)"}}
            s /
            {{mb_field object=$consult_anesth field="tca" tabindex="113" maxlength="2" size="2" onchange="submitForm(this.form)"}}
             s
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$consult_anesth field="plaquettes"}}</th>
          <td>
            {{mb_field object=$consult_anesth field="plaquettes" tabindex="107" size="6" onchange="submitForm(this.form)"}} (x1000) /mm3
          </td>
          <th>{{mb_label object=$consult_anesth field="tsivy" defaultFor="_min_tsivy"}}</th>
          <td>
            {{html_options tabindex="114" name="_min_tsivy" values=$mins output=$mins selected=$consult_anesth->_min_tsivy onchange="submitForm(this.form)"}}
            min
            {{html_options tabindex="115" name="_sec_tsivy" values=$secs output=$secs selected=$consult_anesth->_sec_tsivy onchange="submitForm(this.form)"}}
            s
          </td>
        </tr>
        <tr>
          <td colspan="2"></td>
          <th>{{mb_label object=$consult_anesth field="ecbu"}}</th>
          <td>
            {{mb_field object=$consult_anesth field="ecbu" tabindex="116" onchange="submitForm(this.form)"}}
          </td>
        </tr>
      </table>    
      </form>
    </td>
  </tr>
</table>