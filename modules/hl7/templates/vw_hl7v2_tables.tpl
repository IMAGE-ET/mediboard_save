{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}
<script>
  function editTableDescription(table_id, element) {
    if (element)
      element.up('tr').addUniqueClassName('selected');

    new Url("hl7", "ajax_edit_table_description")
      .addParam("table_id", table_id)
      .requestModal(500, 300);
  }
  
  function loadEntries(table_number, element) {
    if (element)
     element.up('tr').addUniqueClassName('selected');

    new Url("hl7", "ajax_refresh_table_entries")
      .addParam("table_number", table_number)
      .requestModal("90%", "90%");
  }

  function loadTables() {
    var url = new Url("hl7", "ajax_refresh_tables");
    url.requestUpdate("tables-hl7v2", Control.Modal.close());
  }
  
  function changePage(page) {
    $V(getForm('listFilter').page,page);
  }

  function refreshModalTableHL7Submit (table_number) {
     new Url("hl7", "ajax_refresh_hl7v2_table")
      .addParam("table_number", table_number)
      .requestUpdate("refreshModalTableHL7v2");
  }

  Main.add(function() {
    getForm('listFilter').onsubmit();
  });
</script>

<table class="main">
  <tr>
    <td colspan="2">
      <button class="new" onclick="editTableDescription(0)"> {{tr}}CHL7v2TableDescription-title-create{{/tr}} </button>
    </td>
  </tr>
  <tr>
    <td>
      <form name="listFilter" action="?" method="get" onsubmit="return onSubmitFormAjax(this, null, 'tables-hl7v2');">
        <input type="hidden" name="m" value="hl7" />
        <input type="hidden" name="a" value="ajax_refresh_tables"/>
        <input type="hidden" name="page" value="{{$page}}" onchange="this.form.onsubmit()" />

        <table class="main layout">
          <tr>
            <td class="separator expand" onclick="MbObject.toggleColumn(this, $(this).next())"></td>

            <td>
              <table class="form">
                <tr>
                  <th style="width: 8%"> Mots clés :</th>
                  <td>
                    <input type="text" name="keywords" value="{{$keywords}}" onchange="$V(this.form.page, 0)" />
                  </td>
                </tr>

                <tr>
                  <td colspan="2">
                    <button type="submit" class="search">{{tr}}Filter{{/tr}}</button>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
</table>

<div id="tables-hl7v2"></div>