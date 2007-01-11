<script type="text/javascript">

var aActionTypes = ["checkTables", "purgeEmptyTables", "groupSiblingTables", "importData"];

function doAction(sType, bFollowed) {
  var nType = aActionTypes.indexOf(sType);
  if (nType == null) {
    return;
  }

  var sNextType = aActionTypes[++nType];
  var oOptions = {
    onComplete : function () {
      doAction(sNextType, true);
    }
  };
 
  if (!bFollowed) {
    oOptions.onComplete = null;
  }
  
  var url = new Url;
  url.setModuleAction("dPinterop", sType);
  url.addParam("u", "import/ami");
  url.requestUpdate(sType, oOptions);
}

</script>

<table class="main">
  <tr>
    <td>

      <h2>Import des donn�es depuis une base AMI</h2>

      <table class="tbl">
        <tr>
          <th class="category" onclick="doAction('checkTables', true);">
            <button class="tick">Go</button>
          </th>
          <th class="category">Action</th>
          <th class="category">R�sultat</th>
        </tr>

        <tr>
          <td onclick="doAction('checkTables');">
            <button class="tick">Go</button>
          </td>
          <td>Int�grit� du transfert de base</td>
          <td class="text" id="checkTables"></td>
        </tr>

        <tr>
          <td onclick="doAction('purgeEmptyTables');">
            <button class="tick">Go</button>
          </td>
          <td>Suppression des tables vides</td>
          <td class="text" id="purgeEmptyTables"></td>
        </tr>
        
        <tr>
          <td onclick="doAction('groupSiblingTables');">
            <button class="tick">Go</button>
          </td>
          <td>Regroupement des tables soeurs</td>
          <td class="text" id="groupSiblingTables"></td>
        </tr>
        
        <tr>
          <td onclick="doAction('importData');">
            <button class="tick">Go</button>
          </td>
          <td>Import des donnn�es</td>
          <td class="text" id="importData"></td>
        </tr>
      </table>
      
    </td>
  </tr>
</table>