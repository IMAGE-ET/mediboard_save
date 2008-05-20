<!-- Fermeture des tableaux -->
    </td>
  </tr>
</table>

{{assign var=header value=10}}
{{assign var=footer value=5}}

<style type="text/css">

{{include file=../../dPcompteRendu/css/print.css header=10 footer=5}}

</style>
  
<div class="header">
	<table class="main">
	  <tr>
	    <td class="left">
	      {{assign var=praticien value=$prescription->_ref_praticien}}
	      {{assign var=function value=$praticien->_ref_function}}
	      
	      <strong>Dr. {{$praticien->_view}}</strong>
	      <br />
	      {{mb_title object=$praticien field=adeli}}
	      {{mb_value object=$praticien field=adeli}}
	      <br />
	      {{$praticien->_ref_discipline->_view}}
	      <br />
	      {{mb_value object=$praticien field=titres}}
	      <br />
	    </td>
	    
	    <td class="center">
	      <h1>{{$etablissement->_view}}</h1>
	      <h2>{{mb_value object=$function field=soustitre}}</h2>
	    </td>
	  
	    <td class="right">
	      le {{$date|date_format:"%d %B %Y"}}
	      <br />
				A l'attention de 
				<br />		      
	      <strong>{{$prescription->_ref_patient->_view}}</strong>
	    </td>
	  </tr>
	</table>    
</div>

<!-- Affichage du pieds de page -->
<div class="footer">
  {{if $prescription->type == "externe"}}
  <table>
  	<tr>
  	  <td class="left">
		   	{{mb_value object=$function field=_view}}
		   	<br />
		   	{{mb_value object=$function field=soustitre}}
			</td>
  	  <td class="center">
		   	{{mb_value object=$function field=adresse}}
				<br />
				{{$function->cp}} &mdash; {{$function->ville}}
			</td>
  	  <td class="right">
			  Tel: {{$function->tel}}
	  		<br />
	  		Fax: {{$function->fax}}
	  	</td>
		</tr>
  </table>
	{{/if}}
</div>



<!-- Affichage en mode ALD -->
{{if $lines.medicaments.med.ald || $lines.medicaments.med.no_ald ||
     $lines.medicaments.comment.ald || $lines.medicaments.comment.no_ald}}
  
  {{if $linesElt|@count}}
  <div class="body">
  {{else}}
  <div class="bodyWithoutPageBreak">
  {{/if}}

{{if $lines.medicaments.med.ald || $lines.medicaments.comment.ald}}
  <h1>Medicaments</h1>
    <!-- Affichage des ald -->
    <h3>
    Prescriptions relatives au traitement de l'affection de longue dur�e
		</h3>
		<ul>
    {{foreach from=$lines.medicaments.med.ald item=line_medicament_element_ald}}
      {{include file="inc_print_medicament.tpl" med=$line_medicament_element_ald}}
    {{/foreach}}
   
	    {{foreach from=$lines.medicaments.comment.ald item=line_medicament_comment_ald}}
		    <li>
		      {{$line_medicament_comment_ald->commentaire}}
		    </li>
	    {{/foreach}}
    </ul>
    <div class="middle"></div>
    
    <!-- Affichage des no_ald -->
    <h3>
    Prescriptions SANS RAPPORT avec l'affection de longue dur�e
    </h3>
    <ul>
    {{foreach from=$lines.medicaments.med.no_ald item=line_medicament_element_no_ald}}
      {{include file="inc_print_medicament.tpl" med=$line_medicament_element_no_ald}}
    {{/foreach}}
    {{foreach from=$lines.medicaments.comment.no_ald item=line_medicament_comment_no_ald}}
		  <li>
		    {{$line_medicament_comment_no_ald->commentaire}}
		  </li>
	  {{/foreach}}
    </ul>
    
    {{if $traitements_arretes|@count && !$ordonnance}}
    <br />
    <h1>Traitements arr�t�s</h1>
    <ul>
    {{foreach from=$traitements_arretes item=line_traitement}}
      <li>{{$line_traitement->_view}} (le {{$line_traitement->date_arret|date_format:"%d/%m/%Y"}})</li>
    {{/foreach}}
    </ul>
    {{/if}}
    
