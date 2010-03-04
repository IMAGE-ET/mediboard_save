{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=patient value=$prescription->_ref_patient}}
{{assign var=sejour value=$prescription->_ref_object}}

<script type="text/javascript">

Main.add( function(){
  var oForm = getForm("filtreBons");
  
  dates = {
    limit: {
      start: "{{$sejour->_entree}}",
      stop: "{{$sejour->_sortie}}"
    },
    spots: []
  };
  Calendar.regField(oForm.debut, dates);
});
</script>  
  
<!-- Fermeture du tableau pour faire fonctionner le page-break -->
    </td>
  </tr>
</table>

<style type="text/css">
{{include file=../../dPcompteRendu/css/print.css header=8 footer=0 nodebug=true}}

/* decalage du header pour permettre l'insertion de filtres */
@media screen {
	div.header {
	  border-bottom-width: 1px;
	}
	div.header,
	div.footer {
	  position: relative; 
	  background: #ddd;
	  border: 0px solid #aaa;
	  width: 100%;
	  opacity: 0.9;
	  overflow: hidden;
	}
}

</style>

<form name="filtreBons" method="get">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="a" value="print_bon" />
  <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
  <input type="hidden" name="dialog" value="1" />
	<table class="form">
	  <tr>
	    <th colspan="3" class="category">Filtres</th>
	  </tr>
	  <tr>
	    <td>
	      <button type="button" class="print" onclick="window.print();">Imprimer les bons</button>
	    </td>
	    <td>
	      Chapitre
		    <select name="sel_chapitre" onchange=this.form.submit();>
		      <option value="">&mdash; Tous</option>
		      {{foreach from=$chapitres item=_chapitre}}
		        <option value="{{$_chapitre}}" {{if $_chapitre == $sel_chapitre}}selected="selected"{{/if}}>{{tr}}CPrescription._chapitres.{{$_chapitre}}{{/tr}}</option>
		      {{/foreach}}
		    </select>
	    </td>
	    <td>
	      Date {{mb_field object=$filter_line field=debut class=notNull onchange=this.form.submit()}}
	    </td>
	  </tr>
	</table> 
</form>


<div class="header">
  <span style="float: right">
    {{foreach from=$affectations item=_affectation}}
		  Chambre {{$_affectation->_ref_lit->_ref_chambre->_view}} 
			{{if $_affectation->entree|date_format:$dPconfig.date == $debut|date_format:$dPconfig.date}}
			� partir de {{$_affectation->entree|date_format:$dPconfig.time}} 
			{{/if}} 
		  {{if $_affectation->sortie|date_format:$dPconfig.date == $debut|date_format:$dPconfig.date}}
		    {{if $_affectation->sortie}}jusqu'� {{$_affectation->sortie|date_format:$dPconfig.time}}{{/if}}
		  {{/if}} 
			<br />
			  DE: {{$sejour->_entree|date_format:"%d/%m/%Y"}}<br />
		    DS: {{$sejour->_sortie|date_format:"%d/%m/%Y"}}
		{{/foreach}}
  </span>
	
  <div>
    <strong>{{$patient->_view}}</strong>
    N�(e) le {{mb_value object=$patient field=naissance}} - ({{$patient->_age}} ans) - ({{$patient->_ref_constantes_medicales->poids}} kg)
    <br />
    {{assign var=operation value=$sejour->_ref_last_operation}}
    Intervention le {{$operation->_ref_plageop->date|date_format:"%d/%m/%Y"}}
    <strong>(I{{if $operation->_compteur_jour >=0}}+{{/if}}{{$operation->_compteur_jour}})</strong><br /><br />
		<strong>{{$operation->libelle}}</strong> 
    <div style="text-align: left">
		{{if !$operation->libelle}}
      {{foreach from=$operation->_ext_codes_ccam item=curr_ext_code}}
        <strong>{{$curr_ext_code->code}}</strong> :
        {{$curr_ext_code->libelleLong}}<br />
        {{/foreach}}
    {{/if}}
		</div>
	</div>
</div>

<div class="footer">
</div>

{{foreach from=$bons key=chapitre item=_bons_by_hour name=foreach_chap}}
	  {{foreach from=$_bons_by_hour key=hour item=_bons_by_cat name=foreach_hour}}
	    <div class="{{if $smarty.foreach.foreach_chap.last && $smarty.foreach.foreach_hour.last}}bodyWithoutPageBreak{{else}}body{{/if}}">
		    <table class="tbl">
				  <tr>
				    <th colspan="2" class="title">{{tr}}CPrescription._chapitres.{{$chapitre}}{{/tr}} - Examens demand�s pour le {{$debut|date_format:$dPconfig.date}} � {{$hour}} h</th>
				  </tr>
				  {{foreach from=$_bons_by_cat item=_bons key=name_cat}}
				  <tr>
				    <th colspan="2" class="text">
				      {{assign var=category value=$categories.$chapitre.$name_cat}}
			        {{$category->nom}}
			        {{if $dPconfig.dPprescription.CCategoryPrescription.show_header && $category->header}}, {{$category->header}}{{/if}}
			        {{if $dPconfig.dPprescription.CCategoryPrescription.show_description && $category->description}}, {{$category->description}}{{/if}}
		        </th>
				  </tr>
			    {{foreach from=$_bons key=line_id item=_bon}}
			      {{assign var=line value=$lines.$line_id}}
			      <tr>
				      <td>
				      	{{$_bon}} {{$line->_unite_prise}} {{$line->_view}}
				      </td>
				      <td style="width: 20%">
				        Prescripteur: {{$line->_ref_praticien->_view}} ({{$line->_ref_praticien->adeli}})
				      </td>
			      </tr>
			    {{/foreach}}
			    {{/foreach}}
		    </table>
	    </div>
	{{/foreach}}
{{/foreach}}

<!-- re-ouverture du tableau -->
<table>
  <tr>
    <td>