{{assign var=class value=CConstantesMedicales}}

<div class="small-info">
  Les configurations de base sont � gauche, il est possible de d�finir des configurations par �tablissement,
  et de r�definir chaque �l�ment aussi pour chaque service.
</div>

<script type="text/javascript">
  Main.add(function(){
    Configuration.edit(
      'dPpatients',
      [
        'constantes / CService CGroups.group_id',
        'constantes / CFunctions CGroups.group_id',
        'constantes / CBlocOperatoire CGroups.group_id',
        'constantes / CGroups'
      ],
      'configuration-CConstantesMedicales'
    );
  });
</script>
<div id="configuration-CConstantesMedicales"></div>
