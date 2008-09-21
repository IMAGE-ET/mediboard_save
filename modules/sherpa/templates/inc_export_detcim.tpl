{{assign var=codable_id value=$_codable->_id}}
{{assign var=codable_class value=$_codable->_class_name}}

<tr>
  <td colspan="11">
		<!-- Cas du s�jour -->
		{{if $codable_class == "CSejour"}}
		{{assign var=_sejour value=$_codable}}
    Diagnostic Principal : {{$_sejour->DP}}<br />
    {{if $_sejour->DR}}
    Diagnostic Reli� : {{$_sejour->DR}}<br />
    {{/if}}

    {{assign var=dossier_medical value=$_sejour->_ref_dossier_medical}}
    {{if $dossier_medical}}
	    {{foreach from=$dossier_medical->_codes_cim item=code_cim}}
	    Diagnostic associ� : {{$code_cim}}<br />
	    {{/foreach}}
    {{/if}}
    {{/if}}

		<!-- Cas du de l'intervention -->
		{{if $codable_class == "COperation"}}
		{{assign var=_operation value=$_codable}}
		Anapath: {{mb_value object=$_operation field=anapath}}<br />
		Labo  :  {{mb_value object=$_operation field=labo   }}<br />
		{{/if}}
    
  </td>
  <td>
    {{if array_key_exists($codable_id, $detCIM.$codable_class)}}
		{{foreach from=$detCIM.$codable_class.$codable_id item=msg}}
		{{if $msg}}
		<div class="error">{{$msg}}</div>
		{{else}}
		<div class="message">D�tail CIM correctement export�</div>
		{{/if}}
		{{/foreach}}
		{{else}}
		<em>Aucun d�tail CIM export�</em>
		{{/if}}
  </td>
</tr>

