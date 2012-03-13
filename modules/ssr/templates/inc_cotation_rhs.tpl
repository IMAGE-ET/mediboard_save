{{* $Id: vw_aed_rpu.tpl 7951 2010-02-01 10:44:08Z lryo $ *}}

{{*
  * @package Mediboard
  * @subpackage ssr
  * @version $Revision: 7951 $
  * @author SARL OpenXtrem
  * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
  *}}

{{if $bilan->_id && !$bilan->planification}} 
<div class="small-info">
  {{tr}}CBilanSSR-msg-cotation-off{{/tr}}
  <br />
  {{tr}}CBilanSSR-msg-planification-cf{{/tr}}
</div>
{{else}}

<table class="main">
  <tr>
    <td class="narrow">
    
<script type="text/javascript">
Main.add(function() {
  var options = {
    afterChange: function(newContainer) {
	  var rhs_id = newContainer.get("rhs_id");
	  CotationRHS.launchDrawDependancesGraph(rhs_id);
	}
  };
  
  Control.Tabs.create('tabs-rhss', true, options).activeLink.onmouseup();
});
</script>

<ul id="tabs-rhss" class="control_tabs_vertical" style="width: 14em;">
  {{foreach from=$rhss item=_rhs}}
  <li>
    <a href="#cotation-{{if $_rhs->_id}}{{$_rhs->_id}}{{else}}{{$_rhs->date_monday}}{{/if}}" 
      onmouseup="CotationRHS.refreshRHS('{{$_rhs->_id}}');"
      {{if !$_rhs->_id}}class="empty"{{/if}}
      {{if !$_rhs->_in_bounds}}class="wrong"{{/if}}
      >
      {{$_rhs}}
    <br />
    <small>
      du {{mb_value object=$_rhs field=date_monday}}
      au {{mb_value object=$_rhs field=_date_sunday}}
    </small>
    </a>
  </li>
  {{/foreach}}
</ul>

    </td>
    <td>
      
{{foreach from=$rhss item=_rhs}}

{{if $_rhs->_id}}
  <div id="cotation-{{$_rhs->_id}}" style="display: none;" data-rhs_id="{{$_rhs->_id}}">
  </div>
{{else}}
  <div id="cotation-{{$_rhs->date_monday}}" style="display: none;" data-rhs_id="">
  {{mb_include template=inc_create_rhs rhs=$_rhs}}
  </div>
{{/if}}
{{/foreach}}

    </td>
  </tr>
</table>

{{/if}}