<!-- Affichage en mode normal -->
{{else}}
  <h1>M�dicaments</h1>
    <!-- Affichage des no_ald -->
    <ul>
    {{foreach from=$lines.medicaments.med.no_ald item=line_medicament_element_no_ald}}
      {{include file="inc_print_medicament.tpl" med=$line_medicament_element_no_ald}}
    {{/foreach}}
      {{foreach from=$lines.medicaments.comment.no_ald item=line_medicament_comment_no_ald}}
		    <li>
		      {{$line_medicament_comment_no_ald->commentaire}}
		    </li>
	    {{/foreach}}
    </ul>
    
    
    {{if $traitements_arretes|@count && !$ordonnance}}
    <br />
    <h1>Traitements arr�t�s</h1>
    <ul>
    {{foreach from=$traitements_arretes item=line_traitement}}
      <li>{{$line_traitement->_view}} (le {{$line_traitement->date_arret|date_format:"%d/%m/%Y"}})</li>
    {{/foreach}}
    </ul>
    {{/if}}
    
{{/if}}
 </div>
{{/if}}


<!-- Parcours des chapitres -->
{{foreach from=$linesElt key=name_chap item=elementsChap name="foreachChap"}}
<!-- Parcours des categories -->
{{foreach from=$elementsChap key=cat_id item=elementsCat name="foreachCat"}}
  {{assign var=name_category value=$categories.$cat_id}}
  {{foreach from=$elementsCat key=exec item=elements name="foreachExec"}}
    {{if $exec != "aucun"}}
      {{assign var=exec value=$executants.$exec}}
    {{/if}}
     
     {{if $smarty.foreach.foreachChap.last && $smarty.foreach.foreachCat.last && $smarty.foreach.foreachExec.last}}
       <div class="bodyWithoutPageBreak">
     {{else}} 
       <div class="body">
     {{/if}}
     
     <h1>{{tr}}CCategoryPrescription.chapitre.{{$name_chap}}{{/tr}} - {{$name_category->_view}}<br />{{if $exec != "aucun"}}{{$exec->_view}}{{/if}}</h1>
     
     {{if $elements.element.ald || $elements.comment.ald}}
       <h3>
         Prescriptions relatives au traitement de l'affection de longue dur�e
	   	 </h3>
	   	 <ul>
	     <!-- Affichage des ALD -->
	     {{foreach from=$elements.element.ald item=_element_ald}}
	       {{include file="inc_print_element.tpl" elt=$_element_ald}}
	     {{/foreach}}
	     {{foreach from=$elements.comment.ald item=_comment_ald}}
	     <li>
	       {{$_comment_ald->commentaire}}
	     </li>
	     {{/foreach}}
	     </ul>
	     
	     <div class="middle"></div>
	     
	     <h3>
         Prescriptions SANS RAPPORT avec l'affection de longue dur�e
       </h3>
	     <!-- Affichage des no_ald -->
	     <ul>
	     {{foreach from=$elements.element.no_ald item=_element_no_ald}}
	       {{include file="inc_print_element.tpl" elt=$_element_no_ald}}
	     {{/foreach}}
	     {{foreach from=$elements.comment.no_ald item=_comment_no_ald}}
	     <li>
	       {{$_comment_no_ald->commentaire}}
	     </li>
	     {{/foreach}}
	     </ul>
     {{else}}
	     <!-- Affichage normal -->
	     <ul>
	     {{foreach from=$elements.element.no_ald item=_element_no_ald}}
	       {{include file="inc_print_element.tpl" elt=$_element_no_ald}}
	     {{/foreach}}
	     {{foreach from=$elements.comment.no_ald item=_comment_no_ald}}
	     <li>
	       {{$_comment_no_ald->commentaire}}
	     </li>
	     {{/foreach}}
	     </ul>
     {{/if}}
     </div>
  {{/foreach}}
{{/foreach}}
{{/foreach}}

<!-- Re-ouverture des tableaux -->
<table>
  <tr>
    <td>