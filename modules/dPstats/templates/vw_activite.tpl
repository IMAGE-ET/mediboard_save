<script type="text/javascript">

function pageMain() {
  regFieldCalendar("Form1", "debutact");
  regFieldCalendar("Form1", "finact");
  regFieldCalendar("Form2", "debutact");
  regFieldCalendar("Form2", "finact");
}

</script>

<table class="main">
  <tr>
    <td>
      <form name="Form1" action="index.php" method="get" onsubmit="return checkForm(this)">
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
          <td colspan="2" class="button"><button class="search" type="submit">Go</button></td>
        </tr>
        <tr>
          <td colspan="2" class="button">
          </td>
        </tr>
      </table>
      </form>
    </td>
    <td>
      <form name="Form2" action="index.php" method="get" onsubmit="return checkForm(this)">
      <input type="hidden" name="m" value="dPstats" />
      <table class="form">
        <tr>
          <th colspan="2" class="category">Autre graph</th>
        </tr>
        <tr>
          <th><label for="debutact" title="Date de d�but">D�but:</label></th>
          <td class="date">
            <div id="Form2_debutact_da">{{$debutact|date_format:"%d/%m/%Y"}}</div>
            <input type="hidden" name="debutact" class="notNull date" value="{{$debutact}}" />
            <img id="Form2_debutact_trigger" src="./images/icons/calendar.gif" alt="calendar" title="Choisir une date de d�but"/>
         </td>
        </tr>
        <tr>
          <th><label for="finact" title="Date de fin">Fin:</label></th>
          <td class="date">
            <div id="Form2_finact_da">{{$finact|date_format:"%d/%m/%Y"}}</div>
            <input type="hidden" name="finact" class="notNull date moreEquals|debutact" value="{{$finact}}" />
            <img id="Form2_finact_trigger" src="./images/icons/calendar.gif" alt="calendar" title="Choisir une date de d�but"/>
          </td>
        </tr>
        <tr>
          <td colspan="2" class="button"><button class="search" type="submit">Go</button></td>
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