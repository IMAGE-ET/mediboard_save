{{if @$mode == "edit"}}
<script type="text/javascript">
  reloadAfterUploadFile = function() {
    var url = new Url;
    url.setModuleAction("dPpatients", "httpreq_vw_photo_identite");
    url.addParam("patient_id", "{{$patient->_id}}");
    url.addParam("mode", "edit");
    url.requestUpdate("{{$patient->_guid}}-identity", {waitingText: null});
  }

  deletePhoto = function(file_id){
  	var form = getForm('delete-photo-identite-form');
    $V(form.file_id, file_id);
  	return confirmDeletion(
  	    form, {
  	      typeName:'la photo',
  	      objName:'identite.jpg',
  	      ajax:1,
  	      target:'systemMsg'
  	    },{
  	      onComplete:reloadAfterUploadFile
  	    } );
  }
</script>
{{/if}}

{{assign var=file value=$patient->_ref_photo_identite}}

{{if !@$size}}
{{assign var=size value=128}}
{{/if}}

{{if $file->_id}}
  {{assign var=id value=$file->_id}}
  {{assign var=src value="?m=dPfiles&a=fileviewer&suppressHeaders=1&file_id=$id&phpThumb=1&w=$size"}}
{{else}}
  {{if $patient->_age < 15 && $patient->naissance && $patient->naissance != "0000-00-00"}}
    {{assign var=src value="images/pictures/identity_child.png"}}
  {{elseif $patient->sexe == 'm' && $patient->_age < 60}}
    {{assign var=src value="images/pictures/identity_male.png"}}
  {{elseif $patient->sexe == 'm' && $patient->_age >= 60}}
    {{assign var=src value="images/pictures/identity_male_old.png"}}
  {{elseif $patient->_age < 60}}
    {{assign var=src value="images/pictures/identity_female.png"}}
  {{else}}
    {{assign var=src value="images/pictures/identity_female_old.png"}}
  {{/if}}
{{/if}}
<img src="{{$src}}" style="width: {{$size}}px; border: 2px solid #777" alt="Identit�" />

{{if @$mode == "edit"}}
  <br />
  {{if !$patient->_ref_photo_identite->_id}}
    <button type="button" class="search" onclick="uploadFile('{{$patient->_class_name}}', '{{$patient->_id}}', null, 'identite.jpg')">{{tr}}Browse{{/tr}}</button>
  {{else}}
    <button onclick="deletePhoto({{$patient->_ref_photo_identite->_id}})" class="trash" type="button">{{tr}}Delete{{/tr}}</button>
  {{/if}}
{{/if}}