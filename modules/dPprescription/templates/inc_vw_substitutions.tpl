<table class="tbl">
	<tr>
		<th>
			Substitutions pour {{$line->_ref_substitute_for->_view}}
		</th>
	</tr>
	{{foreach from=$line->_ref_substitute_for->_back.substitution item=_subst}}
	  <tr>
	    <td>{{$_subst->_view}}</td>
		</tr>
	{{/foreach}}
	{{if $can_select_equivalent}}
	<tr>
		<th>
			Ajouter une substitution
		</th>
	</tr>
	<tr>
		<td class="button">
			{{include file="../../dPprescription/templates/line/inc_vw_equivalents_selector.tpl"}}
		</td>
	</tr>
	{{/if}}
</table>