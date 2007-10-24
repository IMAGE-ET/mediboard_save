<script type="text/javascript">
function setClose(code) {
  window.opener.oCimField.add(code);
  //window.close();
}

function goToCode(code){
  window.location.href = "?m=dPcim10&amp;a=code_finder&amp;dialog=1&amp;code=" + code;
}

</script>

<table class="tbl">
  {{if $up}}
  <tr>
    <th class="category" colspan="2">
      <a href="?m=dPcim10&amp;a=code_finder&amp;dialog=1&amp;code={{$up->code}}">
        <img src="images/icons/uparrow.png" alt="" />
        {{$up->code}}: {{$up->libelle}}
        <img src="images/icons/uparrow.png" alt="" />
      </a>
    </th>
  </tr>
  {{/if}}
  <tr>
    <th class="category" colspan="2">
      {{if !$cim10->_levelsInf|@count}}
        <button class="tick notext" type="button" onclick="setClose('{{$cim10->code}}')">{{tr}}Modify{{/tr}}</button>
      {{/if}}
      <strong>{{$cim10->code}}: {{$cim10->libelle}}</strong>
    </th>
  </tr>
  <tr>  
    <td class="text" style="vertical-align:top;">
      <strong>Informations sur ce code:</strong>
      {{if $cim10->_isInfo}}
      <ul>
        {{if $cim10->descr|@count}}
        <li>
          Description:
          <ul>
            {{foreach from=$cim10->descr|smarty:nodefaults item=curr_descr}}
            <li>{{$curr_descr}}</li>
            {{/foreach}}
          </ul>
        </li>
        {{/if}}
        {{if $cim10->glossaire|@count}}
        <li>
          Glossaire:
          <ul>
            {{foreach from=$cim10->glossaire|smarty:nodefaults item=curr_glossaire}}
            <li>{{$curr_glossaire}}</li>
            {{/foreach}}
          </ul>
        </li>
        {{/if}}
        {{if $cim10->include|@count}}
        <li>
          Inclusions:
          <ul>
            {{foreach from=$cim10->include|smarty:nodefaults item=curr_include}}
            <li>{{$curr_include}}</li>
            {{/foreach}}
          </ul>
        </li>
        {{/if}}
        {{if $cim10->indir|@count}}
        <li>
          Exclusions indirectes:
          <ul>
            {{foreach from=$cim10->indir|smarty:nodefaults item=curr_indir}}
            <li>{{$curr_indir}}</li>
            {{/foreach}}
          </ul>
        </li>
        {{/if}}
        {{if $cim10->notes|@count}}
        <li>
          Notes:
          <ul>
            {{foreach from=$cim10->notes|smarty:nodefaults item=curr_note}}
            <li>{{$curr_note}}</li>
            {{/foreach}}
          </ul>
        </li>
        {{/if}}
      </ul>
      {{else}}
      <ul>
        <li>Pas d'information disponible</li>
      {{/if}}
    </td>
    <td class="text" style="vertical-align:top;">
      {{if $cim10->_levelsInf|@count or $cim10->_exclude|@count}}
      <strong>Codes associ�s:</strong>
      <ul>
      {{if $cim10->_levelsInf|@count}}
      <li>Pr�cisions:
        <ul>
          {{foreach from=$cim10->_levelsInf item=curr_code}}
          <li>
            <button class="tick notext" type="button" onclick="setClose('{{$curr_code->code}}')">{{tr}}Select{{/tr}}</button>
            <a class="action" href="?m=dPcim10&amp;a=code_finder&amp;dialog=1&amp;code={{$curr_code->code}}">
              <button class="down notext" type="button" onclick="goToCode('{{$curr_code->code}}');">{{tr}}More{{/tr}}</button>
              {{$curr_code->code}}: {{$curr_code->libelle}}
            </a>
          </li>
          {{/foreach}}
        </ul>
      </li>
      {{/if}}
      {{if $cim10->_exclude|@count}}
      <li>Exclusions:
        <ul>
          {{foreach from=$cim10->_exclude item=curr_code}}
          <li>
            <button class="tick notext" type="button" onclick="setClose('{{$curr_code->code}}')">{{tr}}Select{{/tr}}</button>
            <a class="action" href="?m=dPcim10&amp;a=code_finder&amp;dialog=1&amp;code={{$curr_code->code}}">
              <button class="down notext" type="button" onclick="goToCode('{{$curr_code->code}}');">{{tr}}More{{/tr}}</button>
              {{$curr_code->code}}: {{$curr_code->libelle}}
            </a>
          </li>
          {{/foreach}}
        </ul>
      </li>
      {{/if}}
      </ul>
      {{else}}
      <strong>Pas de codes associ�s</strong>
      {{/if}}
    </td>
  </tr>
</table>