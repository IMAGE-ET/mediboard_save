<script>
autoriserSortie = function(value){
  var form = getForm('editSortieAutorise');

  if (!checkForm(getForm('editRPU'))) {
    return false;
  }

  form.elements.sortie_autorisee.value = value;
  onSubmitFormAjax(getForm('editSejour'), function(){
    submitRPU();
  });
};

autoriserEffectuerSortie = function() {
  if (!checkForm(getForm('editRPU'))) {
    return false;
  }

  getForm('editSortieAutorise').elements.sortie_autorisee.value = 1;
  return onSubmitFormAjax(getForm('editSejour'), function(){
    {{if $conf.dPurgences.valid_cotation_sortie_reelle}}
      return onSubmitFormAjax(getForm('ValidCotation'), function(){
        submitSejRpuConsult();
        $('button_reconvoc').disabled = null;
      });
    {{else}}
      $('button_reconvoc').disabled = null;
      return submitSejRpuConsult();
    {{/if}}
  });
}
</script>

<form name="editSortieReelle" method="post" action="?m={{$m}}">
  <input type="hidden" name="dosql" value="do_sejour_aed" />
  <input type="hidden" name="m" value="dPplanningOp" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
  {{if $sejour->sortie_reelle && $rpu->sortie_autorisee}}
    {{tr}}CRPU-sortie_assuree.1{{/tr}} � 
    {{mb_field object=$sejour field="sortie_reelle" register=true form="editSortieReelle" onchange="submitFormAjax(this.form, 'systemMsg')"}}  
    <button class="cancel" type="button" onclick="autoriserSortie(0)">
      Annuler l'autorisation de sortie
    </button>
  {{else}}    
    {{if $rpu->sortie_autorisee}}
      <button class="cancel" type="button" onclick="autoriserSortie(0)">
        Annuler l'autorisation de sortie
      </button>
    {{else}}       
      <button class="tick singleclick" type="button" onclick="ContraintesRPU.checkObligatory('{{$rpu->_id}}', autoriserSortie.curry(1));">
        {{mb_label object=$rpu field="sortie_autorisee"}}
      </button>
      
      {{if !$sejour->sortie_reelle}}
        {{if $rpu->sejour_id != $rpu->mutation_sejour_id}}
          <input type="hidden" name="sortie_reelle" value="{{$now}}" />
          <button class="tick singleclick" type="button" onclick="ContraintesRPU.checkObligatory('{{$rpu->_id}}', autoriserEffectuerSortie.curry());">
            Autoriser et effectuer la sortie
          </button>
        {{/if}}
      {{else}}
        Sortie � 
          {{mb_field object=$sejour field="sortie_reelle" register=true form="editSortieReelle" 
            onchange="submitFormAjax(this.form, 'systemMsg'); reloadSortieReelle();"}}
      {{/if}}
    {{/if}}
  {{/if}}
</form>

<form name="ValidCotation" action="" method="post" onsubmit="return onSubmitFormAjax(this)"> 
  <input type="hidden" name="dosql" value="do_consultation_aed" />
  <input type="hidden" name="m" value="dPcabinet" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="consultation_id" value="{{$consult->_id}}" />
  <input type="hidden" name="valide" value="1" />
</form>
