<!-- Plages -->
{{foreach from=$salle->_ref_plages item=_plage}}
  <hr />

  <form name="anesth{{$_plage->_id}}" action="?" method="post" class="{{$_plage->_spec}}">
    <input type="hidden" name="m" value="dPbloc" />
    <input type="hidden" name="otherm" value="{{$m}}" />
    <input type="hidden" name="dosql" value="do_plagesop_aed" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="_repeat" value="1" />
    <input type="hidden" name="plageop_id" value="{{$_plage->_id}}" />
    <input type="hidden" name="chir_id" value="{{$_plage->chir_id}}" />
    <input type="hidden" name="spec_id" value="{{$_plage->spec_id}}" />

    <table class="form">
      <tr>
        <th class="category text" colspan="2">
          {{mb_include module=system template=inc_object_notes object=$_plage}}
          <a onclick="EditPlanning.order('{{$_plage->_id}}');" href="#" title="Agencer les interventions">
            {{if $_plage->chir_id}}
              Chir : Dr {{$_plage->_ref_chir->_view}}
            {{else}}
              {{$_plage->_ref_spec->_view}}
            {{/if}}
            <br />
            {{$_plage->debut|date_format:$conf.time}} � {{$_plage->fin|date_format:$conf.time}}
          </a>
        </th>
      </tr>

      {{if $vueReduite}}
        <tr>
          <th class="category" colspan="2">
            {{if $_plage->anesth_id}}
              Anesth : Dr {{$_plage->_ref_anesth->_view}}
            {{else}}
              -
            {{/if}}
          </th>
        </tr>
        {{assign var=affectations value=$_plage->_ref_affectations_personnel}}
        {{if $affectations|@is_array}}
          {{foreach from=$affectations key=type item=list_aff}}
            {{if $list_aff|@count}}
              <tr>
                <td>
                  <strong>
                    {{tr}}CPersonnel.emplacement.{{$type}}{{/tr}} :
                  </strong>
                  <div class="compact">
                    <ul>
                    {{foreach from=$list_aff item=_affectation}}
                      <li>{{$_affectation->_ref_personnel->_ref_user}}</li>
                    {{/foreach}}
                    </ul>
                  </div>
                </td>
              </tr>
            {{/if}}
          {{/foreach}}
        {{/if}}
      {{else}}
        <tr>
          <th><label for="anesth_id" title="Anesth�siste associ� � la plage d'op�ration">Anesth�siste</label></th>
          <td>
            <select name="anesth_id" onchange="this.form.submit()">
              <option value="">&mdash; Choisir un anesth�siste</option>
              {{foreach from=$listAnesths item=curr_anesth}}
                <option value="{{$curr_anesth->user_id}}" {{if $_plage->anesth_id == $curr_anesth->user_id}} selected="selected" {{/if}}>
                  {{$curr_anesth->_view}}
                </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        {{if $conf.dPsalleOp.COperation.modif_actes == "button" && !$_plage->actes_locked}}
          <tr>
            <td class="button" colspan="2">
              <input type="hidden" name="actes_locked" value="{{$_plage->actes_locked}}" />
              <button class="submit" type="button" onclick="$V(this.form.actes_locked, 1); if(confirmeCloture()) {this.form.submit()}">Cloturer le codage</button>
            </td>
          </tr>
        {{elseif $_plage->actes_locked}}
          <tr>
            <th class="category" colspan="2">Codage clotur�</th>
          </tr>
        {{/if}}
      {{/if}}
    </table>
  </form>

  <table class="tbl">
    {{if $_plage->_ref_operations}}
      {{include file="../../dPsalleOp/templates/inc_liste_operations.tpl" urgence=0 operations=$_plage->_ref_operations}}
    {{/if}}

    {{if $_plage->_unordered_operations}}
      <tr>
        <th colspan="10">Non plac�es</th>
      </tr>
      {{include file="../../dPsalleOp/templates/inc_liste_operations.tpl" urgence=0 operations=$_plage->_unordered_operations}}
    {{/if}}
  </table>
{{/foreach}}

<!-- D�plac�es -->
{{if $salle->_ref_deplacees|@count}}
  <hr />
  <table class="form">
    <tr>
      <th class="category" colspan="2">
        D�plac�es
      </th>
    </tr>
  </table>
  <table class="tbl">
    {{include file="../../dPsalleOp/templates/inc_liste_operations.tpl" urgence=1 operations=$salle->_ref_deplacees}}
  </table>
{{/if}}

<!-- Urgences -->
{{if $salle->_ref_urgences|@count}}
  <hr />
  <table class="form">
    <tr>
      <th class="category" colspan="2">
        Hors plage
      </th>
    </tr>
  </table>
  <table class="tbl">
    {{include file="../../dPsalleOp/templates/inc_liste_operations.tpl" urgence=1 operations=$salle->_ref_urgences}}
  </table>
{{/if}}