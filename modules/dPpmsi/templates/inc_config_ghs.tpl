<h2>Cr�ation et remplissage des la base des GHS / GHM</h2>

{{include file="../../system/templates/configure_dsn.tpl" dsn=GHS1010}}

<script type="text/javascript">

var aActionTypes = ["extractFiles", "AddCM", "AddDiagCM", "AddActes", "AddGHM", "AddCMA", "AddIncomp", "AddArbre"];

function doGHSAction(sType, bFollowed) {
  var nType = aActionTypes.indexOf(sType);
  if (nType == null) {
    return;
  }

  var sNextType = aActionTypes[++nType];
  var oOptions = {
    onComplete : function () {
      doGHSAction(sNextType, true);
    }
  };
 
  if (!bFollowed) {
    oOptions.onComplete = null;
  }
  
  var url = new Url("dPpmsi", "httpreq_do_ghs_action");
  url.addParam("type", sType);
  url.requestUpdate(sType, oOptions);
}

</script>

<table class="tbl">
  <tr>
    <th class="category" onclick="doGHSAction('extractFiles', true);"><button class="tick">Go</button></th>
    <th class="category">{{tr}}Action{{/tr}}</th>
    <th class="category">R�sultat</th>
  </tr>
  <tr>
    <td onclick="doGHSAction('extractFiles');"><button class="tick">Go</button></td>
    <td>Extraction des fichiers</td>
    <td class="text" id="extractFiles"></td>
  </tr>
  <tr>
    <td onclick="doGHSAction('AddCM');"><button class="tick">Go</button></td>
    <td>Remplissage des Cat�gories Majeures</td>
    <td class="text" id="AddCM"></td>
  </tr>
  <tr>
    <td onclick="doGHSAction('AddDiagCM');"><button class="tick">Go</button></td>
    <td>Ajout des diagnostics d'entr�e dans les CM</td>
    <td class="text" id="AddDiagCM"></td>
  </tr>
  <tr>
    <td onclick="doGHSAction('AddActes');"><button class="tick">Go</button></td>
    <td>Ajout des actes/diagnostics dans les listes</td>
    <td class="text" id="AddActes"></td>
  </tr>
  <tr>
    <td onclick="doGHSAction('AddGHM');"><button class="tick">Go</button></td>
    <td>Ajout des Groupements Homog�nes de Malades</td>
    <td class="text" id="AddGHM"></td>
  </tr>
  <tr>
    <td onclick="doGHSAction('AddCMA');"><button class="tick">Go</button></td>
    <td>Ajout des Complications ou Morbidit�s Associ�es</td>
    <td class="text" id="AddCMA"></td>
  </tr>
  <tr>
    <td onclick="doGHSAction('AddIncomp');"><button class="tick">Go</button></td>
    <td>Ajout des incompatibilit�s DP - CMA</td>
    <td class="text" id="AddIncomp"></td>
  </tr>
  <tr>
    <td onclick="doGHSAction('AddArbre');"><button class="tick">Go</button></td>
    <td>Ajout de l'arbre de d�cision</td>
    <td class="text" id="AddArbre"></td>
  </tr>
</table>