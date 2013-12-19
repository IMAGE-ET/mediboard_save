<tr>
  <th>{{mb_title class=CTarif field=description}}</th>
  {{if $conf.ref_pays == "1"}}
    <th class="narrow">{{mb_title class=CTarif field=_has_mto}}</th>
    <th class="narrow">{{mb_title class=CTarif field=secteur1}}</th>
    <th class="narrow">{{mb_title class=CTarif field=secteur2}}</th>
    <th class="narrow">{{mb_title class=CTarif field=secteur3}}</th>
    <th class="narrow">{{mb_title class=CTarif field=_du_tva}}</th>
  {{/if}}
  <th class="narrow">{{mb_title class=CTarif field=_somme}}</th>
</tr>

{{foreach from=$tarifs item=_tarif}}
<tr {{if $_tarif->_id == $tarif->_id}} class="selected"{{/if}}>
  <td {{if $_tarif->_precode_ready}} class="checked"{{/if}}>
    <a href="#"  onclick="Tarif.edit('{{$_tarif->_id}}', '{{$prat->_id}}')">
      {{mb_value object=$_tarif field=description}}
    </a>
  </td>
  {{if $conf.ref_pays == "1"}}
    <td>{{mb_value object=$_tarif field=_has_mto}}</td>
    <td {{if !$_tarif->_secteur1_uptodate}} class="warned"{{/if}} style="text-align: right">
      {{mb_value object=$_tarif field=secteur1}}
    </td>
    <td style="text-align: right">{{mb_value object=$_tarif field=secteur2}}</td>
    <td style="text-align: right">{{mb_value object=$_tarif field=secteur3}}</td>
    <td style="text-align: right">{{mb_value object=$_tarif field=_du_tva}}</td>
  {{/if}}
  <td style="text-align: right"><strong>{{mb_value object=$_tarif field=_somme}}</strong></td>
</tr>
{{foreachelse}}
<tr>
  <td class="empty" colspan="7">{{tr}}CTarif.none{{/tr}}</td>
</tr>
{{/foreach}}
