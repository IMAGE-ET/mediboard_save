{{*
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage dPcabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

<script type="text/javascript">
  Main.add(function() {
    if ($('planning-plages')) {
      ViewPort.SetAvlHeight("planning-plages", 1);
    }
    $('planningWeek').setStyle({height : '{{$height_calendar}}px' });

    var form = getForm("changeDate");
    if (form) {
      Calendar.regField(getForm("changeDate").debut, null, {noView: true});
    }

    window.save_dates = {
      prev: '{{$prev}}',
      next: '{{$next}}'
    };

    if ($('debut_periode')) {
      $("debut_periode").update("{{$debut|date_format:"%A %d %b %Y"}}");
    }
    if ($('fin_periode')) {
      $("fin_periode").update("{{$fin|date_format:"%A %d %b %Y"}}");
    }
    var button_desistement = $("desistement_count");
    if (button_desistement) {
      button_desistement.writeAttribute("disabled" {{if $count_si_desistement}}, null{{/if}});
      button_desistement.down("span").update("({{$count_si_desistement}})");
    }
    
    $$(".body").each(function(elt) {
      elt.setStyle({backgroundColor: elt.up().getStyle("backgroundColor")});
    });
  });
</script>

<style>
  #CMediusers-{{$chirSel}} table.tbl th.title {
    background-color: #{{$user->_color}};
    color: #{{$user->_font_color}};
  }
</style>

{{assign var=chir_id value=$chirSel}}

{{mb_include module=system template=calendars/vw_week}}

<script>
  Main.add(function() {
    // conges
    {{foreach from=$conges key=k item=_conge}}
      var day = $('CMediusers-{{$chirSel}}').select("th.day-"+{{$k}});
      day = day[0];
      {{if $_conge}}
        day.update(day.innerHTML+" (<em>{{$_conge}})</em>");
      {{/if}}
    {{/foreach}}

    var planning = window['planning-{{$planning->guid}}'];


    planning.onMenuClick = function(action, plageconsult_id, elt) {
      window.action_in_progress = true;
      var consultation_id = elt.get("consultation_id");
      
      if (window.save_elt && window.save_elt != elt) {
        window.save_elt.removeClassName("opacity-50");
      }
      
      window.cut_consult_id = null;
      window.copy_consult_id = null;
        
      if (elt.hasClassName("opacity-50")) {
        elt.removeClassName("opacity-50");
        window.save_elt = null;
      }
      else {
        elt.addClassName("opacity-50");

        if (action == "cut") {
          window.cut_consult_id = consultation_id;
        }
        if (action == "copy") {
          window.copy_consult_id = consultation_id;
        }
        if (action == "add") {
          var plageSel = elt.up(1);
          var date =  plageSel.className.split(" ")[5];
          var hour = plageSel.className.split(" ")[6];
          modalPriseRDV(null, date, hour, plageconsult_id)
        }

        if (action == "tick" || action == "tick_cancel") {
          var oform = getForm('chronoPatient');
          $V(oform.consultation_id, consultation_id);
          $V(oform.chrono, action == "tick" ? 32 : 16);
          $V(oform.arrivee,  action == "tick" ? new Date().toDATETIME(true) : '');
          onSubmitFormAjax(oform, {onComplete: refreshPlanning });
          // clean up
          $V(oform.consultation_id, "");
          $V(oform.chrono, 0);
        }

        window.save_elt = elt;
      }
      
      updateStatusCut();
    };
    
    planning.onEventChange = function(e) {
      window.action_in_progress = true;
      if (!window.save_to) {
        refreshPlanning();
        return;
      }
      var time = e.getTime();
      var hour = time.start.toTIME();
      
      var form = getForm("editConsult");
      var consultation_id = e.draggable_guid.split("-")[1];
      var plageconsult_id = window.save_to.get("plageconsult_id");
      
      $V(form.consultation_id, consultation_id);
      $V(form.plageconsult_id, plageconsult_id);
      $V(form.heure, hour);
      onSubmitFormAjax(form, {onComplete: refreshPlanning });
      window.save_to = null;
    };
    
    $$(".droppable").each(function(elt) {
      Droppables.add(elt, {
      onDrop: function(from, to) {
        window.save_to = to;
      }});
    });
  });
</script>
