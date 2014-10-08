{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script>
  Main.add(function() {
    CUserLog.auto.delay(1);

    Control.Tabs.create("sanitize_tabs");
  });
</script>

<ul class="control_tabs" id="sanitize_tabs">
  <li><a href="#sanitize-removers">{{tr}}sanitize_userlogs-removers{{/tr}}</a></li>
  <li><a href="#sanitize-copies">{{tr}}sanitize_userlogs-copies{{/tr}}</a></li>
  <li><a href="#sanitize-inserts">{{tr}}sanitize_userlogs-inserts{{/tr}}</a></li>
</ul>

<table class="tbl" id="sanitize-removers">
  <tr>
    <th>{{mb_title class=CUserLog field=object_class}}</th>
    <th>{{mb_title class=CUserLog field=type}}</th>
    <th>{{mb_title class=CUserLog field=fields}}</th>
  </tr>

  {{foreach from=$removers item=_remover}}
    <tr>
      <td>{{$_remover.0|default:'-'}}</td>
      <td>{{$_remover.1|default:'-'}}</td>
      <td>{{$_remover.2|default:'-'}}</td>
    </tr>
  {{/foreach}}
</table>

<table class="tbl" id="sanitize-copies" style="display: none;">
  <tr>
    <th>{{mb_title class=CUserLog field=object_class}}</th>
    <th>{{mb_title class=CUserLog field=fields}}</th>
    <th>Champ cible</th>
  </tr>

  {{foreach from=$copies item=_copy}}
    {{assign var=class value=$_copy.0}}
    {{assign var=from  value=$_copy.1}}
    {{assign var=to    value=$_copy.2}}
    <tr>
      <td>{{$class|default:'-'}}</td>
      <td>{{tr}}CUserLog-{{$from}}{{/tr}}</td>
      <td>{{tr}}{{$class}}-{{$to}}{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>

<table class="tbl" id="sanitize-inserts" style="display: none;">
  <tr>
    <th>{{mb_title class=CUserLog field=object_class}}</th>
    <th>{{mb_title class=CUserLog field=type}}</th>
    <th>{{mb_title class=CUserLog field=fields}}</th>
  </tr>

  {{foreach from=$inserts item=_insert}}
    <tr>
      <td>{{$_insert.0|default:'-'}}</td>
      <td>{{$_insert.1|default:'-'}}</td>
      <td>{{$_insert.2|default:'-'}}</td>
    </tr>
  {{/foreach}}
</table>

{{if $log->_id < $offset}}
  <div class="small-info">
    {{tr var1=$log->_id}}sanitize_userlogs-message-finished{{/tr}}
  </div>
{{else}}
  <div class="small-{{$execute|ternary:'warning':'info'}}">
    {{tr var1=$counts.delete var2=$min var3=$max}}sanitize_userlogs-message-foundrows-delete-{{$execute|ternary:'execute':'count'}}{{/tr}}
  </div>

  <div class="small-{{$execute|ternary:'warning':'info'}}">
    {{tr var1=$counts.copy var2=$min var3=$max}}sanitize_userlogs-message-foundrows-copy-{{$execute|ternary:'execute':'count'}}{{/tr}}
  </div>

  <div class="small-{{$execute|ternary:'warning':'info'}}">
    {{tr var1=$counts.insert var2=$min var3=$max}}sanitize_userlogs-message-foundrows-insert-{{$execute|ternary:'execute':'count'}}{{/tr}}
  </div>
{{/if}}

<form name="Sanitize" action="?m={{$m}}" method="get" onsubmit="return CUserLog.sanitize(this);">
<input name="execute" type="hidden" value="" />

<table class="form">
  <tr>
    <td>
      <label for="offset">{{tr}}Offset{{/tr}}</label>
      <input type="text" name="offset" value="{{$offset}}" />
    </td>
    <td>
      <label for="step">{{tr}}Step{{/tr}}</label>
      <input type="text" name="step" value="{{$step}}" />
    </td>
    
    <td>
      <input type="hidden" name="auto" value="{{$auto|ternary:1:0}}" />
      <input type="checkbox" name="_auto" {{if $auto}} checked {{/if}} onclick="$V(this.form.auto, this.checked?1:0)" />
      <label for="_auto">{{tr}}Auto{{/tr}}</label>
    </td>

    <td class="button">
      <button class="search" type="button" onclick="$V(this.form.execute, '0'); this.form.onsubmit();">{{tr}}Count{{/tr}}</button>
      <button class="trash"  type="button" onclick="$V(this.form.execute, '1'); this.form.onsubmit();">{{tr}}Execute{{/tr}}</button>
    </td>
  </tr>
</table>

</form>
