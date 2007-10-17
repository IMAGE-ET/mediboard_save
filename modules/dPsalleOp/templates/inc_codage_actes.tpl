<script language="Javascript" type="text/javascript">
	PairEffect.initGroup("acteEffect");
</script>

{{if $module!="dPcabinet" && $module!="dPhospi" && $subject->libelle}}
  <em>[{{$subject->libelle}}]</em>
{{/if}}
<ul>
  {{foreach from=$subject->_ext_codes_ccam item=curr_code key=curr_key}}
  <li>
    <strong>{{$curr_code->libelleLong}}</strong> 
    <em>(<a class="action" href="?m=dPccam&amp;tab=vw_full_code&amp;codeacte={{$curr_code->code}}">{{$curr_code->code}}</a>)</em>
    {{if $can->edit || $modif_operation}}
    <br />Codes associ�s :
    <select name="asso" onchange="setCodeTemp(this.value)">
      <option value="">&mdash; choix</option>
      {{foreach from=$curr_code->assos item=curr_asso}}
      <option value="{{$curr_asso.code}}">{{$curr_asso.code}}({{$curr_asso.texte|truncate:40:"...":true}})</option>
      {{/foreach}}
    </select>
    {{/if}}
    
    {{foreach from=$curr_code->activites item=curr_activite}}
    {{foreach from=$curr_activite->phases item=curr_phase}}
    {{assign var="acte" value=$curr_phase->_connected_acte}}
    {{assign var="view" value=$acte->_view}}
    {{assign var="key" value="$curr_key$view"}}
    <form name="formActe-{{$acte->_view}}" action="?m={{$module}}" method="post" onsubmit="return checkForm(this)">
    <input type="hidden" name="m" value="dPsalleOp" />
    <input type="hidden" name="dosql" value="do_acteccam_aed" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="acte_id" value="{{$acte->acte_id}}" />
    <input type="hidden" name="object_id" class="{{$acte->_props.object_id}}" value="{{$subject->_id}}" />
    <input type="hidden" name="object_class" class="{{$acte->_props.object_class}}" value="{{$subject->_class_name}}" />
    <input type="hidden" name="code_acte" class="{{$acte->_props.code_acte}}" value="{{$acte->code_acte}}" />
    <input type="hidden" name="code_activite" class="{{$acte->_props.code_activite}}" value="{{$acte->code_activite}}" />
    <input type="hidden" name="code_phase" class="{{$acte->_props.code_phase}}" value="{{$acte->code_phase}}" />
    <input type="hidden" name="montant_depassement" class="{{$acte->_props.montant_depassement}}" value="{{$acte->montant_depassement}}" />

    <table class="form">
      
      <tr id="acte{{$key}}-trigger">  
        <td colspan="2">
          Activit� {{$curr_activite->numero}} ({{$curr_activite->type}}) &mdash; 
          Phase {{$curr_phase->phase}} : {{$curr_phase->libelle}}
          {{if $acte->_id}}
             &mdash; {{$acte->_tarif|string_format:"%.2f"}} �
          {{/if}}
        </td>
      </tr>
    
      <tbody class="acteEffect" id="acte{{$key}}" style="display: none;">
      
      <tr style="display: none;">
        <th><label for="execution" title="Date et heure d'ex�cution de l'acte">Ex�cution</label></th>
        <td>
          <input type="text" name="execution" class="{{$acte->_props.execution}}" readonly="readonly" value="{{$acte->execution}}" />
          <button class="tick" onclick="this.form.execution.value = new Date().toDATETIME());">Maintenant</button><br />
        </td>
      </tr>
      
      <tr class="{{$key}}">
        <th><label for="executant_id" title="Professionnel de sant� ex�cutant l'acte">Ex�cutant</label></th>
        <td>
        
        {{mb_ternary var=listExecutants test=$acte->_anesth value=$listAnesths other=$listChirs}}
       
          {{if $can->edit || $modif_operation}}
          <select name="executant_id" class="{{$acte->_props.executant_id}}">
            <option value="">&mdash; Choisir un professionnel de sant�</option>
            {{foreach from=$listExecutants item=curr_executant}}
            <option class="mediuser" style="border-color: #{{$curr_executant->_ref_function->color}};" value="{{$curr_executant->user_id}}" {{if $acte->executant_id == $curr_executant->user_id}} selected="selected" {{/if}}>
              {{$curr_executant->_view}}
            </option>
            {{/foreach}}
          </select>
          {{elseif $acte->executant_id}}
            {{assign var="keyActe" value=$acte->executant_id}}
            {{assign var="selActe" value=$listExecutants.$keyActe}}
            {{$selActe->_view}}
          {{else}}-{{/if}}
        </td>
      </tr>
      
      <tr>
        <th />
        <td>
          <label onmouseover="ObjectTooltip.create(this, { mode: 'translate', params: { text: 'CActeCCAM-regle-association-{{$acte->_guess_regle_asso}}' } })">
            <strong>
              Code d'association : {{$acte->_guess_association}}
            </strong>
          </label>
        </td>
      </tr>

      <tr class="{{$acte->_view}}">
        <th><label for="modificateurs" title="Modificateurs associ�s � l'acte">Modificateur(s)</label></th>
        <td class="text">
          {{foreach from=$curr_phase->_modificateurs item=curr_mod}}
            {{if $can->edit || $modif_operation}}
            <input type="checkbox" name="modificateur_{{$curr_mod->code}}" {{if $curr_mod->_value}}checked="checked"{{/if}} />
            <label for="modificateur_{{$curr_mod->code}}" title="{{$curr_mod->libelle}}">
              {{$curr_mod->code}} : {{$curr_mod->libelle}}
            </label>
            <br />
            {{elseif $curr_mod->_value}}
              {{$curr_mod->code}} : {{$curr_mod->libelle}}
            {{/if}}
          {{/foreach}}
        </td>
      </tr>
        
      <tr class="{{$acte->_view}}">
        <th><label for="commentaire" title="Commentaires sur l'acte">Commentaire</label></th>
        <td class="text">
          {{if $can->edit || $modif_operation}}
          <textarea name="commentaire" class="{{$acte->_props.commentaire}}">{{$acte->commentaire}}</textarea>
          {{elseif $acte->commentaire}}
            {{$acte->commentaire|nl2br}}
          {{else}}-{{/if}}
        </td>
      </tr>
      
      </tbody>
      
      {{if $can->edit || $modif_operation}}
      <tr>
        <td class="button" colspan="2">
          {{if $acte->acte_id}}
          
          <button class="modify" type="button" onclick="submitFormAjax(this.form, 'systemMsg', {onComplete: function(){loadActes({{$subject->_id}},{{$subject->_praticien_id}})} })">Modifier cet acte</button>
          
          
          <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'l\'acte',objName:'{{$acte->_view|smarty:nodefaults|JSAttribute}}',ajax:'1'}, {onComplete: function(){loadActes({{$subject->_id}},{{$subject->_praticien_id}})} })">
            Supprimer cet acte
          </button>
          
          
          {{else}}
          <button class="submit" type="button" style="background-color: #faa" onclick="submitFormAjax(this.form, 'systemMsg',{onComplete: function(){loadActes({{$subject->_id}},{{$subject->_praticien_id}})} })">Coder cet acte</button>
          {{/if}}
        </td>
      </tr>
      {{/if}}
    </table>
    </form>
  {{/foreach}}
  
  {{if $ajax}}
  
  <script type="text/javascript">
    var oElement = $('acte{{$key}}');
    var oForm = getBoundingForm(oElement);
    prepareForm(oForm);
  </script>
  
  {{/if}}

  {{/foreach}}
  </li>
  {{foreachelse}}
  <li><em>Pas d'acte cod�s</em></li>
  {{/foreach}}
</ul>

