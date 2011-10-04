<tr>
    <th>AntÚcÚdents</th>
    <td>
      {{if $dossier_medical->_count_antecedents}}
        {{foreach from=$dossier_medical->_ref_antecedents_by_type key=curr_type item=list_antecedent}}
        {{foreach from=$list_antecedent item=_antecedent}}
          {{if $_antecedent->type    }} {{mb_value object=$_antecedent field=type    }} {{/if}}
          {{if $_antecedent->appareil}} {{mb_value object=$_antecedent field=appareil}} {{/if}}
          {{if $_antecedent->date}}
            [{{mb_value object=$_antecedent field=date}}] : 
          {{/if}}
          {{$_antecedent->rques}}
          <br />
        {{/foreach}}
        {{/foreach}}
      {{else}}
         {{tr}}CAntecedent.unknown{{/tr}}
      {{/if}}
    </td>
  </tr>
  
  <tr>
    <th>Traitements</th>
    <td>
      {{if $dossier_medical->_ref_prescription}}
        {{foreach from=$dossier_medical->_ref_prescription->_ref_prescription_lines item=_line}}    
         {{$_line->_ucd_view}} ({{$_line->_forme_galenique}})
         <br />
        {{/foreach}}
        {{foreach from=$dossier_medical->_ref_traitements item=curr_trmt}}
          {{$curr_trmt->traitement}}
          <br />
        {{foreachelse}}
        {{if !($dossier_medical->_ref_prescription && $dossier_medical->_ref_prescription->_ref_prescription_lines|@count)}}
          {{tr}}CTraitement.unknown{{/tr}}
        {{/if}}
        {{/foreach}}
      {{/if}}
    </td>
  </tr>  
  
  <tr>
    <th>Diagnostics CIM</th>
    <td>
      {{foreach from=$dossier_medical->_ext_codes_cim item=curr_code}}
        {{$curr_code->code}} - {{$curr_code->libelle}}<br />
      {{foreachelse}}
        {{tr}}CDossierMedical-codes_cim.unknown{{/tr}} <br />
      {{/foreach}}
    </td>
  </tr>     
  
  <tr>
    <th>{{mb_label object=$consult field="histoire_maladie"}}</th>
    <td>{{mb_value object=$consult field="histoire_maladie"}}</td>
  </tr>
          
  <tr>
    <th>{{mb_label object=$consult field="rques"}}</th>
    <td>{{mb_value object=$consult field="rques"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$consult field="examen"}}</th>
    <td>{{mb_value object=$consult field="examen"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$consult field="traitement"}}</th>
    <td>{{mb_value object=$consult field="traitement"}}</td>
  </tr>

  <tr>
    <th>{{mb_label object=$consult field="conclusion"}}</th>
    <td>{{mb_value object=$consult field="conclusion"}}</td>
  </tr>   