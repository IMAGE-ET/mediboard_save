<script type="text/javascript">

function checkIntegrity() {
  var url = new Url;
  url.setModuleAction("dPfiles", "httpreq_check_file_integrity");
  url.requestUpdate("checkIntegrity");
  //url.redirect();  
}

</script>

<h2>Int�grit� de la table de fichiers attach�s</h2>

<button class="search" onclick="checkIntegrity()">
  v�rifier l'int�grit�
</button>

<div id="checkIntegrity" />
