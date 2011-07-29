{{* $Id$
  * Manipulation des documents d'un s�jour et des interventions associ�es
  * @param $sejour CSejour
  *}}

<h1>{{$sejour->_view}}</h1>

<div id="Documents-{{$sejour->_guid}}" style="float: left; width: 50%;">
	<script type="text/javascript">
	Document.register('{{$sejour->_id}}','{{$sejour->_class}}','{{$sejour->praticien_id}}', 'Documents-{{$sejour->_guid}}', 'normal');
	</script>
</div>

<div id="Files-{{$sejour->_guid}}" style="float: left; width: 50%;">
<script type="text/javascript">
  File.register('{{$sejour->_id}}','{{$sejour->_class}}', "Files-{{$sejour->_guid}}");
</script>
</div>

{{foreach from=$sejour->_ref_operations item=operation}}
<h2 style="clear: both;">{{tr}}COperation{{/tr}} du {{$operation->_datetime|date_format:$conf.date}}</h2>
<div id="Documents-{{$operation->_guid}}" style="float: left; width: 50%;">
	<script type="text/javascript">
	Document.register('{{$operation->_id}}','{{$operation->_class}}','{{$operation->chir_id}}', 'Documents-{{$operation->_guid}}', 'normal');
	</script>
</div>
<div id="Files-{{$operation->_guid}}" style="float: left; width: 50%;">
<script type="text/javascript">
  File.register('{{$operation->_id}}','{{$operation->_class}}', "Files-{{$operation->_guid}}");
</script>
</div>

{{/foreach}}
