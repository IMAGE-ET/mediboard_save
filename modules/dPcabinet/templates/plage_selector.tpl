<!-- $Id$ -->

{{assign var="redirect" value="?m=dPcabinet&a=plage_selector&dialog=1&chir_id=$chir_id&period=$period&hour=$hour&hide_finished=$hide_finished"}}

<script type="text/javascript">

var PlageConsult = {
  currPlage: {{if $plage->_id}}{{$plage->_id}}{{else}}0{{/if}},
  setClose: function(time) {
      alert("Veuillez choisir une plage");
    },
  changePlage: function(plage_id) {
      if(this.currPlage) {
        Element.classNames($("plage-"+this.currPlage)).remove("selected");
      }
      this.currPlage = plage_id;
      Element.classNames($("plage-"+this.currPlage)).add("selected");
      this.refreshPlage();
    },
  refreshPlage: function() {
      var url = new Url;
      url.setModuleAction("dPcabinet", "httpreq_list_places");
      url.addParam("plageconsult_id", this.currPlage);
      url.requestUpdate("listPlaces", { waitingText: null });
  },
  addPlaceBefore: function(plage_id) {
      alert("Veuillez choisir une plage");
  }
}

Main.add(function () {
  regRedirectPopupCal("{{$date}}", "{{$redirect|smarty:nodefaults}}&date=");  
});

</script>

<table class="main">

<tr>
  <td class="category" colspan="2">
    
    <form name="Filter" action="?" method="get">
    
    <input type="hidden" name="m" value="dPcabinet" />
    <input type="hidden" name="a" value="plage_selector" />
    <input type="hidden" name="dialog" value="1" />
    <input type="hidden" name="date" value="{{$date}}" />
    <input type="hidden" name="chir_id" value="{{$chir_id}}" />
    <input type="hidden" name="plageconsult_id" value="{{$plage->_id}}" />

    <table class="form">
      <tr>
        <th><label for="period" title="Changer la p�riode de recherche">Planning</label></th>
        <td>
          <select name="period" onchange="this.form.submit()">
            {{foreach from=$periods item="_period"}}
            <option value="{{$_period}}" {{if $_period == $period}}selected="selected"{{/if}}>
              {{tr}}Period.{{$_period}}{{/tr}}
            </option>
            {{/foreach}}
          </select>
        </td>
        
        <td class="button" style="width: 250px;">
          <a style="float:left" href="{{$redirect}}&amp;date={{$pdate}}">&lt;&lt;&lt;</a>
          <a style="float:right" href="{{$redirect}}&amp;date={{$ndate}}">&gt;&gt;&gt;</a>
          <strong>
            {{if $period == "day"  }}{{$date|date_format:" %A %d %B %Y"}}{{/if}}
            {{if $period == "week" }}{{$date|date_format:" semaine du %d %B %Y"}}{{/if}}
            {{if $period == "month"}}{{$date|date_format:" %B %Y"}}{{/if}}
          </strong>
          <img id="changeDate" src="./images/icons/calendar.gif" title="Choisir la date" alt="calendar" />
        </td>
        
        <th><label for="hour" title="Filtrer les plages englobalt l'heure choisie">Filtrer les heures</label></th>
        <td>
          <select name="hour" onchange="this.form.submit()">
    		<option value="">&mdash; Toutes</option>
            {{foreach from=$hours item="_hour"}}
            <option value="{{$_hour}}" {{if $_hour == $hour}} selected="selected" {{/if}}>
              {{$_hour|string_format:"%02d h"}}
            </option>
            {{/foreach}}
          </select>
        </td>
        
        <td>
          
		  <label for="hide_finished">Masquer termin�es :</label>
		  <input type="radio" name="hide_finished" value="0" onchange="this.form.submit()" {{if $hide_finished == "0"}}checked="checked" {{/if}} />
		  <label for="hide_finished_0">Non</label>
		  <input type="radio" name="hide_finished" value="1" onchange="this.form.submit()" {{if $hide_finished == "1"}}checked="checked" {{/if}} />
		  <label for="hide_finished_1">Oui</label>
  
        </td>
      </tr>

    </table>

    </form>
  </td>
</tr>

<tr>
  <td>
    <table class="tbl">
      <tr>
        <th>Date</th>
        <th>Praticien</th>
        <th>Libelle</th>
        <th>Etat</th>
      </tr>
      {{foreach from=$listPlage item=_plage}}
      {{assign var="pct" value=$_plage->_fill_rate}}
      {{if $pct gt 100}}
      {{assign var="pct" value=100}}
      {{/if}}
      {{if $pct lt 50}}{{assign var="backgroundClass" value="empty"}}
      {{elseif $pct lt 90}}{{assign var="backgroundClass" value="normal"}}
      {{elseif $pct lt 100}}{{assign var="backgroundClass" value="booked"}}
      {{else}}{{assign var="backgroundClass" value="full"}}
      {{/if}} 
      <tr {{if $_plage->_id == $plageconsult_id}}class="selected"{{/if}} id="plage-{{$_plage->_id}}">
        <td>
          <div class="progressBar">
            <div class="bar {{$backgroundClass}}" style="width: {{$pct}}%;"></div>
            <div class="text">
              <a href="#nowhere" onclick="PlageConsult.changePlage({{$_plage->_id}})">
                {{$_plage->date|date_format:"%A %d"}}
              </a>
            </div>
          </div>
        </td>
        <td class="text">
          <div class="mediuser" style="border-color: #{{$_plage->_ref_chir->_ref_function->color}};">
            {{$_plage->_ref_chir->_view}}
          </div>
        </td>
        <td class="text">
          {{$_plage->libelle}}
        </td>
        <td>
          {{$_plage->_affected}} / {{$_plage->_total|string_format:"%.0f"}}
        </td>
      </tr>
      {{/foreach}}
    </table>
  </td>
  <td>
    <div id="listPlaces">
      {{include file="inc_list_places.tpl"}}
    </div>
  </td>
</tr>

</table>
