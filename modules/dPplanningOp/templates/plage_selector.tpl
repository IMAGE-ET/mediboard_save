<script type="text/javascript">

function setClose(date, salle_id) {
  var form = document.frmSelector;
  
  var list = form.list;
  if(date == '') {
    date = form._date.value;
  }
  if(salle_id == '') {
    salle_id = form._salle_id.value;
  }
  
  var plage_id = $V(list);
  if (!plage_id) {
    alert('Vous n\'avez pas selectionn� de plage ou la plage selectionn�e n\'est plus disponible � la planification.\n\nPour plus d\'information, veuillez contacter le responsable du bloc');
  	return;
  }
   
  var adm = $V(form.admission);
  var typeHospi = "ambu";
  var hour_entree = form.hour_jour.value;
  var min_entree  = form.min_jour.value;
  // passage en hospi complete si admission == veille
  if(adm == "veille"){
    typeHospi = "comp";
    hour_entree = form.hour_veille.value;
    min_entree  = form.min_veille.value;
  }
    
  window.opener.PlageOpSelector.set(plage_id, salle_id, date, adm, typeHospi, hour_entree, min_entree);  
  window.close();
}  

Main.add(function () {
  var oFormSejour = window.opener.document.editSejour;
  var form = document.frmSelector;   
  $V(form.admission, "aucune");
  if (!oFormSejour.sejour_id.value) {
    $V(form.admission, ["ambu", "exte"].include(oFormSejour.type.value) ? "jour" : "veille");
  }
  Control.Tabs.create('main_tab_group', true);
});
</script>

<table class="main">
  <tr>
    <th class="title" style="font-size: 16px;">
      {{assign var=prev    value=-1}}
      {{assign var=next    value=1}}
      {{assign var=current value=0}}
      <a style="float:left;" href="?m=dPplanningOp&amp;a=plage_selector&amp;dialog=1&amp;curr_op_hour={{$curr_op_hour}}&amp;curr_op_min={{$curr_op_min}}&amp;chir={{$chir}}&amp;date_plagesel={{$listMonthes.$prev.date}}&amp;group_id={{$group_id}}&amp;operation_id={{$operation_id}}">&lt; &lt;</a>
      <a style="float:right;" href="?m=dPplanningOp&amp;a=plage_selector&amp;dialog=1&amp;curr_op_hour={{$curr_op_hour}}&amp;curr_op_min={{$curr_op_min}}&amp;chir={{$chir}}&amp;date_plagesel={{$listMonthes.$next.date}}&amp;group_id={{$group_id}}&amp;operation_id={{$operation_id}}">&gt; &gt;</a>
      <div>
        <form action="?" name="chgDate" method="get">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="a" value="plage_selector" />
        <input type="hidden" name="dialog" value="1" />
        <input type="hidden" name="curr_op_hour" value="{{$curr_op_hour}}" />
        <input type="hidden" name="curr_op_min" value="{{$curr_op_min}}" />
        <input type="hidden" name="chir" value="{{$chir}}" />
        <input type="hidden" name="group_id" value="{{$group_id}}" />
        <input type="hidden" name="operation_id" value="{{$operation_id}}" />
        <select name="date_plagesel" onchange="this.form.submit()">
        {{foreach from=$listMonthes key=curr_key_month item=curr_month}}
          <option value="{{$curr_month.date}}" {{if $curr_key_month == $current}}selected="selected"{{/if}}>
            {{$curr_month.month}}
          </option>  
        {{/foreach}}
        </select>
        </form>
      </div>
    </th>
  </tr>
</table>

<form action="?" name="frmSelector" method="get">
<input type="hidden" name="m" value="dPplanningOp" />
<input type="hidden" name="a" value="plage_selector" />
<input type="hidden" name="dialog" value="1" />
<input type="hidden" name="_salle_id" value="" />
<input type="hidden" name="_date" value="" />

