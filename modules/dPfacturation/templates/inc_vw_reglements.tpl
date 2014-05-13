{{if $facture->_id}}
  {{assign var=object value=$facture}}
{{/if}}
{{mb_script module=facturation script=reglement ajax=true}}
<fieldset>
  <legend>R�glements ({{tr}}{{$object->_class}}{{/tr}})</legend>
  {{if $object->du_patient || $conf.dPfacturation.CReglement.use_lock_acquittement}}
    {{mb_include  module=dPfacturation template=inc_vw_du_patient_reglements}}
  {{else}}
    {{mb_include  module=dPfacturation template=inc_vw_du_tiers_reglements}}
  {{/if}}
</fieldset>