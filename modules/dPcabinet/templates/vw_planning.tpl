<!-- $Id$ -->

<script type="text/javascript">
function checkPlage() {
  var form = document.editFrm;
  var timeDebut = form._hour_deb.value + ":" +form._min_deb.value;
  var timeFin   = form._hour_fin.value + ":" +form._min_fin.value;

  if(timeDebut >= timeFin) {
    alert("L'heure de fin doit �tre sup�rieure � l'heure de d�but de la plage de consultation");
    return false;
  }  

  if(!checkForm(form)){
    return false;
  }
  
  if(form.nbaffected.value!= 0 && form.nbaffected.value!=""){
    if(timeDebut > form._firstconsult_time.value || timeFin < form._lastconsult_time.value){
      if(!(confirm("Certaines consultations se trouvent en dehors de la plage de consultation.\n\nVoulez-vous appliquer les modifications ?"))){
        return false;
      }
    }  
  }
    
  return true;
}

function putArrivee(oForm) {
  var today = new Date();
  oForm.arrivee.value = today.toDATETIME(true);
  oForm.submit();
}

function goToDate(oForm, date) {
  $V(oForm.debut, date);
}

function showConsultSiDesistement(){
  var url = new Url("dPcabinet", "vw_list_consult_si_desistement");
  url.addParam("chir_id", '{{$chirSel}}');
  url.pop(500, 500, "test");
}

function printPlage(plage_id) {
    var form = document.paramFrm;
    var url = new Url;
    url.setModuleAction("dPcabinet", "print_plages");
    url.addParam("plage_id", plage_id);
    url.addParam("_coordonnees", 1);
    url.popup(700, 550, "Planning");
  }

Main.add(function () {
  Calendar.regField(getForm("changeDate").debut, null, {noView: true});
});

</script>

