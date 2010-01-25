<!-- $Id$ -->

<script type="text/javascript">

{{if $dialog}}
  var aProtocoles = {
    sejour: {},
    interv: {}
  };
  {{foreach from=$protocoles key=key_type item=curr_type}}
    {{foreach from=$curr_type item=curr_protocole}}
    aProtocoles['{{$key_type}}'][{{$curr_protocole->protocole_id}}] = {
      protocole_id     : {{$curr_protocole->protocole_id}},
      chir_id          : {{$curr_protocole->chir_id}},
      codes_ccam       : "{{$curr_protocole->codes_ccam}}",
      DP               : "{{$curr_protocole->DP}}",
      libelle          : "{{$curr_protocole->libelle|smarty:nodefaults|escape:"javascript"}}",
      libelle_sejour   : "{{$curr_protocole->libelle_sejour|smarty:nodefaults|escape:"javascript"}}",
      _hour_op         : "{{$curr_protocole->_hour_op}}",
      _min_op          : "{{$curr_protocole->_min_op}}",
      examen           : "{{$curr_protocole->examen|smarty:nodefaults|escape:"javascript"}}",
      materiel         : "{{$curr_protocole->materiel|smarty:nodefaults|escape:"javascript"}}",
      convalescence    : "{{$curr_protocole->convalescence|smarty:nodefaults|escape:"javascript"}}",
      depassement      : "{{$curr_protocole->depassement}}",
      forfait          : "{{$curr_protocole->forfait}}",
      fournitures      : "{{$curr_protocole->fournitures}}",
      type             : "{{$curr_protocole->type}}",
      duree_hospi      : {{$curr_protocole->duree_hospi}},
      rques_sejour     : "{{$curr_protocole->rques_sejour|smarty:nodefaults|escape:"javascript"}}",
      rques_operation  : "{{$curr_protocole->rques_operation|smarty:nodefaults|escape:"javascript"}}",
      protocole_prescription_anesth_id: "{{$curr_protocole->protocole_prescription_anesth_id}}",
      protocole_prescription_chir_id:   "{{$curr_protocole->protocole_prescription_chir_id}}",
      service_id_sejour : "{{$curr_protocole->service_id_sejour}}"
    };
    {{/foreach}}
  {{/foreach}}
  
  Main.add(function(){
    var urlComponents = Url.parse();
    $(urlComponents.fragment || 'interv').show();
  });
{{/if}}

function setClose(type, protocole_id) {
  window.opener.ProtocoleSelector.set(aProtocoles[type][protocole_id]);
  window.close();
}

</script>

{{if !$dialog}}
<ul id="tabs-protocoles" class="control_tabs">
  <li>
  	<a  href="#interv" {{if !count($protocoles.interv)}}class="empty"{{/if}}>
  		Chirurgicaux <small>({{$protocoles.interv|@count}})</small>
  	</a>
  </li>
  <li>
  	<a href="#sejour" {{if !count($protocoles.sejour)}}class="empty"{{/if}}>
  		M�dicaux <small>({{$protocoles.sejour|@count}})</small>
  	</a>
  </li>
</ul>

<script type="text/javascript">
Main.add(function(){
  // Don't use .create() : why ?? (tom)
  Control.Tabs.create('tabs-protocoles', true);
});
</script>

<hr class="control_tabs" />
{{/if}}

<table class="tbl">
{{foreach from=$protocoles key=key_type item=_type}}
  <tbody id="{{$key_type}}" style="display: none;">
    <tr>
      <th>
        {{if $key_type == 'interv'}}
          Chirurgien &mdash; Actes CCAM
        {{else}}
          Praticien &mdash; Dur�e 
        {{/if}}
        &mdash; Diagnostic principal
      </th>
    </tr>
    {{foreach from=$_type item=_protocole}}
    <tr {{if $protSel->_id == $_protocole->_id && !$dialog}}class="selected"{{/if}}>    
      <td class="text">
        {{if $dialog}}
        <a href="#1" onclick="setClose('{{$key_type}}', {{$_protocole->_id}})">
        {{else}}
        <a href="?m={{$m}}&amp;tab={{$tab}}&amp;protocole_id={{$_protocole->_id}}">
        {{/if}}
          <span onmouseover="ObjectTooltip.createEx(this, '{{$_protocole->_guid}}');">
          <strong>
            {{$_protocole->_ref_chir->_view}}
            {{if $key_type == 'interv'}}
              {{if $_protocole->libelle}}
                - <em>[{{$_protocole->libelle}}]</em>
              {{/if}}
            {{else}}
              {{if $_protocole->libelle_sejour}}
                - <em>[{{$_protocole->libelle_sejour}}]</em>
              {{/if}}
            {{/if}}
          </strong>
          </span>
        </a>
        {{$_protocole->duree_hospi}} nuits en {{mb_value object=$_protocole field=type}}
        <br />
        {{if $_protocole->_ext_code_cim->code}}
          {{$_protocole->_ext_code_cim->code}}
          <em>[{{$_protocole->_ext_code_cim->libelle|truncate:80}}]</em>
          <br />
        {{/if}}
        {{foreach from=$_protocole->_ext_codes_ccam item=_code}}
          {{$_code->code}}
          <em>[{{$_code->libelleLong|truncate:80}}]</em>
          <br />
        {{/foreach}}
      </td>
    </tr>
    {{foreachelse}}
    <tr>
      <td colspan="5">{{tr}}CProtocole.none{{/tr}}</td>
    </tr>
    {{/foreach}}
  </tbody>
{{/foreach}}
</table>
