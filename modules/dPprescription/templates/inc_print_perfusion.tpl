<li>
	<strong>{{$perf->_view}}</strong>
	<ul>
	  {{foreach from=$perf->_ref_lines item=_line}}
	  <li>
	    {{$_line->_view}}
	    {{if $_line->date_debut}}, � partir du {{$_line->date_debut|date_format:"%d/%m/%Y"}} 
	      {{if $_line->time_debut}}
	        � {{$_line->time_debut}}
	      {{/if}}
	    {{/if}}
	  </li>  
	  {{/foreach}}
	</ul>
</li>