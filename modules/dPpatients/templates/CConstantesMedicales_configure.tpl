{{assign var=class value=CConstantesMedicales}}

<form name="EditConfig-{{$class}}" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="dosql" value="do_configure" />

<table class="form">
  <tr>
    <th class="category">
      Configurations par service / �tablissement
    </th>
  </tr>
</table>

</form>

<div class="small-info">
  Les configurations de base sont � gauche, il est possible de d�finir des configurations par �tablissement,
  et de r�definir chaque �l�ment aussi pour chaque service.
</div>

<script type="text/javascript">
  Main.add(function(){
    Configuration.edit(
      'dPpatients',
      ['CGroups', 'CService CGroups.group_id', 'CFunctions CGroups.group_id'],
      'configuration-CConstantesMedicales'
    );
  });
</script>
<div id="configuration-CConstantesMedicales"></div>
