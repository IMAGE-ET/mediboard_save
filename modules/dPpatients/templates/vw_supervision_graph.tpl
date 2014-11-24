{{mb_script module=patients script=supervision_graph}}
{{mb_script module=mediusers script=color_selector}}
{{mb_script module=files script=file}}

<script type="text/javascript">
Main.add(function(){
  SupervisionGraph.list(SupervisionGraph.editGraph.curry({{$supervision_graph_id}}));
});
</script>

<table class="main layout">
  <tr>
    <td style="width: 350px;" id="supervision-list"></td>
    <td id="supervision-graph-editor">&nbsp;</td>
  </tr>
</table>