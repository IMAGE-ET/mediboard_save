{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=range value=1}}

{{if $prescription->type == "externe" && $app->user_prefs.duplicata_checked_externe}}
  {{assign var=range value=2}}
{{/if}}

{{if $prescription->object_id}}
	<script type="text/javascript">
	//Main.add(window.print);
	</script> 
	
	<!-- Fermeture des tableaux -->
	    </td>
	  </tr>
	</table>
{{else}}
<html>
  <body>
{{/if}}

<style type="text/css">
{{include file=../../dPcompteRendu/css/print.css nodebug="true"}}

div.body, table.body, div.bodyWithoutPageBreak, table.bodyWithoutPageBreak {
  padding-top: {{$header}}px;
  padding-bottom: {{$footer}}px;
}
	

@media screen {
	div.body, table.body, div.bodyWithoutPageBreak, table.bodyWithoutPageBreak {
	  padding-top: {{$header+25}}px;
	  padding-bottom: {{$footer}}px;
  }
	div.header {
    height: {{$header}}px;
		margin-top: 25px;
  }
}


/* Partie variable */
@media print {
  div.body, table.body, div.bodyWithoutPageBreak, table.bodyWithoutPageBreak {
    padding-top: {{$header+3}}px;
    padding-bottom: {{$footer+3}}px;
  }
	
	* {
    font-size: 12px;
    font-family: Arial,Helvetica,sans-serif;
  }
}

div.header {
  height: {{$header}}px;
}

div.footer {
  height: {{$footer}}px;
}

div.action {
  height: 25px;
  background-color: #DDD;
	border: 0 solid #AAAAAA;
  opacity: 0.9;
  overflow: hidden;
  position: fixed;
  width: 100%;	
	padding-top: 0px;
}

p.duplicata {
  font-size: 1.5em;
  text-align: center;
}
</style>

{{if $prescription->object_id}}
	<div class="action not-printable">
		<button type="button" class="print" onclick="modalPrint = modal($('modal-print'));">
		  Impression partielle
		</button>
		{{if $partial_print}}
		<div class="small-warning" style="display: inline">Ordonnance affich�e partiellement</div>
		{{/if}}
	</div>
	
	<form name="printLinesOrdonnance" medhod="get" action="?" class="not-printable">
		<input type="hidden" name="m" value="dPprescription" />
		<input type="hidden" name="a" value="{{$a}}" />
		<input type="hidden" name="dialog" value="{{$dialog}}" />
		
		{{assign var=numCols value=2}}
		<div id="modal-print" style="display: none;">
		  <table class="form">
		    <tr>
		      <th class="title" colspan="{{$numCols}}">
		        <button type="button" class="cancel notext" onclick="modalPrint.close();" style="float: right;">{{tr}}Close{{/tr}}</button>
		      	Impression partielle
					</th>
		    </tr>
		    {{foreach from=$all_lines item=_lines_by_chap name=chaps}}
		       {{foreach from=$_lines_by_chap item=_line name="lines"}}
					   {{if $smarty.foreach.lines.first}}
						 <tr>
						 	<th class="category" colspan="{{$numCols}}">
						 		{{if $_line instanceof CPrescriptionLineElement}}
								 {{tr}}CCategoryPrescription.chapitre.{{$_line->_chapitre}}{{/tr}}
								{{else}}
								  M�dicaments
						    {{/if}}
								</th>
						 </tr>
						 <tr>
						 {{/if}}
					   
					   {{assign var=i value=$smarty.foreach.lines.iteration}}
						 <td>
		           <input type="checkbox" name="selected_lines[]" value="{{$_line->_guid}}" {{if in_array($_line->_guid, $selected_lines)}}checked="checked"{{/if}} /> {{$_line->_view}}
						 </td>
						 {{if (($i % $numCols) == 0)}}</tr>
						   {{if !$smarty.foreach.lines.last && !$smarty.foreach.chaps.last}}
							   <tr>
							 {{/if}}
						 {{/if}}
						 
		      {{/foreach}}
		    {{/foreach}}
		  </table>
			<div class="button">
			  <button class="search">Filtrer</button>
			</div>
	  </div>
	</form>
{{/if}}

