<script type="text/javascript">
var oFormClick = window.opener.document.forms.click;
var anyFormSubmitted = false;

function submitAllAdministrations() {
  var submitForms = $('administrations').select('form');
  var transForm = getForm('editTrans');

  for (var i = 0; i < submitForms.length; i++) {
    var f = submitForms[i];
    if (($V(f.quantite_prevue)-0 != $V(f.quantite)-0) && !transForm.text.value) {
      alert('Veuillez ajouter une transmission');
      (transForm.text).focus();
      return false;
    }
    if (!checkForm(f)) return false;
    submitFormAjax(f, 'systemMsg');
    anyFormSubmitted = true;
    f.up().update('Administration effectu�e');
  }
  
  if (transForm.text.value) {
    submitFormAjax(transForm, 'systemMsg');
  }
  closeApplyAdministrations();
  return true;
}

function closeApplyAdministrations(dontClose) {
  {{if $administrations|@count && $sejour_id && $date_sel}}
    if (anyFormSubmitted && window.opener) {
      window.opener.loadTraitement('{{$sejour_id}}','{{$date_sel}}', oFormClick.nb_decalage.value);
      window.opener.loadSuivi('{{$sejour_id}}');
    }
  {{/if}}
  if (!dontClose) {
    window.close();
  }
}

Main.add(function () {
  window.onbeforeunload = function () {
    closeApplyAdministrations (true);
  }
});
</script>

<button type="button" class="cancel" onclick="closeApplyAdministrations()">{{tr}}Cancel{{/tr}}</button>
<button type="button" class="tick" onclick="submitAllAdministrations()">Administrer</button>

<table class="form" id="administrations">
{{foreach from=$administrations item=adm key=line_id name=by_adm}}
  {{foreach from=$adm item=by_unite_prise key=unite_prise name=adm_by_unite_prise}}
    {{foreach from=$by_unite_prise item=by_date key=date}}
      {{foreach from=$by_date item=by_hour key=hour}}
      {{assign var=_unite value=$unite_prise|utf8_decode}}
      {{assign var=key value="$line_id-$_unite-$date-$hour"|smarty:nodefaults|JSAttribute}}
      {{if $smarty.foreach.adm_by_unite_prise.first}}
      <tr>
        <th class="title" colspan="2">{{$by_hour.line->_view}}</th>
      </tr>
      {{/if}}
      <tr>
        <td style="text-align: right;" id="adm_{{$key}}">
          <form name="addAdministration_{{$key}}" method="post" action="?" onsubmit="return checkForm(this)" style="float: left;">
            <input type="hidden" name="dosql" value="do_administration_aed" />
            <input type="hidden" name="m" value="dPprescription" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="administration_id" value="" />
            <input type="hidden" name="administrateur_id" value="{{$app->user_id}}" />
            <input type="hidden" name="object_id" value="{{$by_hour.line->_id}}" />
            <input type="hidden" name="object_class" value="{{$by_hour.line->_class_name}}" />
            <input type="hidden" name="unite_prise" value="{{$by_hour.unite_prise}}" />
            <input type="hidden" name="dateTime" value="{{$by_hour.dateTime}}" />
            <input type="hidden" name="prise_id" value="{{$by_hour.prise_id}}" />
            <input type="hidden" name="quantite_prevue" disabled="disabled" value="{{$by_hour.prise->quantite}}" />
            
            <b>{{$date|date_format:"%d/%m/%Y"}}, {{$hour}}h</b> : 
            {{mb_label object=$by_hour.prise field=quantite}}
            {{mb_field object=$by_hour.prise field=quantite min=1 increment=1 form="addAdministration_$key"}}
            
            {{if $by_hour.line->_class_name == "CPrescriptionLineMedicament"}}
              {{$by_hour.line->_ref_produit->libelle_unite_presentation}}
            {{else}}
              {{$by_hour.line->_unite_prise}}
            {{/if}}
          </form>
        </td>
      </tr>
      {{/foreach}}
    {{/foreach}}
  {{/foreach}}
{{foreachelse}}
  <tr><td>Veuillez choisir au moins une prise</td></tr>
{{/foreach}}
</table>

<form name="editTrans" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_transmission_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="m" value="dPhospi" />
  <input type="hidden" name="object_class" value="" />
  <input type="hidden" name="object_id" value="" />
  <input type="hidden" name="sejour_id" value="{{$sejour_id}}" />
  <input type="hidden" name="user_id" value="{{$app->user_id}}" />
  <input type="hidden" name="date" value="now" />
  {{mb_label object=$transmission field="degre"}}
  {{mb_field object=$transmission field="degre"}}<br />
  {{mb_field object=$transmission field="text"}}
</form>