<table class="main">  
  <tr>
    <td class="greedyPane">
      {{if $listPlages|@count > 1}}
      <div class="small-warning">
        Plusieurs blocs sont disponible, veillez � bien choisir le bloc souhait�
      </div>
      {{elseif $listPlages|@count == 0}}
      <div class="small-info">
        Vous n'avez pas de plage ce mois-ci, vous pouvez contacter le responsable de bloc pour ajouter une vacation
      </div>
      {{/if}}
      <ul id="main_tab_group" class="control_tabs">
        {{foreach from=$listPlages key=_key_bloc item=_blocplage}}
        {{assign var=_bloc value=$blocs.$_key_bloc}}
        <li><a href="#bloc-{{$_bloc->_id}}">{{$_bloc->_view}} ({{$_blocplage|@count}})</a></li>
        {{/foreach}}
      </ul>
      <hr class="control_tabs" />
      {{foreach from=$listPlages key=_key_bloc item=_blocplage}}
      {{assign var=_bloc value=$blocs.$_key_bloc}}
      <div id="bloc-{{$_bloc->_id}}" style="display:none">
      <table class="tbl">
        <tr>
          <th class="category" colspan="2">
            Choisir une date
          </th>
        </tr>
        {{foreach from=$_blocplage item=_plage}}
        <tr>
          <td>
            {{assign var="pct" value=$_plage->_fill_rate}}
            {{if $pct > 100}}
              {{assign var="over" value=1}}
              {{assign var="pct" value=100}}
            {{else}}
              {{assign var="over" value=0}}
            {{/if}}
            
            {{if $pct < 100}}
              {{assign var="backgroundClass" value="normal"}}
            {{elseif !$over}}
              {{assign var="backgroundClass" value="booked"}}
            {{else}}
              {{assign var="backgroundClass" value="full"}}
            {{/if}} 
            
            {{if $_plage->spec_id}}
            <img src="images/icons/user-function.png" style="float: left" />
            {{else}}
            <img src="images/icons/user.png" style="float: left" />
            {{/if}}
            <div class="progressBar" {{if $_plage->spec_id}}style="height: 25px;"{{/if}}>
              <div class="bar {{$backgroundClass}}" style="width: {{$pct}}%;"></div>
              <div class="text" style="text-align: left">
                <label 
                  for="list_{{$_plage->_id}}"
                  onmouseover="ObjectTooltip.createDOM(this, 'plage-{{$_plage->_id}}')" 
                  {{if $resp_bloc ||
                    (
                     (!$over || !$conf.dPbloc.CPlageOp.locked)
                     && ($_plage->date >= $date_min)
                     && (($_plage->_nb_operations < $_plage->max_intervention) || ($_plage->max_intervention == 0) || ($_plage->max_intervention == ""))
                    )
                  }}ondblclick="setClose('{{$_plage->date}}', '{{$_plage->salle_id}}')"{{/if}}
                >
                  {{$_plage->date|date_format:"%a %d"}} -
                  {{$_plage->debut|date_format:$conf.time}} -
                  {{$_plage->fin|date_format:$conf.time}}
                  &mdash; {{$_plage->_ref_salle->_view}}
                  {{if $_plage->spec_id}}
                  <br />{{$_plage->_ref_spec->_view|truncate:50}}
                  {{/if}}
                </label>
              </div>
            </div>
            <div id="plage-{{$_plage->_id}}" style="display: none; width: 250px;">
              <table class="tbl">
              	<tr>
              		<th class="category" colspan="2">
              			Plage de {{$_plage->debut|date_format:$conf.time}}-{{$_plage->fin|date_format:$conf.time}}
              		</th>
              	</tr>
                {{foreach from=$_plage->_ref_operations item=curr_op}}
                <tr>
                  <td>
                    {{if $curr_op->time_operation && $curr_op->time_operation != "00:00:00"}}
                      {{$curr_op->time_operation|date_format:$conf.time}} (valid�)
                    {{elseif $curr_op->horaire_voulu && $curr_op->horaire_voulu != "00:00:00"}}
                      {{$curr_op->horaire_voulu|date_format:$conf.time}} (souhait�)
                    {{else}}
                      Pas d'horaire
                    {{/if}}
                  </td>
                  <td class="text">
                    {{if $curr_op->libelle}}
                      <em>{{$curr_op->libelle}}</em>
                    {{else}}
                      {{$curr_op->_text_codes_ccam}}
                    {{/if}}
                  </td>
                </tr>
                {{foreachelse}}
                <tr>
                  <td colspan="2">Aucune intervention</td>
                </tr>
                {{/foreach}}
              </table>
            </div>
         	 </td>
          <td style="text-align: center;" class="narrow">
            {{if $resp_bloc ||
              (
               (!$over || !$conf.dPbloc.CPlageOp.locked)
               && ($_plage->date >= $date_min)
               && (($_plage->_nb_operations < $_plage->max_intervention) || ($_plage->max_intervention == 0) || ($_plage->max_intervention == ""))
              )
            }}
            <input type="radio" name="list" value="{{$_plage->plageop_id}}"
               ondblclick="setClose('{{$_plage->date}}', '{{$_plage->salle_id}}')"
               onclick="document.frmSelector._date.value='{{$_plage->date}}'; document.frmSelector._salle_id.value='{{$_plage->salle_id}}';"/>
            {{else}}
              <img src="images/icons/warning.png" 
                {{if $_plage->max_intervention && $_plage->_nb_operations >= $_plage->max_intervention}}
                  title="Nombre d'interventions maximum atteint ({{$_plage->_nb_operations}}/{{$_plage->max_intervention}})"
                {{elseif $_plage->date < $date_min}}
                  title="Impossible de planifier � cette date"
                {{/if}}
              />
            {{/if}}
          </td>
        </tr>
        {{/foreach}}
      </table>
      </div>
      {{/foreach}}
    </td>
    <td>
      <table class="form">
        <tr>
          <th colspan="3" class="category">
            Admission du patient
          </th>
        </tr>
        <tr>
          <td>
            <input type="radio" name="admission" value="veille" />
          </td>
          <td>
            <label for="admission_veille">La veille</label> �
          </td>
          <td>
            <select name="hour_veille">
              {{foreach from=$hours|smarty:nodefaults item=hour}}
              <option value="{{$hour}}" {{if $heure_entree_veille == $hour}}selected="selected"{{/if}}>{{$hour}}</option>
              {{/foreach}}
            </select>
            h
            <select name="min_veille">
              {{foreach from=$mins|smarty:nodefaults item=min}}
              <option value="{{$min}}">{{$min}}</option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <td>
            <input type="radio" name="admission" value="jour" />
          </td>
          <td>
            <label for="admission_jour">Le jour m�me</label> �
          </td>
          <td>
            <select name="hour_jour">
              {{foreach from=$hours|smarty:nodefaults item=hour}}
              <option value="{{$hour}}" {{if $heure_entree_jour == $hour}}selected="selected"{{/if}}>{{$hour}}</option>
              {{/foreach}}
            </select>
            h
            <select name="min_jour">
              {{foreach from=$mins|smarty:nodefaults item=min}}
              <option value="{{$min}}">{{$min}}</option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <td>
            <input type="radio" name="admission" value="aucune" />
          </td>
          <td colspan="2">
            <label for="admission_aucune">Ne pas modifier</label>
          </td>
        </tr>
        <tr>
          <th class="category" colspan="3">
            L�gende
          </th>
        </tr>
        <tr>
          <td>
            <img src="images/icons/user.png" />
          </td>
          <td colspan="2">plage personnelle</td>
        </tr>
        <tr>
          <td>
            <img src="images/icons/user-function.png" />
          </td>
          <td colspan="2">plage de sp�cialit�</td>
        </tr>
        <tr>
          <td style="width:10px;">
            <div class="progressBar">
              <div class="bar full"></div>
            </div>
          </td>
          <td colspan="2">plage pleine</td>
        </tr>
        <tr>
          <td style="width:10px;">
            <div class="progressBar">
              <div class="bar booked"></div>
            </div>
          </td>
          <td colspan="2">plage presque pleine</td>
        </tr>
        <tr>
          <td style="width:10px;">
            <div class="progressBar">
              <div class="bar normal" style="width: 60%;"></div>
            </div>
          </td>
          <td colspan="2">taux de remplissage</td>
        </tr>
        <tr>
          <td colspan="3" class="button">
            <button class="cancel" type="button" onclick="window.close()">{{tr}}Cancel{{/tr}}</button>
            <button class="tick" type="button" onclick="setClose('', '')">{{tr}}OK{{/tr}}</button>          
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</form>