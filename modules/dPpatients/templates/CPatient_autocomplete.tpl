<span class="view" data-sexe="{{$match->sexe}}">{{$match->_view}}</span>
<div style="color: #999; font-size: 0.9em;">
  {{$match->adresse|replace:"\n":" &ndash; "}}
  {{if $match->cp || $match->ville}}
    &ndash;
    {{mb_value object=$match field=cp}}
    {{mb_value object=$match field=ville}}
  {{/if}}
</div>
<div style="color: #999; font-size: 0.9em; float: right;">{{mb_value object=$match field=naissance}}</div>
<div class="patientTel" style="color: #999; font-size: 0.9em; float: left;" data-tel="{{$match->tel}}" data-tel2="{{$match->tel2}}">{{mb_value object=$match field=tel}}</div>