
{{assign var=field_name value=$ex_field->name}}

{{if $ex_field->report_level}}
  {{assign var=reported_from value=$ex_object->_reported_fields.$field_name}}
	
  {{if $reported_from}}
	  <img src="./images/icons/reported.png" style="outline: 0 solid green; background: #7f7;"
		     title="Valeur report�e depuis {{$reported_from->_ref_ex_class->name}} - {{mb_value object=$reported_from->_ref_last_log field=date}} - {{$reported_from->_ref_object}}"  />
	{{else}}
	  <img src="./images/icons/reported.png" title="Valeur non report�e" class="opacity-50" />
	{{/if}}
{{/if}}