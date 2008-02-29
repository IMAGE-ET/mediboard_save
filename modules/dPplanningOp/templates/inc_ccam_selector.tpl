{{* $Id: $
  * @param $fusion array|codes
  *}}

<tr>
{{foreach from=$fusion item=curr_code name=fusion}}
  <td>
    <strong><span style="float:left">{{$curr_code->code}}</span>
    {{if $curr_code->occ==0}}
    <span style="float:right">Favoris</span>
    {{else}}
    <span style="float:right">{{$curr_code->occ}} acte(s)</span>
    {{/if}}
    </strong><br />
    <small>(
    {{foreach from=$curr_code->activites item=curr_activite}}
      {{foreach from=$curr_activite->phases item=curr_phase}}
        <a href="#" onclick="setClose('{{$curr_code->code}}-{{$curr_activite->numero}}-{{$curr_phase->phase}}', '{{$type}}','{{$curr_code->_default}}' )">{{$curr_activite->numero}}-{{$curr_phase->phase}}</a>
      {{/foreach}}
    {{/foreach}}   
    )</small>
    <br />
    {{$curr_code->libelleLong}}
    <br />
    <button class="tick" type="button" onclick="setClose('{{$curr_code->code}}', '{{$type}}','{{$curr_code->_default}}' )">
      {{tr}}Select{{/tr}}
    </button>
  </td>  
{{if $smarty.foreach.fusion.index % 3 == 2}}
</tr><tr>
{{/if}}
{{foreachelse}}
   <td><em>Aucun code</em></td>
{{/foreach}}
</tr>
