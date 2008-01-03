<script type="text/javascript">

// Fonction de refresh du temps d'attente
function updateAttente(sejour_id){
  var url = new Url;
  url.setModuleAction("dPurgences", "httpreq_vw_attente");
  url.addParam("sejour_id", sejour_id);
  url.periodicalUpdate('attente-'+sejour_id, { frequency: 60, waitingText: null });
}


function checkPraticien(oForm){
  var prat = oForm.prat_id.value;
  if(prat == ""){
    alert("Veuillez s�lectionner un praticien");
    return false;
  }
  return true;
}

 
function printMainCourante() {
  var url = new Url;
  url.setModuleAction("dPurgences", "print_main_courante");
  url.addParam("date", "{{$date}}");
  url.popup(800, 600, "Impression main courante");
}



function pageMain() {
  regRedirectPopupCal("{{$date}}", "?m={{$m}}&tab={{$tab}}&date=");
}


</script>

<table style="width:100%">
  <tr>
    <td>
      <a class="buttonnew" href="?m=dPurgences&amp;tab=vw_aed_rpu&amp;rpu_id=0">
        Ajouter un patient
      </a> 
    </td>
    <th>
     le
     {{$date|date_format:"%A %d %B %Y"}}
     <img id="changeDate" src="./images/icons/calendar.gif" title="Choisir la date" alt="calendar" />
    </th>
    <td style="text-align: right">
     Type d'affichage
     <form name="selView" action="?m=dPurgences&amp;tab=vw_idx_rpu" method="post">
	      <select name="selAffichage" onchange="submit();">
	        <option value="tous" {{if $selAffichage == "tous"}}selected = "selected"{{/if}}>Tous</option>
	        <option value="prendre_en_charge" {{if $selAffichage == "prendre_en_charge"}} selected = "selected" {{/if}}>A prendre en charge</option>
	      </select>
	    </form>
      <a href="#" onclick="printMainCourante()" class="buttonprint">Main courante</a>
    
    </td>
  </tr>
</table>

