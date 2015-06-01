<script>
  impression = function(){
    $('form_upload').hide();
    $('button_print').hide();
    window.print();
    $('form_upload').show();
    $('button_print').show();
  }
</script>

<div id="form_upload">
  <h2>Import de fichier V11 pour les {{tr}}{{$facture_class}}{{/tr}}</h2>
  <div class="big-info">
    T�l�versez un fichier v11.
  </div>

  <form method="post" action="?m={{$m}}&amp;{{$actionType}}={{$action}}&amp;dialog=1&amp;" name="import" enctype="multipart/form-data">
    <input type="hidden" name="m" value="{{$m}}" />
    <input type="hidden" name="{{$actionType}}" value="{{$action}}" />
    <input type="hidden" name="MAX_FILE_SIZE" value="4096000" />
    <input type="hidden" name="facture_class" value="{{$facture_class}}" />
    <input type="file" name="import" />
    <input type="checkbox" name="dryrun" value="1" checked="checked" />
    <label for="dryrun">{{tr}}DryRun{{/tr}}</label>
    <button type="submit" class="submit">{{tr}}Save{{/tr}}</button>
  </form>
</div>

{{if $results|@count}}
  <table class="tbl">
    <tr>
      <th class="title" colspan="15">
        {{$results|@count}} r�glements trouv�es
        <button id="button_print" class="print" type="button" style="float:right;" onclick="impression();">{{tr}}Print{{/tr}}</button>
      </th>
    </tr>
    <tr>
      <th>Trans.</th>
      <th>N� adh�rent</th>
      <th>Dossier</th>
      <th>Facture</th>
      <th>D�biteur</th>
      <th>Montant</th>
      <th>R�f�rence</th>
      <th>Date d�p�t</th>
      <th>Date trait</th>
      <th>Date val.</th>
      <th>Rejet</th>
      <th>R</th>
      <th>Microfilm</th>
      <th>Erreur</th>
    </tr>
    {{foreach from=$results item=_reglement}}
      {{assign var=facture value=$_reglement.facture}}
      <tr>
        <td class="text">{{$_reglement.genre}}</td>
        <td class="text">{{$_reglement.num_client}}</td>
        <td class="text">
          {{if $facture->_class == "CFactureEtablissement"}}
            {{$facture->_ref_last_sejour->_id}}
          {{/if}}
        </td>
        <td class="text">{{$facture->_view}}</td>
        <td class="text">{{$facture->_ref_patient->_view}}</td>
        <td class="text" style="text-align: right;">{{$_reglement.montant}}</td>
        <td class="text">{{$_reglement.reference}}</td>
        <td class="text">{{$_reglement.date_depot|date_format:"%d/%m/%Y"}}</td>
        <td class="text">{{$_reglement.date_traitement|date_format:"%d/%m/%Y"}}</td>
        <td class="text">{{$_reglement.date_inscription|date_format:"%d/%m/%Y"}}</td>
        <td class="text">{{$_reglement.code_rejet}}</td>
        <td class="text">{{$facture->_ref_relances|@count}}</td>
        <td class="text">{{$_reglement.num_microfilm}}</td>
        <td class="text {{if $_reglement.errors|@count}}error{{elseif $_reglement.warning|@count}}warning{{else}}ok{{/if}} compact">
          {{foreach from=$_reglement.errors item=_error}}
            <div>{{$_error}}</div>
          {{/foreach}}
          {{foreach from=$_reglement.warning item=_error}}
            <div>{{$_error}}</div>
          {{/foreach}}
        </td>
      </tr>
    {{/foreach}}
  </table>
  <br/>
  <br/>
  <table class="form tbl" style="width: 500px;">
    <tr>
      <th>Date</th>
      <th>Enregistrements</th>
      <th>Montant</th>
    </tr>
    {{foreach from=$totaux.impute.dates item=ligne key=date}}
      <tr>
        <td>{{$date|date_format:"%d/%m/%Y"}}</td>
        <td style="text-align: center;">{{$ligne.count}}</td>
        <td style="text-align: right;">{{$ligne.total|string_format:"%0.2f"}}</td>
      </tr>
    {{/foreach}}
<tr>
  <td colspan="3"><br/></td>
</tr>
    <tr>
      <td>Total pour imputation:</td>
      <td style="text-align: center;">{{$totaux.impute.count}}</td>
      <td style="text-align: right;">{{$totaux.impute.total|string_format:"%0.2f"}}</td>
    </tr>
    <tr>
      <td>Total rejet�:</td>
      <td style="text-align: center;">{{$totaux.rejete.count}}</td>
      <td style="text-align: right;">{{$totaux.rejete.total|string_format:"%0.2f"}}</td>
    </tr>
    <tr>
      <td>Total PTT:</td>
      <td style="text-align: center;">{{$totaux.total.count}}</td>
      <td style="text-align: right;">{{$totaux.total.total|string_format:"%0.2f"}}</td>
    </tr>
  </table>
{{/if}}
