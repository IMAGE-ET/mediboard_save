{{assign var=codable_id value=$_codable->_id}}
{{if @$entCCAM.$codable_id}}
<div class="error">{{$entCCAM.$codable_id}}</div>
{{else}}
<div class="message">Ent�te CCAM correctement export�</div>
{{/if}}
