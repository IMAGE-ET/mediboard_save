{{if $boardItem}}
      <table class="tbl" style="font-size: 9px;">
{{else}}
      <table class="tbl">
{{/if}}
        <tr>
          <th class="title" colspan="5">
            {{if $boardItem}}
              Interventions
            {{else}}
              Op�rations
            {{/if}}
          </th>
        </tr>
        <tr>
          <th>Patient</th>
          <th>Actes m�dicaux</th>
          <th>Heure pr�vue</th>
          <th>Dur�e</th>
          {{if !$boardItem}}
            <th>Compte-rendu</th>
          {{/if}}
        </tr>
        {{if $urgences}}
        {{foreach from=$listUrgences item=curr_op}}
        <tr>
          <td class="text">
<!--            <a href="?m=dPpatients&amp;tab=vw_full_patients&amp;patient_id={{$curr_op->_ref_sejour->_ref_patient->patient_id}}">-->
            <a href="index.php?m=dPcabinet&amp;tab=vw_dossier&amp;patSel={{$curr_op->_ref_sejour->_ref_patient->patient_id}}"> 
              {{$curr_op->_ref_sejour->_ref_patient->_view}}
            </a>
          </td>
          <td class="text">
            <a href="index.php?m={{$m}}&amp;tab=vw_edit_urgence&amp;operation_id={{$curr_op->operation_id}}">
              {{if $curr_op->libelle}}
                <em>[{{$curr_op->libelle}}]</em>
                <br />
              {{/if}}
              {{foreach from=$curr_op->_ext_codes_ccam item=curr_code}}
              <strong>{{$curr_code->code}}</strong>
              {{if !$board}}
              : {{$curr_code->libelleLong}}
              {{/if}}
              <br />
              {{/foreach}}
            </a>
          </td>
          <td style="text-align: center;">
            {{if $curr_op->annulee}}
            [ANNULEE]
            {{else}}
            <a href="index.php?m={{$m}}&amp;tab=vw_edit_urgence&amp;operation_id={{$curr_op->operation_id}}">
              {{$curr_op->_datetime|date_format:"le %d/%m/%Y � %Hh%M"}}
            </a>
            {{/if}}
          </td>
          <td style="text-align: center;">
            <a href="index.php?m={{$m}}&amp;tab=vw_edit_urgence&amp;operation_id={{$curr_op->operation_id}}">
              {{$curr_op->temp_operation|date_format:"%Hh%M"}}
            </a>
          </td>
          <td>
            <table>
            {{foreach from=$curr_op->_ref_documents item=document}}
              <tr>
                <th>{{$document->nom}}</th>
                <td class="button">
                  <form name="editDocumentFrm{{$document->compte_rendu_id}}" action="?m={{$m}}" method="post">
                  <input type="hidden" name="m" value="dPcompteRendu" />
                  <input type="hidden" name="del" value="0" />
                  <input type="hidden" name="dosql" value="do_modele_aed" />
                  <input type="hidden" name="object_id" value="{{$curr_op->operation_id}}" />
                  {{mb_field object=$document field="compte_rendu_id" hidden=1 spec=""}}
                  <button class="edit notext" type="button" onclick="editDocument({{$document->compte_rendu_id}})">
                  </button>
                  <button class="trash notext" type="button" onclick="confirmDeletion(this.form, {typeName:'le document',objName:'{{$document->nom|smarty:nodefaults|JSAttribute}}'})" />
                  </form>
                </td>
              </tr>
            {{/foreach}}
            </table>
            <form name="newDocumentFrm" action="?m={{$m}}" method="post">
            <table>
              <tr>
                <td>
                  <select name="_choix_modele" onchange="if (this.value) createDocument(this.value, {{$curr_op->operation_id}})">
                    <option value="">&mdash; Choisir un mod�le</option>
                    <optgroup label="Op�ration">
                    {{foreach from=$crList item=curr_cr}}
                    <option value="{{$curr_cr->compte_rendu_id}}">{{$curr_cr->nom}}</option>
                    {{/foreach}}
                    </optgroup>
                    <optgroup label="Hospitalisation">
                    {{foreach from=$hospiList item=curr_hospi}}
                    <option value="{{$curr_hospi->compte_rendu_id}}">{{$curr_hospi->nom}}</option>
                    {{/foreach}}
                    </optgroup>
                  </select>
                </td>
              </tr>
            </table>
            </form>
          </td>
        </tr>
        {{/foreach}}
        {{else}}
        {{foreach from=$listDay item=curr_plage}}
        <tr>
          <th colspan="6">{{$curr_plage->_ref_salle->nom}} : de {{$curr_plage->debut|date_format:"%Hh%M"}} � {{$curr_plage->fin|date_format:"%Hh%M"}}</th>
        </tr>
        {{foreach from=$curr_plage->_ref_operations item=curr_op}}
        <tr>
          <td class="text">
