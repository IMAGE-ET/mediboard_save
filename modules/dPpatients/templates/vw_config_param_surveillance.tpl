<div class="small-warning">
  Ces param�tres <strong>ne sont pas</strong> ceux qui sont utilis�s dans les volets "Surveillance" ou "Constantes". <br />
  Il sont utilis�s pour la r�cup�ration automatique depuis des syst�mes automatiques de mesures (au bloc par exemple).
</div>

<script>
Main.add(function(){
  Control.Tabs.create("main_tabs_types", true);
});
</script>

<ul id="main_tabs_types" class="control_tabs">
  <li><a href="#view-types">Types</a></li>
  <li><a href="#view-units">Unit�s</a></li>
</ul>

<table class="main layout" id="view-types" style="display: none;">
  <tr>
    <td style="width: 50%" id="list-types">
      <a class="button new" href="?m=dPpatients&amp;tab=vw_config_param_surveillance&amp;value_type_id=0" style="float: right;">
        {{tr}}CObservationValueType-title-create{{/tr}}
      </a>

      <table class="main tbl">
        <tr>
          <th class="narrow">{{mb_title class=CObservationValueType field=coding_system}}</th>
          <th class="narrow">{{mb_title class=CObservationValueType field=code}}</th>
          <th class="narrow">{{mb_title class=CObservationValueType field=datatype}}</th>
          <th>{{mb_title class=CObservationValueType field=label}}</th>
          <th>{{mb_title class=CObservationValueType field=desc}}</th>
          <th class="narrow"></th>
        </tr>
        
        {{foreach from=$types item=_type}}
          <tr {{if $_type->_id == $type->_id}} class="selected" {{/if}}>
            <td>{{mb_value object=$_type field=coding_system}}</td>
            <td>{{mb_value object=$_type field=code}}</td>
            <td>{{mb_value object=$_type field=datatype}}</td>
            <td>{{mb_value object=$_type field=label}}</td>
            <td>{{mb_value object=$_type field=desc}}</td>
            <td>
              <a class="button edit notext compact" href="?m=dPpatients&amp;tab=vw_config_param_surveillance&amp;value_type_id={{$_type->_id}}">
                {{tr}}Edit{{/tr}}
              </a>
            </td>
          </tr>
        {{/foreach}}
      </table>
    </td>
    <td>
      <form name="edit-value-type" action="?m=dPpatients&amp;{{$actionType}}={{$action}}" method="post" onsubmit="return checkForm(this)">
        <input type="hidden" name="m" value="dPpatients" />
        <input type="hidden" name="@class" value="CObservationValueType" />
        {{mb_key object=$type}}
        
        <table class="main form">
          {{mb_include module=system template=inc_form_table_header css_class="text" object=$type}}
          <tr>
            <th>{{mb_label object=$type field=coding_system}}</th>
            <td>
              {{mb_field object=$type field=coding_system list="edit-value-type-coding_systems"}}

              {{if "patientMonitoring"|module_active}}
                <datalist id="edit-value-type-coding_systems">
                  {{foreach from="CObservationValueCodingSystem"|static:"_list" item=_name key=_key}}
                    <option value="{{$_key}}">{{$_name}}</option>
                  {{/foreach}}
                </datalist>
              {{/if}}
            </td>
          </tr>
          <tr>
            <th>{{mb_label object=$type field=label}}</th>
            <td>{{mb_field object=$type field=label}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$type field=code}}</th>
            <td>{{mb_field object=$type field=code}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$type field=datatype}}</th>
            <td>{{mb_field object=$type field=datatype}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$type field=desc}}</th>
            <td>{{mb_field object=$type field=desc}}</td>
          </tr>
          <tr>
            <td colspan="2" class="button">
              {{if $type->_id}}
                <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
                <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'', objName:'{{$type->_view|smarty:nodefaults|JSAttribute}}'})">
                  {{tr}}Delete{{/tr}}
                </button>
              {{else}}
                <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
              {{/if}}
            </td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
</table>

<table class="main layout" id="view-units" style="display: none;">
  <tr>
    <td style="width: 50%" id="list-units">
      <a class="button new" href="?m=dPpatients&amp;tab=vw_config_param_surveillance&amp;value_unit_id=0" style="float: right;">
        {{tr}}CObservationValueUnit-title-create{{/tr}}
      </a>

      <table class="main tbl">
        <tr>
          <th class="narrow">{{mb_title class=CObservationValueUnit field=coding_system}}</th>
          <th class="narrow">{{mb_title class=CObservationValueUnit field=code}}</th>
          <th>{{mb_title class=CObservationValueUnit field=label}}</th>
          <th>{{mb_title class=CObservationValueUnit field=desc}}</th>
          <th class="narrow"></th>
        </tr>
        
        {{foreach from=$units item=_unit}}
          <tr {{if $_unit->_id == $unit->_id}} class="selected" {{/if}}>
            <td>{{mb_value object=$_unit field=coding_system}}</td>
            <td>{{mb_value object=$_unit field=code}}</td>
            <td>{{mb_value object=$_unit field=label}}</td>
            <td>{{mb_value object=$_unit field=desc}}</td>
            <td>
              <a class="button edit notext compact" href="?m=dPpatients&amp;tab=vw_config_param_surveillance&amp;value_unit_id={{$_unit->_id}}">
                {{tr}}Edit{{/tr}}
              </a>
            </td>
          </tr>
        {{/foreach}}
      </table>
    </td>
    <td>
      <form name="edit-value-unit" action="?m=dPpatients&amp;{{$actionType}}={{$action}}" method="post" onsubmit="return checkForm(this)">
        <input type="hidden" name="m" value="dPpatients" />
        <input type="hidden" name="@class" value="CObservationValueUnit" />
        {{mb_key object=$unit}}
        
        <table class="main form">
          {{mb_include module=system template=inc_form_table_header css_class="text" object=$unit}}
          <tr>
            <th>{{mb_label object=$unit field=coding_system}}</th>
            <td>
              {{mb_field object=$unit field=coding_system list="edit-value-unit-coding_systems"}}

              {{if "patientMonitoring"|module_active}}
                <datalist id="edit-value-unit-coding_systems">
                  {{foreach from="CObservationValueCodingSystem"|static:"_list" item=_name key=_key}}
                    <option value="{{$_key}}">{{$_name}}</option>
                  {{/foreach}}
                </datalist>
              {{/if}}
            </td>
          </tr>
          <tr>
            <th>{{mb_label object=$unit field=label}}</th>
            <td>{{mb_field object=$unit field=label}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$unit field=code}}</th>
            <td>{{mb_field object=$unit field=code}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$unit field=desc}}</th>
            <td>{{mb_field object=$unit field=desc}}</td>
          </tr>
          <tr>
            <td colspan="2" class="button">
              {{if $unit->_id}}
                <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
                <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'', objName:'{{$unit->_view|smarty:nodefaults|JSAttribute}}'})">
                  {{tr}}Delete{{/tr}}
                </button>
              {{else}}
                <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
              {{/if}}
            </td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
</table>
