<div>
  {{$acte->code_acte}} 
  {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$acte->_ref_executant}}

  <div class="compact">
  activit�: {{$acte->code_activite}}
  &ndash; phase: {{$acte->code_phase}}
  &ndash; Asso:  {{$acte->code_association|default:"aucun"}}
  {{if $acte->modificateurs}}
    &ndash; modifs: {{$acte->modificateurs}}
  {{/if}}
  {{if $acte->montant_depassement}}
    &ndash; DH: {{$acte->montant_depassement|currency}}
  {{/if}}
  </div>
</div>