<!--            <a href="?m=dPpatients&amp;tab=vw_full_patients&amp;patient_id={{$curr_op->_ref_sejour->_ref_patient->patient_id}}"> -->
            <a href="index.php?m=dPcabinet&amp;tab=vw_dossier&amp;patSel={{$curr_op->_ref_sejour->_ref_patient->patient_id}}">
              {{$curr_op->_ref_sejour->_ref_patient->_view}}
            </a>
          </td>
          <td class="text">
            <a href="index.php?m={{$m}}&amp;tab=vw_edit_planning&amp;operation_id={{$curr_op->operation_id}}">
              {{if $curr_op->libelle}}
                <em>[{{$curr_op->libelle}}]</em>
                <br />
              {{/if}}
              {{foreach from=$curr_op->_ext_codes_ccam item=curr_code}}
              <strong>{{$curr_code->code}}</strong>
              {{if !$board}}
              : {{$curr_code->libelleLong}}
              {{/if}}
              {{if $boardItem}}
              : {{$curr_code->libelleLong|truncate:50:"...":false}}
              {{/if}}
              <br />
              {{/foreach}}
            </a>
          </td>
          <td style="text-align: center;">
            {{if $curr_op->annulee}}
            [ANNULEE]
            {{else}}
            <a href="index.php?m={{$m}}&amp;tab=vw_edit_planning&amp;operation_id={{$curr_op->operation_id}}">
              {{$curr_op->time_operation|date_format:"%Hh%M"}}
            </a>
            {{/if}}
          </td>
          <td style="text-align: center;">
            <a href="index.php?m={{$m}}&amp;tab=vw_edit_planning&amp;operation_id={{$curr_op->operation_id}}">
              {{$curr_op->temp_operation|date_format:"%Hh%M"}}
            </a>
          </td>
          {{if !$boardItem}}
          <td>
            <table>
            {{foreach from=$curr_op->_ref_documents item=document}}
              <tr>
                <th>{{$document->nom}}</th>
                <td class="button">
                  <form name="editDocumentFrm{{$document->compte_rendu_id}}" action="?m={{$m}}" method="post">
                  <input type="hidden" name="m" value="dPcompteRendu" />
                  <input type="hidden" name="del" value="0" />
                  <input type="hidden" name="dosql" value="do_modele_aed" />
                  <input type="hidden" name="object_id" value="{{$curr_op->operation_id}}" />
                  {{mb_field object=$document field="compte_rendu_id" hidden=1 spec=""}}
                  <button class="edit notext" type="button" onclick="editDocument({{$document->compte_rendu_id}})">
                  </button>
                  <button class="trash notext" type="button" onclick="confirmDeletion(this.form, {typeName:'le document',objName:'{{$document->nom|smarty:nodefaults|JSAttribute}}',ajax:1,target:'systemMsg'},{onComplete:reloadAfterSaveDoc})" />
                  </form>
                </td>
              </tr>
            {{/foreach}}
            </table>
            <form name="newDocumentFrm" action="?m={{$m}}" method="post">
            <table>
              <tr>
                <td>
                  <select name="_choix_modele" onchange="if (this.value) createDocument(this.value, {{$curr_op->operation_id}})">
                    <option value="">&mdash; Choisir un mod�le</option>
                    <optgroup label="Op�ration">
                    {{foreach from=$crList item=curr_cr}}
                    <option value="{{$curr_cr->compte_rendu_id}}">{{$curr_cr->nom}}</option>
                    {{/foreach}}
                    </optgroup>
                    <optgroup label="Hospitalisation">
                    {{foreach from=$hospiList item=curr_hospi}}
                    <option value="{{$curr_hospi->compte_rendu_id}}">{{$curr_hospi->nom}}</option>
                    {{/foreach}}
                    </optgroup>
                  </select>
                </td>
              </tr>
            </table>
            </form>
          </td>
          {{/if}}
        </tr>
        {{/foreach}}
        {{/foreach}}
        {{/if}}
      </table>