<form action="?" name="selection" method="get">
  <input type="hidden" name="m" value="{{$m}}" />
  <script type="text/javascript">
    regRedirectPopupCal("{{$date}}", "?m={{$m}}&tab=vw_bloodSalvage_sspi&date=");
  </script>
  <table class="form">
    <tr>
      <th class="category" colspan="2">
        <div style="float: right;">{{$hour|date_format:"%Hh%M"}}</div>
        {{$date|date_format:"%A %d %B %Y"}}
        <img id="changeDate" src="./images/icons/calendar.gif" title="Choisir la date" alt="calendar" />
      </th>
    </tr>
  </table>
</form>


<table class="tbl">
	<tr>
	  <th>Salle</th>
	  <th>Praticien</th>
	  <th>Patient</th>
	  <th>Entr�e r�veil</th>
	</tr>
	
	{{foreach from=$listReveil item=rspo}}
		<tr class="hoverable">
		  <td class="text">
  		  <a href="?m=bloodSalvage&amp;tab=vw_bloodSalvage_sspi&amp;op={{$rspo->_id}}" title="G�rer le Cell Saver">
  		    {{$rspo->_ref_salle->_view}}
        </a>
		  </td>
		  <td class="text">
  		  <a href="?m=bloodSalvage&amp;tab=vw_bloodSalvage_sspi&amp;op={{$rspo->_id}}" title="G�rer le Cell Saver">
  		    Dr {{$rspo->_ref_chir->_view}}
        </a>
		  </td>
		  <td class="text">
  		  <a href="?m=bloodSalvage&amp;tab=vw_bloodSalvage_sspi&amp;op={{$rspo->_id}}" title="G�rer le Cell Saver">  
  		    {{$rspo->_ref_sejour->_ref_patient->_view}}
        </a>
		  </td>
		  <td class="text">{{$rspo->entree_reveil|date_format:"%H:%M"}}</td>
		</tr>
	{{foreachelse}}
	<tr>
	  <td colspan="4">{{tr}}CBloodSalvage.none{{/tr}}</i></td>
	</tr>
	{{/foreach}}
</table>
