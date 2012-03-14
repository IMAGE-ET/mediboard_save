<table class="tbl">
	<tr>
		<th colspan="4" class="title">
			Administrations Per-op�ratoires
		</th>
	</tr>
	<tr>
		<th>Date</th>
		<th>Heure</th>
		<th>Produit</th>
		<th>Quantit�</th>
	</tr>
  {{foreach from=$administrations item=_perop}}
    <tr>
        <td style="text-align: center;">{{mb_ditto name=date value=$_perop->dateTime|date_format:$conf.date}}</td>
        <td style="text-align: center;">{{mb_ditto name=time value=$_perop->dateTime|date_format:$conf.time}}</td>

        {{assign var=unite value=""}}
        {{if $_perop->_ref_object instanceof CPrescriptionLineMedicament || $_perop->_ref_object instanceof CPrescriptionLineMixItem}}
          {{assign var=unite value=$_perop->_ref_object->_ref_produit->libelle_unite_presentation}}
        {{/if}}

        <td>
          {{if $_perop->_ref_object instanceof CPrescriptionLineElement}}
            {{$_perop->_ref_object->_view}}
          {{else}}
            {{$_perop->_ref_object->_ucd_view}}
          {{/if}}
        </td>
				<td>
					<strong>{{$_perop->quantite}} {{$unite}}</strong>
          {{if $_perop->_quantite_mg && $unite != "mg"}}
            soit {{$_perop->_quantite_mg}} mg
          {{/if}}
        </td>
    </tr>
  {{foreachelse}}
	<tr>
		<td class="empty" colspan="4">
			Aucune administration per-op�ratoire
		</td>
	</tr>
	{{/foreach}}
</table>