<div class="header" onclick="window.print();" style="cursor: pointer">
	{{if $_ald}}
	 <table class="main">
        <tr>
          <td class="left">
            <strong>Dr {{$praticien->_view}}</strong>
            <br />
						{{mb_value object=$praticien field=spec_cpam_id}}
            <br />
            {{mb_value object=$etablissement field=adresse}}
	          {{$etablissement->cp}} {{$etablissement->ville}}
					  <br />
            
            <span style="float: right">
					  {{mb_value object=$praticien field=secteur}}
            </span>

						<table style="width: 1%">
							<tr>
								<td style="white-space: nowrap;">
								  {{mb_value object=$praticien field=adeli}}
								</td>
								{{if $praticien->cab}}
								<td>
			            <table style="width: 1%">
			              <tr><td style="border-bottom: 1px solid black; text-align: center;">{{mb_value object=$praticien field=cab}}</td></tr>
			              <tr><td>{{mb_label object=$praticien field=cab}}</td></tr>
			            </table>
								</td>
								{{/if}}
                {{if $praticien->conv}}
                <td>
                  <table style="width: 1%">
                    <tr><td style="border-bottom: 1px solid black; text-align: center;">{{mb_value object=$praticien field=conv}}</td></tr>
                    <tr><td>{{mb_label object=$praticien field=conv}}</td></tr>
                  </table>
                </td>
                {{/if}}
                {{if $praticien->zisd}}
								<td>
                  <table style="width: 1%">
                    <tr><td style="border-bottom: 1px solid black; text-align: center;">{{mb_value object=$praticien field=zisd}}</td></tr>
                    <tr><td>{{mb_label object=$praticien field=zisd}}</td></tr>
                  </table>
                </td>
								{{/if}}
								{{if $praticien->ik}}
                <td>
                  <table style="width: 1%">
                    <tr><td style="border-bottom: 1px solid black; text-align: center;">{{mb_value object=$praticien field=ik}}</td></tr>
                    <tr><td>{{mb_label object=$praticien field=ik}}</td></tr>
                  </table>
                </td>
								{{/if}}
							</tr>
						</table>

					</td>
          <td class="right">
          	le {{$date|date_format:"%d %B %Y"}}<br />
						A l'attention de 
            <br />          
            <strong>{{$prescription->_ref_patient->_view}}</strong>
          </td>
        </tr>
      </table>  
	{{else}}
		{{if $generated_header}}
	    {{$generated_header|smarty:nodefaults}}
	  {{else}}
	  	<table class="main">
			  <tr>
			    <td class="left">
			      {{if $praticien->_id}}
				      <strong>Dr {{$praticien->_view}}</strong>
				      <br />
				      {{mb_title object=$praticien field=adeli}}
				      {{mb_value object=$praticien field=adeli}}
				      <br />
				      {{$praticien->_ref_discipline->_view}}
				      <br />
				      {{mb_value object=$praticien field=titres}}
				      {{if $code_rpps}}
                <table style="width: 10%">
                  <tr style="text-align: center;">
                    <td>
                      N� RPPS
                    </td>
                {{*    <td>
                      N� AM
                    </td>   *}}
                  </tr>
                  <tr>
                    <td>
                      <img src="{{$code_rpps}}" width="160" height="45"/>
                    </td>
                 {{*   <td>
                      <img src="{{$am}}" width="160" height="45"/>
                    </td>  *}}
                  </tr>
                </table>
              {{/if}}
			      {{elseif $prescription->object_id}}
			        Prescription globale
			      {{/if}}
			    </td>
			    <td class="center">
			      <h1>{{$etablissement->_view}}</h1>
			      {{if $function}}
			        {{mb_value object=$function field=soustitre}}
			      {{/if}}
			    </td>
			    <td class="right">
			      le {{$date|date_format:"%d %B %Y"}}
			      <br />
			      {{if $prescription->object_id}}
						A l'attention de 
						<br />		      
			      <strong>{{$prescription->_ref_patient->_view}}</strong>
			      <br />
			      Age: {{$prescription->_ref_patient->_age}} ans<br />
			      Poids: {{$poids}} kg
			      {{else}}
			      Protocole: {{$prescription->libelle}}
			      {{/if}}
			    </td>
			  </tr>
			</table>  
		{{/if}} 
	{{/if}}
