<ul>
  {{foreach from=$consult_anesth->_ref_techniques item=curr_tech}}
  <li>
    <form name="delTechFrm-{{$curr_tech->_id}}" action="?m=dPcabinet" method="post" onsubmit="return submitTech(this)">
	    <input type="hidden" name="m" value="dPcabinet" />
	    <input type="hidden" name="del" value="1" />
	    <input type="hidden" name="dosql" value="do_technique_aed" />
	    {{mb_key object=$curr_tech}}
			
	    <button class="trash notext" type="submit">{{tr}}Delete{{/tr}}</button>
	    {{$curr_tech->technique}}
    </form>
  </li>
  {{foreachelse}}
  <li class="empty">Pas de technique compl�mentaire</li>
  {{/foreach}}
</ul>