<!-- $Id$ -->

<script>
var consultationRdV = Class.create ({
  plage_id        : null,
  consult_id      : null,
  date            : null,
  heure           : null,
  _chirview       : null,
  chir_id         : null,
  is_cancelled    : 0,

  initialize: function (plage_id, consult_id, date, heure, chir_id, chir_view, todelete) {
    this.plage_id         = plage_id;
    this.consult_id       = consult_id;
    this.date             = date;
    this.heure            = heure;
    this.chir_id          = chir_id;
    this._chirview        = chir_view;
    this.is_cancelled     = todelete;
  }
});


RDVmultiples = {
  is_multiple     : 0,
  slots           : {},
  current_rank    : 0,
  automatic_rank  : 1,
  max_rank        : {{$app->user_prefs.NbConsultMultiple}},

  init: function(consultation_ids, multiple) {

    // selected
    RDVmultiples.is_multiple = multiple;
    if (consultation_ids.length > 0) {
      for (var b=0; b<consultation_ids.length; b++) {
        var plage_id    = consultation_ids[b][0];
        var consult_id  = consultation_ids[b][1];
        var date        = consultation_ids[b][2];
        var time        = consultation_ids[b][3];
        var chir_id     = consultation_ids[b][4];
        var chir_view   = consultation_ids[b][5];
        var annule      = consultation_ids[b][6];
        RDVmultiples.addSlot(RDVmultiples.current_rank, plage_id, consult_id, date, time, chir_id, chir_view, annule);        // insert
        RDVmultiples.loadPlageConsult(plage_id, consult_id, RDVmultiples.is_multiple);  // display
        if (multiple) {
          RDVmultiples.selRank(RDVmultiples.current_rank+1);
        }
      }
    }
    else {
      if (multiple) {
        $('tools_plage_0').addUniqueClassName('selected');
      }
      //show the default page
      var aselected = $$("tr.selected");
      if (aselected.length == 1) {
        aselected[0].select("a").invoke('onclick');
      }
    }
  },

  // add a slot to the list.
  addSlot : function(slot_number, plage_id, consult_id, date, time, chir_id, _chir_view, toTrash) {
    var oldslot = this.slots[slot_number];

    // if consult_id, We keep it
    if (oldslot && oldslot.consult_id && consult_id != oldslot.consult_id) {
      consult_id = oldslot.consult_id;
    }

    // if consult && is_cancelled, we keep the status
    if (oldslot && oldslot.is_cancelled == 1 && !toTrash) {
      toTrash = 1;
    }
    this.slots[slot_number] = new consultationRdV(plage_id, consult_id, date, time, chir_id, _chir_view, toTrash);

    // creation                           plage_id && !consult_id
    // modif de consultation              !plage_id && consult_id
    // modifier la plage de la consult    plage_id && consult_id
    // simple modif                       !plage_id && !consult_id

  },

  resetSlots : function() {
    for (var a = 0; a<this.max_rank; a++ ) {
      RDVmultiples.removeSlot(a, 1);
    }
  },

  //enlever un slot (ne doit pas avoir de consult_id)
  removeSlot : function(rank, reset) {
    var _reset = reset ? 1 : 0;
    var slot = this.slots[rank];

    // si consult_id => annulation du rendez-vous
    if (slot && slot.consult_id) {
      if (!_reset) {
        console.log(slot.is_cancelled);

        if (slot.is_cancelled == 1) {
          slot.is_cancelled = 0;
          $('cancel_plage_'+rank).hide();
          $('discancel_plage_'+rank).show();
        }
        else {
          slot.is_cancelled = 1;
          $('cancel_plage_'+rank).show();
          $('discancel_plage_'+rank).hide();
        }
      }
    }
    // sinon on le supprime + refresh
    else {
      delete this.slots[rank];
      $("listPlaces-"+rank).update("");
    }
  },
  selRank: function(rank) {
    if (rank <= RDVmultiples.max_rank) {
      RDVmultiples.current_rank = rank;
      if (this.is_multiple) {
        $$('.tools_plage').each(function(elt) {
          $(elt).removeClassName('selected');
        });

        var target = $('tools_plage_'+rank);
        if ($(target)) {
          $(target).addUniqueClassName('selected');
        }
      }
    }
  },

  cleanRank: function(rank) {
    RDVmultiples.selRank(rank);
    $('plistPlaces-'+rank).update('');
  },

  refreshSlot : function(rank, plage_id, consult_id, multiple) {
  this.selRank(rank);
  this.loadPlageConsult(plage_id, consult_id, multiple);
  },

  // load the plageconsult to the right
  loadPlageConsult : function(plageconsult_id, consult_id, multiple) {
  var url = new Url("dPcabinet", "httpreq_list_places");
  url.addParam("plageconsult_id", plageconsult_id);
  url.addParam("consult_id"     , consult_id);
  url.addParam("multipleMode", multiple);
  url.addParam("slot_id", this.current_rank);
  url.requestUpdate("listPlaces-"+this.current_rank);
  },

  sendData : function() {
    var consult_list = $H(this.slots);
    if (consult_list.size()) {
      window.parent.PlageConsultSelector.consultations = consult_list;
      window.parent.PlageConsultSelector.updateFromSelector();
    }
    else {
      alert("Selectionner au moins une plage");
    }
  }
};

