<script type="text/javascript">
  addLineContigue = function(oForm){
    if(document.selPraticienLine){
      oForm.praticien_id.value = document.selPraticienLine.praticien_id.value;
    }
    submitFormAjax(oForm, 'systemMsg'); 
  }
</script>

<!-- Creation d'une ligne avec des dates contigu�s -->
<form name="addLineCont-{{$line->_id}}" method="post" action="">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="do_add_line_contigue_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="prescription_line_id" value="{{$line->_id}}" />
  <input type="hidden" name="prescription_id" value="{{$prescription_reelle->_id}}" />
  <input type="hidden" name="mode_pharma" value="{{$mode_pharma}}" />
  <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
  <button type="button" class="new" onclick="addLineContigue(document.forms['addLineCont-{{$line->_id}}'])">Modifier la ligne</button>
</form>