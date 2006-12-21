<script language="JavaScript" type="text/javascript">

function alertAction() {
  if(confirm("Voulez confirmer votre action ?")) {
    return true;
  }
  return false;
}

function pageMain() {
  {{if $isprat}}
  PairEffect.initGroup("effectCategory");
  {{/if}}
  regFieldCalendar("addPlage", "date");
  regRedirectPopupCal("{{$debut}}", "?m={{$m}}&tab={{$tab}}&debut="); 
}

</script>

<table class="main">
  <tr>
    <th class="title">
      <a href="index.php?m={{$m}}&amp;debut={{$prec}}">&lt;&lt;&lt;</a>
      semaine du {{$debut|date_format:"%A %d %B %Y"}}
      <img id="changeDate" src="./images/icons/calendar.gif" title="Choisir la date" alt="calendar" />
      <a href="index.php?m={{$m}}&amp;debut={{$suiv}}">&gt;&gt;&gt;</a>
    </th>
    <th class="title">Votre compte</th>
  </tr>
  <tr>
    <td>
      <table width="100%">
        <tr>
          <th></th>
          {{foreach from=$plages|smarty:nodefaults key=curr_day item=plagesPerDay}}
          <th>{{$curr_day|date_format:"%A %d"}}</th>
          {{/foreach}}
        </tr>
        {{foreach from=$listHours|smarty:nodefaults item=curr_hour}}
        <tr>
          <th>{{$curr_hour}}h</th>
          {{foreach from=$plages key=curr_day item=plagesPerDay}}
            {{assign var="isNotIn" value=1}}
            {{foreach from=$plagesPerDay item=curr_plage}}
              {{if $curr_plage->_hour_deb == $curr_hour}}
                {{if ($curr_plage->_state == $curr_plage|const:'PAYED') && ($curr_plage->prat_id != $app->user_id)}}
                <td align="center" bgcolor="{{$curr_plage|const:'OUT'}}" rowspan="{{$curr_plage->_hour_fin-$curr_plage->_hour_deb}}">
                {{else}}
                <td style="vertical-align:middle; text-align:center; background-color:{{$curr_plage->_state}}" rowspan="{{$curr_plage->_hour_fin-$curr_plage->_hour_deb}}">
                {{/if}}
                  {{if $curr_plage->prat_id == $app->user_id}}
                  <font style="font-weight: bold; color: #060;">
                  {{/if}}
                  {{if $curr_plage->libelle}}
                    {{$curr_plage->libelle}}
                    <br />
                  {{/if}}
                  {{$curr_plage->tarif}} �
                  <br />
                  {{$curr_plage->debut|date_format:"%H"}}h - {{$curr_plage->fin|date_format:"%H"}}h
                  {{if $curr_plage->prat_id}}
                    <br />
                    Dr. {{$curr_plage->_ref_prat->_view}}
                  {{/if}}
                  {{if $curr_plage->prat_id == $app->user_id}}
                  </font>
                  {{/if}}
                  <br />
                  {{if $isprat && (($curr_plage->_state == $curr_plage|const:'FREE') || (($curr_plage->_state == $curr_plage|const:'BUSY') && ($curr_plage->prat_id == $app->user_id)))}}
                  <form name="editPlage{{$curr_plage->plageressource_id}}" action="?m={{$m}}" method="post" onSubmit=" return alertAction()">
                  <input type='hidden' name='dosql' value='do_plageressource_aed' />
                  <input type='hidden' name='del' value='0' />
                  <input type='hidden' name='plageressource_id' value='{{$curr_plage->plageressource_id}}' />
                    {{if $curr_plage->_state == $curr_plage|const:'FREE'}}
                    <input type='hidden' name='prat_id' value='{{$app->user_id}}' />
                    <button class="tick" type="submit">R�server</button>
                    {{else}}
                    <input type='hidden' name='prat_id' value='' />
                    <button class="cancel" type="submit">Annuler</button>
                    {{/if}}
                  </form>
                  {{/if}}
                </td>
              {{/if}}
              {{if ($curr_plage->_hour_deb <= $curr_hour) && ($curr_plage->_hour_fin > $curr_hour)}}
                {{assign var="isNotIn" value=0}}
              {{/if}}
            {{/foreach}}
            {{if $isNotIn}}
              <td bgcolor="#ffffff"></td>
            {{/if}}
          {{/foreach}}
        </tr>
        {{/foreach}}
      </table>
    </td>
    <td>
      <table class="form">
        {{if $isprat}}
        <tr id="impayes-trigger">
          <th style="background:#ddf">Plages � r�gler</th>
          <td>{{$compte.impayes.total}} ({{$compte.impayes.somme}} �)</td>
        </tr>
        <tbody class="effectCategory" id="impayes">
          {{foreach from=$compte.impayes.plages item=curr_plage}}
          <tr>
            <td colspan="2" class="text">
              <a href="index.php?m={{$m}}&amp;debut={{$curr_plage->date|date_format:"%Y-%m-%d"}}">
              {{$curr_plage->date|date_format:"%A %d %B %Y"}} &mdash;
              {{if $curr_plage->libelle}}
                {{$curr_plage->libelle}} &mdash;
              {{/if}}
              de {{$curr_plage->debut|date_format:"%H"}}h � {{$curr_plage->fin|date_format:"%H"}}h &mdash;
              {{$curr_plage->tarif}} �
              </a>
            </td>
          </tr>
          {{foreachelse}}
          <tr>
            <td colspan="2" class="text">
              <em>Aucun</em>
            </td>
          </tr>
          {{/foreach}}
        </tbody>
        <tr id="inf15-trigger">
          <th style="background:#ddf">Plages r�serv�es et bloqu�es</th>
          <td>{{$compte.inf15.total}} ({{$compte.inf15.somme}} �)</td>
        </tr>
        <tbody class="effectCategory" id="inf15">
          {{foreach from=$compte.inf15.plages item=curr_plage}}
          <tr>
            <td colspan="2" class="text">
              <a href="index.php?m={{$m}}&amp;debut={{$curr_plage->date|date_format:"%Y-%m-%d"}}">
              {{$curr_plage->date|date_format:"%A %d %B %Y"}} &mdash;
              {{if $curr_plage->libelle}}
                {{$curr_plage->libelle}} &mdash;
              {{/if}}
              de {{$curr_plage->debut|date_format:"%H"}}h � {{$curr_plage->fin|date_format:"%H"}}h &mdash;
              {{$curr_plage->tarif}} �
              </a>
            </td>
          </tr>
          {{foreachelse}}
          <tr>
            <td colspan="2" class="text">
              <em>Aucun</em>
            </td>
          </tr>
          {{/foreach}}
        </tbody>
        <tr id="sup15-trigger">
          <th style="background:#ddf">Plages r�serv�es � plus de 15 jours</th>
          <td>{{$compte.sup15.total}} ({{$compte.sup15.somme}} �)</td>
        </tr>
        <tbody class="effectCategory" id="sup15">
        {{foreach from=$compte.sup15.plages item=curr_plage}}
          <tr>
            <td colspan="2" class="text">
              <a href="index.php?m={{$m}}&amp;debut={{$curr_plage->date|date_format:"%Y-%m-%d"}}">
              {{$curr_plage->date|date_format:"%A %d %B %Y"}} &mdash;
              {{if $curr_plage->libelle}}
                {{$curr_plage->libelle}} &mdash;
              {{/if}}
              de {{$curr_plage->debut|date_format:"%H"}}h � {{$curr_plage->fin|date_format:"%H"}}h &mdash;
              {{$curr_plage->tarif}} �
              </a>
            </td>
          </tr>
          {{foreachelse}}
          <tr>
            <td colspan="2" class="text">
              <em>Aucun</em>
            </td>
          </tr>
          {{/foreach}}
        </tbody>
        {{/if}}
        <tr>
          <th colspan="2" class="category">L�gende</th>
        </tr>
        <tr>
          <th style="background:{{$curr_plage|const:'OUT'}}" />
          <td class="text">Plage termin�e</td>
        </tr>
        <tr>
          <th style="background:{{$curr_plage|const:'FREE'}}" />
          <td class="text">Plage libre</td>
        </tr>
        <tr>
          <th style="background:{{$curr_plage|const:'FREEB'}}" />
          <td class="text">Plage libre non r�servable (dans plus d'1 mois)</td>
        </tr>
        <tr>
          <th style="background:{{$curr_plage|const:'BUSY'}}" />
          <td class="text">Plage r�serv�e (ech�ance dans plus de 15 jours)</td>
        </tr>
        <tr>
          <th style="background:{{$curr_plage|const:'BLOCKED'}}" />
          <td class="text">Plage bloqu�e (�ch�ance dans moins de 15 jours)</td>
        </tr>
        <tr>
          <th style="background:{{$curr_plage|const:'PAYED'}}" />
          <td class="text">Plage r�gl�e</td>
        </tr>
        <tr>
          <th style="font-weight: bold; color: #060;">Dr. {{$prat->_view}}</th>
          <td class="text">Plage vous appartenant</td>
        </tr>
      </table>
    </td>
  </tr>
</table>