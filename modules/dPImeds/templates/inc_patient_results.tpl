<iframe 
  id="Imeds-patient"
  name="Imeds-patient"
  src="{{$url}}?ctyp=p&amp;cidc={{$idImeds.cidc}}&amp;cdiv={{$idImeds.cdiv}}&amp;csdv={{$idImeds.csdv}}&amp;dpat={{$patient->_IPP}}&amp;login={{$idImeds.login}}&amp;password={{$idImeds.password}}" 
  onload="ViewPort.SetFrameHeight(this)"
  width="100%"
  >
  Serveur de r�sultats indisponible pour le patient '{{$patient->_view}}';
</iframe>