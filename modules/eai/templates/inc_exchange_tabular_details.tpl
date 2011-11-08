{{*
 * Details Exchange Tabular EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

{{if $exchange->_message === null && $exchange->_acquittement === null}}
  <div class="small-info">{{tr}}{{$exchange->_class}}-purge-desc{{/tr}}</div>
{{else}}
<script type="text/javascript">
  Main.add(function () {
    Control.Tabs.create('tabs-contenu', true);
  });
</script>
<tr>
  <td>
    <ul id="tabs-contenu" class="control_tabs">
      <li> 
        <a href="#msg-message"> 
          {{mb_title object=$exchange field="_message"}} 
          <button class="modify notext" onclick="window.open('?m=eai&a=download_exchange&exchange_guid={{$exchange->_guid}}&dialog=1&suppressHeaders=1&message=1')"></button>
        </a> 
      </li>
      <li> 
        <a href="#ack" {{if !$exchange->_acquittement}}class="empty"{{/if}}>
          {{mb_title object=$exchange field="_acquittement"}}
          <button class="modify notext" onclick="window.open('?m=eai&a=download_exchange&exchange_guid={{$exchange->_guid}}&dialog=1&suppressHeaders=1&ack=1')"></button>
          </a> 
      </li>
    </ul>
    
    <hr class="control_tabs" />
    
    <div id="msg-message" style="display: none;">
      <script>
        Main.add(function(){
          Control.Tabs.create("msg-message-tab");
          var tree = new TreeView("msg-message-tree");
          tree.collapseAll();
        });
      </script>
      
      <h1>{{$msg_segment_group->description}} ({{$msg_segment_group->version}}) <span class="type">{{$msg_segment_group->name}}</span></h1>
      
      <ul class="control_tabs" id="msg-message-tab">
        <li><a href="#msg-message-tree">Arbre</a></li>
        <li><a href="#msg-message-er7">ER7</a></li>
        <li><a href="#msg-message-xml">XML</a></li>
        <li><a href="#msg-message-warnings" class="{{if $exchange->_doc_warnings_msg}}wrong{{else}}empty{{/if}}">Avertissements</a></li>
        <li><a href="#msg-message-errors" class="{{if $exchange->_doc_errors_msg}}wrong{{else}}empty{{/if}}">Erreurs</a></li>
      </ul>
      
      <hr class="control_tabs" />
          
      <ul id="msg-message-tree" style="display: none;" class="hl7-tree">
        {{mb_include module=hl7 template=inc_segment_group_children segment_group=$msg_segment_group}}
      </ul>
      
      <div id="msg-message-er7" style="display: none;">
        {{$msg_segment_group->flatten(true)|smarty:nodefaults}}
      </div>
      
      <div id="msg-message-xml" style="display: none;">
        {{if $msg_segment_group->_xml}}
          {{$msg_segment_group->_xml|smarty:nodefaults}}
        {{/if}}
      </div>
      
      <div id="msg-message-warnings" style="display: none;">
        {{mb_include module=hl7 template=inc_hl7v2_errors errors=$msg_segment_group->errors level=1}}
      </div>
      
      <div id="msg-message-errors" style="display: none;">
        {{mb_include module=hl7 template=inc_hl7v2_errors errors=$msg_segment_group->errors level=2}}
      </div>
    </div>
    
    
    <div id="ack" style="display: none;">
      <script>
        Main.add(function(){
          Control.Tabs.create("ack-message-tab");
          var tree = new TreeView("ack-message-tree");
          tree.collapseAll();
        });
      </script>
      
      <h1>{{$ack_segment_group->description}} ({{$ack_segment_group->version}}) <span class="type">{{$ack_segment_group->name}}</span></h1>
      
      <ul class="control_tabs" id="ack-message-tab">
        <li><a href="#ack-message-tree">Arbre</a></li>
        <li><a href="#ack-message-er7">ER7</a></li>
        <li><a href="#ack-message-xml">XML</a></li>
        <li><a href="#ack-message-warnings" class="{{if $exchange->_doc_warnings_ack}}wrong{{else}}empty{{/if}}">Avertissements</a></li>
        <li><a href="#ack-message-errors" class="{{if $exchange->_doc_errors_ack}}wrong{{else}}empty{{/if}}">Erreurs</a></li>
      </ul>
      
      <hr class="control_tabs" />
          
      <ul id="ack-message-tree" style="display: none;" class="hl7-tree">
        {{mb_include module=hl7 template=inc_segment_group_children segment_group=$ack_segment_group}}
      </ul>
      
      <div id="ack-message-er7" style="display: none;">
        {{$ack_segment_group->flatten(true)|smarty:nodefaults}}
      </div>
      
      <div id="ack-message-xml" style="display: none;">
        {{if $ack_segment_group->_xml}}
          {{$ack_segment_group->_xml|smarty:nodefaults}}
        {{/if}}
      </div>
      
      <div id="ack-message-warnings" style="display: none;">
        {{mb_include module=hl7 template=inc_hl7v2_errors errors=$ack_segment_group->errors level=1}}
      </div>
      
      <div id="ack-message-errors" style="display: none;">
        {{mb_include module=hl7 template=inc_hl7v2_errors errors=$ack_segment_group->errors level=2}}
      </div>
    </div>
  </td>
</tr> 
{{/if}}
      