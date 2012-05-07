<form name="editConfigOperation" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />
  
  {{assign var=class value=COperation}}

  <table class="form">
    <tr>
      <th class="title" colspan="2">{{tr}}{{$class}}{{/tr}}</th>
    </tr>
    
    {{mb_include module=system template=inc_config_bool var=use_ccam}}
    {{mb_include module=system template=inc_config_bool var=verif_cote}}
    {{mb_include module=system template=inc_config_bool var=horaire_voulu}}
    {{mb_include module=system template=inc_config_bool var=delete_only_admin}}
    
    {{mb_include module=system template=inc_config_enum var=duree_deb  values=$hours skip_locales=true}}
    {{mb_include module=system template=inc_config_enum var=duree_fin  values=$hours skip_locales=true}}
    {{mb_include module=system template=inc_config_enum var=hour_urgence_deb  values=$hours skip_locales=true}}
    {{mb_include module=system template=inc_config_enum var=hour_urgence_fin  values=$hours skip_locales=true}}
    
    {{mb_include module=system template=inc_config_enum var=min_intervalle values=$intervals skip_locales=true}}
    <tr>
      <td class="button" colspan="2">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>