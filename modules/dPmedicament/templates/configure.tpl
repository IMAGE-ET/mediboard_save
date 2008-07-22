<h2>Configuration g�n�rale</h2>

<script type="text/javascript">

var BCBScripts = {
  install: function (sDSN) {
    var url = new Url;
    url.setModuleAction("dPmedicament", "httpreq_do_bcbscripts");
    url.addParam("action", "install");
    url.addElement(document.DoBCBScripts.password);
    url.requestUpdate("BCBSripts");
  },
  
  test: function (sDSN) {
    var url = new Url;
    url.setModuleAction("dPmedicament", "httpreq_do_bcbscripts");
    url.addParam("action", "test");
    url.requestUpdate("BCBSripts");
  }
}

</script>

<table class="tbl">

<tr>
  <th class="title" colspan="100">
    {{tr}}BCBScripts{{/tr}}
  </th>
</tr>

<!-- Install -->
<tr>
  <td>
    <button type="button" class="search" onclick="BCBScripts.test();">
      {{tr}}BCBScripts-test{{/tr}}
    </button>
  </td>
  <td id="BCBSripts" rowspan="2" />
</tr>

<tr>
  <td>
    <form name="DoBCBScripts" action="?" method="get">
      <label for="password">{{tr}}BCBScripts_password{{/tr}}</label>
      <input name="password" type="password" />
    </form>
    
    <button type="button" class="search" onclick="BCBScripts.install()">
      {{tr}}BCBScripts-install{{/tr}}
    </button>
    
  </td>
</tr>

</table>


<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="dosql" value="do_configure" />

<table class="form">

  <!-- Niveau d'affichage des produits pour la recherche dans les classes ATC -->  
  <tr>
    <th class="category" colspan="100">Configuration recherche ATC</th>
  </tr>
  
  <tr>
    {{assign var="class" value="CBcbClasseATC"}}
    {{assign var="var" value="niveauATC"}}
    <th colspan="3">
      <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td colspan="3">
      <select name="{{$m}}[{{$class}}][{{$var}}]">
        <option value="1" {{if 1 == $dPconfig.$m.$class.$var}} selected="selected" {{/if}}>1</option>
        <option value="2" {{if 2 == $dPconfig.$m.$class.$var}} selected="selected" {{/if}}>2</option>
        <option value="3" {{if 3 == $dPconfig.$m.$class.$var}} selected="selected" {{/if}}>3</option>
        <option value="4" {{if 4 == $dPconfig.$m.$class.$var}} selected="selected" {{/if}}>4</option>
        <option value="5" {{if 5 == $dPconfig.$m.$class.$var}} selected="selected" {{/if}}>5</option>
        <option value="6" {{if 6 == $dPconfig.$m.$class.$var}} selected="selected" {{/if}}>6</option>
      </select>
    </td>
  </tr>
    
  <tr>
    <th class="category" colspan="100">Configuration recherche BCB</th>
  </tr>
  
  <tr>
    {{assign var="class" value="CBcbClasseTherapeutique"}}
    {{assign var="var" value="niveauBCB"}}
    <th colspan="3">
      <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td colspan="3">
      <select name="{{$m}}[{{$class}}][{{$var}}]">
        <option value="1" {{if 1 == $dPconfig.$m.$class.$var}} selected="selected" {{/if}}>1</option>
        <option value="2" {{if 2 == $dPconfig.$m.$class.$var}} selected="selected" {{/if}}>2</option>
        <option value="3" {{if 3 == $dPconfig.$m.$class.$var}} selected="selected" {{/if}}>3</option>
        <option value="4" {{if 4 == $dPconfig.$m.$class.$var}} selected="selected" {{/if}}>4</option>
        <option value="5" {{if 5 == $dPconfig.$m.$class.$var}} selected="selected" {{/if}}>5</option>
      </select>
    </td>
  </tr>
  <tr>
    <td class="button" colspan="6">
      <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
    </td>
  </tr>
</table>

</form>

<h2>Base de donn�es BCB</h2>

{{include file="../../system/templates/configure_dsn.tpl" dsn=bcb}}

<script type="text/javascript">

function startBCBGES(){
  var url = new Url;
  url.setModuleAction("dPmedicament", "httpreq_do_add_bcbges");
  url.requestUpdate("do_add_bcbges");
}

function startUncaseBCBTables(){
  var url = new Url;
  url.setModuleAction("dPmedicament", "httpreq_do_uncase_bcb_tables");
  url.requestUpdate("uncase_bcb_tables");
}

function startSyncProducts(category_id){
  if (category_id) {
    var url = new Url;
    url.setModuleAction("dPmedicament", "httpreq_do_sync_products");
    url.addParam("category_id", category_id);
    url.requestUpdate("do_sync_products");
  }
}

function importCSV(){
  var url = new Url;
  url.setModuleAction("dPmedicament", "vw_bcb_import");
  url.pop(400, 400, "Import de fichier CSV");
}

</script>
<table class="tbl">
  <tr>
    <th>Action</th>
    <th>Status</th>
  </tr>
  
  <tr>
    <td><button class="tick" onclick="startUncaseBCBTables()">Mettre les tables BCB en majuscules</button></td>
    <td id="uncase_bcb_tables">
      <div class="big-info">
      	Cette action n'est pas n�cessaire (et ne fonctionnera pas) sur les serveurs MS Windows.
      	<br />
      	Ces derniers ne sont pas sensibles � la casse pour les noms de table 
      </div>
    </td>
  </tr>
</table>

<h2>Base de donn�es BCBGES</h2>

{{include file="../../system/templates/configure_dsn.tpl" dsn=bcbges}}

<table class="tbl">
  <tr>
    <th>Action</th>
    <th>Status</th>
  </tr>
  
  <tr>
    <td><button class="tick" onclick="startBCBGES()" >Importer la base de donn�es BCB GESTION</button></td>
    <td id="do_add_bcbges"></td>
  </tr>
  <tr>
    <td><button class="tick" onclick="importCSV()" >Importer un fichier CSV de livrets th�rapeutiques</button></td>
    <td></td>
  </tr>
  <tr>
    <td>
      <form name="sync-products" action="" onsubmit="return false">
        {{assign var="class" value="CBcbProduitLivretTherapeutique"}}
        {{assign var="var" value="product_category_id"}}
        <select name="{{$m}}[{{$class}}][{{$var}}]">
          <option value="0">{{tr}}CProductCategory.select{{/tr}}</option>
          {{foreach from=$categories_list item=category}}
            <option value="{{$category->_id}}" {{if $category->_id==$dPconfig.$m.$class.$var}}selected="selected"{{/if}}>{{$category->name}}</option>
          {{/foreach}}
        </select>
        <button class="tick" onclick="startSyncProducts($V(this.form['{{$m}}[{{$class}}][{{$var}}]']))" >Synchroniser les produits du stock</button>
      </form>
    </td>
    <td id="do_sync_products"></td>
  </tr>
</table>