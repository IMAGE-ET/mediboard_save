<script language="Javascript" type="text/javascript">

function purgeObjects() {
  if (confirm("Merci de confirmer la purge de tous les �l�ments synchronis�s")) {
    var url = new Url;
    url.setModuleAction("dPsante400", "httpreq_purge_objects");
    url.requestUpdate("purgeObjects");
  }
}

</script>

<h2>Purge des donn�es import�s</h2>

<div class="big-warning">
  Attention, cette option permet de purger la base de donn�es de tous les �l�ments 
  synchronis�s dupuis une application tierces, A utiliser avec une extr�me prudence, 
  car <strong>l'op�ration est irr�versible</strong> !
</div>

<table class="tbl">
  <tr>
    <th>Action</th>
    <th>Status</th>
  </tr>
  <tr>
    <td>
      <button class="tick" onclick="purgeObjects()">
        Purger tous les objets syncrhonis�s
      </button>
    </td>
    <td class="text" id="purgeObjects" />
  </tr>

</table>