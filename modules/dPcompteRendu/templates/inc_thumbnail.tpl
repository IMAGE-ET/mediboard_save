<script type='text/javascript'>
	Thumb.nb_thumbs = {{$nbpages}};
</script>

<div id="mess" style="position: fixed; width: 160px; font-size: 12pt; font-weight: bold; display: none; cursor: pointer;">
  <br/><br/>Vignettes obsol�tes : cliquez sur une vignette pour r�actualiser.<br/>
</div>

{{assign var=i value=0}}
{{foreach from=$vignettes item=_vignette}}
  <p style="margin-bottom: 10px;">
	  <a id="thumb_{{$i}}" href="#1" onclick="(new Url).ViewFilePopup('CCompteRendu',{{$compte_rendu_id}},'CFile','{{$file_id}}', {{$i}}); return false;">
	    <img class="thumbnail" src="data:image/jpg;base64,{{$_vignette}}" style="margin-bottom: 0px;"/>
	  </a>
		<br/>
		{{$i+1}} / {{$nbpages}}
	</p>
	{{assign var=i value=$i+1}}
{{/foreach}}