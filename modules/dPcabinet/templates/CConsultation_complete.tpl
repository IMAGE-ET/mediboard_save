<script type="text/javascript">

newExam = function(sAction, consultation_id) {
  if (sAction) {
    var url = new Url;
    url.setModuleAction("dPcabinet", sAction);
    url.addParam("consultation_id", consultation_id);
    url.popup(900, 600, "Examen");  
  }
}
printFiche = function() {
  var url = new Url("dPcabinet", "print_fiche"); 
  url.addParam("consultation_id", '{{$object->_id}}');
  url.addParam("print", true);
  url.popup(700, 500, "printFiche");
}
</script>

<table class="form">
  <tr>
    <th class="title text" colspan="2">
      {{mb_include module=system template=inc_object_idsante400 object=$object}}
      {{mb_include module=system template=inc_object_history    object=$object}}
      {{mb_include module=system template=inc_object_notes      object=$object}}
      
      <button class="print" type="button" style="float: right;" onclick="printFiche();">
        Imprimer la fiche
      </button>
      
      {{$object->_view}}
    </th>
  </tr>
  <tr>
    <td>
      <strong>Date :</strong>
      <i>le {{$object->_ref_plageconsult->date|date_format:"%d %B %Y"}} � {{$object->heure|date_format:$conf.time}}</i>
    </td>
    <td>
      <strong>Praticien :</strong>
      <i>Dr {{$object->_ref_plageconsult->_ref_chir->_view}}</i>
    </td>
  </tr>
  <tr>
    <td class="text">
      <strong>Motif :</strong>
      <i>{{$object->motif|nl2br}}</i>
    </td>
    <td class="text">
      <strong>Remarques :</strong>
      <i>{{$object->rques|nl2br}}</i>
    </td>
  </tr>
  
  
  
  <tr>
    <td class="text">
      <strong>Examens :</strong>
      <i>{{$object->examen|nl2br}}</i>
    </td>
    <td class="text">
      <strong>Traitement :</strong>
      <i>{{$object->traitement|nl2br}}</i>
    </td>
  </tr>
  {{if $object->_ref_examaudio->examaudio_id}}
  <tr>
    <td>
      <a href="#" onclick="newExam('exam_audio', {{$object->consultation_id}})">
        <strong>Audiogramme</strong>
      </a>
    </td>
  </tr>
  {{/if}}
</table>

<table class="tbl">
	{{mb_include module=dPcabinet template=inc_list_actes_ccam subject=$object vue=complete}}
</table>
{{if $object->_ref_plageconsult->chir_id == $app->user_id || $can->admin}}
<table class="form">
  <tr>
    <th class="category" colspan="2">
      Facturation
    </th>
  </tr>
  <tr>
    <td>
      <strong>Date d'acquittement :</strong>
      {{if $object->patient_date_reglement}}
      <i>{{mb_value object=$object field=patient_date_reglement}}</i>
      {{else}}
      <i>Non acquitt�</i>
      {{/if}}
    </td>
    <td rowspan="3">
      <table class="tbl">
        <tr>
          <th class="category">{{tr}}CReglement-mode{{/tr}}</th>
          <th class="category">{{tr}}CReglement-montant{{/tr}}</th>
          <th class="category">{{tr}}CReglement-date{{/tr}}</th>
          <th class="category">{{tr}}CReglement-banque_id{{/tr}}</th>
        </tr>
      {{foreach from=$object->_ref_reglements item=reglement}}
        <tr>
          <td>{{tr}}CReglement.mode.{{$reglement->mode}}{{/tr}}</d>
          <td>{{mb_value object=$reglement field=montant}}</td>
          <td>{{mb_value object=$reglement field=date}}</td>
          <td>{{$reglement->_ref_banque->_view}}</td>
        </tr>
      {{foreachelse}}
        <tr>
            <td colspan="4">Aucun r�glement effectu�</d>
        </tr>
      {{/foreach}}
      </table>
    </td>
  </tr>
  <tr>
    <td>
      <strong>Partie conventionn�e :</strong>
      <i>{{mb_value object=$object field=secteur1}}</i>
    </td>
  </tr>
  <tr>
    <td>
      <strong>D�passement d'honoraires :</strong>
      <i>{{mb_value object=$object field=secteur2}}</i>
    </td>
  </tr>
</table>
{{/if}}
