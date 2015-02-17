<script>
  Main.add(function () {
    getForm("editCRelance-config")["dPfacturation[CRelance][nb_days_first_relance]"].addSpinner({min:1, step:1});
    getForm("editCRelance-config")["dPfacturation[CRelance][nb_days_second_relance]"].addSpinner({min:10,step:1});
    getForm("editCRelance-config")["dPfacturation[CRelance][nb_days_third_relance]"].addSpinner({min:10,step:1});
  });
</script>

<form name="editCRelance-config" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />
  <table class="form">
    <tr>
      <th class="title" colspan="2">{{tr}}CRelance{{/tr}}</th>
    </tr>
    {{assign var=class value=CRelance}}
    {{mb_include module=system template=inc_config_bool var=use_relances}}
    {{mb_include module=system template=inc_config_num var=nb_days_first_relance  size="3" suffix="jours"}}
    {{mb_include module=system template=inc_config_num var=nb_days_second_relance size="3" suffix="jours"}}
    {{mb_include module=system template=inc_config_num var=nb_days_third_relance  size="3" suffix="jours"}}
    {{mb_include module=system template=inc_config_num var=add_first_relance}}
    {{mb_include module=system template=inc_config_num var=add_second_relance}}
    {{mb_include module=system template=inc_config_num var=add_third_relance}}
    {{mb_include module=system template=inc_config_num var=nb_generate_pdf_relance}}
    <tr>
      <th class="category" colspan="2">Relance Assurance</th>
    </tr>
    {{mb_include module=system template=inc_config_str var=message_relance1_assur  textarea=1}}
    {{mb_include module=system template=inc_config_str var=message_relance2_assur  textarea=1}}
    {{mb_include module=system template=inc_config_str var=message_relance3_assur textarea=1}}

    <tr>
      <th class="category" colspan="2">Relance Patient</th>
    </tr>
    {{mb_include module=system template=inc_config_str var=message_relance1_patient textarea=1}}
    {{mb_include module=system template=inc_config_str var=message_relance2_patient textarea=1}}
    {{mb_include module=system template=inc_config_str var=message_relance3_patient textarea=1}}
    <tr>
      <td class="button" colspan="2">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>