<!-- $Id$ -->
{{mb_script module=planningOp script=ccam_selector}}


<script type="text/javascript">
function checkPrint(form) {
  if (!checkForm(form)) {
    return false;
  }
  
  popPrint(form);
}

  
function popPrint(form) {
  var url = new Url('pmsi', 'print_planning');
  url.addElement(form._date_min);
  url.addElement(form._date_max);
  url.addElement(form._codes_ccam);
  url.addElement(form._prat_id);
  url.addElement(form._specialite);
  url.addElement(form.salle_id);
  url.addElement(form.type);
  url.addRadio(form._plage);
  url.addRadio(form._ranking);
  url.addRadio(form._cotation);
  url.addRadio(form._ccam_libelle);
  url.popup(900, 550, 'Planning');
}

function changeDate(input, sMin, sMax){
  var form = input.form;
  form._date_min.value = sMin;
  form._date_max.value = sMax;
  form._date_min_da.value = Date.fromDATE(sMin).toLocaleDate();
  form._date_max_da.value = Date.fromDATE(sMax).toLocaleDate();  
}

function changeDateCal(input) {
  var form = input.form;
  form.select_days[0].checked = false;
  form.select_days[1].checked = false;
}
</script>


<form name="paramFrm" action="?m=pmsi" method="post" onsubmit="return checkPrint(this)">
<input type="hidden" name="_class" value="COperation" />
<input type="hidden" name="_chir" value="{{$chir}}" />
<table class="main">
  <tr>
    <td>
      <table class="form">
        <tr>
          <th class="category" colspan="3">Choix de la p�riode</th>
        </tr>
        <tr>
          <th>{{mb_label object=$filter field="_date_min"}}</th>
          <td>{{mb_field object=$filter field="_date_min" form="paramFrm" canNull="false" onchange="changeDateCal(this)" register=true}} </td>
          <td rowspan="2">
            <input type="radio" name="select_days" onclick="changeDate(this, '{{$yesterday}}','{{$yesterday}}');" value="yesterday" /> 
            <label for="select_days_yesterday">Jour pr�c�dent</label>
            <br />
            <input type="radio" name="select_days" onclick="changeDate(this, '{{$now}}','{{$now}}');"  value="day" checked="checked" /> 
            <label for="select_days_day">Jour courant</label>
          </td>
        </tr>
        <tr>
           <th>{{mb_label object=$filter field="_date_max"}}</th>
           <td>{{mb_field object=$filter field="_date_max" form="paramFrm" canNull="false" onchange="changeDateCal(this)" register=true}} </td>
        </tr>
      </table>

    </td>
    <td>

      <table class="form">
        <tr>
          <th class="category" colspan="2">Choix des filtres</th>
        </tr>
        
        <tr>
          <th>{{mb_label object=$filter field=_ranking}}</th>
          <td>{{mb_field object=$filter field=_ranking emptyLabel=All typeEnum=radio}}</td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$filter field=_cotation}}</th>
          <td>{{mb_field object=$filter field=_cotation emptyLabel=All typeEnum=radio}}</td>
        </tr>

        <tr>
          <th>{{mb_label object=$filter field="_prat_id"}}</th>
          <td>
            <select name="_prat_id">
              <option value="0">&mdash; Tous</option>
              {{mb_include module=mediusers template=inc_options_mediuser list=$listPrat}}
            </select>
          </td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$filter field="_specialite"}}</th>
          <td>
            <select name="_specialite">
              <option value="0">&mdash; Toutes</option>
              {{mb_include module=mediusers template=inc_options_function list=$listSpec}}
            </select>
          </td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$filter field="salle_id"}}</th>
          <td>
            <select name="salle_id">
              <option value="0">&mdash; Toutes</option>
              {{foreach from=$listBlocs item=_bloc}}
              <optgroup label="{{$_bloc}}">
                {{foreach from=$_bloc->_ref_salles item=_salle}}
                <option value="{{$_salle->_id}}" {{if $_salle->_id == $filter->salle_id}}selected="selected"{{/if}}>
                  {{$_salle->nom}}
                </option>
                {{foreachelse}}
                <option value="" disabled="disabled">{{tr}}CSalle.none{{/tr}}</option>
                {{/foreach}}
              </optgroup>
              {{/foreach}}
            </select>
          </td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$filterSejour field="type"}}</th>
          <td>{{mb_field object=$filterSejour field="type" canNull=true emptyLabel="All"}}</td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$filter field="_codes_ccam"}}</th>
          <td><input type="text" name="_codes_ccam" size="10" value="" />
          <button type="button" class="search" onclick="CCAMSelector.init()">s�lectionner un code</button>
          <script type="text/javascript">
            CCAMSelector.init = function(){
              this.sForm  = 'paramFrm';
              this.sClass = '_class';
              this.sChir  = '_chir';
              this.sView  = '_codes_ccam';
              this.pop();
            }
            Main.add(function() {
              var oForm = getForm('paramFrm');
              var url = new Url('ccam', 'httpreq_do_ccam_autocomplete');
              url.autoComplete(oForm._codes_ccam, '', {
                minChars: 1,
                dropdown: true,
                width: '250px',
                updateElement: function(selected) {
                  $V(oForm._codes_ccam, selected.down('strong').innerHTML);
                }
              });
            });
          </script>
          </td>
        </tr>

      </table>

    </td>
  </tr>
  <tr>
    <td colspan="2">
      <table class="form">
        <tr>
          <th class="category" colspan="2">Param�tres d'affichage</th>
        </tr>
        {{assign var="class" value="CPlageOp"}}
            
        <tr>
          <th style="width: 50%">{{mb_label object=$filter field="_plage"}}</th>
          <td>  
            {{assign var="var" value="plage_vide"}}
            <label for="_plage">Oui</label>
            <input type="radio" name="_plage" value="1" {{if $conf.dPbloc.$class.$var == "1"}}checked="checked"{{/if}}/> 
            <label for="_plage">Non</label>
            <input type="radio" name="_plage" value="0" {{if $conf.dPbloc.$class.$var == "0"}}checked="checked"{{/if}}/> 
          </td>
        </tr>
         <tr>
          <th>{{mb_label object=$filter field="_ccam_libelle"}}</th>
          <td>  
            {{assign var="var" value="libelle_ccam"}}
            <label for="_ccam_libelle">Oui</label>
            <input type="radio" name="_ccam_libelle" value="1" {{if $conf.dPbloc.$class.$var == "1"}}checked="checked"{{/if}}/> 
            <label for="_ccam_libelle">Non</label>
            <input type="radio" name="_ccam_libelle" value="0" {{if $conf.dPbloc.$class.$var == "0"}}checked="checked"{{/if}}/> 
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td colspan="2">
      <table class="form"><tr><td class="button"><button class="print" type="button" onclick="checkPrint(this.form)">Afficher</button></td></tr></table>
    </td>
  </tr>
</table>
</form>