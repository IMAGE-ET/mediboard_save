{{assign var=canCabinet value=$modules.dPcabinet->_can}}
<script type="text/javascript">

var ViewFullPatient = {
  select: function(eLink) {
    // Select current row
    if (this.eCurrent) {
      Element.classNames(this.eCurrent).remove("selected");
    }
    this.eCurrent = eLink.parentNode.parentNode;
    Element.classNames(this.eCurrent).add("selected");
  }
  
}

function effectHighlight(){
  var elts = document.getElementsByClassName("current"); 
  for (var i=0;i<elts.length;i++) { 
        new Effect.Highlight(elts[i]);
  }
}

Main.add(function () {
  searchTabs = Control.Tabs.create('main_tab_group');
  var periode = new PeriodicalExecuter(effectHighlight,1);
});

function editIntervention(op_id) {
  window.opener.location.href="?m=dPplanningOp&tab=vw_edit_planning&operation_id="+op_id;
}
</script>

<ul id="main_tab_group" class="control_tabs">
  <li><a href="#parcour">Parcours</a></li>
  <li><a href="#mouvements">Mouvements</a></li>
</ul>

<hr class="control_tabs" />

<div id="parcour" style="display: none;">
  {{if $sejour->_ref_service->is_soins_continue || $sejour->_ref_last_affectation->_ref_service->is_soins_continue}}
    <div class="small-info">{{tr}}CService-msg.is_soins_continue{{/tr}}</div>
  {{/if}}
  <table id="diagramme">
    <tr> 
      <th colspan=5>{{$sejour->_view}} <br/><br/> </th>
    </tr>
    <tr>
      {{if ($diagramme.bloc.type) != "none"}}
        <td class="only done ray" colspan=2> ADMIS <br/> Date : {{$diagramme.admission.entree.date|date_format:$conf.datetime}}</td>
      {{else}}
        <td class="only" colspan=2> ADMIS <br/> Date : {{$diagramme.admission.entree.date|date_format:$conf.datetime}}</td>
      {{/if}}
      <td> </td>
      {{if ($diagramme.bloc.type) != "none" && $diagramme.admission.sortie.reelle == "sortie_prevue"}}
        <td class="only expect ray" colspan=2> SORTIE <br/> Date : {{$diagramme.admission.sortie.date|date_format:$conf.datetime}} <br/> Mode Sortie : {{$diagramme.admission.sortie.mode_sortie}}</td>
      {{elseif ($diagramme.admission.sortie.reelle) == "sortie_reelle"}}
        <td class="only current" colspan=2> SORTIE <br/> Date : {{$diagramme.admission.sortie.date|date_format:$conf.datetime}} <br/> Mode Sortie : {{$diagramme.admission.sortie.mode_sortie}}</td>  
      {{else}}
        <td class="only" colspan=2> SORTIE <br/> Date : {{$diagramme.admission.sortie.date|date_format:$conf.datetime}} <br/> Mode Sortie : {{$diagramme.admission.sortie.mode_sortie}}</td>
      {{/if}}
      <td> </td>
    </tr>
    <tr>
      <td class="arrowdown" colspan=2> </td>
      <td> </td>
      <td class="arrowup" colspan=2> </td>
      <td> </td>
    </tr>
    <tr>
      {{if ($diagramme.admission.sortie.reelle) == "sortie_reelle"}}
        <td class="only done ray" colspan=5> HOSPITALIS� <br/> Chambre : {{$diagramme.hospitalise.chambre}}</td>
      {{else}}
        <td class="only current" colspan=5> HOSPITALIS� <br/> Chambre : {{$diagramme.hospitalise.chambre}}</td>
      {{/if}}
      <td>
        Liste des affectations :
        {{foreach from=$affectations item=curr_aff}}
          {{if ($curr_aff->_id == $diagramme.hospitalise.affectation)}}  
            <span class="listeCurrent">
          {{else}}
            <span>
          {{/if}}
          <br/>
          <span
            onmouseover="ObjectTooltip.createEx(this, '{{$curr_aff->_guid}}')">
            Affectations du {{$curr_aff->entree|date_format:"%d/%m/%Y"}}
                          au {{$curr_aff->sortie|date_format:"%d/%m/%Y"}}
            </span>
            </span>
          {{/foreach}}
      </td>
    </tr>
    {{if ($diagramme.bloc)}}
    <tr>
      <td class="space"> </td>
      <td class="arrowdown" colspan=1> </td>
      <td> </td>
      <td class="arrowup" colspan=1> </td>
      <td class="space"> </td>
      <td> </td>
    </tr>
    <tr>
      {{if ($diagramme.bloc.sortieSalleReveil) != ""}}
        <td class="space"> </td>
        <td class="only done ray" colspan=3> AU BLOC <br/> 
          <span onmouseover="ObjectTooltip.createEx(this, 'COperation-{{$diagramme.bloc.id}}')">
              {{$diagramme.bloc.vue}}
          </span>
        </td>
        <td class="space"> </td>
      {{elseif ($diagramme.bloc.salle != "") && ($diagramme.bloc.sortieSalleReveil == "")}}
        <td class="space"> </td>
        <td class="only current" colspan=3> AU BLOC <br/> 
          <span onmouseover="ObjectTooltip.createEx(this, 'COperation-{{$diagramme.bloc.id}}')">
              {{$diagramme.bloc.vue}}
          </span>
         </td>
         <td class="space"> </td>
      {{else}}
        <td class="space"> </td>
        <td class="only expect ray" colspan=3> AU BLOC <br/> 
          <span onmouseover="ObjectTooltip.createEx(this, 'COperation-{{$diagramme.bloc.id}}')">
            {{$diagramme.bloc.vue}}
          </span>
         </td>
         <td class="space"> </td>
      {{/if}}
      <td colspan=2>
        Liste des interventions :
        {{foreach from=$sejour->_ref_operations item=curr_op}}
          {{if ($diagramme.bloc.checkCurrent == "check" && $diagramme.bloc.idCurrent == $curr_op->_id)}}  
            {{if ($curr_op->_id == $diagramme.bloc.id)}}  
              <span class="listeCurrent">
            {{else}}
              <span>
            {{/if}}
            <br/>
            {{if $canCabinet->edit}}
              <a href="#" title="Modifier l'intervention" onclick="editIntervention({{$curr_op->_id}})">
                <img src="images/icons/edit.png" alt="Planifier"/>
              </a>
            {{/if}}
            <img src="images/icons/tick.png" alt="edit" title="Etat du S�jour" />
            <a href="?m=dPhospi&amp;dialog=1&amp;a=vw_parcours&amp;operation_id={{$curr_op->_id}}" 
                onmouseover="ObjectTooltip.createEx(this, '{{$curr_op->_guid}}')">
                Intervention du {{$curr_op->_datetime|date_format:$conf.datetime}}
              </a>
          {{else}}
              {{if ($curr_op->_id == $diagramme.bloc.id)}}  
              <span class="listeCurrent">
            {{else}}
              <span>
            {{/if}}
            <br/>
            {{if $canCabinet->edit}}
              <a href="#" title="Modifier l'intervention" onclick="editIntervention({{$curr_op->_id}})">
                <img src="images/icons/edit.png" alt="Planifier"/>
              </a>
            {{/if}}
              <a href="?m=dPhospi&amp;dialog=1&amp;a=vw_parcours&amp;operation_id={{$curr_op->_id}}" 
                onmouseover="ObjectTooltip.createEx(this, '{{$curr_op->_guid}}')">
              Intervention du {{$curr_op->_datetime|date_format:$conf.datetime}}
              </a>
            {{/if}}
            </span>
            {{if $conf.dPplanningOp.COperation.verif_cote && ($curr_op->cote == "droit" || $curr_op->cote == "gauche")}}
            <form name="editCoteOp{{$curr_op->_id}}" action="?" method="post" onsubmit="return onSubmitFormAjax(this)">
              <input type="hidden" name="m" value="dPplanningOp" />
              <input type="hidden" name="dosql" value="do_planning_aed" />
              {{mb_key object=$curr_op}}
              {{mb_label object=$curr_op field="cote_admission"}} :
              {{mb_field emptyLabel="Choose" object=$curr_op field="cote_hospi" onchange="this.form.onsubmit();"}}
            </form>
          {{/if}}
          {{/foreach}}
      </td>
    </tr>
    <tr>
      <td class="space"> </td>
      <td class="arrowdown" colspan=1> </td>
      <td> </td>
      <td class="arrowup" colspan=1> </td>
      <td class="space"> </td>
      <td> </td>
    </tr>
    {{if ($diagramme.bloc.type) == "current"}}
    <tr>
      {{if ($diagramme.bloc.sortieSalle) == ""}}
        <td class="space"> </td>
        <td class="only current"> EN SALLE <br/> Heure : {{$diagramme.bloc.salle|date_format:$conf.time}}</td>
      {{else}}
        <td class="space"> </td>
        <td class="only done ray"> EN SALLE <br/> Heure : {{$diagramme.bloc.salle|date_format:$conf.time}}</td>
      {{/if}}  
      <td> </td>
      {{if ($diagramme.bloc.sortieSalleReveil) == ""}}
        <td class="only expect ray"> SORTIE SALLE DE R�VEIL </td>
        <td class="space"> </td>
      {{else}}
        <td class="only done ray"> SORTIE SALLE DE R�VEIL <br/> Heure : {{$diagramme.bloc.sortieSalleReveil|date_format:$conf.time}} </td>
        <td class="space"> </td>
      {{/if}}  
      <td> </td>
    </tr>
    <tr>
      <td class="space"> </td>
      <td class="arrowdown" colspan=1> </td>
      <td> </td>
      <td class="arrowup" colspan=1> </td>
      <td class="space"> </td>
      <td> </td>
    </tr>
    <tr>
      {{if ($diagramme.bloc.sortieSalle) == ""}}
        <td class="space"> </td>
        <td class="only expect ray"> SORTIE DE SALLE </td>
      {{elseif ($diagramme.bloc.salleReveil) != ""}}
        <td class="space"> </td>
        <td class="only done ray"> SORTIE DE SALLE <br/> Heure : {{$diagramme.bloc.sortieSalle|date_format:$conf.time}} </td>
      {{else}}
        <td class="space"> </td>
        <td class="only current"> SORTIE DE SALLE <br/> Heure : {{$diagramme.bloc.sortieSalle|date_format:$conf.time}} </td>
      {{/if}}
      <td class="arrowright">  </td>
      {{if ($diagramme.bloc.salleReveil) == ""}}
        <td class="only expect ray"> EN SALLE DE R�VEIL </td>
        <td class="space"> </td>
      {{elseif ($diagramme.bloc.sortieSalleReveil) != ""}}
        <td class="only done ray"> EN SALLE DE R�VEIL <br/> Heure : {{$diagramme.bloc.salleReveil|date_format:$conf.time}} </td>
        <td class="space"> </td>
      {{else}}
        <td class="only current"> EN SALLE DE R�VEIL <br/> Heure : {{$diagramme.bloc.salleReveil|date_format:$conf.time}} </td>
        <td class="space"> </td>
      {{/if}}
      <td> </td>
    </tr>
    {{elseif ($diagramme.bloc.type) == "done"}}
    <tr>
      <td class="space"> </td>
      <td class="only done ray"> EN SALLE <br/> Heure : {{$diagramme.bloc.salle|date_format:$conf.time}}</td>
      <td> </td>
      <td class="only done ray"> SORTIE SALLE DE R�VEIL <br/> Heure : {{$diagramme.bloc.sortieSalleReveil|date_format:$conf.time}} </td>
      <td class="space"> </td>
      <td> </td>
    </tr>
    <tr>
      <td class="space"> </td>
      <td class="arrowdown" colspan=1> </td>
      <td> </td>
      <td class="arrowup" colspan=1> </td>
      <td class="space"> </td>
      <td> </td>
    </tr>
    <tr>
      <td class="space"> </td>
      <td class="only done ray"> SORTIE DE SALLE <br/> Heure : {{$diagramme.bloc.sortieSalle|date_format:$conf.time}} </td>
      <td class="arrowright">  </td>
      <td class="only done ray"> EN SALLE DE R�VEIL <br/> Heure : {{$diagramme.bloc.salleReveil|date_format:$conf.time}} </td>
      <td class="space"> </td>
      <td> </td>
    </tr>
    {{elseif ($diagramme.bloc.type) == "expect"}}
    <tr>
      <td class="space"> </td>
      <td class="only expect ray">  <br/> EN SALLE<br/><br/></td>
      <td> </td>
      <td class="only expect ray"> SORTIE SALLE DE R�VEIL </td>
      <td class="space"> </td>
      <td> </td>
    </tr>
    <tr>
      <td class="space"> </td>
      <td class="arrowdown" colspan=1> </td>
      <td> </td>
      <td class="arrowup" colspan=1> </td>
      <td class="space"> </td>
      <td> </td>
    </tr>
    <tr>
      <td class="space"> </td>
      <td class="only expect ray"> <br/>SORTIE DE SALLE <br/><br/> </td>
      <td class="arrowright">  </td>
      <td class="only expect ray"> EN SALLE DE R�VEIL </td>
      <td class="space"> </td>
      <td> </td>
    </tr>
    {{/if}}
    {{/if}}
  </table>
</div>

<div id="mouvements" style="display: none;">
  {{mb_include module=hospi template=inc_movements}}
</div>