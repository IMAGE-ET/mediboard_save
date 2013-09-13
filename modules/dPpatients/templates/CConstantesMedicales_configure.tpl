{{assign var=class value=CConstantesMedicales}}

<form name="EditConfig-{{$class}}" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="dosql" value="do_configure" />

<table class="form">
  <col style="width: 50%" />

  <tr>
    {{mb_include module=system template=inc_config_enum skip_locales=true var=unite_ta values=cmHg|mmHg}}
    {{mb_include module=system template=inc_config_enum skip_locales=true var=unite_glycemie values=g/l|mmol/l}}
    {{mb_include module=system template=inc_config_enum skip_locales=true var=unite_cetonemie values=g/l|mmol/l}}
  </tr>
  <tr>
    <td class="button" colspan="2">
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>

  <tr>
    <th class="category" colspan="2">
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
      ['CService CGroups.group_id', 'CFunctions CGroups.group_id'],
      'configuration-CConstantesMedicales'
    );
  });
</script>
<div id="configuration-CConstantesMedicales"></div>