</div>

<!-- Affichage du pieds de page -->
{{if !$_ald}}
<div class="footer">
  {{if $generated_footer}}
    {{$generated_footer|smarty:nodefaults}}
  {{else}}
	  <table>
	  	<tr>
	  	  <td class="left">
			   	{{mb_value object=$function field=soustitre}}
				</td>
	  	  <td class="center">
			   	{{mb_value object=$function field=adresse}}
					<br />
					{{$function->cp}} &mdash; {{$function->ville}}
				</td>
	  	  <td class="right">
				  Tel: {{mb_value object=$function field=tel}}
		  		<br />
		  		Fax: {{mb_value object=$function field=fax}}
		  	</td>
			</tr>
	  </table>
  {{/if}}
</div>
{{/if}}

{{if $only_dmi}}
  <div style="display: none;">
{{/if}}

{{foreach from=1|range:$range item=i}}
  {{if $i == 2}}
    <br style="page-break-before: always;" />
  {{/if}}
<!-- Affichage en mode ALD -->
{{if $lines.medicaments.med.ald || $lines.medicaments.med.no_ald ||
     $lines.medicaments.comment.ald || $lines.medicaments.comment.no_ald}}
  
  {{if $linesElt|@count && $i==1}}
  <div class="body">
  {{else}}
  <div class="bodyWithoutPageBreak">
  {{/if}}
  
  {{if $i==2}}
    <p class="duplicata opacity-70">
      Duplicata ne permettant pas la d�livrance de m�dicaments
    </p>
  {{/if}}
  
{{if $lines.medicaments.med.ald || $lines.medicaments.comment.ald || $lines.medicaments.dm.ald}}
    <!-- Affichage des ald -->
   <div style="border: 1px dotted #555; padding-right: 10px; text-align: center;">
     <strong>Prescriptions relatives au traitement de l'affection de longue dur�e reconnue (liste ou hors liste)</strong>
		 <br />
		 (AFFECTION EXONERANTE)
	 </div>
		
		<ul>
      {{foreach from=$lines.medicaments.med.ald item=line_medicament_element_ald}}
        {{include file="inc_print_medicament.tpl" med=$line_medicament_element_ald nodebug=true}}
      {{/foreach}}
      {{foreach from=$lines.medicaments.comment.ald item=line_medicament_comment_ald}}
		      {{include file="inc_print_commentaire.tpl" comment=$line_medicament_comment_ald nodebug=true}}
	    {{/foreach}}
			{{foreach from=$lines.medicaments.dm.ald item=line_medicament_dm_ald}}
        <li>{{$line_medicament_dm_ald->_ref_dm->libelle}} 
				{{if $line_medicament_dm_ald->quantite_dm}}
				  ({{$line_medicament_dm_ald->quantite_dm}})
				{{/if}}
			  </li>
			{{/foreach}}
    </ul>
		
		{{if $prescription->QSP}}
    <br />
    <strong style="padding-left: 40px;">
      QSP {{$prescription->QSP}}
    </strong>
    {{/if}}
		
    <div class="middle"></div>
    <!-- Affichage des no_ald -->
		<div style="border: 1px dotted #555; padding-right: 10px; text-align: center;">
     <strong>Prescriptions SANS RAPPORT avec l'affection de longue dur�e</strong>
     <br />
    (MALADIES INTERCURRENTES)
   </div>
    <ul>
    {{foreach from=$lines.medicaments.med.no_ald item=line_medicament_element_no_ald}}
      {{if $line_medicament_element_no_ald->_class == "CPrescriptionLineMedicament"}}
        {{include file="inc_print_medicament.tpl" med=$line_medicament_element_no_ald nodebug=true}}
      {{else}}
        {{include file="inc_print_prescription_line_mix.tpl" perf=$line_medicament_element_no_ald nodebug=true}}
      {{/if}} 
    {{/foreach}}
    {{foreach from=$lines.medicaments.comment.no_ald item=line_medicament_comment_no_ald}}
      {{include file="inc_print_commentaire.tpl" comment=$line_medicament_comment_no_ald nodebug=true}}
	  {{/foreach}}
		{{foreach from=$lines.medicaments.dm.no_ald item=line_medicament_dm_no_ald}}
       <li>{{$line_medicament_dm_no_ald->_ref_dm->libelle}} 
			 {{if $line_medicament_dm_no_ald->quantite_dm}}
			   ({{$line_medicament_dm_no_ald->quantite_dm}})
			 {{/if}}
			 </li>
     {{/foreach}}
    </ul>
		
		{{if $prescription->QSP}}
    <br />
    <strong style="padding-left: 40px;">
      QSP {{$prescription->QSP}}
    </strong>
    {{/if}}
		
