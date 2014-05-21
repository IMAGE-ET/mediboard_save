<ul>
  {{foreach from=$matches item=match}}
    <li id="match-{{$match->_id}}" data-id="{{$match->_id}}">
      <strong class="view">{{$match->_view|emphasize:$keywords}}</strong><br />
      <small>{{$match->cp}} {{$match->ville}} - {{$match->adresse}} - {{$match->disciplines|@truncate:25}}</small>
    </li>
  {{/foreach}}
</ul>