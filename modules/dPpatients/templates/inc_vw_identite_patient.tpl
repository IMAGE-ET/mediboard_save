{{mb_default var=tooltip value=0}}

{{if "dmp"|module_active}}
  {{mb_script module="dmp" script="cdmp" ajax="true"}}
{{/if}}

<table class="form">
  <tr {{if $patient->deces}}class="hatching"{{/if}}>
    <th class="title text" colspan="5">
      {{mb_include module=dPpatients template=inc_view_ins_patient patient=$patient}}

      {{mb_include module=system template=inc_object_idsante400 object=$patient}}
      {{mb_include module=system template=inc_object_history object=$patient}}
      {{mb_include module=system template=inc_object_notes object=$patient}}
      {{$patient}} {{mb_include module=patients template=inc_vw_ipp ipp=$patient->_IPP}}
    </th>
  </tr>
  {{if !$tooltip}}
    <tr>
      <th class="category" colspan="3" style="width: 50%;">
        Identit�
      </th>
      <th class="category" colspan="2" style="width: 50%;">Coordonn�es</th>
    </tr>
  {{/if}}

  <tr>
    <td rowspan="4" class="narrow" style="vertical-align: middle; text-align: center;">
      {{mb_include module=patients template=inc_vw_photo_identite mode="read" size="64"}}
    </td>
    <th>{{mb_label object=$patient field="nom"}}</th>
    <td>{{mb_value object=$patient field="nom"}}</td>
    <th rowspan="2">{{mb_label object=$patient field="adresse"}}</th>
    <td rowspan="2" class="text">{{mb_value object=$patient field="adresse"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="prenom"}}</th>
    <td>{{mb_value object=$patient field="prenom"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="sexe"}}</th>
    <td>{{mb_value object=$patient field="sexe"}}</td>
    <th>{{mb_label object=$patient field="ville"}}</th>
    <td>
      {{mb_value object=$patient field="cp"}}
      {{mb_value object=$patient field="ville"}}
    </td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="_age"}}</th>
    <td>
      {{mb_value object=$patient field="_age"}}
    </td>
    <th>{{mb_label object=$patient field="tel"}}</th>
    <td>{{mb_value object=$patient field="tel"}}</td>
  </tr>

  <tr>
    <th colspan="2">{{mb_label object=$patient field="naissance"}}</th>
    <td>{{mb_value object=$patient field="naissance"}}</td>
    <th>{{mb_label object=$patient field="tel2"}}</th>
    <td>{{mb_value object=$patient field="tel2"}}</td>
  </tr>
  
  {{if $patient->deces}}
  <tr>
    <th colspan="2">{{mb_label object=$patient field="deces"}}</th>
    <td colspan="3">{{mb_value object=$patient field="deces"}}</td>
  </tr
  {{/if}}
  
  <tr>
    <th colspan="2">{{mb_label object=$patient field="nom_jeune_fille"}}</th>
    <td>{{mb_value object=$patient field="nom_jeune_fille"}}</td>
    <th>{{mb_label object=$patient field="tel_autre"}}</th>
    <td>{{mb_value object=$patient field="tel_autre"}}</td>
  </tr>
  
  {{if $patient->rques || $patient->matricule || $patient->avs}}
  <tr>
    <th colspan="2">{{mb_label object=$patient field="rques"}}</th>
    <td class="text">
      {{mb_value object=$patient field="rques"}}
    </td>
    {{if $conf.ref_pays == 1}}
      <th>{{mb_label object=$patient field="matricule"}}</th>
      <td>{{mb_value object=$patient field="matricule"}}</td>
    {{else}}
      <th>{{mb_label object=$patient field="avs"}}</th>
      <td>{{mb_value object=$patient field="avs"}}</td>
    {{/if}}
  </tr>
  {{/if}}
  <tr>
    <td class="button" colspan="10">
      <button type="button" class="search" onclick="Patient.view('{{$patient->_id}}')">
        Dossier complet
      </button>
      
      <!-- Dossier r�sum� -->
      <button class="search" onclick="new Url('dPcabinet', 'vw_resume').addParam('patient_id', '{{$patient->_id}}').popup(800, 500, '{{tr}}Summary{{/tr}}');">
        {{tr}}Summary{{/tr}}
      </button>
      
      <button type="button" class="print" onclick="Patient.print('{{$patient->_id}}')">
        {{tr}}Print{{/tr}}
      </button>
      
      {{if $canPatients->edit}}
        {{if !@$useVitale}}
          {{assign var=useVitale value=0}}
        {{/if}}
        <button type="button" class="edit" onclick="Patient.edit('{{$patient->_id}}', '{{$useVitale}}')">
          {{tr}}Modify{{/tr}}
          {{if $useVitale}}avec Vitale{{/if}}
        </button>
      {{/if}}

      {{if "dmp"|module_active}}
        <button type="button" class="dmp-creer" onclick="Cdmp.openDMP('{{$patient->_id}}')">
          {{tr}}DMP{{/tr}}
        </button>
      {{/if}}
      
      {{if $can->admin}} 
      <form name="Purge-{{$patient->_guid}}" action="?m={{$m}}&amp;tab=vw_idx_patients" method="post" onsubmit="return confirmCreation(this)">
        <input type="hidden" name="dosql" value="do_patients_aed" />
        <input type="hidden" name="tab" value="vw_idx_patients" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="_purge" value="0" />
        <input type="hidden" name="patient_id" value="{{$patient->_id}}" />
        
        <script type="text/javascript">
          confirmPurge = function(form) {
            if (confirm("ATTENTION : Vous �tes sur le point de purger le dossier d'un patient !")) {
              form._purge.value = "1";
              confirmDeletion(form,  {
                typeName:'le patient',
                objName:'{{$patient->_view|smarty:nodefaults|JSAttribute}}'
              });
            }
          }
        </script>
        <button type="button" class="cancel" onclick="confirmPurge(this.form);">
          {{tr}}Purge{{/tr}}
        </button>
      </form>
      {{/if}}
      
      {{if $app->user_prefs.vCardExport}}
      <button type="button" class="vcard" onclick="Patient.exportVcard('{{$patient->_id}}')">
        {{tr}}Export{{/tr}}
      </button>
      {{/if}}
    </td>
  </tr>
  
  {{if ($patient->medecin_traitant || $patient->_ref_medecins_correspondants|@count)}}
    <tr>
      <th class="category" colspan="5">Correspondants m�dicaux</th>
    </tr>
    
    <tr>
      <td colspan="5" class="text">
        {{assign var=medecin value=$patient->_ref_medecin_traitant}}
        {{if $medecin->_id}}
        <span onmouseover="ObjectTooltip.createEx(this, '{{$medecin->_guid}}');">
          <strong>{{$medecin}}</strong> ;
        </span>
        {{/if}}
        {{foreach from=$patient->_ref_medecins_correspondants item=curr_corresp}}
          {{assign var=medecin value=$curr_corresp->_ref_medecin}}
          <span onmouseover="ObjectTooltip.createEx(this, '{{$medecin->_guid}}');">
            {{$medecin}} ;
          </span>
        {{/foreach}}
      </td>
    </tr>
  {{/if}}
</table>