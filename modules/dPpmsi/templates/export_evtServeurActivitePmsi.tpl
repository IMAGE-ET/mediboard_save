<!-- $Id$ -->

{{mb_script module="system" script="object_selector"}}

<script type="text/javascript">

popupEchangeViewer = function(echange_hprim_id) {
  var url = new Url("hprimxml", "echangeviewer");
	url.addParam("echange_hprim_id", echange_hprim_id);
  url.popup(700, 550, "Message Echange");
	return false;
}

function choosePreselection(oSelect) {
  if (!oSelect.value) { 
    return;
  }
  
  var aParts = oSelect.value.split("|");
  var sLibelle = aParts.pop();
  var sCode = aParts.pop();

  var oForm = oSelect.form;
  oForm.cmca_uf_code.value = sCode;
  oForm.cmca_uf_libelle.value = sLibelle;
  
  oSelect.value = "";
}

</script>

<table class="main">
  <tr>
    <td class="text">
      {{if $mbObject->_id}}
      {{if $typeObject == "op"}}
      {{assign var="mbSejour" value=$mbObject->_ref_sejour}}
      {{else}}
      {{assign var="mbSejour" value=$mbObject}}
      {{/if}}
      {{if !$doc_valid}}
      <h3>Document non valide : pensez � valider les valeurs suivantes !</h3>
      <ul>
        <li>
          {{if $mbSejour->_ref_patient->_IPP}}
          <div class="info">
          {{else}}
          <div class="error">
          {{/if}}
          Identifiant S@nt�.com du patient : 
          '{{$mbSejour->_ref_patient->_IPP}}'
          </div>
        </li>
        <li>
          {{if $mbSejour->_num_dossier}}
          <div class="info">
          {{else}}
          <div class="error">
          {{/if}}
          Identifiant S@nt�.com de la venue : 
          '{{$mbSejour->_num_dossier}}'
          </div>
        </li>
        <li>
          {{if $mbSejour->_ref_praticien->adeli}}
          <div class="info">
          {{else}}
          <div class="error">
          {{/if}}
          Code Adeli du praticien responsable du s�jour : 
          '{{mb_value object=$mbSejour->_ref_praticien field=adeli}}' 
          (Dr {{$mbSejour->_ref_praticien->_view}})
          </div>
        </li>
        {{if $typeObject =="op"}}
        <li>
          {{if $mbObject->_ref_chir->_view}}
          <div class="info">
          {{else}}
          <div class="error">
          {{/if}}
          Code Adeli du chirurgien responsable de l'intervention : 
          '{{mb_value object=$mbObject->_ref_chir field=adeli}}'
          (Dr {{$mbObject->_ref_chir->_view}})
          </div>
        </li>
        <li>
          {{if $mbObject->code_uf}}
          <div class="info">
          {{else}}
          <div class="error">
          {{/if}}
          Code d'unit� fonctionnelle S@nt�.com : 
          '{{$mbObject->code_uf}}'
          </div>
        </li>
        <li>
          {{if $mbObject->libelle_uf}}
          <div class="info">
          {{else}}
          <div class="error">
          {{/if}}
          Libell� d'unit� fonctionnelle S@nt�.com : 
          '{{$mbObject->libelle_uf}}'
          </div>
        </li>
        {{/if}}
      </ul>
      {{/if}}
      <h3>

      {{if $evenementActivitePMSI->documentfilename}}
      <h3>XML: G�n�ration du document</h3>
      <ul>
        <li>
          Consulter <a href="{{$evenementActivitePMSI->documentfilename}}">le Document H'XML</a>: 
          Le document <strong>{{if $doc_valid}}est valide!{{else}}n'est pas valide...{{/if}}</strong>
        </li>
        {{if $typeObject == "op"}}
        <li>
          Visualiser <a href="?m=dPplanningOp&amp;tab=vw_edit_planning&amp;operation_id={{$mbObject->_id}}">l'intervention correspondante</a>
        </li>
        {{/if}}
      </ul>
      {{/if}}
      {{if $logs|@count}}
        <h3>Envoi du document au serveur S@nt�.com</h3>
      <ul>
      {{foreach from=$logs|smarty:nodefaults item=log}}
        <li>{{$log}}</li>
      {{/foreach}}
      </ul>
      {{/if}}
      <h3>Tous les �changes envoy�s pour cet objet ({{$mbObject->_back.echanges_hprim|@count}})</h3>
      <ul>
        {{foreach from=$mbObject->_back.echanges_hprim item=_echange}}
        <li>
          Echange <a href="#1" onclick="return popupEchangeViewer('{{$_echange->_id}}')">{{$_echange->_id}}</a>
          produit le {{$_echange->date_production|date_format:"%A %d %B %Y � %H:%M:%S"}} 
					{{if $_echange->date_echange}}
					 �chang� le {{$_echange->date_echange|date_format:"%A %d %B %Y � %H:%M:%S"}} 
					{{/if}}
        </li>
        {{foreachelse}}
        <li>Aucun �change envoy�</li>
        {{/foreach}}
      </ul>
      {{/if}}
    </td>
  </tr>
  <tr>
</table>