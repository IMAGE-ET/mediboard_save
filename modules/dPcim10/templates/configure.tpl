{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPcim10
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{include file="../../system/templates/configure_dsn.tpl" dsn=cim10}}

<script type="text/javascript">

function startCIM10() {
  var url = new Url;
  url.setModuleAction("dPcim10", "httpreq_do_add_cim10");
  url.requestUpdate("cim10");
}

function importModifsCim10() {
  var url = new Url;
  url.setModuleAction("dPcim10", "ajax_import_modifs_cim10");

  url.requestUpdate('cim10_import_modifs');
}

function modalImportFavoris() {
  new Url("ccam", "ajax_import_favoris")
  .addParam("nomenclature", "cim10")
  .pop(640, 400);
}

</script>

<table class="form">
  <tr>
    <th class="category">Outils</th>
  </tr>
  <tr>
    <td>
      <button type="button" onclick="modalImportFavoris()" class="hslip">Import CSV de favoris CIM10</button>
    </td>
  </tr>
</table>

<h2>Import de la base de donn�es CIM10</h2>

<table class="tbl">
  <tr>
    <th>{{tr}}Action{{/tr}}</th>
    <th>{{tr}}Status{{/tr}}</th>
  </tr>

  <tr>
    <td><button class="tick" onclick="startCIM10()">Importer la base de donn�es CIM10</button></td>
    <td id="cim10"></td>
  </tr>

  <tr>
    <td><button class="tick" onclick="importModifsCim10()">Importer les modifications de la CIM10</button></td>
    <td id="cim10_import_modifs"></td>
  </tr>
</table>