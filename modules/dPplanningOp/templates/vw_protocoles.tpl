<!-- $Id$ -->

<script type="text/javascript">

{{if $dialog}}
var aProtocoles = new Array();
{{foreach from=$protocoles item=curr_protocole}}
aProtocoles[{{$curr_protocole->protocole_id}}] = {
  protocole_id     : {{$curr_protocole->protocole_id}},
  chir_id          : {{$curr_protocole->chir_id}},
  _chir_view       : "{{$curr_protocole->_ref_chir->_view|escape:javascript}}",
  codes_ccam       : "{{$curr_protocole->codes_ccam}}",
  DP               : "{{$curr_protocole->DP}}",
  libelle          : "{{$curr_protocole->libelle|escape:javascript}}",
  _hour_op         : "{{$curr_protocole->_hour_op}}",
  _min_op          : "{{$curr_protocole->_min_op}}",
  examen           : "{{$curr_protocole->examen|escape:javascript}}",
  materiel         : "{{$curr_protocole->materiel|escape:javascript}}",
  convalescence    : "{{$curr_protocole->convalescence|escape:javascript}}",
  depassement      : "{{$curr_protocole->depassement}}",
  type             : "{{$curr_protocole->type}}",
  duree_hospi      : {{$curr_protocole->duree_hospi}},
  rques_sejour     : "{{$curr_protocole->rques_sejour|escape:javascript}}"
  rques_operation  : "{{$curr_protocole->rques_operation|escape:javascript}}"
}
{{/foreach}}

function setClose(protocole_id) {
  window.opener.setProtocole(aProtocoles[protocole_id]);
  window.close();
}
{{/if}}

</script>