updatePlage = function(sdate, callback) {
  var form = getForm("Filter");
  if ($V(form.period) == "weekly") {
    form.submit();
  }
  var url = new Url("cabinet", "ajax_list_plages");
  url.addParam("dialog"             , 1);
  url.addParam("chir_id"            , $V(form.chir_id));
  url.addParam("function_id"        , $V(form.function_id));
  url.addParam("plageconsult_id"    , $V(form.plageconsult_id));
  url.addParam("consultation_id"    , $V(form.consultation_id));
  url.addParam("_line_element_id"   , $V(form._line_element_id));
  url.addParam("period"             , $V(form.period));
  url.addParam("multipleMode"       , "{{$multipleMode}}");
  url.addParam("hour"               , $V(form.hour));
  url.addParam("date"               , sdate ? sdate : $V(form.date));
  url.addParam("hide_finished"      , $V(form.hide_finished));
  url.addParam("function_id"       , $V(form._function_id));
  url.requestUpdate('listePlages', callback);
};

Main.add(function () {
  {{* Calendar.regField(getForm("Filter").date, null, {noView: true}); *}}
  updatePlage(null, RDVmultiples.init.curry({{$consultation_ids|@json}}, {{$multipleMode}}));


  $('plage_list_container').on("click", '{{if !$multipleMode}}button.validPlage{{else}}input.validPlage{{/if}}', function(event, element) {
    var consult_id  = element.get("consult_id");
    var plage_id    = element.get("plageid");
    var time        = element.get("time");
    var date        = element.get("date");
    var chir_id     = element.get("chir_id");
    var chir_view   = element.get("chir_name");
    var slot_id     = element.get("slot_id");
    RDVmultiples.addSlot(slot_id, plage_id, consult_id, date, time, chir_id, chir_view);

    //end of treatment
    {{if !$multipleMode}}
      RDVmultiples.sendData();
    {{/if}}
  });
});

</script>

