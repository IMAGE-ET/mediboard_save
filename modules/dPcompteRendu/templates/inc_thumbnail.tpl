<script type="text/javascript">
  Thumb.nb_thumbs = {{$nbpages}};
  Thumb.file_id = {{$file_id}};
</script>

<div id="mess" style="position: fixed; width: 160px; font-size: 12pt; font-weight: bold; display: none; cursor: pointer;">
  <br/><br/>Vignettes obsol�tes : cliquez sur une vignette pour r�actualiser.<br/>
</div>

{{assign var=i value=0}}
{{foreach from=$vignettes item=_vignette}}
  <p style="margin-bottom: 10px;">
	  <a id="thumb_{{$i}}" class="thumb" href="#1" onclick="return false">
	    <img class="thumbnail" src="data:image/jpg;base64,{{$_vignette}}" style="margin-bottom: 0px;" />
	  </a>
		<br/>
		{{$i+1}} / {{$nbpages}}
	</p>
	{{assign var=i value=$i+1}}
{{/foreach}}