<!-- Affichage en mode normal -->
{{else}}
    <!-- Affichage des no_ald -->
    <ul>
    {{foreach from=$lines.medicaments.med.no_ald item=line_medicament_element_no_ald name="foreach_med"}}
		  {{if !$smarty.foreach.foreach_med.first && $smarty.foreach.foreach_med.index%15 == 0 && $prescription->object_id}}
		   </ul>
			 </div>
			 <div class="body">
       {{if $i==2}}
          <p class="duplicata opacity-70">
            Duplicata ne permettant pas la d�livrance de m�dicaments
          </p>
        {{/if}}
			 <ul>
			{{/if}}
		
      {{if $line_medicament_element_no_ald->_class == "CPrescriptionLineMedicament"}}
        {{include file="inc_print_medicament.tpl" med=$line_medicament_element_no_ald nodebug=true}}
        {{if !$prescription->object_id}}
	        {{if $line_medicament_element_no_ald->_ref_variantes.CPrescriptionLineMedicament|@count
	          || $line_medicament_element_no_ald->_ref_variantes.CPrescriptionLineMix|@count}}
	        <br />
	        <ul style="margin-left: 15px;">
	        <strong>Variantes possibles:</strong> 
	        {{foreach from=$line_medicament_element_no_ald->_ref_variantes item=_subst_line_by_chap}}
	        {{foreach from=$_subst_line_by_chap item=_subst_line_med}}
	          {{if $_subst_line_med->_class == "CPrescriptionLineMedicament"}}
	            {{include file="inc_print_medicament.tpl" med=$_subst_line_med nodebug=true}}
	          {{else}}
	            {{include file="inc_print_prescription_line_mix.tpl" perf=$_subst_line_med nodebug=true}}
	          {{/if}}
	        {{/foreach}}
	        {{/foreach}}
	        </ul>
	        {{/if}}  
        {{/if}}
      {{else}}
        {{include file="inc_print_prescription_line_mix.tpl" perf=$line_medicament_element_no_ald}}
        {{if !$prescription->object_id}}
	        {{if $line_medicament_element_no_ald->_ref_variantes.CPrescriptionLineMedicament|@count
	          || $line_medicament_element_no_ald->_ref_variantes.CPrescriptionLineMix|@count}}
	        <br />
	        <ul style="margin-left: 15px;">
	          <strong>Variantes possibles:</strong> 
	        {{foreach from=$line_medicament_element_no_ald->_ref_variantes item=_subst_line_by_chap}}
	        {{foreach from=$_subst_line_by_chap item=_subst_line_med}}
	          {{if $_subst_line_med->_class == "CPrescriptionLineMedicament"}}
	            {{include file="inc_print_medicament.tpl" med=$_subst_line_med nodebug=true}}
	          {{else}}
	            {{include file="inc_print_prescription_line_mix.tpl" perf=$_subst_line_med nodebug=true}}
	          {{/if}}
	        {{/foreach}}
	        {{/foreach}}
	        </ul>
	        {{/if}}  
        {{/if}}
      {{/if}}  
			
			
			
    {{/foreach}}
      {{foreach from=$lines.medicaments.comment.no_ald item=line_medicament_comment_no_ald}}
        {{include file="inc_print_commentaire.tpl" comment=$line_medicament_comment_no_ald nodebug=true}}
	    {{/foreach}}
			{{foreach from=$lines.medicaments.dm.no_ald item=line_medicament_dm_no_ald}}
        <li>{{$line_medicament_dm_no_ald->_ref_dm->libelle}} 
				{{if $line_medicament_dm_no_ald->quantite_dm}}
				  ({{$line_medicament_dm_no_ald->quantite_dm}})
				{{/if}}
				</li>
      {{/foreach}}
			
    </ul>
		
		{{if $prescription->QSP}}
		<br />
		<strong style="padding-left: 40px;">
		  QSP {{$prescription->QSP}}
		</strong>
		{{/if}}
{{/if}}
 </div>
{{/if}}


