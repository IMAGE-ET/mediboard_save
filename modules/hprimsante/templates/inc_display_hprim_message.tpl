{{*
 * $Id$
 *
 * @category hprimsante
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<script>
  Main.add(function(){
    Control.Tabs.create("message-tab-{{$key}}", true);
    var tree = new TreeView("message-{{$key}}-tree");
    tree.collapseAll();
  });
</script>
<div {{if $key != "input"}} style="display: none;" {{/if}} id="message-{{$key}}">
  <h1>{{$message->description}} ({{$message->version}}) <span class="type">{{$message->name}}</span> [{{$message->getEncoding()}}]</h1>
  
  <ul class="control_tabs" id="message-tab-{{$key}}">
    <li><a href="#message-{{$key}}-tree">{{tr}}tree{{/tr}}</a></li>
    <li><a href="#message-{{$key}}-hpr-input">HPR Input</a></li>
    <li><a href="#message-{{$key}}-hpr-output">HPR Output</a></li>
    <li><a href="#message-{{$key}}-xml">XML</a></li>
    <li><a href="#message-{{$key}}-warnings" {{if $message->_warnings_msg}} class="wrong"{{/if}}>Avertissements</a></li>
    <li><a href="#message-{{$key}}-errors" {{if $message->_errors_msg}} class="wrong" {{/if}}>Erreurs</a></li>
  </ul>
  <hr class="control_tabs" />
   
  <div id="message-{{$key}}-tree" style="display: none;">
    <ul class="hl7-tree">
      {{mb_include module=hprimsante template=inc_segment_group_children segment_group=$message}}
    </ul>
  </div>
  
  <div id="message-{{$key}}-hpr-input" style="display: none;">
    {{$message->highlight($message->data)|smarty:nodefaults}}
  </div>
  
  <div id="message-{{$key}}-hpr-output" style="display: none;">
    {{$message->flatten(true)|smarty:nodefaults}}
  </div>
  
  <div id="message-{{$key}}-xml" style="display: none;">
    {{$message->_xml|smarty:nodefaults}}
  </div>
  
  <div id="message-{{$key}}-warnings" style="display: none;">
    {{mb_include module=hprimsante template=inc_hprim_errors errors=$message->errors level=1}}
  </div>
  
  <div id="message-{{$key}}-errors" style="display: none;">
    {{mb_include module=hprimsante template=inc_hprim_errors errors=$message->errors level=2}}
  </div>
</div>
