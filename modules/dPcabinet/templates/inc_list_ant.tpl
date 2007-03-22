     {{if $dPconfig.dPcabinet.addictions}}
        {{include file="inc_consult_anesth/inc_list_addiction.tpl}}    
      {{/if}}
      
      <strong>Ant�c�dents du patient</strong>
      <ul>
      {{if $patient->_ref_antecedents}}
        {{foreach from=$patient->_ref_antecedents key=curr_type item=list_antecedent}}
        {{if $list_antecedent|@count}}
        <li>
          {{tr}}CAntecedent.type.{{$curr_type}}{{/tr}}
          {{foreach from=$list_antecedent item=curr_antecedent}}
          <ul>
            <li>
              <form name="delAntFrm" action="?m=dPcabinet" method="post">

              <input type="hidden" name="m" value="dPpatients" />
              <input type="hidden" name="del" value="0" />
              <input type="hidden" name="dosql" value="do_antecedent_aed" />
              <input type="hidden" name="antecedent_id" value="{{$curr_antecedent->antecedent_id}}" />
              
              <button class="trash notext" type="button" onclick="confirmDeletion(this.form, {typeName:'cet ant�c�dent',ajax:1,target:'systemMsg'},{onComplete:reloadAntecedents})">
              </button> 
              {{if $_is_anesth}}
              <button class="add notext" type="button" onclick="copyAntecedent({{$curr_antecedent->antecedent_id}})">
              </button>
              {{/if}}         
              {{if $curr_antecedent->date}}
                {{$curr_antecedent->date|date_format:"%d/%m/%Y"}} :
              {{/if}}
              <em>{{$curr_antecedent->rques}}</em>
            </form>
            </li>
          </ul>
          {{/foreach}}
        </li>
        {{/if}}
        {{/foreach}}
      {{else}}
        <li>Pas d'ant�c�dents</li>
      {{/if}}
      </ul>
      <strong>Traitements du patient</strong>
      <ul>
        {{foreach from=$patient->_ref_traitements item=curr_trmt}}
        <li>
          <form name="delTrmtFrm" action="?m=dPcabinet" method="post">
          <input type="hidden" name="m" value="dPpatients" />
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="dosql" value="do_traitement_aed" />
          <input type="hidden" name="traitement_id" value="{{$curr_trmt->traitement_id}}" />
          <button class="trash notext" type="button" onclick="confirmDeletion(this.form,{typeName:'ce traitement',ajax:1,target:'systemMsg'},{onComplete:reloadAntecedents})">
          </button>
          {{if $_is_anesth}}
          <button class="add notext" type="button" onclick="copyTraitement({{$curr_trmt->traitement_id}})">
          </button>
          {{/if}}
          {{if $curr_trmt->fin}}
            Du {{$curr_trmt->debut|date_format:"%d/%m/%Y"}} au {{$curr_trmt->fin|date_format:"%d/%m/%Y"}} :
          {{elseif $curr_trmt->debut}}
            Depuis le {{$curr_trmt->debut|date_format:"%d/%m/%Y"}} :
          {{/if}}
          <em>{{$curr_trmt->traitement}}</em>
          </form>
        </li>
        {{foreachelse}}
        <li>Pas de traitements</li>
        {{/foreach}}
      </ul>
      <strong>Diagnostics du patient</strong>
      <ul>
        {{foreach from=$patient->_codes_cim10 item=curr_code}}
        <li>
          <button class="trash notext" type="button" onclick="oCimField.remove('{{$curr_code->code}}')">
          </button>
          {{if $_is_anesth}}
          <button class="add notext" type="button" onclick="oCimAnesthField.add('{{$curr_code->code}}')">
          </button>
          {{/if}}
          {{$curr_code->code}}: {{$curr_code->libelle}}
        </li>
        {{foreachelse}}
        <li>Pas de diagnostic</li>
        {{/foreach}}
      </ul>