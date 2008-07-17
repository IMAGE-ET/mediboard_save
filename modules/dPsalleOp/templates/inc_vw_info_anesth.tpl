{{assign var="consult_anesth" value=$selOp->_ref_consult_anesth}}
{{if $consult_anesth->_id}}
<table class="form">
  <!-- Affichage d'information complementaire pour l'anestesie -->
  <tr>
    <td class="button">
      Vu par {{$consult_anesth->_ref_consultation->_ref_chir->_view}}
    </td>
  </tr>
  
  <tr>
   <td>
   <table style="width: 100%">
   
<tr>
  <th colspan="3" class="category">Conditions d'intubation</th>
</tr>
<tr>
  {{if $consult_anesth->mallampati}}
  <td rowspan="4">
    <img src="images/pictures/{{$consult_anesth->mallampati}}.png" alt="{{tr}}CConsultAnesth.mallampati.{{$consult_anesth->mallampati}}{{/tr}}" />
    <br />Mallampati<br />de {{tr}}CConsultAnesth.mallampati.{{$consult_anesth->mallampati}}{{/tr}}
   </td>
   {{/if}}
	        
   <th>Ouverture de la bouche</th>
     <td>
       {{tr}}CConsultAnesth.bouche.{{$consult_anesth->bouche}}{{/tr}}
     </td>
   </tr>
   <tr>
     <th>Distance thyro-mentonni�re</th>
     <td>{{tr}}CConsultAnesth.distThyro.{{$consult_anesth->distThyro}}{{/tr}}</td>
   </tr>
	 <tr>
	   <th>Etat bucco-dentaire</th>
	   <td>{{$consult_anesth->etatBucco}}</td>
	 </tr>
   <tr>
     <th>Examen cardiovasculaire</th>
     <td>{{$consult_anesth->examenCardio}}</td>
   </tr>
   <tr>
     <th>Examen pulmonaire</th>
     <td>{{$consult_anesth->examenCardio}}</td>
   </tr>
	 <tr>
	   <th>Conclusion</th>
	   <td>{{$consult_anesth->conclusion}}</td>
	  </tr>
	  <tr>
	   {{if $consult_anesth->_intub_difficile}}
	    <td colspan="3"  style="text-align:center;color:#F00;">
	      Intubation Difficile Pr�visible
	    </td>
	  {{else}}
	    <td colspan="3" style="text-align:center;">
	       Pas Intubation Difficile Pr�visible
	     </td>        
	  {{/if}}
	  </tr>     
	  <tr>
        <th class="category" colspan="3">
          Examens Compl�mentaires
        </th>
      </tr>
      
      <tr>
      {{foreach from=$listChamps item=aChamps}}
        <td>
          {{if $aChamps}}
          <table>
          {{/if}}
          {{foreach from=$aChamps item=champ}}
            {{assign var="donnees" value=$unites.$champ}}
            <tr>
              <th>{{$donnees.nom}}</th>
              <td>
                {{if $champ=="tca"}}
                  {{$consult_anesth->tca_temoin}} s / {{$consult_anesth->tca}}
                {{elseif $champ=="tsivy"}}
                  {{$consult_anesth->tsivy|date_format:"%Mm%Ss"}}
                {{elseif $champ=="ecbu"}}
                  {{tr}}CConsultAnesth.ecbu.{{$consult_anesth->ecbu}}{{/tr}}
                {{else}}
                  {{$consult_anesth->$champ}}
                {{/if}}
                {{$donnees.unit}}
              </td>
            </tr>
          {{/foreach}}
          {{if $aChamps}}</table>{{/if}}
        </td>
      {{/foreach}}
      </tr>
         
      {{foreach from=$consult_anesth->_ref_consultation->_types_examen key=curr_type item=list_exams}}
      {{if $list_exams|@count}}
      <tr>
        <th>
          Examens Compl�mentaires : {{tr}}CExamComp.realisation.{{$curr_type}}{{/tr}}
        </th>
        <td class="text">
          <ul>
            {{foreach from=$list_exams item=curr_examcomp}}
            <li>
              {{$curr_examcomp->examen}}
              {{if $curr_examcomp->fait}}
                (Fait)
              {{else}}
                (A Faire)
              {{/if}}
            </li>
            {{/foreach}}
          </ul>
        </td>
      </tr>
    {{/if}}
    {{foreachelse}}
    <tr>
      <td>
        <em>Pas d'examen compl�mentaire</em>
      </td>
    </tr>
    {{/foreach}}
   
    <tr>
      <td colspan="2">
        {{assign var=consult value=$consult_anesth->_ref_consultation}}
        {{mb_include_script module="dPcabinet" script="exam_dialog"}}
        <script type="text/javascript">
          ExamDialog.register('{{$consult->_id}}','{{$consult->_class_name}}');
        </script>
      
      
      </td>
    </tr>
      
    {{if $consult_anesth->_ref_consultation->rques}}
    <tr>
      <th>
        Remarques
      </th>
      <td>
        {{$consult_anesth->_ref_consultation->rques|nl2br}}
      </td>
    </tr>
    {{/if}}
  
    <tr>
      <th class="category" colspan="3">Liste des Documents Edit�s</th>
    </tr>
    <tr>
      <td>
        {{if $consult_anesth->_ref_consultation->_ref_documents}}
        <ul>
        {{foreach from=$consult_anesth->_ref_consultation->_ref_documents item=currDoc}}
          <li>{{$currDoc->nom}}</li>
        {{/foreach}}
        </ul>
        {{else}}
          Aucun Document
        {{/if}}
      </td>
    </tr>
  
    {{if $consult_anesth->premedication}}
    <tr>
        <th class="category">
          Pr�m�dication
        </th>
      </tr>
      <tr>
        <td>
          {{$consult_anesth->premedication|nl2br}}
        </td>
      </tr>
    {{/if}}
    {{if $consult_anesth->prepa_preop}}
      <tr>
        <th class="category">
          Pr�paration pr�-op�ratoire
        </th>
      </tr>
      <tr>
        <td>
          {{$consult_anesth->prepa_preop|nl2br}}
        </td>
      </tr>
    {{/if}}
  </table>
  </td>
  </tr>
  
