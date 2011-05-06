{{* $Id$ *}}

<script type="text/javascript">

var Action = {
  execute: function (type) {
    if (!confirm("Voulez-vous r�element �xecuter cette action ?")) return;
    
    var url = new Url("forms", "do_batch_action");
    url.addParam("action", type);
    url.requestUpdate("action-" + type);
  }
};

</script>

<div class="small-error">
	Ces actions agissent sur l'ensemble des donn�es des formulaires, elles sont � manipuler avec pr�caution
</div>

<table class="tbl" style="table-layout: fixed;">
  <tr>
    <th class="title" colspan="2">Actions par lot</th>
  </tr>
  
  <tr>
    <td>
      <button class="tick" onclick="Action.execute('bool_defaul_reset')">Remettre les champs et concepts bool�ens en "Ind�fini"</button>
    </td>
    <td id="action-bool_defaul_reset"></td>
  </tr>
  
  <tr>
    <td>
      <button class="tick" onclick="Action.execute('str_to_text')">Passer les champs et concepts de type "Texte court" en "Texte long"</button>
    </td>
    <td id="action-str_to_text"></td>
  </tr>
</table>
