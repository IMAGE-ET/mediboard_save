{{if $javascript}}
<script type="text/javascript">
periodicalTimeUpdater.currentlyExecuting = true;
</script>
{{/if}}

{{if $temps}}
  <i>Temps estim� : {{$temps}}</i>
{{else}}
  <i>Temps estim� : -</i>
{{/if}}