<form name="Filter" action="?" method="get">
  <input type="hidden" name="m" value="dPcabinet" />
  <input type="hidden" name="a" value="plage_selector" />
  <input type="hidden" name="dialog" value="1" />
  <input type="hidden" name="chir_id" value="{{$chir_id}}" />
  <input type="hidden" name="function_id" value="{{$function_id}}" />
  <input type="hidden" name="plageconsult_id" value="{{$plageconsult_id}}" />
  <input type="hidden" name="_line_element_id" value="{{$_line_element_id}}" />
  <input type="hidden" name="consultation_id" value="{{$consultation_id}}"/>
  <table class="form">
    <tr>
      <!-- planning type -->
      <th><label for="period" title="Changer la p�riode de recherche">Planning</label></th>
      <td>
        <select name="period" onchange="updatePlage()">
          {{foreach from=$periods item="_period"}}
          <option value="{{$_period}}" {{if $_period == $period}}selected="selected"{{/if}}>
            {{tr}}Period.{{$_period}}{{/tr}}
          </option>
          {{/foreach}}
        </select>
      </td>

      <!-- date -->
      <td class="button" style="width: 250px;">
        <input type="hidden" name="date" class="date" value="{{$date}}" onchange="$V(getForm('Filter').plageconsult_id, '');" />
      </td>

      <!-- filter -->
      <th><label for="hour" title="Filtrer les plages englobant l'heure choisie">Filtrer les heures</label></th>
      <td>
        <select name="hour" onchange="updatePlage()">
          <option value="">&mdash; Toutes</option>
          {{foreach from=$hours item="_hour"}}
          <option value="{{$_hour}}" {{if $_hour == $hour}} selected="selected" {{/if}}>
            {{$_hour|string_format:"%02d h"}}
          </option>
          {{/foreach}}
        </select>
      </td>

      <!-- hide -->
      <td>
        <label for="hide_finished">Masquer termin�es :</label>
        <input type="radio" name="hide_finished" value="0" onclick="updatePlage()" {{if !$hide_finished}}checked="checked" {{/if}} />
        <label for="hide_finished_0">Non</label>
        <input type="radio" name="hide_finished" value="1" onclick="updatePlage()" {{if $hide_finished == "1"}}checked="checked" {{/if}} />
        <label for="hide_finished_1">Oui</label>
      </td>

      <th>Filtre par fonction</th>
      <td>
        <select name="_function_id" style="width: 15em;" onchange="updatePlage()">
          <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
          {{foreach from=$listFunctions item=_function}}
            <option value="{{$_function->_id}}" class="mediuser" style="border-color: #{{$_function->color}};" {{if $function_id == $_function->_id}}selected="selected" {{/if}}>
              {{$_function->_view}}
            </option>
          {{/foreach}}
        </select>
      </td>

      {{if $multipleMode}}
        <td>
          <button type="button" id="consult_multiple_button_validate" class="button tick" onclick="RDVmultiples.sendData(); Control.Modal.close()">Valider</button>
          <button type="button" class="button cleanup notext" onclick="RDVmultiples.resetSlots()">Vider les plages cr��es</button>
          <button type="button" class="help" onclick="Modal.open('help_consult_multiple');">{{tr}}Help{{/tr}}</button>
        </td>
      {{/if}}
    </tr>
  </table>
</form>

<div id="help_consult_multiple" style="display: none;">
  <button onclick="Control.Modal.close();" class="tick button" style="float:right;">Merci</button>
  <h2>Aide consultations multiple</h2>
  <ul>
    <li>La plage active selectionn�e est de couleur brune, il faut selectionner la plage puis cliquer � gauche dans le selecteur pour changer de plage de consultation</li>
    <li>Toutes vos actions ne seront appliqu�es qu'� l'enregistrement de la page de modification de la consultation</li>
    <li>En mode �dition, l'appuie sur le bouton corbeille va annuler la consultation et non la supprimer</li>
    <li></li>
  </ul>
</div>

<!-- liste des plages -->
<div style="float: left; width: {{if $multipleMode}}28{{else}}50{{/if}}%" id="listePlages"></div>


<!-- liste du contenu des plages -->
{{if $multipleMode}}
  {{math assign=width equation="(72/(b))" b=$app->user_prefs.NbConsultMultiple}}
  {{assign var=nbConsult value=$app->user_prefs.NbConsultMultiple}}
{{else}}
  {{assign var=nbConsult value=1}}
  {{assign var=width value="50"}}
{{/if}}

<div id="plage_list_container">
  {{foreach from=1|range:$nbConsult:-1 item=j}}
    <div id="listPlage_dom_{{$j-1}}" data-slot_number="{{$j-1}}" style="width:{{$width}}%; float:left;">
      {{if $multipleMode}}
        <div id="tools_plage_{{$j-1}}" class="tools_plage" style="text-align: center;">
          <button class="button target" onclick="RDVmultiples.selRank('{{$j-1}}')"> RDV {{$j}}</button>
          <button type="button" class="trash notext" onclick="RDVmultiples.removeSlot('{{$j-1}}')"></button>
          <input type="hidden" name="consult_id" value=""/>
          <div id="cancel_plage_{{$j-1}}" style="display: none;">Ce RDV sera annul�</div>
          <div id="discancel_plage_{{$j-1}}" style="display: none;">Ce RDV ne sera pas annul�</div>
        </div>
      {{/if}}
      <div id="listPlaces-{{$j-1}}" class="listPlace"></div>
    </div>
    <script>
      ViewPort.SetAvlHeight('listPlaces-{{$j-1}}',.95);
    </script>
  {{/foreach}}
</div>

 �
<script>
  ViewPort.SetAvlHeight('listePlages', .95);
</script>