<table class="tbl">
  <tr>
    <th>{{mb_colonne class="CRPU" field="ccmu" order_col=$order_col order_way=$order_way url="?m=$m&amp;tab=vw_idx_rpu"}}</th>
    <th>{{mb_colonne class="CRPU" field="_patient_id" order_col=$order_col order_way=$order_way url="?m=$m&amp;tab=vw_idx_rpu"}}</th>
    <th>{{mb_colonne class="CRPU" field="_entree" order_col=$order_col order_way=$order_way url="?m=$m&amp;tab=vw_idx_rpu"}}</th>
    {{if $date == $today}}
    <th>Temps d'attente</th>
    {{/if}}
    <th>{{tr}}CRPU-_responsable_id{{/tr}}</th>
    <th>{{tr}}CRPU-diag_infirmier{{/tr}}</th>
    <th>Prise en charge</th>
  </tr>
  {{foreach from=$listSejours item=curr_sejour}}
  <tr>
    <td {{if $curr_sejour->_ref_rpu->ccmu}}class="ccmu-{{$curr_sejour->_ref_rpu->ccmu}}"{{else}}style="background-color: #fff"{{/if}}>
      <a href="?m=dPurgences&amp;tab=vw_aed_rpu&amp;rpu_id={{$curr_sejour->_ref_rpu->_id}}">
        {{if $curr_sejour->_ref_rpu->ccmu}}
          {{tr}}CRPU.ccmu.{{$curr_sejour->_ref_rpu->ccmu}}{{/tr}}
        {{/if}}
      </a>
    </td>
    <td style="{{if $curr_sejour->_ref_rpu->_count_consultations != 0}}background-color:#cfc;{{/if}}">
      <a href="?m=dPurgences&amp;tab=vw_aed_rpu&amp;rpu_id={{$curr_sejour->_ref_rpu->_id}}">
        <strong>
        {{$curr_sejour->_ref_patient->_view}}
        {{if $curr_sejour->_ref_patient->_IPP}}
          [{{$curr_sejour->_ref_patient->_IPP}}]
        {{/if}}
        </strong>
      </a>
      
    </td>
    <td style="{{if $curr_sejour->_ref_rpu->_count_consultations != 0}}background-color:#cfc;{{/if}}">
      <a href="?m=dPurgences&amp;tab=vw_aed_rpu&amp;rpu_id={{$curr_sejour->_ref_rpu->_id}}">
        {{$curr_sejour->_entree|date_format:"%d/%m/%Y � %Hh%M"}}
        <strong>
        {{if $curr_sejour->_num_dossier}}
          [{{$curr_sejour->_num_dossier}}]
        {{/if}}
        </strong>
      </a>
    </td>
    
    {{if $date == $today}}
    {{if $curr_sejour->_ref_rpu->_count_consultations < 1}}
      <td id="attente-{{$curr_sejour->_id}}" style="text-align: center">
        <!-- Affichage du temps d'attente de chaque patient -->
        <script type="text/javascript">
          updateAttente("{{$curr_sejour->_id}}");
        </script>
      </td>
    {{else}}
      <td style="background-color:#cfc; text-align: center">
      Consultation � {{$curr_sejour->_ref_rpu->_ref_consult->heure|date_format:"%Hh%M"}}
      
      {{assign var="sejour_id" value=$curr_sejour->_id}}
      ({{$tps_attente.$sejour_id|date_format:"%Hh%M"}})
      
      </td>
    {{/if}}
    {{/if}}
    <td style="{{if $curr_sejour->_ref_rpu->_count_consultations != 0}}background-color:#cfc;{{/if}}">
      <a href="?m=dPurgences&amp;tab=vw_aed_rpu&amp;rpu_id={{$curr_sejour->_ref_rpu->_id}}">
        {{$curr_sejour->_ref_praticien->_view}}
      </a>
    </td>
    <td class="text" style="{{if $curr_sejour->_ref_rpu->_count_consultations != 0}}background-color:#cfc;{{/if}}">
      <a href="?m=dPurgences&amp;tab=vw_aed_rpu&amp;rpu_id={{$curr_sejour->_ref_rpu->_id}}">
        {{$curr_sejour->_ref_rpu->diag_infirmier|nl2br}}
      </a>
    </td>
    <td class="button" style="{{if $curr_sejour->_ref_rpu->_count_consultations != 0}}background-color:#cfc;{{/if}}">
      <!-- ici c'est comme pour une consult imm�diate -->
      {{if $curr_sejour->_ref_rpu->_count_consultations < 1}}
        {{if $can->edit}}
       <form name="createConsult{{$curr_sejour->_ref_rpu->_id}}" method="post" action="?">
         <input type="hidden" name="dosql" value="do_consult_now" />
         <input type="hidden" name="m" value="dPcabinet" />
         <input type="hidden" name="del" value="0" />
         <input type="hidden" name="sejour_id" value="{{$curr_sejour->_id}}" />   
         <input type="hidden" name="patient_id" value="{{$curr_sejour->patient_id}}" />    
         <select name="prat_id">
           <option value="">&mdash; Choisir un praticien</option>
           {{foreach from=$listPrats item="curr_prat"}}
           <option class="mediuser" style="border-color: #{{$curr_prat->_ref_function->color}};" value="{{$curr_prat->_id}}" {{if $userCourant == $curr_prat->_id}}selected = "selected"{{/if}}>
             {{$curr_prat->_view}}
           </option>
           {{/foreach}}
         </select>
         <br />
         <button type="submit" class="new" onclick="return checkPraticien(this.form)">Prendre en charge</button>
       </form>
         {{else}}
          - 
         {{/if}}
       {{else}}
         Patient d�j� pris en charge par {{$curr_sejour->_ref_rpu->_ref_consult->_ref_plageconsult->_ref_chir->_view}}
         {{if $can->edit}}
         <br />
         <!-- 
         <a style="display: inline" href="?m=dPurgences&amp;tab=edit_consultation&amp;selConsult={{$curr_sejour->_ref_rpu->_ref_consult->_id}}">
           Voir prise en charge 
         </a>
          -->
          
         <a class="action" style="display: inline" title="Prise en charge" href="?m=dPpatients&amp;tab=vw_full_patients&amp;patient_id={{$curr_sejour->_ref_patient->_id}}&amp;sejour_id={{$curr_sejour->_id}}">
           Voir prise en charge
         </a>
      
      
         <a style="display: inline" title="Modifier le s�jour" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$curr_sejour->_id}}">
           <img src="images/icons/planning.png" alt="Planifier"/>
         </a>
         {{/if}}
         
       {{/if}}
    </td>
  </tr>
  
  
  {{/foreach}}
</table>