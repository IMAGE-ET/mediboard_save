{{if @$modules.soins}}
<div class="small-warning">
Le module d�di� aux soins est maintenant pr�sent dans Mediboard.
Cet onglet y est d�j� disponible et disparaitra prochainement du module <strong>Hospitalisation</strong>.
Pour l'utiliser, veuillez d'ores et d�j� aller dans le module nomm� <strong>Soins</strong>
</div>
{{/if}}
{{include file="../../soins/templates/vw_bilan_prescription.tpl"}}