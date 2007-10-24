<script type="text/javascript">

function pageMain() {
  regFieldCalendar("users", "debutlog");
  regFieldCalendar("users", "finlog");
  regFieldCalendar("Form1", "debutact");
  regFieldCalendar("Form1", "finact");
}

</script>

<table class="main">
  <tr>
    <td>
      <form name="users" action="?" method="get" onsubmit="return checkForm(this)">
      <input type="hidden" name="m" value="dPstats" />
      <table class="form">
        <tr>
          <th colspan="4" class="category">Activit� des utilisateurs</th>
        </tr>
        <tr>
          <th><label for="debutlog" title="Date de d�but">D�but:</label></th>
          <td class="date">
            <div id="users_debutlog_da">{{$debutlog|date_format:"%d/%m/%Y"}}</div>
            <input type="hidden" name="debutlog" class="notNull date" value="{{$debutlog}}" />
            <img id="users_debutlog_trigger" src="./images/icons/calendar.gif" alt="calendar" title="Choisir une date de d�but"/>
         </td>
          <th><label for="user_id" title="Utilisateur">Utilisateur</label></th>
          <td>
            <select name="user_id">
            {{foreach from=$listUsers item=curr_user}}
              <option value={{$curr_user->user_id}} {{if $curr_user->user_id == $user_id}}selected="selected"{{/if}}>
                {{$curr_user->_view}}
              </option>
            {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <th><label for="finlog" title="Date de fin">Fin:</label></th>
          <td class="date">
            <div id="users_finlog_da">{{$finlog|date_format:"%d/%m/%Y"}}</div>
            <input type="hidden" name="finlog" class="notNull date moreEquals|debutlog" value="{{$finlog}}" />
            <img id="users_finlog_trigger" src="./images/icons/calendar.gif" alt="calendar" title="Choisir une date de d�but"/>
         </td>
          <td colspan="2" />
        </tr>
        <tr>
          <td colspan="4" class="button"><button class="search" type="submit">Afficher</button></td>
        </tr>
        <tr>
          <td colspan="4" class="button">
            <img alt="Activit� utilisateur" src='?m=dPstats&amp;a=graph_users&amp;suppressHeaders=1&amp;debut={{$debutlog}}&amp;fin={{$finlog}}&amp;user_id={{$user_id}}' />
          </td>
        </tr>
      </table>
      </form>
    </td>
    <td>
      <form name="Form1" action="?" method="get" onsubmit="return checkForm(this)">
      <input type="hidden" name="m" value="dPstats" />
      <table class="form">
        <tr>
          <th colspan="2" class="category">Autre graph</th>
        </tr>
        <tr>
          <th><label for="debutact" title="Date de d�but">D�but:</label></th>
          <td class="date">
            <div id="Form1_debutact_da">{{$debutact|date_format:"%d/%m/%Y"}}</div>
            <input type="hidden" name="debutact" class="notNull date" value="{{$debutact}}" />
            <img id="Form1_debutact_trigger" src="./images/icons/calendar.gif" alt="calendar" title="Choisir une date de d�but"/>
         </td>
        </tr>
        <tr>
          <th><label for="finact" title="Date de fin">Fin:</label></th>
          <td class="date">
            <div id="Form1_finact_da">{{$finact|date_format:"%d/%m/%Y"}}</div>
            <input type="hidden" name="finact" class="notNull date moreEquals|debutact" value="{{$finact}}" />
            <img id="Form1_finact_trigger" src="./images/icons/calendar.gif" alt="calendar" title="Choisir une date de d�but"/>
          </td>
        </tr>
        <tr>
          <td colspan="2" class="button"><button class="search" type="submit">Afficher</button></td>
        </tr>
        <tr>
          <td colspan="2" class="button">
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
</table>