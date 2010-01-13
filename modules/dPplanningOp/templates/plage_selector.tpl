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
    <th class="category">
      {{assign var=prev    value=-1}}
      {{assign var=next    value=1}}
      {{assign var=current value=0}}
      <a style="float:left;" href="?m=dPplanningOp&amp;a=plage_selector&amp;dialog=1&amp;curr_op_hour={{$curr_op_hour}}&amp;curr_op_min={{$curr_op_min}}&amp;chir={{$chir}}&amp;date={{$listMonthes.$prev.date}}&amp;group_id={{$group_id}}&amp;operation_id={{$operation_id}}">&lt; &lt;</a>
      <a style="float:right;" href="?m=dPplanningOp&amp;a=plage_selector&amp;dialog=1&amp;curr_op_hour={{$curr_op_hour}}&amp;curr_op_min={{$curr_op_min}}&amp;chir={{$chir}}&amp;date={{$listMonthes.$next.date}}&amp;group_id={{$group_id}}&amp;operation_id={{$operation_id}}">&gt; &gt;</a>
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
        <select name="date" onchange="this.form.submit()">
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
            <div class="progressBar">
              <div class="bar {{$backgroundClass}}" style="width: {{$pct}}%;"></div>
              <div class="text" style="text-align: left">
                <label 
                  for="list_{{$_plage->_id}}"
                  onmouseover="ObjectTooltip.createDOM(this, 'plage-{{$_plage->_id}}')" 
                  {{if $resp_bloc ||
                    (
                     (!$over || !$dPconfig.dPbloc.CPlageOp.locked)
                     && ($_plage->date >= $date_min)
                     && (($_plage->_nb_operations < $_plage->max_intervention) || ($_plage->max_intervention == 0) || ($_plage->max_intervention == ""))
                    )
                  }}ondblclick="setClose('{{$_plage->date}}', '{{$_plage->salle_id}}')"{{/if}}
                >
                  {{$_plage->date|date_format:"%a %d"}} -
                  {{$_plage->debut|date_format:"%Hh%M"}} -
                  {{$_plage->fin|date_format:"%Hh%M"}} 
                  &mdash; {{$_plage->_ref_salle->_shortview}}
                </label>
              </div>
            </div>
            <div id="plage-{{$_plage->_id}}" style="display: none; width: 200px;">
              <table class="tbl">
              	<tr>
              		<th class="category">
              			Plage de {{$_plage->debut|date_format:'%Hh%M'}}-{{$_plage->fin|date_format:'%Hh%M'}}
              		</th>
              	</tr>
                {{foreach from=$_plage->_ref_operations item=curr_op}}
                <tr>
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
                  <td>Aucune intervention</td>
                </tr>
                {{/foreach}}
              </table>
            </div>
          </td>
          <td style="width: 1%;">
            {{if $resp_bloc ||
              (
               (!$over || !$dPconfig.dPbloc.CPlageOp.locked)
               && ($_plage->date >= $date_min)
               && (($_plage->_nb_operations < $_plage->max_intervention) || ($_plage->max_intervention == 0) || ($_plage->max_intervention == ""))
              )
            }}
            <input type="radio" name="list" value="{{$_plage->plageop_id}}"
               ondblclick="setClose('{{$_plage->date}}', '{{$_plage->salle_id}}')"
               onclick="document.frmSelector._date.value='{{$_plage->date}}'; document.frmSelector._salle_id.value='{{$_plage->salle_id}}';"/>
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
            <button class="tick" type="button" onclick="setClose('', '')">{{tr}}Select{{/tr}}</button>          
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</form>