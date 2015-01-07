{{mb_script module=cabinet script=tarif}}

<table class="main">
  <tr>
    <td colspan="2" class="halfPane">
      <button onclick="Tarif.edit(0, '{{$prat->_id}}')" class="new">
        {{tr}}CTarif-title-create{{/tr}}
      </button>
    </td>
  </tr>
  {{if !$user->_is_praticien && !$user->_is_secretaire}}
    <tr>
      <td class="text">
        <div class="big-info">
          N'�tant pas praticien, vous n'avez pas acc�s � la liste de tarifs personnels.
        </div>
      </td>
    </tr>
  {{/if}}

  {{if $user->_is_secretaire}}
    <tr>
      <td colspan="10">
        <form action="?" name="selectPrat" method="get">
          <input type="hidden" name="tarif_id" value="" />
          <input type="hidden" name="m" value="{{$m}}" />
          <select name="prat_id" onchange="this.form.submit()">
            <option value="">&mdash; Aucun praticien</option>
            {{mb_include module=mediusers template=inc_options_mediuser selected=$prat->_id list=$listPrat}}
          </select>
        </form>
      </td>
    </tr>
  {{/if}}

  <tr>
    <td class="halfPane">
      <table id="inc_list_tarifs_table" class="tbl">
        <tr>
          <th id="inc_list_tarifs_th_prat" colspan="10" class="title">{{tr}}CMediusers-back-tarifs{{/tr}}</th>
        </tr>
        {{if $user->_is_praticien || $user->_is_secretaire}}
          {{if $prat->_id}}
            <tr>
              <th colspan="10">
                <form name="recalculTarifsPraticien" method="post" action="?" style="float: right;"
                      onsubmit="return onSubmitFormAjax(this, {onComplete:  function() {document.location.reload();} });">
                  <input type="hidden" name="m" value="{{$m}}" />
                  <input type="hidden" name="dosql" value="do_tarif_aed" />
                  <input type="hidden" name="reloadAlltarifs" value="1" />
                  <input type="hidden" name="praticien_id" value="{{$prat->_id}}" />
                  <button class="reboot" type="submit">Recalculer tous les tarifs</button>
                </form>
                {{$prat}}
              </th>
            </tr>
          {{/if}}
          {{mb_include module=cabinet template=inc_list_tarifs_by_owner tarifs=$listeTarifsChir}}
        {{/if}}
      </table>
    </td>

    <td class="halfPane">
      <table class="tbl">
        <tr>
          <th colspan="10" class="title">{{tr}}CFunctions-back-tarifs{{/tr}}</th>
        </tr>
        {{if $prat->function_id}}
          <tr>
            <th colspan="10">
              <form name="recalculTarifsFunction" method="post" action="?" style="float: right;"
                    onsubmit="return onSubmitFormAjax(this, {onComplete:  function() {document.location.reload();} });">
                <input type="hidden" name="m" value="{{$m}}" />
                <input type="hidden" name="dosql" value="do_tarif_aed" />
                <input type="hidden" name="reloadAlltarifs" value="1" />
                <input type="hidden" name="function" value="{{$prat->function_id}}" />
                <button class="reboot" type="submit">Recalculer tous les tarifs</button>
              </form>
              {{$prat->_ref_function}}
            </th>
          </tr>
        {{/if}}
        {{mb_include module=cabinet template=inc_list_tarifs_by_owner tarifs=$listeTarifsSpe}}
      </table>
    </td>

    {{if $listeTarifsEtab|@count}}
      <td class="halfPane">
        <table class="tbl">
          <tr>
            <th colspan="10" class="title">{{tr}}CGroups-back-tarif_group{{/tr}}</th>
          </tr>
          {{mb_include module=cabinet template=inc_list_tarifs_by_owner tarifs=$listeTarifsEtab}}
        </table>
      </td>
    {{/if}}
  </tr>
</table>