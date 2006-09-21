<script type="text/javascript">
function calculImcVst(){
   var oForm = document.editAnesthPatFrm;
   if(oForm.poid.value && !isNaN(parseFloat(oForm.poid.value)) && parseFloat(oForm.poid.value)>0){
     oForm._vst.value = {{if $patient->sexe=="m"}}70{{else}}65{{/if}}*parseFloat(oForm.poid.value);
     if(oForm.taille.value && !isNaN(parseInt(oForm.taille.value)) && parseInt(oForm.taille.value)>0){
       oForm._imc.value = round(parseFloat(oForm.poid.value) / (parseInt(oForm.taille.value) * parseInt(oForm.taille.value) * 0.0001),2);
     }else{
       oForm._imc.value = "";
     }
   }else{
     oForm._vst.value = "";
     oForm._imc.value = "";
   }
   calculPSA(); 
   calculClairance();  
}
</script>

<table class="form">
  <tr>
    <td class="HalfPane">
      <form name="editAnesthPatFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
      <input type="hidden" name="consultation_anesth_id" value="{{$consult_anesth->consultation_anesth_id}}" />
      <table class="form">
        <tr>
          <th><label for="poid" title="Poids du patient">Poids</label></th>
          <td>
            <input type="text" size="4" name="poid" title="{{$consult_anesth->_props.poid}}" value="{{$consult_anesth->poid}}" onchange="javascript:calculImcVst();submitForm(this.form);"/>
            kg
          </td>
          <th><label for="tasys" title="Pression arterielle">TA</label></th>
          <td>
            <input type="text" size="2" name="tasys" onchange="submitForm(this.form);" title="{{$consult_anesth->_props.tasys}}" value="{{$consult_anesth->tasys}}" />
            /
            <input type="text" size="2" name="tadias" onchange="submitForm(this.form);" title="{{$consult_anesth->_props.tadias}}" value="{{$consult_anesth->tadias}}" />
            cm Hg
          </td>
        </tr>
        <tr>
          <th><label for="taille" title="Taille du patient">Taille</label></th>
          <td>
            <input type="text" size="4" name="taille" title="{{$consult_anesth->_props.taille}}" value="{{$consult_anesth->taille}}" onchange="javascript:calculImcVst();submitForm(this.form);"/>
            cm
          </td>
          <th><label for="pouls" title="Pouls du patient">Pouls</label></th>
          <td>
            <input type="text" size="4" name="pouls" onchange="submitForm(this.form);" title="{{$consult_anesth->_props.pouls}}" value="{{$consult_anesth->pouls}}" />
            / min
          </td>
        </tr>
        <tr>
          <th><label for="_imc" title="Indice de Masse Corporel du Patient">IMC</label></th>
          <td class="readonly">
            <input type="text" size="4" name="_imc" value="{{$consult_anesth->_imc}}" readonly="readonly" />
          </td>
          <th><label for="spo2" title="Spo2">Spo2</label></th>
          <td>
            <input type="text" size="4" name="spo2" onchange="submitForm(this.form);" title="{{$consult_anesth->_props.spo2}}" value="{{$consult_anesth->spo2}}" />
            %
          </td>
        </tr>
        <tr>
          <th><label for="_vst" title="Volume Sanguin Total du patient">VST</label></th>
          <td class="readonly">
            <input type="text" size="4" name="_vst" value="{{$consult_anesth->_vst}}" readonly="readonly" />
            ml
          </td>
          <td colspan="2"></td>
        </tr>
        </table>
      </form>
    </td>
    <td class="HalfPane">
      <form class="watch" name="editFrmExams" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_consultation_aed" />
      <input type="hidden" name="consultation_id" value="{{$consult->consultation_id}}" />
      <input type="hidden" name="_check_premiere" value="{{$consult->_check_premiere}}" />
      <label for="examen" title="Bilan de l'examen clinique">Examens</label>
      <select name="_helpers_examen" size="1" onchange="pasteHelperContent(this);this.form.examen.onchange();">
        <option value="">&mdash; Choisir une aide</option>
        {{html_options options=$consult->_aides.examen}}
      </select><br />
      <textarea name="examen" onchange="submitFormAjax(this.form, 'systemMsg')">{{$consult->examen}}</textarea><br />
      </form>
    </td>
  </tr>
</table>      
      
{{include file="inc_consult_anesth/intubation.tpl"}}