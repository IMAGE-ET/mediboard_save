{{if !$app->_is_intranet}}
<div class="big-info">
Pour des raisons de s�curit�, l'affichage des r�sultats de laboratoire n'est pas disponible depuis l'acc�s distant.
<br />
Merci de r�essayer ult�rieurement depuis un acc�s sur site.
</div>

{{else}}
<script type="text/javascript">

Main.add(function() {
  var oForm = document.Imeds_prescription_form;
  oForm.submit();
} );

</script>

<form target="Imeds-prescription" name="Imeds_prescription_form" action="{{$url}}?nameframe=Imeds-prescription&amp;ctyp=s&amp;cidc={{$idImeds.cidc}}&amp;cdiv={{$idImeds.cdiv}}&amp;csdv={{$idImeds.csdv}}&amp;ndos={{$numPresc}}" method="post">
  <input type="hidden" name="login" value="{{$idImeds.login}}" />
  <input type="hidden" name="password" value="{{$idImeds.password}}" />
</form>

<iframe 
  id="Imeds-prescription" 
  name="Imeds-prescription" 
  onload="ViewPort.SetFrameHeight(this)"
  width="100%" 
  >
  Serveur de r�sultats indisponible pour la prescription '{{$prescription->_view}}'
</iframe>
{{/if}}