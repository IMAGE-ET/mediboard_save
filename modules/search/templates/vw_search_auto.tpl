{{*
 * $Id$
 *  
 * @category Search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

<!--Vue de recherche automatique utilis�e dans le dossier de soins (prescription, pharmacie) et le pmsi-->

{{mb_script module=search script=search}}
<script>
  Main.add(function () {
    var tab = Control.Tabs.create('tabs-favoris', true, {
      afterChange: function (container) {
        switch (container.id) {
          case "tab-General"    :
            var form = getForm("esSearch");
            form.words.focus();
            break;
          default :
            break;
        }
      }
    });
  });
</script>
<table class="main layout" id="table_main">
  <tr>
    <td class="button">
      <button type="button" class="favoris" onclick="Search.manageThesaurus('{{$sejour_id}}', '{{$contexte}}')">G�rer mes favoris
      </button>
    </td>
  </tr>
  <tr>
    <td class="narrow" style="vertical-align: top">
      <ul id="tabs-favoris" class="control_tabs_vertical" style="width: 15em">
        <!--Vue de recherche classique-->
        <li>
          <a href="#tab-General" style="line-height: 1em">{{tr}}CSearch classic search{{/tr}}</a>
        </li>
        {{foreach from=$results key=_search item=_result}}
          <li title="mots recherch�s : {{$_result.entry}}">
            <a href="#tab-{{$_search}}" style="line-height: 1em" class="{{if $_result.nb_results == 0}}empty{{/if}}">
              {{if $_result.titre}}
                <span class="text">{{$_result.titre}} ({{$_result.nb_results}})</span>
              {{else}}
                <span>Sans-titre ({{$_result.nb_results}})</span>
              {{/if}}
            </a>
          </li>
        {{/foreach}}
      </ul>
    </td>
    <td style="vertical-align: top">
      <div id="tab-General" style="display: none;">
        <!--Vue de recherche classique-->
        {{mb_include module=search template=vw_search_manual}}
      </div>
      {{foreach from=$results key=_search item=_result}}
        <div id="tab-{{$_search}}" style="display: none;">
          <table class="tbl">
            <tr>
              <th class="category" colspan="4">R�sultats ({{$_result.nb_results}} obtenus en {{$_result.time}}ms)</th>
            </tr>
            <tr>
              <th class="narrow">Date <br /> Type</th>
              <th colspan="3">Document</th>
            </tr>
            <tr>
              <th class="section" colspan="4">Tri�s par pertinence - limit�s aux 30 premiers r�sultats</th>
            </tr>
            {{if isset($_result.results|smarty:nodefaults)}}
              {{foreach from=$_result.results item=__result}}
                <tr>
                  <td class="text">
                    <span>{{$__result._source.date|substr:0:10|date_format:'%d/%m/%Y'}}</span>

                    <div class="compact">{{tr}}{{$__result._type}}{{/tr}}</div>
                  </td>

                  {{if $__result._type == "CExObject"}}
                    {{assign var=guid value="`$__result._type`_`$__result._source.ex_class_id`-`$__result._source.id`"}}
                  {{else}}
                    {{assign var=guid value="`$__result._type`-`$__result._id`"}}
                  {{/if}}

                  <td class="text" onmouseover="ObjectTooltip.createEx(this, '{{$guid}}')">
                    {{if $__result._source.title != ""}}
                      <span>{{$__result._source.title|utf8_decode}}</span>
                    {{else}}
                      <span> ---- Titre non pr�sent --- </span>
                    {{/if}}
                    {{if isset($__result.highlight|smarty:nodefaults)}}
                      <div class="compact">{{$__result.highlight.body.0|purify|smarty:nodefaults}}</div>
                    {{/if}}
                  </td>
                  {{if $contexte == "pmsi" && "atih"|module_active}}
                    <td class="narrow not-printable">
                      {{if !in_array("`$__result._type`-`$__result._source.id`", $items)}}
                        <button class="add notext"
                                onclick="Search.addItemToRss(null, '{{$sejour_id}}', '{{$__result._type}}', '{{$__result._source.id}}', null)"></button>
                      {{/if}}
                      {{foreach from=$rss_items key=_key item=_item}}
                        {{if $__result._type == $_item->search_class && $__result._source.id == $_item->search_id}}
                          <span id="{{$_item->_guid}}" onmouseover="ObjectTooltip.createEx(this, 'CSearchItem-{{$_item->_id}}')">
                            <i class="fa fa-check fa-2x" style="color:green"></i>
                          </span>
                        {{/if}}
                      {{/foreach}}
                    </td>
                  {{/if}}
                </tr>
                {{foreachelse}}
                <tr>
                  <td colspan="6" class="empty" style="text-align: center">
                    Aucun document ne correspond � la recherche
                  </td>
                </tr>
              {{/foreach}}
            {{else}}
              <tr>
                <td colspan="6" class="empty" style="text-align: center">
                  Aucun r�sultat pour ce favori
                </td>
              </tr>
            {{/if}}
          </table>
        </div>
      {{/foreach}}
    </td>
  </tr>
</table>