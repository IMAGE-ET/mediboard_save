{{if !"dPfacturation"|module_active}}
<div class=" big-warning">
  Veuiller activer le module "Facturation" pour utiliser la comptabilit�
</div>
{{else}}
  {{mb_include module=facturation template=vw_compta}}
{{/if}}