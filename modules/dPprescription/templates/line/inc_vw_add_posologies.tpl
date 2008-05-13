<script type="text/javascript">

Main.add( function(){
  prepareForm(document.forms['addPriseFoisPar{{$type}}{{$line->_id}}']);
  prepareForm(document.forms['addPriseMoment{{$type}}{{$line->_id}}']); 
  prepareForm(document.forms['addPriseTousLes{{$type}}{{$line->_id}}']);
} );

// Affichage des div d'ajout de posologies
selDivPoso = function(type, line_id, type_elt){
  if(!type){
    type = "foisPar"+type_elt;
  }
  
  oDivMoment = $('moment'+type_elt+line_id);
  oDivFoisPar = $('foisPar'+type_elt+line_id);
  oDivTousLes = $('tousLes'+type_elt+line_id);
  
  oDivMoment.hide();
  oDivFoisPar.hide();
  oDivTousLes.hide();
  $(type+line_id).show();
}


reloadPrises = function(prescription_line_id, type){
  url = new Url;
  url.setModuleAction("dPprescription", "httpreq_vw_prises");
  url.addParam("prescription_line_id", prescription_line_id);
  url.addParam("type", type);
  url.requestUpdate('prises-'+type+prescription_line_id, { waitingText: null });
}

submitPrise = function(oForm, type){
  if(!oForm.object_id.value){
    return;
  }
  submitFormAjax(oForm, 'systemMsg', { onComplete:
    function(){
      reloadPrises(oForm.object_id.value, type);
      oForm.quantite.value = 0;
      oForm.moment_unitaire_id.value = "";
  } });
}


</script>

{{assign var=line_id value=$line->_id}}
  <select name="selShowDivPoso{{$type}}" onchange="selDivPoso(this.value,'{{$line->_id}}','{{$type}}');">
    <option value="">&mdash; Posologies manuelles</option>
    <option value="moment{{$type}}">Moment</option>
    <option value="foisPar{{$type}}">x fois par y</option>
    <option value="tousLes{{$type}}">tous les x y</option>
  </select>
  <br />
  <div id="foisPar{{$type}}{{$line->_id}}">
		<form name="addPriseFoisPar{{$type}}{{$line->_id}}" action="?" method="post" >
	    <input type="hidden" name="dosql" value="do_prise_posologie_aed" />
	    <input type="hidden" name="del" value="0" />
	    <input type="hidden" name="m" value="dPprescription" />
	    <input type="hidden" name="prise_posologie_id" value="" />
	    <input type="hidden" name="object_id" value="{{$line->_id}}" />
	    <input type="hidden" name="object_class" value="{{$line->_class_name}}" />
		  Quantit�: 
		  {{mb_field object=$prise_posologie field=quantite size=3 increment=1 form=addPriseFoisPar$type$line_id}}
		  {{if $line->_class_name == "CPrescriptionLineMedicament"}}
		    {{$line->_unite_prise}}(s)
		  {{/if}}
		  {{mb_field object=$prise_posologie field=nb_fois size=3 increment=1 form=addPriseFoisPar$type$line_id}} fois par 
		  {{mb_field object=$prise_posologie field=unite_fois}}
      {{if $line->_id}}
	      <button type="button" class="submit notext" onclick="submitPrise(this.form,'{{$type}}');">Enregistrer</button>
		  {{/if}}
		</form>
  </div>
<div id="moment{{$type}}{{$line->_id}}" style="display: none">
  <form name="addPriseMoment{{$type}}{{$line->_id}}" action="?" method="post" >
	  <input type="hidden" name="dosql" value="do_prise_posologie_aed" />
	  <input type="hidden" name="del" value="0" />
	  <input type="hidden" name="m" value="dPprescription" />
	  <input type="hidden" name="prise_posologie_id" value="" />
	  <input type="hidden" name="object_id" value="{{$line->_id}}" />
	  <input type="hidden" name="object_class" value="{{$line->_class_name}}" />
		  
	  Quantit�: 
	  {{mb_field object=$prise_posologie field=quantite size=3 increment=1 form=addPriseMoment$type$line_id}}
	  {{if $line->_class_name == "CPrescriptionLineMedicament"}}
		    {{$line->_unite_prise}}(s)
		  {{/if}}
	  <!-- Selection du moment -->
	  <select name="moment_unitaire_id" style="width: 150px">      
	  <option value="">&mdash; S�lection du moment</option>
	  {{foreach from=$moments key=type_moment item=_moments}}
	     <optgroup label="{{$type_moment}}">
	     {{foreach from=$_moments item=moment}}
	     <option value="{{$moment->_id}}">{{$moment->_view}}</option>
	     {{/foreach}}
	     </optgroup>
	  {{/foreach}}
	  </select>	
	  {{if $line->_id}}
      <button type="button" class="submit notext" onclick="submitPrise(this.form,'{{$type}}');">Enregistrer</button>
    {{/if}}
  </form>
</div>
      <div id="tousLes{{$type}}{{$line->_id}}" style="display: none">
      	<form name="addPriseTousLes{{$type}}{{$line->_id}}" action="?" method="post" >
	    <input type="hidden" name="dosql" value="do_prise_posologie_aed" />
	    <input type="hidden" name="del" value="0" />
	    <input type="hidden" name="m" value="dPprescription" />
	    <input type="hidden" name="prise_posologie_id" value="" />
	    <input type="hidden" name="object_id" value="{{$line->_id}}" />
        <input type="hidden" name="object_class" value="{{$line->_class_name}}" />
		  
        Quantit�: 
		  {{mb_field object=$prise_posologie field=quantite size=3 increment=1 form=addPriseTousLes$type$line_id}}
      {{if $line->_class_name == "CPrescriptionLineMedicament"}}
		    {{$line->_unite_prise}}(s)
		  {{/if}}
         tous les
		  {{mb_field object=$prise_posologie field=nb_tous_les size=3 increment=1 form=addPriseTousLes$type$line_id}}				   
		  {{mb_field object=$prise_posologie field=unite_tous_les}}
      {{if $line->_id}}
        <button type="button" class="submit notext" onclick="submitPrise(this.form,'{{$type}}');">Enregistrer</button>
      {{/if}}
    </form>  
  </div>
