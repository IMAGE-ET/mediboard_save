{{assign var=tbl_class value="main tbl"}}
{{if !@$offline}}
	<script type="text/javascript">
	  Main.add(window.print);
	</script>
	<button class="print not-printable" onclick="window.print()">{{tr}}Print{{/tr}}</button>
	</td>
	</tr>
  </table>

  {{assign var=tbl_class value="print"}}
{{/if}}

<table class="{{$tbl_class}}">
  <tr>
    <th class="title" colspan="10" style="font-size: 16px">
      Dossier d'urgence de <span style="font-size: 20px">{{$patient->_view}}</span> {{mb_include module=dPpatients template=inc_vw_ipp ipp=$patient->_IPP}} <br />
      n�(e) le {{mb_value object=$patient field=naissance}} de sexe {{if $patient->sexe == "m"}} masculin {{else}} f�minin {{/if}} <br /> <hr />
      <span style="font-size: 14px">par le Dr {{$consult->_ref_praticien}} le {{mb_value object=$consult field=_date}} - Dossier {{mb_include module=dPplanningOp template=inc_vw_numdos num_dossier=$sejour->_num_dossier}}</span>
    </th>
  </tr>

  {{mb_include module=dPcabinet template=print_inc_dossier_medical}}

</table>

<hr />

<table class="{{$tbl_class}}">
  <tr>
  	<th width="50%">{{mb_label object=$consult field="motif"}}</th>
    <td>{{mb_value object=$consult field="motif"}}</td>
	</tr>
	
	<tr>
		<th>Param�tres � l'arriv�e</th>
		<td></td>
	</tr>
</table>

{{include file="../../dPpatients/templates/print_constantes.tpl"}}

<table class="{{$tbl_class}}">
  <tr>
    <th>{{mb_label object=$rpu field="motif"}}</th>
    <td>{{mb_value object=$rpu field="motif"}}</td>
  </tr>
  
	{{mb_include module=dPcabinet template=print_inc_constantes}}
</table>

{{if !@$offline}}
<br style="page-break-after: always;" />
{{/if}}

<table class="{{$tbl_class}}">
  <tr><th class="category" colspan="10">Transmissions paramedicales de passage aux urgences</th></tr>
	
	<tr>
    <th>IPP</th>
    <td>{{$patient->_IPP}}</td>
    
    <th></th>
    <td></td>
  </tr>
    
  <tr>
    <th>{{mb_label object=$patient field="nom"}}</th>
    <td>{{mb_value object=$patient field="nom"}}</td>
    
    <th>{{mb_label object=$sejour field="entree_reelle"}} </th>
    <td>{{mb_value object=$sejour field="entree_reelle"}} </td>
  </tr>
  
	<tr>
    <th>{{mb_label object=$patient field="prenom"}}</th>
    <td>{{mb_value object=$patient field="prenom"}}</td>
    
    <th>{{mb_label object=$sejour field="sortie_reelle"}} </th>
    <td>{{mb_value object=$sejour field="sortie_reelle"}} </td>
  </tr>
	
	<tr>
    <th>N�(e) le </th>
    <td>{{mb_value object=$patient field=naissance}} </td>
    
    <th></th>
    <td></td>
  </tr>
	
  <tr>
    <th>{{mb_label object=$patient field="adresse"}}</th>
    <td>{{mb_value object=$patient field="adresse"}}</td>
		
		<th></th>
    <td></td>
  </tr>
  
  <tr>
    <th></th>
    <td>{{mb_value object=$patient field="cp"}} {{mb_value object=$patient field="ville"}}</td>
		
		<th>Personne � pr�venir</th>
    <td>{{mb_value object=$patient field="prevenir_nom"}} {{mb_value object=$patient field="prevenir_prenom"}} <br /> 
		    {{mb_value object=$patient field="tel"}} 
		</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$rpu field="ccmu"}}</th>
    <td>{{mb_value object=$rpu field="ccmu"}}</td>
		
		<th></th>
    <td></td>
  </tr>
  
  
  <tr> 
    <th>{{mb_label object=$rpu field="mode_entree"}}</th>
    <td>{{mb_value object=$rpu field="mode_entree"}}</td>
		
		<th>{{mb_label object=$rpu field="provenance"}}</th>
    <td>{{mb_value object=$rpu field="provenance"}}</td>
  </tr>
  
  <tr>   
    <th>{{mb_label object=$rpu field="transport"}}</th>
    <td>{{mb_value object=$rpu field="transport"}}</td>

    <th>{{mb_label object=$rpu field="pec_transport"}}</th>
    <td>{{mb_value object=$rpu field="pec_transport"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$rpu field="box_id"}}</th>
    <td>{{mb_value object=$rpu field="box_id"}}</td>
		
		<th></th>
    <td></td>
  </tr>
</table>

<hr />

<table class="{{$tbl_class}}">
  <tr>
    <th width="50%">{{mb_label object=$consult field="motif"}}</th>
    <td>{{mb_value object=$consult field="motif"}}</td>
  </tr>
</table>

{{include file="../../dPhospi/templates/inc_list_transmissions.tpl" without_del_form=true}}

<table class="{{$tbl_class}}">
  <tr>
    <th>Documents</th>
    <td>
        {{foreach from=$consult->_ref_documents item=_document}}
          {{$_document->_view}} <br />
        {{/foreach}}
    </td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$sejour field="mode_sortie"}}</th>
    <td>{{mb_value object=$sejour field="mode_sortie"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$rpu field="gemsa"}}</th>
    <td>{{mb_value object=$rpu field="gemsa"}}</td>
  </tr>
  
  <tr><th colspan="2" style="text-align: center; border: 0"><br />Pr�cisions sur la sortie</th></tr>
  
  <tr>
    <th>{{mb_label object=$rpu field="orientation"}}</th>
    <td>{{mb_value object=$rpu field="orientation"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$rpu field="destination"}}</th>
    <td>{{mb_value object=$rpu field="destination"}}</td>
  </tr>
  
</table>

<table class="{{$tbl_class}}">
  <tr><th class="category" colspan="10">Actes</th></tr>
</table>

{{include file="../../dPcabinet/templates/print_actes.tpl" without_del_form=true}}

{{if !@$offline}}
<table>
<tr>
<td>
{{/if}}