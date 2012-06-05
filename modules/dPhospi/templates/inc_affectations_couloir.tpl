{{if $sejourNonAffectes|@count}}
  {{foreach from=$sejourNonAffectes item=_by_service key=service_id}}
    {{assign var=curr_service value=$services.$service_id}}
    <table class="tbl">
      <tr>
        <th class="title">
          {{$curr_service}}
        </th>
      </tr>
      {{foreach from=$_by_service item=_affectation}}
        <tr>
          <td>
            {{assign var="sejour" value=$_affectation->_ref_sejour}}
            {{assign var="patient" value=$sejour->_ref_patient}}
            {{assign var="aff_prev" value=$_affectation->_ref_prev}}
            {{assign var="aff_next" value=$_affectation->_ref_next}}
            <form name="addAffectationaffectation_{{$_affectation->_id}}" action="?m={{$m}}" method="post" class="prepared">
              <input type="hidden" name="m" value="dPhospi" />
              <input type="hidden" name="dosql" value="do_affectation_aed" />
              <input type="hidden" name="affectation_id" value="{{$_affectation->_id}}" />
              <input type="hidden" name="lit_id" value="" />
              <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
              <input type="hidden" name="entree" value="{{$_affectation->entree}}" />
              <input type="hidden" name="sortie" value="{{$_affectation->sortie}}" />
            </form>
            <table class="tbl sejourcollapse" id="affectation_{{$_affectation->_id}}">
              <tr class="patient">
                <td class="text button" style="width: 1%;">
                  {{if $can->edit}}
                  <script type="text/javascript">new Draggable('affectation_{{$_affectation->_id}}', {revert:true})</script>
                  {{/if}}
                  {{if $sejour->_couvert_cmu}}
                  <div><strong>CMU</strong></div>
                  {{/if}}
                  {{if $sejour->_couvert_ald}}
                  <div><strong {{if $sejour->ald}}style="color: red;"{{/if}}>ALD</strong></div>
                  {{/if}}
                  {{if $conf.dPhospi.CLit.alt_icons_sortants}}
                    {{assign var=suffixe_icons value="2"}}
                  {{else}}
                    {{assign var=suffixe_icons value=""}}
                  {{/if}}
                  {{if $sejour->type == "ambu"}}
                  <img src="modules/dPhospi/images/X{{$suffixe_icons}}.png" alt="X" title="Ambulatoire" />
                  {{elseif $_affectation->sortie|iso_date == $demain}}
                    {{if $aff_next->_id}}
                  <img src="modules/dPhospi/images/OC{{$suffixe_icons}}.png" alt="OC" title="D�plac� demain" />
                    {{else}}
                  <img src="modules/dPhospi/images/O{{$suffixe_icons}}.png" alt="O" title="Sortant demain" />
                    {{/if}}
                  {{elseif $_affectation->sortie|iso_date == $date}}
                    {{if $aff_next->_id}}
                  <img src="modules/dPhospi/images/OoC{{$suffixe_icons}}.png" alt="OoC" title="D�plac� aujourd'hui" />
                    {{else}}
                  <img src="modules/dPhospi/images/Oo{{$suffixe_icons}}.png" alt="Oo" title="Sortant aujourd'hui" />
                    {{/if}}
                  {{/if}}
                </td>  
                {{if $sejour->confirme}}
                <td class="text" style="background-image:url(images/icons/ray.gif); background-repeat:repeat;">
                {{else}}
                <td class="text patient"
                  onclick="flipAffectationCouloir({{$_affectation->_id}});
                  Calendar.setupAffectation({{$_affectation->_id}}, {
                    sejour: {
                      start: '{{$_affectation->_ref_sejour->entree_prevue}}',
                      stop: '{{$_affectation->_ref_sejour->sortie_prevue}}'
                    },
                    currAffect : {
                      start : '{{$_affectation->entree}}',
                      stop : '{{$_affectation->sortie}}'}});">
                {{/if}}
                  {{if !$sejour->entree_reelle || ($aff_prev->_id && $aff_prev->effectue == 0)}}
                    <span class="patient-not-arrived">
                  {{elseif $sejour->septique}}
                    <span class="septique">
                  {{else}}
                    <span>
                  {{/if}}
                  <span style="float: right;">
                    {{mb_include module=patients template=inc_vw_antecedents type=deficience readonly=1}}
                  </span>
                  <span onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}')">
                    <strong {{if $sejour->type == "ambu"}}style="font-style: italic;"{{/if}}>
                      {{$patient}}
                    </strong>
                  </span>
          
                  {{if (!$sejour->entree_reelle) || ($aff_prev->_id && $aff_prev->effectue == 0)}}
                    {{$_affectation->entree|date_format:"%d/%m %Hh%M"}}
                  {{/if}}
                  </span>
                </td>
                <td class="action" style="background:#{{$sejour->_ref_praticien->_ref_function->color}}" 
                  onmouseover="ObjectTooltip.createTimeHospi(this, '{{$sejour->praticien_id}}', '{{$sejour->_codes_ccam_operations}}' );">
                  {{$sejour->_ref_praticien->_shortview}}
                </td>
              </tr>
              {{if !$_affectation->sejour_id}}
              <tr class="dates">   
                <td class="text" colspan="2">
                  {{if $can->edit}}
                  <form name="entreeAffectation{{$_affectation->_id}}" action="?m={{$m}}" method="post" style="float: right;" class="prepared">
                    <input type="hidden" name="m" value="dPhospi" />
                    <input type="hidden" name="dosql" value="do_affectation_aed" />
                    <input type="hidden" name="affectation_id" value="{{$_affectation->_id}}" />
                    <input type="hidden" name="entree" class="dateTime notNull" value="{{$_affectation->entree}}" />
                  </form>
                  {{/if}}
                  <strong>Du</strong>:
                  {{$_affectation->entree|date_format:"%a %d %b %Hh%M"}}
                  ({{$_affectation->_entree_relative}}j)
                </td>
                <td class="action">
                  {{if $can->edit}}
                  <form name="rmvAffectation{{$_affectation->_id}}" action="?m={{$m}}" method="post" class="prepared">
                    <input type="hidden" name="m" value="dPhospi" />
                    <input type="hidden" name="dosql" value="do_affectation_aed" />
                    <input type="hidden" name="del" value="1" />
                    <input type="hidden" name="affectation_id" value="{{$_affectation->_id}}" />
                  </form>
                  <a href="#" onclick="confirmDeletion(document.rmvAffectation{{$_affectation->_id}},{typeName:'l\'affectation',objName:'{{$patient->_view|smarty:nodefaults|JSAttribute}}'})">
                    <img src="images/icons/trash.png" alt="trash" title="Supprimer l'affectation" />
                  </a>
                  {{/if}}
                </td>
              </tr>
              <tr class="dates">
                <td class="text" colspan="2">
                  {{if $can->edit && (!$sejour->sortie_reelle || $aff_next->_id)}}
                  <form name="sortieAffectation{{$_affectation->_id}}" action="?m={{$m}}" method="post" style="float: right;" class="prepared">
                    <input type="hidden" name="m" value="dPhospi" />
                    <input type="hidden" name="dosql" value="do_affectation_aed" />
                    <input type="hidden" name="affectation_id" value="{{$_affectation->_id}}" />
                    <input type="hidden" name="sortie" class="dateTime notNull" value="{{$_affectation->sortie}}" />
                  </form>
                  {{/if}}
                  <strong>Au</strong>:
                  {{$_affectation->sortie|date_format:"%a %d %b %Hh%M"}}
                  ({{$_affectation->_sortie_relative}}j)
                </td>
                <td class="action">
                </td>
              </tr>
          
              {{if $_affectation->rques}}
              <tr class="dates">
                <td class="text highlight" colspan="3">
                  <strong>Remarques:</strong> {{$_affectation->rques|nl2br}}
                </td>
              </tr>
              {{/if}}
              {{else}}
          
              <tr class="dates">
                <td class="text" colspan="2">
                  {{if $can->edit && (!$sejour->entree_reelle || $aff_prev->_id)}}
                  <form name="entreeAffectation{{$_affectation->_id}}" action="?m={{$m}}" method="post" style="float: right;" class="prepared">
                    <input type="hidden" name="m" value="dPhospi" />
                    <input type="hidden" name="dosql" value="do_affectation_aed" />
                    <input type="hidden" name="affectation_id" value="{{$_affectation->_id}}" />
                    <input type="hidden" name="entree" class="dateTime notNull" value="{{$_affectation->entree}}" onchange="return onSubmitFormAjax(this.form, {onComplete: reloadTableau});" />
                  </form>
                  {{/if}}
                  
                  {{if $curr_service->externe}}
                    <strong>D�part</strong>
                    {{if $aff_prev->_id}}
                      ({{$aff_prev->_ref_lit->_ref_chambre->_shortview}})
                    {{/if}}
                  {{elseif $aff_prev->_id}}
                    <strong>D�plac�</strong> ({{$aff_prev->_ref_lit->_ref_chambre->_shortview}})
                  {{else}}
                    <strong>Entr�e</strong>
                  {{/if}}
                  :
                  {{$_affectation->entree|date_format:"%a %d %b %Hh%M"}}
                  ({{$_affectation->_entree_relative}}j)
                </td>
                <td class="action">
                  {{if $can->edit}}
                  <form name="rmvAffectation{{$_affectation->_id}}" action="?m={{$m}}" method="post">
                    <input type="hidden" name="m" value="dPhospi" />
                    <input type="hidden" name="dosql" value="do_affectation_aed" />
                    <input type="hidden" name="del" value="1" />
                    <input type="hidden" name="affectation_id" value="{{$_affectation->_id}}" />
                  </form>
                  <a href="#" onclick="confirmDeletion(document.rmvAffectation{{$_affectation->_id}},{typeName:'l\'affectation',objName:'{{$patient->_view|smarty:nodefaults|JSAttribute}}'})">
                    <img src="images/icons/trash.png" alt="trash" title="Supprimer l'affectation" />
                  </a>
                  {{/if}}
                </td>
              </tr>
              <tr class="dates">
                <td colspan="3"><strong>Age</strong>: {{$patient->_age}} ({{mb_value object=$patient field=naissance}})</td>
              </tr>
          
              {{if $sejour->prestation_id}}
              <tr class="dates">
                <td colspan="3">
                  <strong>Prestation:</strong> {{$sejour->_ref_prestation->_view}}
                </td>
              </tr>
              {{/if}}
              
              <tr class="dates">
                <td class="text" colspan="3">
                  <strong>
                    {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$sejour->_ref_praticien}}
                  </strong>
                </td>
              </tr>
          
              {{if $sejour->libelle}}
              <tr class="dates">
                <td class="text" colspan="3">
                  {{$sejour->libelle}}
                </td>
              </tr>
              {{/if}}
          
              <tr class="dates">
                <td class="text" colspan="3">
                  {{foreach from=$sejour->_ref_operations item=_operation}}
                    {{mb_include module=planningOp template=inc_vw_operation operation=$_operation}}
                  {{/foreach}}
                </td>
              </tr>
              <tr class="dates">
                <td class="text" colspan="3">
                  <form name="SeptieSejour{{$sejour->_id}}" action="?m=dPhospi" method="post" class="prepared">
                    <input type="hidden" name="m" value="dPplanningOp" />
                    <input type="hidden" name="otherm" value="dPhospi" />
                    <input type="hidden" name="dosql" value="do_sejour_aed" />
                    <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
                    <strong>Pathologie</strong>:
                    {{$sejour->pathologie}}
                    -
                    {{if $can->edit}}
                    <label title="S�jour propre">
                      <input type="radio" name="septique" value="0" {{if $sejour->septique == 0}} checked="checked" {{/if}} onclick="this.form.submit()" />
                      Propre
                    </label>
                    <label title="S�jour septique">
                      <input type="radio" name="septique" value="1" {{if $sejour->septique == 1}} checked="checked" {{/if}} onclick="this.form.submit()" />
                      Septique
                    </label>
                    {{else}}
                      {{if $sejour->septique == 0}}
                      Propre
                      {{else}}
                      Septique
                      {{/if}}
                    {{/if}}
                  </form>
                </td>
              </tr>
              {{if $sejour->rques != ""}}
              <tr class="dates">
                <td class="text highlight" colspan="3">
                  <strong>S�jour</strong>: {{$sejour->rques|nl2br}}
                </td>
              </tr>
              {{/if}}
              {{foreach from=$sejour->_ref_operations item=curr_operation}}
              {{if $curr_operation->rques != ""}}
              <tr class="dates">
                <td class="text highlight" colspan="3">
                  <strong>Intervention</strong>: {{$curr_operation->rques|nl2br}}
                </td>
              </tr>
              {{/if}}
              {{/foreach}}
              {{if $patient->rques != ""}}
              <tr class="dates">
                <td class="text highlight" colspan="3">
                  <strong>Patient</strong>: {{$patient->rques|nl2br}}
                </td>
              </tr>
              {{/if}}
              <tr class="dates">
                <td class="text" colspan="3">
                  {{if $can->edit}}
                  <form name="editChFrm{{$sejour->_id}}" action="?m=dPhospi" method="post" class="prepared">
                    <input type="hidden" name="m" value="dPplanningOp" />
                    <input type="hidden" name="dosql" value="do_sejour_aed" />
                    <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
                    {{if $sejour->chambre_seule}}
                    <input type="hidden" name="chambre_seule" value="0" />
                    <button class="change" type="submit" style="color: #f22;">
                      Chambre simple
                    </button>
                    {{else}}
                    <input type="hidden" name="chambre_seule" value="1" />
                    <button class="change" type="submit">
                      Chambre double
                    </button>
                    {{/if}}
                  </form>
                  {{/if}}
                </td>
              </tr>
              {{/if}}
            </table>
          </td>
        </tr>
      {{/foreach}}
    </table>
  {{/foreach}}
{{/if}}