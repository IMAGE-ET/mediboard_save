{{if $listEtab|@count}}
<select name="etablissement_transfert_id">
<option value="">&mdash; Etab. de transfert</option>
{{foreach from=$listEtab item="etab"}}
<option value="{{$etab->_id}}">{{$etab->_view}}</option>
{{/foreach}}
</select>
{{/if}}