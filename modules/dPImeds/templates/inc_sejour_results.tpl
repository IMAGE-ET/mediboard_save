{{if !$app->_is_intranet}}
<div class="big-info">
Pour des raisons de s�curit�, l'affichage des r�sultats de laboratoire n'est pas disponible depuis l'acc�s distant.
<br />
Merci de r�essayer ult�rieurement depuis un acc�s sur site.
</div>

{{else}}
<script type="text/javascript">

Main.add(function() {
  var oForm = document.Imeds_sejour_form;
  oForm.submit();
} );

</script>

<form target="Imeds-sejour" name="Imeds_sejour_form" action="{{$url}}?nameframe=Imeds-sejour&amp;ctyp=s&amp;cidc={{$idImeds.cidc}}&amp;cdiv={{$idImeds.cdiv}}&amp;csdv={{$idImeds.csdv}}&amp;ndos={{$sejour->_num_dossier}}" method="post">
  <input type="hidden" name="login" value="{{$idImeds.login}}" />
  <input type="hidden" name="password" value="{{$idImeds.password}}" />
</form>

<iframe 
  id="Imeds-sejour" 
  name="Imeds-sejour" 
  onload="ViewPort.SetFrameHeight(this)"
  width="100%" 
  >
  Serveur de r�sultats indisponible pour le s�jour '{{$sejour->_view}}'
</iframe>
{{/if}}