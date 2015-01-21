{{* $Id$ *}}

{{mb_script module="dPcompteRendu" script="document"}}

<script>
  markAsSelected = function(anchor) {
    if (anchor) {
      $(anchor).up('tr').addUniqueClassName('selected');
    }
  };
</script>

<table class="main">
  <tr>
    <td class="halfPane">
      {{mb_include template="inc_list_patient"}}
    </td>
    {{if !$board}}
    <td class="halfPane" id="vwPatient">
      <div class="small-info">
        Veuillez s�lectionner un patient sur la gauche pour pouvoir le visualiser
      </div>
		</td>
    {{/if}}
  </tr>
</table>