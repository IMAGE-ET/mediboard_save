<script type="text/javascript">

periodicalTimeUpdater.currentlyExecuting = true;

</script>

{{if $temps}}
  <i>Temps estim� : {{$temps|date_format:"%Hh%M"}}</i>
{{else}}
  <i>Temps estim� : -</i>
{{/if}}