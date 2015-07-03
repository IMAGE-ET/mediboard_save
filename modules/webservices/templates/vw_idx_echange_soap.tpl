{{* $Id: vw_idx_echange_hprim.tpl 6287 2009-05-13 15:37:54Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage webservices
 * @version $Revision: 6287 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module="webservices" script="echange_soap"}}

<script>
  Main.add(function() {
    EchangeSOAP.fillSelect($V(getForm('listFilter').service), 'web_service');

    getForm('listFilter').onsubmit();
  });
</script>

<div id="empty_area" style="display: none;"></div>

<table class="main">
  <!-- Filtres -->
  <tr>
    <td style="text-align: center;">

      <form name="listFilter" action="?" method="get"
            onsubmit="return onSubmitFormAjax(this, null, 'list_echanges_soap')">
        <input type="hidden" name="m" value="webservices" />
        <input type="hidden" name="a" value="ajax_search_echanges_soap" />
        <input type="hidden" name="page" value="{{$page}}" onchange="this.form.onsubmit()"/>

        <table class="main layout">
          <tr>
            <td class="separator expand" onclick="MbObject.toggleColumn(this, $(this).next())"></td>

            <td>
              <table class="form">
                <tr>
                  <th>{{mb_label object=$echange_soap field=date_echange}}</th>
                  <td class="text">
                    {{mb_field object=$echange_soap field=_date_min register=true form="listFilter" prop=dateTime onchange="\$V(this.form.elements.start, 0)"}}
                    <b>&raquo;</b>
                    {{mb_field object=$echange_soap field=_date_max register=true form="listFilter" prop=dateTime onchange="\$V(this.form.elements.start, 0)"}}
                  </td>

                  <th>{{mb_label object=$echange_soap field="echange_soap_id"}}</th>
                  <td>{{mb_field object=$echange_soap field="echange_soap_id"}}</td>

                  <th></th>
                  <td></td>
                </tr>

                <tr>
                  <th>{{mb_label object=$echange_soap field="type"}}</th>
                  <td>
                    <select class="str" name="service"
                            onchange="EchangeSOAP.fillSelect(this.value, 'web_service')">
                      <option value="">&mdash; Liste des types de services</option>
                      {{foreach from=$services item=_service}}
                        <option value="{{$_service}}" {{if $service == $_service}} selected="selected"{{/if}}>
                          {{$_service}}
                        </option>
                      {{/foreach}}
                    </select>
                  </td>

                  <th>{{mb_label object=$echange_soap field="function_name"}}</th>
                  <td>
                    <select class="str" id = "web_service" name="web_service"
                            onchange="EchangeSOAP.fillSelect($V(getForm('listFilter').service), 'fonction')">
                      <option value="">&mdash; Liste des web services</option>
                    </select>
                  </td>

                  <th>{{mb_label object=$echange_soap field="web_service_name"}}</th>
                  <td>
                    <select class="str" name="fonction" id="fonction">
                      <option value="">&mdash; Liste des fonctions</option>
                    </select>
                  </td>
                </tr>

                <tr>
                  <td colspan="6">
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

<div id="list_echanges_soap" style="overflow: hidden"></div>