</table>

{{elseif $selOp->_ref_sejour->_ref_consult_anesth->_id}}
{{assign var="consult_anesth" value=$selOp->_ref_sejour->_ref_consult_anesth}}

<form name="linkConsultAnesth" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
<input type="hidden" name="dosql" value="do_consult_anesth_aed" />
<input type="hidden" name="m" value="dPcabinet" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="consultation_anesth_id" value="{{$consult_anesth->_id}}" />
<input type="hidden" name="sejour_id" value="" />
<input type="hidden" name="operation_id" value="{{$selOp->_id}}" />
<table class="form">
  <tr>
    <td class="text">
      <div class="big-info">
        Une consultation d'anesth�sie a �t� effectu�e pour le s�jour de ce patient
        le <strong>{{$consult_anesth->_date_consult|date_format:"%d/%m/%Y"}}</strong>
        par le <strong>Dr {{$consult_anesth->_ref_consultation->_ref_chir->_view}}</strong>.
        Vous devez <strong>relier cette consultation � l'intervention courante</strong> si vous d�sirez y acc�der.
      </div>
    </td>
  </tr>
  <tr>
    <td class="button">
      <button type="submit" class="submit">Relier</button>
    </td>
  </tr>
</table>
</form>

{{else}}


<form name="createConsult" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
<input type="hidden" name="dosql" value="do_consult_now" />
<input type="hidden" name="m" value="dPcabinet" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="consultation_id" value="" />
<input type="hidden" name="_operation_id" value="{{$selOp->_id}}" />
<input type="hidden" name="_m_redirect" value="{{$m}}" />
<table class="form">
  <tr>
    <td class="text">
      <div class="big-info">
        Aucune consultation d'anesth�sie n'a �t� effectu�e pour ce patient.
        Vous devez cr�er son dossier d'anesth�sie pour y acc�der.
      </div>
    </td>
  </tr>
  <tr>
    <td class="button">
      <select name="prat_id">
        {{foreach from=$listAnesths item=curr_anesth}}
        <option value="{{$curr_anesth->user_id}}" {{if $selOp->_ref_anesth->user_id == $curr_anesth->user_id}} selected="selected" {{/if}}>
          {{$curr_anesth->_view}}
        </option>
        {{/foreach}}
      </select>
      <button type="submit" class="submit">Cr�er</button>
    </td>
  </tr>
</table>
</form>

{{/if}}
