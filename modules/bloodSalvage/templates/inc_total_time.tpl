<table class="form">
  <tr>
	<td style ="text-align:center;">
	<b>Dur�e totale RSPO : </b>
	<input name="total" size="3" maxLength="5" type="text" value="{{$totaltime|date_format:"%H:%M"}}" />
	</td>
	</tr>
</table>
{{if $totaltime|date_format:"%H:%M:%S" > "05:00:00" && $totaltime|date_format:"%H:%M:%S" < "06:00:00" }} 
  <div class="big-warning" style="text-align:center;">
    Limite l�gale de 6H bient�t atteinte ! <br /><br />
    <b>Temps restant : {{$timeleft|date_format:"%H h %M min"}}</b>
  </div>
{{/if}}
{{if $totaltime|date_format:"%H:%M:%S" > "06:00:00"}} 
  <div class="big-warning" style="text-align:center;">
    Limite l�gale de 6H d�pass�e ! <br /><br />
  </div>
{{/if}}
