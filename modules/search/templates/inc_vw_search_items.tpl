{{*
 * $Id$
 *  
 * @category ${Module}
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @link     http://www.mediboard.org*}}
{{mb_script module=search script=Search ajax=true}}

<table class="tbl" id="list_search_items">
  <tr>
    <th class="category" colspan="4">Liste des search items ajout�s au RSS n�{{$rss->sejour_id}}</th>
  </tr>
  <tr>
    <th class="narrow">
      Document
    </th>
    <th>
      {{mb_label object=$search_item field=rmq}}
    </th>
    <th class="narrow"></th>
  </tr>
  {{foreach from=$search_items item=_search_item}}
    <tr>
      <td class="text">
        <span onmouseover="ObjectTooltip.createEx(this, '{{$_search_item->search_class}}-{{$_search_item->search_id}}')">{{tr}}{{$_search_item->search_class}}{{/tr}}</span>
      </td>
      <td class="text">
        {{mb_value object=$_search_item field=rmq}}
      </td>
      <td class="button">
        <button class="edit notext" onclick="Search.addItemToRss('{{$_search_item->_id}}', null,null,null, null)"></button>
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td class="empty" colspan="2">Pas de search items trouv�s pour le RSS n�{{$rss->sejour_id}}</td>
    </tr>
  {{/foreach}}

</table>