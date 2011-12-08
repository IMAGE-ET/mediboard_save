<form name="editAffect" method="post" action="?" onsubmit="return onSubmitFormAjax(this, {onComplete: Control.Modal.close})">
  <input type="hidden" name="m" value="dPhospi" />
  <input type="hidden" name="dosql" value="do_affectation_aed" />
  <input type="hidden" name="del" value="0" />
  {{mb_key object=$affectation}}
  {{mb_field object=$affectation field=lit_id hidden=true}}
  
  <table class="form">
    <tr>
      <th>
        {{mb_label object=$affectation field=entree}}
      </th>
      <td>
        {{mb_field object=$affectation field=entree form=editAffect register=true}}
      </td>
      <th>
        {{mb_label object=$affectation field=sortie}}
      </th>
      <td>
        {{mb_field object=$affectation field=sortie form=editAffect register=true}}
      </td>
    </tr>
    <tr>
      <td colspan="4" class="button">
        <button type="button" class="save" onclick="this.form.onsubmit();">
          {{if $affectation->_id}}
            {{tr}}Save{{/tr}}
          {{else}}
            {{tr}}Create{{/tr}}
          {{/if}}
        {{if $affectation->_id}}
          <button type="button" class="cancel" onclick="$V(this.form.del, 1); this.form.onsubmit();">{{tr}}Delete{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>
{{if $affectation->_id}}
  {{mb_include module=dPhospi template=inc_cut_affectation}}
{{/if}}