<!-- Parcours des chapitres -->
{{foreach from=$linesElt key=name_chap item=elementsChap name="foreachChap"}}
{{if $i==1 || $name_chap=="med_elt"}}
<!-- Parcours des categories -->
  {{foreach from=$elementsChap item=elements name="foreachExec"}}
     
     {{if $smarty.foreach.foreachChap.last && $smarty.foreach.foreachExec.last &&  !$linesDMI|@count}}
       <div class="bodyWithoutPageBreak">
     {{else}} 
       <div class="body">
     {{/if}}

     {{if $i==2}}
        <p class="duplicata opacity-70">
          Duplicata ne permettant pas la d�livrance de m�dicaments
        </p>
      {{/if}}

     <h2>{{$conf.dPprescription.CCategoryPrescription.$name_chap.phrase}}</h2>
     {{if array_key_exists("ald", $elements) && $elements.ald|@count}}
	   <div style="border: 1px dotted #555; padding-right: 10px; text-align: center;">
	     <strong>Prescriptions relatives au traitement de l'affection de longue dur�e reconnue (liste ou hors liste)</strong>
	     <br />
	     (AFFECTION EXONERANTE)
	   </div>
	   {{/if}}

     {{if array_key_exists("ald", $elements)}}
     <ul>
	     <!-- Affichage des ALD -->
	     {{foreach from=$elements.ald key=name_cat item=_elements_ald name="foreach_elts_ald"}}  
	        {{foreach from=$_elements_ald  item=_element_ald name=foreach_elt_ald}}
	           {{if $smarty.foreach.foreach_elt_ald.first}}
						   {{if $name_cat != "inj"}}
			           {{assign var=category value=$categories.$name_chap.$name_cat}}
				         <strong>{{$category->nom}}</strong>
				         {{if $conf.dPprescription.CCategoryPrescription.show_header && $category->header}}, {{$category->header}}{{/if}}
				         {{if $conf.dPprescription.CCategoryPrescription.show_description && $category->description}}, {{$category->description}}{{/if}}
		           {{else}}
							   <strong>Injections par IDE � domicile (f�ri� et dimanche)</strong>
							 {{/if}}
						 {{/if}}

             {{if $name_cat == "inj"}}
               {{include file="inc_print_medicament.tpl" med=$_element_ald nodebug=true}}
             {{else}}
			         {{if $_element_ald->_class == "CPrescriptionLineElement"}} 
		             <!-- Affichage de l'element -->
		             {{include file="inc_print_element.tpl" elt=$_element_ald nodebug=true}}
		           {{else}}
	               {{include file="inc_print_commentaire.tpl" comment=$_element_ald nodebug=true}}
		           {{/if}}
						 {{/if}}
	        {{/foreach}}
	     {{/foreach}}
	     </ul>
			 {{/if}}
	     
	    {{if array_key_exists("ald", $elements) && $elements.ald|@count}}
	    <div class="middle"></div>
	    <div style="border: 1px dotted #555; padding-right: 10px; text-align: center;">
	     <strong>Prescriptions SANS RAPPORT avec l'affection de longue dur�e</strong>
	     <br />
	    (MALADIES INTERCURRENTES)
	   </div>

	    {{/if}}
	     <!-- Affichage des no_ald -->
	    <ul>
	     {{foreach from=$elements.no_ald key=name_cat item=_elements_no_ald name="foreach_elts_no_ald"}}
	       {{foreach from=$_elements_no_ald  item=_element_no_ald name=foreach_elt_no_ald}}
	           {{if $smarty.foreach.foreach_elt_no_ald.first}}
						   {{if $name_cat != "inj"}}
							   {{assign var=category value=$categories.$name_chap.$name_cat}}
			           <strong>{{$category->nom}}</strong>
			         	 {{if $conf.dPprescription.CCategoryPrescription.show_header && $category->header}}, {{$category->header}}{{/if}}
				         {{if $conf.dPprescription.CCategoryPrescription.show_description && $category->description}}, {{$category->description}}{{/if}}
			         {{else}}
							   <strong>Injections par IDE � domicile (f�ri� et dimanche)</strong>
							 {{/if}}
						 {{/if}}
		
		         {{if $name_cat == "inj"}}
						   {{include file="inc_print_medicament.tpl" med=$_element_no_ald nodebug=true}}
						 {{else}}
			         {{if $_element_no_ald->_class == "CPrescriptionLineElement"}}
		             <!-- Affichage de l'element -->
		             {{include file="inc_print_element.tpl" elt=$_element_no_ald nodebug=true}}
		           {{else}}
	               {{include file="inc_print_commentaire.tpl" comment=$_element_no_ald nodebug=true}}
		           {{/if}}
						 {{/if}}
	        {{/foreach}}
	     {{/foreach}}
	     </ul>
     </div>
  {{/foreach}}
  {{/if}}
{{/foreach}}
{{/foreach}}

