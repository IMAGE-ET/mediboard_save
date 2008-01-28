{{mb_include_script module=dPhospi script=vw_affectations}}

<script type="text/javascript">

function pageMain() {
  // PairEffect.InitGroup can't be used because it scans all DOM nodes
  {{foreach from=$services item=curr_service}}
  new PairEffect("service{{$curr_service->service_id}}", {
    bStartVisible: true,
    sEffect: "appear",
    sCookieName: "fullService"
  } );
  {{/foreach}}

  regRedirectFlatCal("{{$date}}", "?m={{$m}}&tab={{$tab}}&date=");
  
 
  regFieldCalendar("addAffectationsejour", "entree", true);
  regFieldCalendar("addAffectationsejour", "sortie", true);

}

</script>

<table class="main">
  <tr>
    <td colspan="2">
      <div style="float:right;">
        <strong>Planning du {{$date|date_format:"%A %d %B %Y"}}</strong>
      </div>
      {{if $alerte}}
      <div class="warning">
        <a href="#" onclick="showAlerte()">Il y a {{$alerte}} patient(s) � placer dans la semaine qui vient</a>
      </div>
      {{else}}
      <div class="message">
        Tous les patients sont plac�s pour la semaine � venir
      </div>
      {{/if}}
    </td>
    <th>
      {{$totalLits}} place(s) de libre
    </th>
  </tr>

  <tr>
    <td>
      <a href="#" onclick="showLegend()" class="buttonsearch">L�gende</a>
      <a href="#" onclick="showRapport('{{$date}}')" class="buttonprint">Rapport</a>
    </td>
    
    <td>
      <form name="chgAff" action="?m={{$m}}" method="get">
      {{foreach from=$services item=curr_service}}
        <input
          type="checkbox"
          name="service{{$curr_service->_id}}"
          id="service{{$curr_service->_id}}-trigger"
          value="{{$curr_service->_id}}"
          onchange="reloadService(this, {{$mode}});"
          {{if $curr_service->_vwService}}checked="checked"{{/if}}
        />
        <label for="service{{$curr_service->service_id}}" title="Afficher le service {{$curr_service->nom}}">
          {{$curr_service->nom}}
        </label>
      {{/foreach}}
      </form>
    </td>

    {{include file="inc_mode_hospi.tpl"}}
  </tr>

  <tr>
    <td class="greedyPane" colspan="2">
      <table class="affectations">
        <tr>
        {{foreach from=$services item=curr_service}}
          <td class="fullService" id="service{{$curr_service->service_id}}">
          {{include file="inc_affectations_services.tpl"}}
          </td>
        {{/foreach}}
        </tr>
      </table>
    </td>
    <td class="pane">
      <div id="calendar-container"></div>
      {{if $can->edit}}
      
      <table class="form">
        <tr>
          <td class="button">
            <form name="chgFilter" action="?m={{$m}}" method="get">
              <input type="hidden" name="m" value="{{$m}}" />
              <label for="filterAdm" title="Admissions � afficher">Admissions</label>
              <select name="filterAdm" onchange="submit()">
                <option value="tout" {{if $filterAdm == 0}}selected="selected"{{/if}}>&mdash; Tout afficher</option>
                <option value="ambu" {{if $filterAdm == "ambu"}}selected="selected"{{/if}}>Ambulatoires</option>
                <option value="comp" {{if $filterAdm == "comp"}}selected="selected"{{/if}}>Hospi. compl�tes</option>
                <option value="csejour" {{if $filterAdm == "csejour"}}selected="selected"{{/if}}>Courts s�jours</option>
              </select>
            </form>
          </td>
        </tr>
        <tr>
          <td class="button">
            <form name="chgFilter_" action="?m={{$m}}" method="get">
              <input type="hidden" name="m" value="{{$m}}" />
              <label for="triAdm" title="Admissions � afficher">Trier par</label>
              <select name="triAdm" onchange="submit()">
                <option value="praticien" {{if $triAdm == "praticien"}}selected="selected"{{/if}}>Praticien</option>
                <option value="date_entree" {{if $triAdm == "date_entree"}}selected="selected"{{/if}}>Heure d'entr�e</option>
              </select>
            </form>
          </td>
        </tr>
      </table>
      
      <form name="addAffectationsejour" action="?m={{$m}}" method="post">
      <input type="hidden" name="dosql" value="do_affectation_aed" />
      <input type="hidden" name="lit_id" value="" />
      <input type="hidden" name="sejour_id" value="" />
            
      <table class="sejourcollapse" id="sejour_bloque">
        <tr>
        <td class="selectsejour">
          <input type="radio" id="hospitalisation" onclick="selectHospitalisation()" />
          <script type="text/javascript">new Draggable('sejour_bloque', {revert:true})</script>
        </td>
        <td class="patient" onclick="flipSejour('bloque')">
          <strong><a name="sejourbloque">[BLOQUER UN LIT]</a></strong>
        </td>
        </tr>
        <tr>
          <td class="date"><em>Entr�e</em></td>
          <td class="date">{{mb_field object=$affectation field="entree" form="addAffectationsejour" }}</td>
        </tr>
        <tr>
          <td class="date"><em>Sortie</em></td>
          <td class="date">{{mb_field object=$affectation field="sortie" form="addAffectationsejour" }}</td>
      </tr>
      <tr>
        <td class="date highlight" colspan="2">
          <label for="rques">Remarques</label> : 
          <textarea name="rques"></textarea>
        </td>
      </tr>
      </table>
      </form>
      
      {{foreach from=$groupSejourNonAffectes key=group_name item=sejourNonAffectes}}
        {{include file="inc_affectations_liste.tpl"}}
      {{/foreach}}
      {{/if}}
    </td>
  </tr>

</table>