<!--  Passage en UHCD / Revenir en dossier d'urgences -->
<form name="editPassageUHCD" method="post" action="?m={{$m}}">
  <input type="hidden" name="dosql" value="do_sejour_aed" />
  <input type="hidden" name="m" value="dPplanningOp" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
  
  {{if !$sejour->UHCD}}
    <input type="hidden" name="UHCD" value="1" />
    <input type="hidden" name="type" value="comp" />
    <button class="hslip singleclick" type="button" onclick="ContraintesRPU.checkObligatory('{{$rpu->_id}}', (function () {this.form.submit();}).bind(this));">
      Passer en dossier UHCD
    </button>
  {{else}}
    <input type="hidden" name="UHCD" value="0" />
    <input type="hidden" name="type" value="urg" />
    <button class="hslip singleclick" type="button" onclick="ContraintesRPU.checkObligatory('{{$rpu->_id}}', (function () {this.form.submit();}).bind(this));">
      Revenir en dossier ATU
    </button>
  {{/if}}
</form>

