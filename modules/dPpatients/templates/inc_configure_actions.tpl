<script type="text/javascript">

var Actions = {
  civilite: function(mode) {
	  if (mode == "repair") {
		  if (!confirm("Etes-vous sur de vouloir r�parer les civilit�s ?")) {
			  return;
			}
		}
	  var url = new Url("dPpatients", "ajax_civilite");
		url.addParam("mode", mode);
		url.requestUpdate("ajax_civilite");
	}
}
</script>

<h2>Actions de maintenances</h2>

<table class="tbl">
	<tr>
	  <th style="width: 50%">Action</th>
	  <th style="width: 50%">Status</th>
	</tr>
	  
	<tr>
	  <td>
			<button class="search" onclick="Actions.civilite('check')">
			  V�rifier les civilit�s
			</button>
			<br />
      <button class="change" onclick="Actions.civilite('repair')">
        Corriger les civilit�s
      </button>
	  </td>
	  <td id="ajax_civilite">
	  </td>
	</tr>

</table>