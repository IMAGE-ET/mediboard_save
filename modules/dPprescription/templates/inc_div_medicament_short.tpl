<script type="text/javascript">
// On met � jour les valeurs de praticien_id
Main.add( function(){
  Prescription.refreshTabHeader("div_medicament","{{$prescription->_counts_by_chapitre.med}}");
} );


</script>
{{if $prescription->_ref_lines_med_comments.med || $prescription->_ref_lines_med_comments.comment}}
<table class="tbl">
  {{foreach from=$prescription->_ref_lines_med_comments.med item=curr_line}}
    {{include file="inc_vw_line_pack.tpl" line=$curr_line}}
  {{/foreach}}
  
  {{if $prescription->_ref_lines_med_comments.comment|@count}}
  <tr>
    <th colspan="8">Commentaires</th>
  </tr>
  {{/if}}
  <!-- Parcours des commentaires --> 
  {{foreach from=$prescription->_ref_lines_med_comments.comment item=_line_comment}}
    {{include file="inc_vw_line_pack.tpl" line=$_line_comment}}
  {{/foreach}}
</table> 
{{else}}
  <div class="big-info"> 
     Il n'y a aucun m�dicament dans cette prescription.
  </div>
{{/if}}
