{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage Stock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script>
changePageEndowment = function(start) {
  $V(getForm("filter-endowments").start, start);
};

changeLetterEndowment = function(letter) {
  var form = getForm("filter-endowments");
  $V(form.start, 0, false);
  $V(form.letter, letter);
};

filterEndowments = function(form) {
  var url = new Url("dPstock", "httpreq_vw_endowments_list");
  url.addFormData(form);
  url.requestUpdate("list-endowments");
  return false;
};

loadEndowment = function(endowment_id, endowment_item) {
  if (endowment_item) {
    endowment_id = endowment_item.endowment_id;
  }
  
  var url = new Url("dPstock", "httpreq_vw_endowment_form");
  
  if (!Object.isUndefined(endowment_id))
    url.addParam("endowment_id", endowment_id);
    
  url.requestUpdate("endowment-form");
  return false;
};

Main.add(function(){
  filterEndowments(getForm("filter-endowments"));
  loadEndowment();
});

</script>

<table class="main">
  <tr>
    <td class="halfPane">
      <form name="filter-endowments" action="?" method="get" onsubmit="return filterEndowments(this)">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="start" value="0" onchange="this.form.onsubmit()" />
        <input type="hidden" name="letter" value="{{$letter}}" onchange="this.form.onsubmit()" />
        
        <input type="text" name="keywords" value="{{$keywords}}" />
        
        <button type="submit" class="search notext">{{tr}}Filter{{/tr}}</button>
        <button type="button" class="cancel notext" onclick="$(this.form).clear(false); this.form.onsubmit();"></button>
        
        {{mb_include module=system template=inc_pagination_alpha current=$letter change_page=changeLetterEndowment}}
      </form>

      <div id="list-endowments"></div>
    </td>
    
    <td class="halfPane" id="endowment-form"></td>
  </tr>
</table>