{{mb_default var=sejour_maman value=""}}

<script>
  checkCut = function(elt) {
    if (elt.value == $V(elt.form.entree)) {
      $('cut_affectation').disabled='disabled'
    }
    else {
      $('cut_affectation').disabled = '';
    }
  }
  
  addUfs = function(){
    var form = getForm("affect_uf");
    var form2 = getForm("cutAffectation");
    form2.uf_hebergement_id.value = form.uf_hebergement_id.value;
    form2.uf_medicale_id.value    = form.uf_medicale_id.value;
    form2.uf_soins_id.value       = form.uf_soins_id.value;
  }

  {{if $from_tempo}}
    Main.add(function() {
      var dates = {
        limit: {
          start: "{{$affectation->entree}}",
          stop: "{{$affectation->sortie}}"
        }
      }
      Calendar.regField(getForm('cutAffectation')._date_cut, dates, {timePicker: true});
    } );
  {{/if}}
  
  liaisonMaman = function(status, parent_affectation_id, datetime) {
    var oForm = getForm('cutAffectation');
    if (status && parent_affectation_id) {
      changeLit('{{$affectation->_id}}', 1, datetime);
      return;
    }
    oForm.onsubmit();
    Control.Modal.close();
  }
  
  submitLiaison = function(lit_id) {
    var oForm = getForm('cutAffectation');
    $V(oForm.lit_id, lit_id);
    oForm.onsubmit();
  }
  
  refreshNewLit = function(id, obj) {
    refreshMouvements(null, obj.lit_id);
  }

  selectService = function() {
    var url = new Url("hospi", "ajax_select_service");
    url.requestModal(null, null, {maxHeight: '600'});
  }
</script>

<table class="form">
  {{if $from_tempo}}
    <tr>
      <th class="category" colspan="4">Autres actions</th>
    </tr>
    <tr>
      <td colspan="4">
        <form name="cutAffectation" method="post" action="?"
          onsubmit="addUfs();
            return onSubmitFormAjax(this, refreshMouvements.curry(Control.Modal.close, '{{$affectation->lit_id}}'));">
          <input type="hidden" name="m" value="hospi" />
          <input type="hidden" name="dosql" value="do_cut_affectation_aed" />
          <input type="hidden" name="lit_id" value="{{$lit_id}}" />
          <input type="hidden" name="entree" value="{{$affectation->entree}}" />
          <input type="hidden" name="uf_hebergement_id" value="" />
          <input type="hidden" name="uf_medicale_id" value="" />
          <input type="hidden" name="uf_soins_id" value="" />
          <input type="hidden" name="callback" value="refreshNewLit" />
          <input type="hidden" name="service_id" value="" />
          {{mb_key object=$affectation}}
          <input type="text" name="_date_cut_da" value="{{$smarty.now|date_format:$conf.datetime}}" readonly="readonly"/>
          <input type="hidden" name="_date_cut" class="dateTime" value="{{$smarty.now|@date_format:"%Y-%m-%d %H:%M:%S"}}"
            onchange="checkCut(this)"/>
          <button type="button" class="hslip"
            onclick="
            {{if "maternite"|module_active && $sejour_maman}}
              liaisonMaman(this.form._action_maman.checked, '{{$affectation->parent_affectation_id}}', '{{$smarty.now|@date_format:"%Y-%m-%d %H:%M:%S"}}')
            {{elseif !$lit_id}}
              changeLit('{{$affectation->_id}}', 1, $V(this.form._date_cut));
            {{else}}
              this.form.onsubmit();
            {{/if}}" id="cut_affectation">Cr�er un mouvement</button>
          {{if "maternite"|module_active && $sejour_maman}}
            <label>
              <input type="checkbox" name="_action_maman" checked="checked"/>
              {{if $affectation->parent_affectation_id}}D�tacher de{{else}}Attacher � {{/if}} la maman ({{$sejour_maman->_ref_patient}})
            </label>
          {{/if}}
          {{if !$sejour_maman}}
            <button type="button" class="door-out" onclick="selectService()">Placer dans le couloir</button>
          {{/if}}
        </form>
        <br />
      </td>
    </tr>
  {{/if}}
  <tr>
    <td id="ufs_affectation">
      <script>
        Main.add(function() {
          var url = new Url("hospi", "ajax_vw_association_uf");
          url.addParam("curr_affectation_guid", '{{$affectation->_guid}}');
          url.addParam("lit_guid", "CLit-"+'{{$affectation->lit_id}}');
          url.addParam("see_validate", 0);
          url.requestUpdate('ufs_affectation');
        } );
      </script>
    </td>
  </tr>
</table>