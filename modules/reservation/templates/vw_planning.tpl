{{*
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage reservation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}
 
<script type="text/javascript">
  Main.add(function() {
    var form = getForm("filterPlanning");
    window.calendar_planning = Calendar.regField(form.date_planning);
    refreshPlanning();
  });
  
  refreshPlanning = function() {
    var form = getForm("filterPlanning");
    var url = new Url("reservation", "ajax_vw_planning");
    url.addParam("date_planning", $V(form.date_planning));
    url.addParam("praticien_id" , $V(form.praticien_id));
    url.addParam("bloc_id"      , $V(form.bloc_id));
    url.addParam("show_cancelled", form.show_cancelled.checked ? 1 : 0);
    url.addParam("current_m"    , "{{$current_m}}");
    var week_container = $$(".week-container")[0];
    
    if (week_container) {
      url.addParam("scroll_top", week_container.scrollTop);
    }
    url.requestUpdate("planning");
  }
  
  modifIntervention = function(date, hour, salle_id, operation_id, enplage) {
    if (enplage) {
      var url = new Url("dPplanningOp", "vw_edit_planning");
    }
    else {
      var url = new Url("dPplanningOp", "vw_edit_urgence");
      url.addParam("date_urgence", date);
      url.addParam("hour_urgence", hour);
      url.addParam("salle_id"    , salle_id);
      url.addParam("min_urgence" , "00");
    }

    url.addParam("operation_id", operation_id);
    url.addParam("dialog", 1);
    url.modal({width: 1000, height: 700});

    url.modalObject.observe("afterClose", function() {
      refreshPlanning();
    });
  }
  
  pasteIntervention = function(operation_id, salle_id, heure, sejour_id, duree) {
    var heure = heure+":00:00";
    var date = window.calendar_planning.altElement.defaultValue;
    var datetime_interv = date + " " + heure;
    
    // Mode copier
    // Ouverture de modale pour modifier �ventuellement les dates du s�jour
    if (sejour_id) {
      window.save_copy_operation =
      { "operation_id": operation_id,
        "date": date,
        "duree": duree,
        "time_operation": heure,
        "salle_id": salle_id,
      };
      
      // Cr�ation d'un nouveau s�jour si la date d'intervention ne colle pas
      if (datetime_interv < window.save_entree_prevue || datetime_interv > window.save_sortie_prevue) {
        modifSejour(null, datetime_interv, sejour_id, null, null, true, "afterCopy");
      }
      // Sinon, on passe le sejour_id pour une modification
      else {
        modifSejour(null, datetime_interv, sejour_id, window.save_entree_prevue, window.save_sortie_prevue, false, "afterCopy");
      }
      return;
    }
    
    // Mode couper
    if (datetime_interv < window.save_entree_prevue || datetime_interv > window.save_sortie_prevue) {
      window.save_cut_operation =
      { "operation_id": operation_id,
        "date": date,
        "time_operation": heure,
        "salle_id": salle_id
      };
      
      modifSejour(operation_id, datetime_interv, null, null, null, null, "afterModifSejour");
      return;
    }
    
    var form = getForm("cutOperation");
    
    $V(form.operation_id  , operation_id);
    $V(form.date          , date);
    $V(form.salle_id      , salle_id);
    $V(form.time_operation, heure);
    
    onSubmitFormAjax(form, {onComplete: function() {
      window.cut_operation_id = null;
      updateStatusCut();
      refreshPlanning();
    } });
  }
  
  updateStatusCut = function() {
    var div = $("status_cut");
    if (window.cut_operation_id) {
      div.update("Couper en cours");
      div.setStyle({borderColor: "#080"});
    }
    else if (window.copy_operation_id) {
      div.update("Copier en cours");
      div.setStyle({borderColor: "#080"});
    }
    else {
      div.update();
      div.setStyle({borderColor: "#ddd"});
      if (window.save_elem) {
        window.save_elem.removeClassName("opacity-50");
      }
    }
  }
  
  modifSejour = function(operation_id, date_move, sejour_id, entree_prevue, sortie_prevue, new_sejour, callback) {
    var url = new Url("dPplanningOp", "ajax_edit_dates_sejour");
    if (sejour_id) {
      url.addParam("sejour_id", sejour_id);
      url.addParam("new_sejour", new_sejour ? 1 : 0);
      if (new_sejour) {
        url.addParam("hour_intervention", window.save_copy_operation.time_operation);
        url.addParam("duree", window.save_copy_operation.duree);
      }
    }
    else {
      url.addParam("operation_id", operation_id);
    }
    
    url.addParam("date_move", date_move);
    if (callback) {
      url.addParam("callback", callback);
    }
    if (entree_prevue && sortie_prevue) {
      url.addParam("entree_prevue", entree_prevue);
      url.addParam("sortie_prevue", sortie_prevue);
    }
    url.requestModal(300);
    url.modalObject.observe("afterClose", refreshPlanning);
  }
  
  afterModifSejour = function() {
    // Apr�s un drag and drop
    if (window.save_operation) {
      var form = getForm("editOperation");
      $V(form.operation_id  , window.save_operation.operation_id);
      $V(form.time_operation, window.save_operation.time_operation);
      $V(form.temp_operation, window.save_operation.temp_operation);
      $V(form.salle_id      , window.save_operation.salle_id);
      
      onSubmitFormAjax(form, {onComplete: function() {
        getForm("editSejour").onsubmit = "";
        window.cut_operation_id = null;
        updateStatusCut();
        onSubmitFormAjax(getForm("editSejour"), {onComplete: Control.Modal.close});
        }
      });
      window.save_cut_operation = null;
      return;
    }
    
    
    // Apr�s un couper
    if (window.save_cut_operation) {
      var form = getForm("cutOperation");
      $V(form.operation_id, window.save_cut_operation.operation_id);
      $V(form.date, window.save_cut_operation.date);
      $V(form.time_operation, window.save_cut_operation.time_operation);
      $V(form.salle_id, window.save_cut_operation.salle_id);
      onSubmitFormAjax(form, {onComplete: function() {
        getForm("editSejour").onsubmit = "";
        window.cut_operation_id = null;
        updateStatusCut();
        onSubmitFormAjax(getForm("editSejour"), {onComplete: Control.Modal.close});
        }
      });
      window.save_cut_operation = null;
    }
  }
  
  modifCommentaire = function(date, hour, salle_id, commentaire_id, clone, callback) {
    var url = new Url("reservation", "ajax_edit_commentaire");
    
    if (commentaire_id) {
      url.addParam("commentaire_id", commentaire_id);
    }

    if (clone) {
      url.addParam("clone", true);
    }
    
    if (callback) {
      url.addParam("callback", callback);
    }
    
    url.addParam("date", date);
    url.addParam("hour", hour);
    url.addParam("salle_id", salle_id);
    
    url.requestModal(500, 300);
    url.modalObject.observe("afterClose", refreshPlanning);
  }
  
  afterCopy = function(sejour_id, sejour) {
    // Apr�s la copie de s�jour et intervention,
    // on ne vide pas l'op�ration sauvegard�e
    // pour continuer � la coller
    var form = getForm("copyOperation");
    $V(form.copy_operation_id, window.save_copy_operation.operation_id);
    $V(form.salle_id, window.save_copy_operation.salle_id);
    $V(form.sejour_id, sejour_id);
    $V(form.date, window.save_copy_operation.date);
    $V(form.time_operation, window.save_copy_operation.time_operation);
    onSubmitFormAjax(form, {onComplete: Control.Modal.close});
  }

  openLegend = function() {
    var url = new Url("reservation", "ajax_legend_planning");
    url.requestModal();
  }
</script>

<form name="editOperation" method="post">
  <input type="hidden" name="m" value="dPplanningOp" />
  <input type="hidden" name="dosql" value="do_planning_aed" />
  <input type="hidden" name="operation_id" />
  <input type="hidden" name="time_operation" />
  <input type="hidden" name="temp_operation" />
  <input type="hidden" name="salle_id" />
</form>

<form name="cutOperation" method="post">
  <input type="hidden" name="m" value="dPplanningOp" />
  <input type="hidden" name="dosql" value="do_cut_operation" />
  <input type="hidden" name="operation_id" />
  <input type="hidden" name="date" />
  <input type="hidden" name="time_operation" />
  <input type="hidden" name="salle_id" />
</form>

<form name="copyOperation" method="post">
  <input type="hidden" name="m" value="dPplanningOp" />
  <input type="hidden" name="dosql" value="do_copy_operation" />
  <input type="hidden" name="copy_operation_id" />
  <input type="hidden" name="salle_id" />
  <input type="hidden" name="sejour_id" />
  <input type="hidden" name="date" />
  <input type="hidden" name="time_operation" />
</form>

<form name="editCommentairePlanning" method="post">
  <input type="hidden" name="m" value="reservation" />
  <input type="hidden" name="dosql" value="do_commentaire_planning_aed" />
  <input type="hidden" name="commentaire_planning_id" />
  <input type="hidden" name="debut" />
  <input type="hidden" name="fin" />
  <input type="hidden" name="salle_id" />
</form>

<form name="filterPlanning" method="get"> 
  <table class="form">
    <tr>
      <th class="category" colspan="4">
        Filtre
      </th>
      <th class="category">
        Interface
      </th>
      <th class="category">
        <a href="#" class="button search" onclick="openLegend();">{{tr}}Legend{{/tr}}</a>
      </th>
    </tr>
    <tr>
      <td>
        <a href="#1" onclick="window.calendar_planning.datePicked(new Date(new Date(window.calendar_planning.altElement.defaultValue).setHours('-24')))">&lt;&lt;&lt;</a>
        <label>
        Date <input name="date_planning" type="hidden" value="{{$date_planning}}" class="date" onchange="refreshPlanning();"/>
        </label>
        <a href="#1" onclick="window.calendar_planning.datePicked(new Date(new Date(window.calendar_planning.altElement.defaultValue).setHours('+24')))">&gt;&gt;&gt;</a>
      </td>
      <td>
        <label>
          Praticien
          <select name="praticien_id" onchange="refreshPlanning();">
            <option value="">&mdash; Tous les praticiens</option>
            {{mb_include module=mediusers template=inc_options_mediuser list=$praticiens selected=$praticien_id}}
          </select>
        </label>
      </td>
      <td>
        <label>
          {{tr}}CBlocOperatoire{{/tr}}
          <select name="bloc_id" onchange="refreshPlanning();">
            <option value="">&mdash; {{tr}}CBlocOperatoire.all{{/tr}}</option>
            {{foreach from=$blocs item=_bloc}}
              <option value="{{$_bloc->_id}}" {{if $bloc_id == $_bloc->_id}}selected{{/if}}>{{$_bloc->nom}}</option>
            {{/foreach}}
          </select>
        </label>
      </td>
      <td>
        <label>
          <input type="checkbox" name="show_cancelled" {{if $show_cancelled}}checked{{/if}} onclick="refreshPlanning()"/>
            {{tr}}checkbox-COperation-show_cancelled{{/tr}}
        </label>
      </td>
      <td>
        <label>
          <input type="checkbox" name="_comment_mode" /> Mode commentaire
        </label>
      </td>
      <td class="narrow">
        <div id="status_cut" style="width: 100px; height: 14px; border: 2px dashed #ddd; font-weight: bold; text-align: center; cursor: pointer;"
          onclick="window.cut_operation_id = null; window.copy_operation_id = null; updateStatusCut();">
        </div>
      </td>
    </tr>
  </table>
</form>

<div id="planning"></div>
