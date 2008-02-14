<script type="text/javascript">
Main.add(function() {
	Calendar.regField("Filter", "_date_sortie");	
} );
</script>

<!-- Filter -->
<form name="Filter" action="?m={{$m}}" method="get" onsubmit="return checkForm(this)">

<input type="hidden" name="do" value="1" />
<input type="hidden" name="m" value="{{$m}}" />

<table class="form">
  <tr>
    <th class="category" colspan="10">Export d'actes vers le T2A</th>
  </tr>

  <tr>
    <th>{{mb_label object=$filter field=_num_dossier}}</th>
    <td>{{mb_field object=$filter field=_num_dossier}}</td>
  </tr>

  <tr>
    <th>{{mb_label object=$filter field=_date_sortie}}</th>
    <td class="date">{{mb_field object=$filter field=_date_sortie form=Filter}}</td>
  </tr>

  <tr>
    <td class="button" colspan="10">
      <button class="tick" type="submit">
        Exporter les actes
      </button>
    </td>
  </tr>
</table>

</form>

{{if $do}}
{{include file=inc_list_export_actes.tpl}}
{{else}}
<div class="big-info">
  Il est n�cessaire de valider l'export pour le r�aliser.
  Merci de cliquer sur <strong>Exporter les actes</strong> apr�s avoir choisi :
  <dl>
    <dt>soit une <em>date</em></dt>
    <dd>Pour exporter les actes de tous les s�jours ayant une sortie r�elle ce jour.</dd>
    <dt>soit un <em>num�ro de dossier</em></dt>
    <dd>Pour exporter les actes sp�cifiques � un s�jour en particulier.</dd>
  </dl>
</div>
{{/if}}

