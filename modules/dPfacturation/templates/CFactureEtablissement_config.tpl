<form name="editCFactureEtablissement-config" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />
  <table class="form">
    <tr>
      <th class="title" colspan="2">G�n�ral</th>
    </tr>
    <tr>
      <th class="category" colspan="2">{{tr}}CFactureEtablissement{{/tr}}</th>
    </tr>
    {{assign var=class value=CFactureEtablissement}}
    {{mb_include module=system template=inc_config_bool var=use_temporary_bill}}
    {{mb_include module=system template=inc_config_bool var=use_auto_cloture}}
    {{mb_include module=system template=inc_config_bool var=view_bill}}

    <tr>
      <th class="category" colspan="2">{{tr}}CFactureCabinet{{/tr}}</th>
    </tr>
    {{assign var=class value=CFactureCabinet}}
    {{mb_include module=system template=inc_config_bool var=use_auto_cloture}}
    {{mb_include module=system template=inc_config_bool var=view_bill}}

    <tr>
      <th class="category" colspan="2">{{tr}}CReglement{{/tr}}</th>
    </tr>
    {{assign var=class value=CReglement}}
    {{mb_include module=system template=inc_config_bool var=use_debiteur}}
    {{mb_include module=system template=inc_config_bool var=add_pay_not_close}}
    {{mb_include module=system template=inc_config_bool var=use_lock_acquittement}}
    {{mb_include module=system template=inc_config_enum var=use_mode_default values="none|cheque|CB|especes|virement|BVR|autre"}}
    <tr>
      <th class="category" colspan="2">{{tr}}Other{{/tr}}</th>
    </tr>
    {{assign var=class value=Other}}
    {{mb_include module=system template=inc_config_bool var=use_view_chainage}}
    {{mb_include module=system template=inc_config_bool var=use_view_quantitynull}}
    {{mb_include module=system template=inc_config_bool var=use_strict_cloture}}
    {{mb_include module=system template=inc_config_bool var=use_field_definitive}}

    {{if $conf.ref_pays == 2}}
      {{assign var=class value=CEditPdf}}
      {{mb_include module=system template=inc_config_bool var=use_bill_etab}}
      
      {{mb_include module=system template=inc_config_str var=home_nom}}
      {{mb_include module=system template=inc_config_str var=home_adresse}}
      {{mb_include module=system template=inc_config_str var=home_cp}}
      {{mb_include module=system template=inc_config_str var=home_ville}}
      {{mb_include module=system template=inc_config_str var=home_EAN}}
      {{mb_include module=system template=inc_config_str var=home_RCC}}
      {{mb_include module=system template=inc_config_str var=home_tel}}
      {{mb_include module=system template=inc_config_str var=home_fax}}
    {{/if}}
    <tr>
      <td class="button" colspan="2">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>