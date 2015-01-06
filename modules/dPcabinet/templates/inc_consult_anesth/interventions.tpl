<script>
  selectOperation = function(operation_id) {
    var oForm = getForm("addOpFrm");
    $V(oForm.operation_id, operation_id);
  }
  selectSejour = function(sejour_id) {
    var oForm = getForm("addOpFrm");
    $V(oForm.sejour_id, sejour_id);
  }
  selectSejourReprescription = function(sejour_id) {
    var url = new Url("prescription", "ajax_select_sejour_represcription");
    url.addParam("sejour_id", sejour_id);
    url.requestModal(500);
  };

  modalRePrescriptions = function(sejour_id, prescription_id, current_prat_id) {
    var url = new Url("prescription", "ajax_vw_represcription_sejour");
    url.addParam("sejour_id"      , sejour_id);
    url.addParam("prescription_id", prescription_id);
    {{if "dPprescription general role_propre"|conf:"CGroups-$g"}}
      url.addParam("praticien_id", '{{$app->user_id}}');
    {{else}}
      url.addParam("praticien_id", current_prat_id);
    {{/if}}
    url.addParam("origine_consult", 1);
    url.requestModal('85%', '80%',
      {showReload: true});
  };

  {{if !$consult_anesth->libelle_interv && !$consult_anesth->sejour_id && !$consult_anesth->operation_id && ($nextSejourAndOperation.COperation->_id || $nextSejourAndOperation.CSejour->_id)}}
    Main.add(function () {
        GestionDA.edit();
      });
  {{/if}}
  {{if $represcription && $consult_anesth->sejour_id}}
    Main.add(function () {
      selectSejourReprescription('{{$consult_anesth->sejour_id}}');
    });
  {{/if}}

  {{if $consult->sejour_id && !$consult_anesth->operation_id}}
    Main.add(function () {
      DossierMedical.sejour_id = '{{$consult->sejour_id}}';
    });
  {{/if}}
</script>

<table class="form main" style="display: none;" id="evenement-chooser-modal">
  {{assign var=next_operation value=$nextSejourAndOperation.COperation}}
  {{assign var=next_sejour    value=$nextSejourAndOperation.CSejour   }}
  {{if $next_operation->_id}}
    <tr>
      <td colspan="2"> <div class="small-info">Une intervention � venir est pr�sente pour ce patient</div></td>
    </tr>
    <tr>
      <td></td>
      <td><strong>{{$next_operation}}</strong></td>
    </tr>
    <tr>
      <th>{{mb_title object=$next_operation field=libelle}}</th>
      <td><strong>{{$next_operation->libelle}}</strong></td>
    </tr>
    <tr>
      <th>{{mb_title object=$next_operation field=cote}}</th>
      <td><strong>{{mb_value object=$next_operation field=cote}}</strong></td>
    </tr>
    <tr>
      <th>Pr�vue le </th>
      <td><strong>{{$next_operation->_datetime|date_format:$conf.date}}</strong></td>
    </tr>
    <tr>
      <th>Avec le Dr </th>
      <td><strong>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$next_operation->_ref_chir}}</strong></td>
    </tr>
    <tr>
      <td class="button" colspan="2"><button class="tick" onclick="selectOperation('{{$next_operation->_id}}');">Associer au dossier d'anesth�sie</button>
        <button class="cancel" onclick="modalWindow.close();">Ne pas associer</button></td>
    </tr>
        {{elseif $next_sejour->_id}}
    <tr>
      <td colspan="2"> <div class="small-info">Un s�jour � venir est pr�sent dans le syst�me pour ce patient</div></td>
    </tr>
    <tr>
      <td></td>
      <td><strong>{{$next_sejour}}</strong></td>
    </tr>
    <tr>
      <th>{{mb_title object=$next_sejour field=libelle}}</th>
      <td><strong>{{$next_sejour->libelle}}</strong></td>
    </tr>
    <tr>
      <th>Avec le Dr </th>
      <td><strong>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$next_sejour->_ref_praticien}}</strong></td>
    </tr>
    <tr>
      <td class="button" colspan="2">
        <button class="tick" onclick="selectSejour('{{$next_sejour->_id}}');">Associer au dossier d'anesth�sie</button>
      <button class="cancel" onclick="modalWindow.close();">Ne pas associer</button></td>
    </tr>
  {{/if}}
</table>

<div id="dossiers_anesth_area">
  {{mb_include module=cabinet template=inc_consult_anesth/inc_multi_consult_anesth}}
</div>

{{assign var=operation value=$consult_anesth->_ref_operation}}

{{if $operation->_id}}
  <hr />

  <form name="editOpFrm" action="?m=dPcabinet" method="post" onsubmit="return onSubmitFormAjax(this);">
    <input type="hidden" name="m" value="dPplanningOp" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="dosql" value="do_planning_aed" />
    {{mb_key object=$operation}}

    <table class="layout main">
      {{if $conf.dPplanningOp.COperation.verif_cote && ($operation->cote == "droit" || $operation->cote == "gauche")}}
        <tr>
          <th>{{mb_label object=$operation field="cote_consult_anesth"}} :</th>
          <td>{{mb_field emptyLabel="Choose" object=$operation field="cote_consult_anesth" onchange="this.form.onsubmit();"}}</td>
        </tr>
      {{/if}}
      <tr>
        <th>{{mb_label object=$operation field="depassement_anesth"}} :</th>
        <td>
          {{mb_field object=$operation field="depassement_anesth" onchange="this.form.onsubmit();"}}
          <button type="button" class="notext submit">{{tr}}Save{{/tr}}</button>
        </td>
      </tr>
    </table>
  </form>
{{/if}}