{{if $only_dmi}}
  </div>
{{/if}}

{{if $linesDMI|@count}}
<div class="{{if $linesDMI|@count <= 7}}bodyWithoutPageBreak{{else}}body{{/if}}">
    <ul>
      <h1 class="no-break">DMI</h1>
    {{foreach from=$linesDMI item=_line_dmi name=dmis}}
		  {{if !$smarty.foreach.dmis.first && $smarty.foreach.dmis.index%7 == 0}}
         </ul>
			 </div>
       <div class="bodyWithoutPageBreak">
         <ul>
      {{/if}}
		
      {{if !$_line_dmi->septic}}
    <li>
		 <strong>{{$_line_dmi->_ref_product->name}}</strong>:
      <ul>
        <li>Quantit�: <strong>{{$_line_dmi->quantity}}</strong></li>
        <li>Code produit: <strong>{{$_line_dmi->_ref_product->code}}</strong></li>
        <li>Code lot: <strong>{{$_line_dmi->_ref_product_order_item_reception->code}}</strong></li>
      </ul>
      </li>
      {{/if}}
		 {{/foreach}}
    </ul>
	 </div>
{{/if}}

{{if $prescription->object_id}}
	<!-- Re-ouverture des tableaux -->
	<table>
	  <tr>
	    <td>
{{else}}
  </body>
</html>
{{/if}}