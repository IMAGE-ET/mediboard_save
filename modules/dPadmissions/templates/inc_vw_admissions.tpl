<script type="text/javascript">

regRedirectPopupCal("{{$date}}", "index.php?m={{$m}}&tab=vw_idx_admission&date=");

</script>

<table class="tbl">
  <tr>
    <th colspan="9">
      <a href="index.php?m=dPadmissions&tab=vw_idx_admission&date={{$hier}}"><<<</a>
      {{$date|date_format:"%A %d %B %Y"}}
      <img id="changeDate" src="./images/icons/calendar.gif" title="Choisir la date" alt="calendar" />
      <a href="index.php?m=dPadmissions&tab=vw_idx_admission&date={{$demain}}">>>></a>
      <br /> 
      <em>
      {{if $selAdmis == "n"}}Admissions non effectu�es
      {{elseif $selSaisis == "n"}}Dossiers non pr�par�s
      {{else}}Toutes les admissions
      {{/if}}
      {{if $selTri == "nom"}}tri�es par nom
      {{elseif $selTri == "heure"}}tri�es par heure
      {{/if}}
      </em>
    </th>
  </tr>
  <tr>
    <th><a href="index.php?m={{$m}}&amp;tab=vw_idx_admission&amp;selAdmis=0&amp;selTri=nom">Nom</a></th>
    <th>Chirurgien</th>
    <th><a href="index.php?m={{$m}}&amp;tab=vw_idx_admission&amp;selAdmis=0&amp;selTri=heure">Heure</a></th>
    <th>Chambre</th>
    <th>Admis</th>
    <th>
      <form name="editAllAdmFrm" action="index.php" method="post">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="dosql" value="do_edit_admis" />
      <input type="hidden" name="id" value="{{$date}}" />
      <input type="hidden" name="mode" value="allsaisie" />
      <input type="hidden" name="value" value="1" />
      <button class="tick" type="submit">
        {{tr}}CSejour-saisi_SHS-tous{{/tr}}
      </button>
      </form>
    </th>
    <th>Anesth</th>
    <th>CMU</th>
    <th>DH</th>
  </tr>
  {{foreach from=$today item=curr_adm}}
  <tr id="admission{{$curr_adm->sejour_id}}">
  {{include file="inc_vw_admission_line.tpl"}}
  </tr>
  {{/foreach}}
</table>