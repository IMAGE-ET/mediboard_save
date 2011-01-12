{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $ex_classes|@count}}

<script type="text/javascript">
selectExClass = function(element, object_guid, event, _element_id) {
  showExClassForm($V(element), object_guid, element.options[element.options.selectedIndex].innerHTML, null, event, _element_id);
  element.selectedIndex = 0;
}

var _popup = !!Control.Overlay.container;

showExClassForm = function(ex_class_id, object_guid, title, ex_object_id, event, _element_id) {
  var url = new Url("system", "view_ex_object_form");
  url.addParam("ex_class_id", ex_class_id);
  url.addParam("object_guid", object_guid);
  url.addParam("ex_object_id", ex_object_id);
  url.addParam("event", event);
  url.addParam("_element_id", _element_id);

  if (_popup) {
    url.popup(800, 600, title);
  }
  else {
    url.modale({title: title});
    url.modaleObject.observe("afterClose", function(){
      ExObject.register(_element_id, {
        ex_class_id: ex_class_id, 
        object_guid: object_guid, 
        event: event, 
        _element_id: _element_id
      });
    });
  }
}
</script>

<div style="display: inline-block;">
  <select onchange="selectExClass(this, '{{$object->_guid}}', '{{$event}}', '{{$_element_id}}')">
    <option selected="selected" disabled="disabled">
      {{$count_available}} formulaire(s) disponible(s)
    </option>
    
    {{foreach from=$ex_classes item=_ex_class}}
      <option value="{{$_ex_class->_id}}" {{if $_ex_class->_disabled}}disabled="disabled"{{/if}}>{{$_ex_class->name}}</option>
    {{/foreach}}
  </select>
  
  {{else}}
    <em style="color: #999;">Aucun formulaire disponible</em>
  {{/if}}
</div>

<ul id="CExObject.{{$object->_guid}}.{{$event}}">
{{assign var=_events value=$object->_spec->events}}

{{foreach from=$ex_objects item=_ex_objects key=_ex_class_id}}
  {{assign var=_ex_class value=$ex_classes.$_ex_class_id}}
	
	{{if $_events.$event.multiple}}
	  <li>
	    <strong>{{$_ex_class->name}}</strong>
	    <ul>
	    {{foreach from=$_ex_objects item=_ex_object}}
	      <li>
	        <a href="#{{$_ex_object->_guid}}" 
	           onclick="showExClassForm({{$_ex_class_id}}, '{{$object->_guid}}', '{{$_ex_object}}', '{{$_ex_object->_id}}', '{{$event}}', '{{$_element_id}}')">
	          {{mb_value object=$_ex_object->_ref_last_log field=date}} &ndash; 
	          {{mb_value object=$_ex_object->_ref_last_log field=user_id}}
	        </a>
	      </li>
	    {{foreachelse}}
	      <li><em>{{tr}}CExObject.none{{/tr}}</em></li>
	    {{/foreach}}
	    </ul>
	  </li>
	{{else}}
	  {{foreach from=$_ex_objects item=_ex_object}}
    <a href="#{{$_ex_object->_guid}}" title="{{mb_value object=$_ex_object->_ref_last_log field=date}} &ndash; {{mb_value object=$_ex_object->_ref_last_log field=user_id}}"
       onclick="showExClassForm({{$_ex_class_id}}, '{{$object->_guid}}', '{{$_ex_object}}', '{{$_ex_object->_id}}', '{{$event}}', '{{$_element_id}}')">
      {{$_ex_class->name}}
    </a>
		{{/foreach}}
	{{/if}}
{{/foreach}}
</ul>