<table class="main">
  <tr>
    <th style="width: 60%;">
      <form action="?" name="changeDate" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="{{$tab}}" />
        <input type="hidden" name="plageconsult_id" value="0" />
        
        <a href="#1" onclick="$V($(this).getSurroundingForm().debut, '{{$prec}}')">&lt;&lt;&lt;</a>
        
        Semaine du {{$debut|date_format:"%A %d %b %Y"}} au {{$fin|date_format:"%A %d %b %Y"}}
        <input type="hidden" name="debut" class="date" value="{{$debut}}" onchange="this.form.submit()" />
        
        <a href="#1" onclick="$V($(this).getSurroundingForm().debut, '{{$suiv}}')">&gt;&gt;&gt;</a>
        <br />
        <a href="#1" onclick="$V($(this).getSurroundingForm().debut, '{{$today}}')">Aujourd'hui</a>
      </form>
    </th>
    <td style="min-width: 350px;">
      <form action="?" name="selection" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="{{$tab}}" />
        <select name="chirSel" style="width: 15em;" onchange="this.form.submit()">
          <option value="-1" {{if $chirSel == -1}} selected="selected" {{/if}}>&mdash; Choisir un professionnel</option>
          {{foreach from=$listChirs item=curr_chir}}
          <option class="mediuser" style="border-color: #{{$curr_chir->_ref_function->color}};" value="{{$curr_chir->user_id}}" {{if $chirSel == $curr_chir->user_id}} selected="selected" {{/if}}>
            {{$curr_chir->_view}}
          </option>
          {{/foreach}}
        </select>
        
        Cacher les : 
          <label>
            <input type="checkbox" onchange="$V(this.form.hide_payees, this.checked ? 1 : 0); this.form.submit()" {{if $hide_payees}}checked="checked"{{/if}} name="_hide_payees"> pay�es
            <input type="hidden" name="hide_payees" value="{{$hide_payees}}" />
          </label>
          <label>
            <input type="checkbox" onchange="$V(this.form.hide_annulees, this.checked ? 1 : 0); this.form.submit()" {{if $hide_annulees}}checked="checked"{{/if}} name="_hide_annulees"> annul�es
            <input type="hidden" name="hide_annulees" value="{{$hide_annulees}}" />
          </label>
      </form>
      
      <br />
      
      {{if $chirSel && $chirSel != -1}}
        <button type="button" class="lookup" 
                {{if !$count_si_desistement}}disabled="disabled"{{/if}}
                onclick="showConsultSiDesistement()">
          {{tr}}CConsultation-si_desistement{{/tr}} ({{$count_si_desistement}})
        </button>
      {{/if}}
      
      {{if $plageSel->_id}}
        <a class="button new" href="?m={{$m}}&amp;tab=edit_planning&amp;consultation_id=0&amp;plageconsult_id={{$plageSel->_id}}">Planifier une consultation dans cette plage</a>
      {{/if}}
    </td>
  </tr>
  <tr>
    <td>
      <table id="weeklyPlanning">
        <tr>
          <!-- Affichage du nom des jours -->
          <th></th>
          {{foreach from=$listDays key=curr_day item=plagesPerDay}}
          <th style="width: {{math equation="100/x" x=$listDays|@count}}%" {{if $smarty.now|date_format:"%A %d" == $curr_day|date_format:"%A %d"}}class="today"{{/if}} scope="col">{{$curr_day|date_format:"%A %d"}}</th>
          {{/foreach}}
        </tr>       
        <!-- foreach sur les heures -->
        {{assign var=hours_start value=$conf.dPcabinet.CPlageconsult.hours_start}}
        {{assign var=hours_stop value=$conf.dPcabinet.CPlageconsult.hours_stop}}
        {{foreach from=$listHours item=curr_hour}}
        <tr>
          <th rowspan="{{$nb_intervals_hour}}" scope="row">{{$curr_hour}}h</th>
          <!-- foreach sur les minutes -->
          {{foreach from=$listMins item=curr_mins key=keyMins}}
            {{if $keyMins}}
              </tr><tr>
            {{/if}}

            {{foreach from=$listDays item=curr_day}}
              {{assign var="keyAff" value="$curr_day $curr_hour:$curr_mins:00"}}
              {{assign var="affichage" value=$affichages.$keyAff}}
             
              {{if $affichage === "empty"}}
                <td class="empty {{if $curr_mins == '00'}}hour_start{{/if}} 
                {{if $curr_hour < $hours_start || $curr_hour > $hours_stop}}opacity-30{{/if}}"></td>
              {{elseif $affichage == "hours"}}
                <td class="empty hour_start" rowspan="{{$nb_intervals_hour}}"></td>
              {{elseif $affichage === "full"}}
              
              {{else}}
                {{assign var="_listPlages" value=$listPlages.$curr_day}}
                {{assign var=plage value=$_listPlages.$affichage}}
              
                <td class="{{if $plageconsult_id == $plage->plageconsult_id}}selectedPlage{{else}}nonEmpty{{/if}} {{if $curr_mins == '00'}}hour_start{{/if}}" rowspan="{{$plage->_nb_intervals}}">
                  <a href="?m={{$m}}&amp;tab={{$tab}}&amp;plageconsult_id={{$plage->plageconsult_id}}" title="Voir le contenu de la plage">
                    {{if $plage->libelle}}{{$plage->libelle}}<br />{{/if}}
                    {{$plage->debut|date_format:$conf.time}} - {{$plage->fin|date_format:$conf.time}}
                  </a>
                  {{assign var="pct" value=$plage->_fill_rate}}
                  {{if $pct gt 100}}
                    {{assign var="pct" value=100}}
                  {{/if}}
                  {{if $pct lt 50}}{{assign var="backgroundClass" value="empty"}}
                  {{elseif $pct lt 90}}{{assign var="backgroundClass" value="normal"}}
                  {{elseif $pct lt 100}}{{assign var="backgroundClass" value="booked"}}
                  {{else}}{{assign var="backgroundClass" value="full"}}
                  {{/if}} 
                  <a href="?m={{$m}}&amp;tab=edit_planning&amp;consultation_id=0&amp;plageconsult_id={{$plage->plageconsult_id}}" title="Planifier une consultation dans cette plage"> 
                    <div class="progressBar">
                      <div class="bar {{$backgroundClass}}" style="width: {{$pct}}%;"></div>
                      <div class="text">
                        {{if $plage->locked}}
                        <img style="float: right; height: 12px;" src="style/mediboard/images/buttons/lock.png" />
                        {{/if}}
                        {{$plage->_affected}} {{if $plage->_nb_patients != $plage->_affected}}({{$plage->_nb_patients}}){{/if}} / {{$plage->_total|string_format:"%.0f"}}
                      </div>
                    </div>
                  </a>
                </td>
              {{/if}}
            {{/foreach}}
          {{/foreach}}  
        {{/foreach}}
      </table>
      
    {{if $plageSel->_id}}
    <a class="button new" href="?m={{$m}}&amp;tab={{$tab}}&amp;plageconsult_id=0">Cr�er une nouvelle plage</a>
    {{/if}}
    <table class="form">
      <tr>
        {{if !$plageSel->_id}}
        <th class="title" colspan="4">Cr�er une plage</th>

        {{else}}
        <th class="title modify" colspan="4">
		      {{mb_include module=system template=inc_object_idsante400 object=$plageSel}}
		      {{mb_include module=system template=inc_object_history    object=$plageSel}}
          Modifier cette plage
        </th>
        {{/if}}
      </tr>
      <tr>
        <td>
          <form name='editFrm' action='?m=dPcabinet' method='post' onsubmit='return checkPlage()'>
          <input type='hidden' name='dosql' value='do_plageconsult_multi_aed' />
          <input type='hidden' name='del' value='0' />
          {{mb_key object=$plageSel}}
          
					<input type='hidden' name='nbaffected' value='{{$plageSel->_affected}}' />
          <input type='hidden' name='_firstconsult_time' value='{{$_firstconsult_time}}' />
          <input type='hidden' name='_lastconsult_time' value='{{$_lastconsult_time}}' />
          <table class="form">
            <tr>
              <th>{{mb_label object=$plageSel field="chir_id"}}</th>
              <td>
                <select name="chir_id" class="{{$plageSel->_props.chir_id}}" style="width: 14em;">
                  <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
									{{mb_include module=mediusers template=inc_options_mediuser list=$listChirs selected=$chirSel}}
                </select>
              </td>
              <th>{{mb_label object=$plageSel field="libelle"}}</th>
              <td>{{mb_field object=$plageSel field="libelle"}}</td>
            </tr>
            <tr>
              <th>{{mb_label object=$plageSel field="_hour_deb"}}</th>
              <td><select name="_hour_deb" class="notNull num">
                {{foreach from=$listHours item=curr_hour}}
                  <option value="{{$curr_hour|string_format:"%02d"}}" {{if $curr_hour == $plageSel->_hour_deb}} selected="selected" {{/if}}>
                    {{$curr_hour|string_format:"%02d"}}
                  </option>
                {{/foreach}}
                </select> h
                <select name="_min_deb">
                  {{foreach from=$listMins item=curr_min}}
                    <option value="{{$curr_min|string_format:"%02d"}}" {{if $curr_min == $plageSel->_min_deb}} selected="selected" {{/if}}>
                      {{$curr_min|string_format:"%02d"}}
                    </option>
                  {{/foreach}}
                  {{if !in_array($plageSel->_min_deb, $listMins)}}
                    <option value="{{$plageSel->_min_deb|string_format:"%02d"}}" selected="selected">
                      {{$plageSel->_min_deb|string_format:"%02d"}}
                    </option>
                  {{/if}}
                </select> min
              </td>
              <th>{{mb_label object=$plageSel field="date"}}</th>
              <td>
                <select name="date" class="{{$plageSel->_props.date}}">
                  <option value="">&mdash; Choisir le jour</option>
                  {{foreach from=$listDaysSelect item=curr_day}}
                  <option value="{{$curr_day}}" {{if $curr_day == $plageSel->date}} selected="selected" {{/if}}>
                    {{$curr_day|date_format:"%A"}}
                  </option>
                  {{/foreach}}
                </select>
              </td>
            </tr>     
            <tr>
              <th>{{mb_label object=$plageSel field="_hour_fin"}}</th>
              <td>
                <select name="_hour_fin" class="notNull num moreEquals|_hour_deb">
                  {{foreach from=$listHours item=curr_hour}}
                    <option value="{{$curr_hour|string_format:"%02d"}}" {{if $curr_hour == $plageSel->_hour_fin}} selected="selected" {{/if}}>
                      {{$curr_hour|string_format:"%02d"}}
                    </option>
                  {{/foreach}}
                </select> h
                <select name="_min_fin">
                  {{foreach from=$listMins item=curr_min}}
                    <option value="{{$curr_min|string_format:"%02d"}}" {{if $curr_min == $plageSel->_min_fin}} selected="selected" {{/if}}>
                      {{$curr_min|string_format:"%02d"}}
                    </option>
                  {{/foreach}}
                  {{if !in_array($plageSel->_min_fin, $listMins)}}
                    <option value="{{$plageSel->_min_fin|string_format:"%02d"}}" selected="selected">
                      {{$plageSel->_min_fin|string_format:"%02d"}}
                    </option>
                  {{/if}}
                </select> min
              </td>
              <th><label for="_repeat" title="Nombre de plages � cr�er">Nombre de plages</label></th>
              <td><input type="text" size="2" name="_repeat" value="1" /></td>
            </tr>      
            <tr>
              <th>{{mb_label object=$plageSel field="_freq"}}</th>
              <td>
                <select name="_freq">
                  <option value="05" {{if ($plageSel->_freq == "05")}} selected="selected" {{/if}}>05</option>
                  <option value="10" {{if ($plageSel->_freq == "10")}} selected="selected" {{/if}}>10</option>
                  <option value="15" {{if ($plageSel->_freq == "15") || (!$plageSel->_id)}} selected="selected" {{/if}}>15</option>
                  <option value="20" {{if ($plageSel->_freq == "20")}} selected="selected" {{/if}}>20</option>
                  <option value="30" {{if ($plageSel->_freq == "30")}} selected="selected" {{/if}}>30</option>
                  <option value="45" {{if ($plageSel->_freq == "45")}} selected="selected" {{/if}}>45</option>
               </select> minutes
              </td>
              <th>
                <label for="_type_repeat" title="Espacement des plages">Type de r�p�tition</label>
              </th>
              <td>
                <select name="_type_repeat">
                  <option value="1">Normale</option>
                  <option value="2">Une semaine sur 2</option>
                  <option value="3">Une semaine sur 3</option>
                  <option value="4">Une semaine sur 4</option>
                  <option value="5">Une semaine sur 5</option>
                  <option value="6">Une semaine sur 6</option>
                  <option value="7">Une semaine sur 7</option>
                  <option value="8">Une semaine sur 8</option>
                  <option value="9">Une semaine sur 9</option>
                </select>
              </td>
            </tr>
            <tr>
              <td colspan="2"></td>
              <th>{{mb_label object=$plageSel field="locked"}}</th>
              <td>{{mb_field object=$plageSel field="locked" typeEnum="checkbox"}}</td>
            </tr>
            <tr>
              <td colspan="4" class="text">
                <div class="small-info">
                  Pour modifier plusieurs plages (nombre de plages > 1),
                  veuillez ne pas changer les champs d�but et fin en m�me temps
                </div>
              </td>
            </tr>
            <tr>
              {{if !$plageSel->_id}}
              <td class="button" colspan="4"><button type="submit" class="submit">{{tr}}Create{{/tr}}</button></td>
              {{else}}
              <td class="button" colspan="4"><button type="submit" class="modify">{{tr}}Modify{{/tr}}</button></td>
              {{/if}}
            </tr>
          </table>
          </form>
      
          {{if $plageSel->_id}}
	      <form name='removeFrm' action='?m=dPcabinet' method='post'>
      	  <input type='hidden' name='dosql' value='do_plageconsult_multi_aed' />
	        <input type='hidden' name='del' value='1' />
	        {{mb_key object=$plageSel}}
					
          <table class="form">
	        <tr>
	          <th class="title modify" colspan="2">Supprimer cette plage</th>
          </tr>
	        <tr>
	          <th>Supprimer cette plage pendant</th>
	          <td><input type='text' name='_repeat' size="1" value='1' /> semaine(s)</td>
	        </tr>
	        <tr>
	          <td class="button" colspan="2">
	            <button class="trash" type='button' onclick="confirmDeletion(this.form,{typeName:'la plage de consultations du',objName:'{{$plageSel->date|date_format:$conf.longdate}}'})">
	              {{tr}}Delete{{/tr}}
	            </button>
	          </td>
	        </tr>
	      </table>
	    </form>
        {{/if}}        
        </td>
      </tr>
    </table>
    </td>
    <td>
      <table class="tbl">
        <tr>
          <th class="title" colspan="10">
            <strong>
            {{if $plageSel->_id}}
            <button class="print" onclick="printPlage({{$plageSel->_id}})" style="float:right">{{tr}}Print{{/tr}}</button>
            {{mb_include module=system template=inc_object_notes object=$plageSel}}
              Consultations du {{$plageSel->date|date_format:$conf.longdate}}
            {{else}}
            {{tr}}CPlageconsult.none{{/tr}}
            {{/if}}
            </strong>
          </th>
        </tr>

        <tr>
          <th class="narrow">{{mb_title class=CConsultation field=heure}}</th>
          <th>{{mb_title class=CConsultation field=patient_id}}</th>
          <th>{{mb_title class=CConsultation field=motif}}</th>
          <th>{{mb_title class=CConsultation field=rques}}</th>
          <th>RDV</th>
          <th>{{mb_title class=CConsultation field=_etat}}</th>
        </tr>
        {{foreach from=$plageSel->_ref_consultations item=_consult}}
        <tr>
          {{assign var="consult_id" value=$_consult->_id}}
          {{assign var=patient value=$_consult->_ref_patient}}
          {{assign var="href_consult" value="?m=$m&tab=edit_consultation&selConsult=$consult_id"}}
          {{assign var="href_planning" value="?m=$m&tab=edit_planning&consultation_id=$consult_id"}}

          {{if !$patient->_id}}
            {{assign var="style" value="style='background: #ffa;'"}}          
          {{elseif $_consult->premiere}} 
            {{assign var="style" value="style='background: #faa;'"}}
					{{elseif $_consult->_ref_sejour->_id}} 
            {{assign var="style" value="style='background: #CFFFAD;'"}}
          {{else}} 
            {{assign var="style" value=""}}
          {{/if}}
          
          <td {{$style|smarty:nodefaults}}>
            <div style="float: left">
            {{if $patient->_id}}
              <a href="{{$href_consult}}" title="Voir la consultation">
                <span onmouseover="ObjectTooltip.createEx(this, '{{$_consult->_guid}}')">
                  {{$_consult->heure|date_format:$conf.time}}
                </span>
               </a>
            {{else}}
              <span onmouseover="ObjectTooltip.createEx(this, '{{$_consult->_guid}}')">
                {{mb_value object=$_consult field=heure}}
              </span>
            {{/if}}
            </div>
            
            {{assign var=categorie value=$_consult->_ref_categorie}}
            {{if $categorie->_id}}
            <div style="float: right">
              <img src="./modules/dPcabinet/images/categories/{{$categorie->nom_icone}}" alt="{{$categorie->nom_categorie}}" title="{{$categorie->nom_categorie}}" />
            </div>
            {{/if}}
          </td>

          <td class="text" {{$style|smarty:nodefaults}}>
            {{if !$patient->_id}}
              [PAUSE]
            {{else}}
            <a style="float: right"  title="Modifier le dossier administratif" href="?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id={{$patient->_id}}">
              <img src="images/icons/edit.png" alt="modifier" />
            </a>
            <a href="{{$href_consult}}">
              <span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">
              {{$patient}}
              </span>
            </a>
            {{/if}}
          </td>
          <td class="text" {{$style|smarty:nodefaults}}>
            {{if $patient->_id}}
              <a href="{{$href_consult}}"  title="Voir la consultation">{{$_consult->motif|truncate:35:"...":false|nl2br}}</a>
            {{else}}
              {{$_consult->motif|truncate:35:"...":false|nl2br}}
            {{/if}}
          </td>
          <td class="text" {{$style|smarty:nodefaults}}>
            {{if $patient->_id}}
              <a href="{{$href_consult}}"  title="Voir la consultation">{{$_consult->rques|truncate:35:"...":false|nl2br}}</a>
            {{else}}
              {{$_consult->rques|truncate:35:"...":false|nl2br}}
            {{/if}}
          </td>
          <td {{$style|smarty:nodefaults}}>
            <form name="etatFrm{{$_consult->_id}}" action="?m=dPcabinet" method="post">
            <input type="hidden" name="m" value="dPcabinet" />
            <input type="hidden" name="dosql" value="do_consultation_aed" />
            {{mb_key object=$_consult}}
            <input type="hidden" name="chrono" value="{{$_consult|const:'PATIENT_ARRIVE'}}" />
            <input type="hidden" name="arrivee" value="" />
            </form>
            
            <form name="cancelFrm{{$_consult->_id}}" action="?m=dPcabinet" method="post">
            <input type="hidden" name="m" value="dPcabinet" />
            <input type="hidden" name="dosql" value="do_consultation_aed" />
            {{mb_key object=$_consult}}
            <input type="hidden" name="chrono" value="{{$_consult|const:'TERMINE'}}" />
            <input type="hidden" name="annule" value="1" />
            </form>
            
            <a class="action" href="{{$href_planning}}">
              <img src="images/icons/planning.png" title="Modifier le rendez-vous" alt="modifier" />
            </a>
						{{if $_consult->chrono == $_consult|const:'PLANIFIE' && $patient->_id}}
            <a class="action" href="#" onclick="putArrivee(document.etatFrm{{$_consult->_id}})">
              <img src="images/icons/check.png" title="Notifier l'arriv�e du patient" alt="arrivee" />
            </a>
            <a class="action" href="#" onclick="if(confirm('Voulez-vous vraiment annuler cette consultation ?')) {document.cancelFrm{{$_consult->_id}}.submit()}">
              <img src="images/icons/cancel.png" title="Annuler ce rendez-vous" alt="annuler" />
            </a>
            {{/if}}
          </td>
          <td {{$style|smarty:nodefaults}} {{if $_consult->annule}}class="error"{{/if}}>
            {{if $patient->_id}}
              {{$_consult->_etat}}
            {{/if}}
          </td>
        </tr>
        {{foreachelse}}
        <tr>
          <td colspan="6" class="empty">{{tr}}CConsultation.none{{/tr}}</td>
        </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
</table>