<table class="main">
  <tr>
    <td colspan="2">

      <form name="selectFrm" action="index.php" method="get">
      
      <input type="hidden" name="m" value="{$m}" />
      <input type="hidden" {{if $dialog}} name="a" {{else}} name="tab" {{/if}} value="vw_protocoles" />
      <input type="hidden" name="dialog" value="{{$dialog}}" />

      <table class="form">
        <tr>
          <th><label for="chir_id" title="Filtrer les protocoles d'un praticien">Praticien :</label></th>
          <td>
            <select name="chir_id" onchange="this.form.submit()">
              <option value="" >&mdash; Tous les chirurgiens</option>
              {{foreach from=$listPrat item=curr_prat}}
              {{if $curr_prat->_ref_protocoles|@count}}
              <option value="{{$curr_prat->user_id}}" {{if $chir_id == $curr_prat->user_id}} selected="selected" {{/if}}>
                {{$curr_prat->_view}} ({{$curr_prat->_ref_protocoles|@count}})
              </option>
              {{/if}}
              {{/foreach}}
            </select>
          </td>
          <th><label for="code_ccam" title="Filtrer avec un code CCAM">Code CCAM :</label></th>
          <td>
            <select name="code_ccam" onchange="this.form.submit()">
              <option value="" >&mdash; Tous les codes</option>
              {{foreach from=$listCodes key=curr_code item=code_nomber}}
              <option value="{{$curr_code}}" {{if $code_ccam == $curr_code}} selected="selected" {{/if}}>
                {{$curr_code}} ({{$code_nomber}})
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
      </table>
      </form>    
      
    </td>
  </tr>

  <tr>
    {{if $dialog}}
    <td class="greedyPane">
    {{else}}
    <td class="halfPane">
    {{/if}}

      <table class="tbl">
        <tr>
          <th>Chirurgien &mdash; Acte CCAM</th>
        </tr>
        
        {{foreach from=$protocoles item=curr_protocole}}
        <tr>    
          <td class="text">
            {{if $dialog}}
            <a href="javascript:setClose({{$curr_protocole->protocole_id}})">
            {{else}}
            <a href="?m={{$m}}&amp;tab={{$tab}}&amp;protocole_id={{$curr_protocole->protocole_id}}">
            {{/if}}
              <strong>
                {{$curr_protocole->_ref_chir->_view}} 
                {{foreach from=$curr_protocole->_ext_codes_ccam item=curr_code}}
                &mdash; {{$curr_code->code}}
                {{/foreach}}
              </strong>
            </a>
            {{foreach from=$curr_protocole->_ext_codes_ccam item=curr_code}}
            {{$curr_code->libelleLong}} <br />
            {{/foreach}}
          </td>
        </tr>
        {{/foreach}}

      </table>

    </td>
    <td class="halfPane">

      {{if $protSel->protocole_id && !$dialog}}
      <table class="form">
        <tr>
          <th class="category" colspan="2">D�tails du protocole</th>
        </tr>

        <tr>
          <th>Chirurgien :</th>
          <td colspan="3"><strong>{{$protSel->_ref_chir->_view}}</strong></td>
        </tr>

        <tr>
          <th>Acte M�dical :</th>
          <td class="text">
          {{foreach from=$protSel->_ext_codes_ccam item=curr_code}}
            <strong>{{$curr_code->code}}</strong>
            <br />
            {{$curr_code->libelleLong}}
            <br />
          {{/foreach}}
          </td>
        </tr>
        
        <tr>
          <th>Temps op�ratoire :</th>
          <td>{{$protSel->temp_operation|date_format:"%Hh%M"}}</td>
        </tr>

        {{if $protSel->depassement}}
        <tr>	
          <th>D�passement d'honoraire:</th>
          <td>{{$protSel->depassement}}�</td>
		</tr>
		{{/if}}

        {{if $protSel->examen}}
        <tr>
          <th class="text" colspan="2">Bilan Pr�-op</th>
        </tr>
                 
        <tr>
          <td class="text" colspan="2">{{$protSel->examen|nl2br}}</td>
        </tr>
        {{/if}}
        
        {{if $protSel->materiel}}
        <tr>
          <th class="text" colspan="2">Mat�riel � pr�voir</th>
        </tr>
                 
        <tr>
          <td class="text" colspan="2">{{$protSel->materiel|nl2br}}</td>
        </tr>
        {{/if}}
        
        {{if $protSel->convalescence}}
        <tr>
          <th class="text" colspan="2">Convalescence</th>
        </tr>
                 
        <tr>
          <td class="text" colspan="2">{{$protSel->convalescence|nl2br}}</td>
        </tr>
        {{/if}}

        <tr>
          <th class="category" colspan="2">D�tails de l'hospitalisation</th>
        </tr>
        
        <tr>
          <th>Admission en:</th>
          <td>
            {{if $protSel->type == "comp"}} Hospitalisation compl�te{{/if}}
            {{if $protSel->type == "ambu"}} Ambulatoire{{/if}}
            {{if $protSel->type == "exte"}} Externe{{/if}}
          </td>
        </tr>

        <tr>
          <th>Dur�e d'hospitalisation:</th>
          <td>{{$protSel->duree_hospi}} jours</td>
        </tr>
  
        {{if $protSel->rques_sejour}}
        <tr>
          <th class="text" colspan="2">Remarques du s�jour</th>
        </tr>
                 
        <tr>
          <td class="text" colspan="2">{{$protSel->rques_sejour|nl2br}}</td>
        </tr>
        {{/if}}

        {{if $canEdit}}
        <tr>
          <td class="button" colspan="2">
            <form name="modif" action="./index.php" method="get">
            <input type="hidden" name="m" value="{{$m}}" />
            <input type="hidden" name="tab" value="vw_edit_protocole" />
            <input type="hidden" name="protocole_id" value="{{$protSel->protocole_id}}" />
            <input type="submit" value="Modifier" />
            </form>
          </td>
        </tr>
        {{/if}}
      
      </table>
      
      {{/if}} 
     </td>
  </tr>
</table>