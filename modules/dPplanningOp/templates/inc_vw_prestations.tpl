{{mb_default var=editRights value=0}}

<script>
  uncheckPrestation = function(elts) {
    elts.each(function(elt) {
      elt.checked = "";
    });
  };
  
  openModal = function(id_div) {
    var div = $(id_div);
    window.save_checked = div.select("input").pluck("checked");
    Modal.open(id_div).observe("afterClose",
      function() {
        Prestations.urlPresta.refreshModal();
      }
    );
  };
  
  closeModal = function(id_div) {
    var div = $(id_div);
    div.select("input").each(function(elt, index) {
      elt.checked = window.save_checked[index];
    });
    Control.Modal.close();
  };
  
  autoRealiser = function(input) {
    var name = input.name.replace("souhait", "realise").replace("new", "temp");
    
    $A(input.form.elements[name]).each(function(elt) {
      if (elt.value == input.value) {
        elt.checked = "checked";
      }
    });
  };
  
  switchToNew = function(input) {
    input.name = input.name.replace("[temp]", "[new]");
  };

  switchToNewSousItem = function(input) {
    var input_item = input.up('fieldset').down('legend').down('input');
    input_item.checked = true;
    input.up('td').select('.sous_item').each(function(input) {
      input.checked = false;
    });
    input.checked = true;
    switchToNew(input_item);
    switchToNew(input);
  };

  onSubmitLiaisons = function(form) {
    return onSubmitFormAjax(form, function() {
      form.up('div.modal').down('button.change').click();
    });
  };

  emptyLiaison = function(button) {
    var tr_souhait = button.up("tr").next("tr");
    var tr_realise = tr_souhait.next("tr");
    tr_souhait.down("input[type=radio]").checked = "checked";
    tr_souhait.down("input[type=radio]").onclick();
    tr_realise.down("input[type=radio]").checked = "checked";
    tr_realise.down("input[type=radio]").onclick();
  }
</script>
{{math equation=x+2 x=$dates|@count assign="colspan"}}

<form name="add_prestation_ponctuelle" method="post" action="?" onsubmit="return onSubmitLiaisons(this);">
  <input type="hidden" name="m" value="dPhospi" />
  <input type="hidden" name="dosql" value="do_add_item_ponctuelle_aed" />
  <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
  <input type="hidden" name="item_prestation_id" value="" />
  <input type="hidden" name="date" value="" />
</form>

<div style="height: 100%; overflow-y: auto;">
  <form name="edit_prestations" method="post" action="?" onsubmit="return onSubmitLiaisons(this);">
    <input type="hidden" name="m" value="dPhospi"/>
    <input type="hidden" name="dosql" value="do_items_liaisons_aed" />
    <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
    
    <table class="tbl">
      <tr>
        <th class="title narrow"></th>
        <th class="title" style="max-width: 45%" colspan="{{$prestations_j|@count}}">Journali�res</th>
        {{if $context == "all"}}
          <th class="title">
            Ponctuelles
          </th>
        {{/if}}
      </tr>
      
      {{assign var=count_prestations value=$prestations_j|@count}}
      {{math equation=45/x x=$count_prestations assign=width_prestation}}
      
      <tr>
        <th class="narrow"></th>
        {{foreach from=$prestations_j item=_prestation}}
          <th style="width: {{$width_prestation}}%" class="text">{{$_prestation->nom}}</th>
        {{/foreach}}
        {{if $context == "all"}}
          <th>
              {{if $editRights}}
                <input type="hidden" name="date" class="date" value="{{$today}}"/>
                <input type="text" class="autocomplete" name="prestation_ponctuelle_view"/>
                <div class="autocomplete" id="prestation_autocomplete" style="display: none; color: #000; text-align: left;"></div>
              {{/if}}
              <script>
                Main.add(function() {
                  var form = getForm("edit_prestations");
                  var url = new Url("system", "httpreq_field_autocomplete");
                  url.addParam("class","CItemPrestation");
                  url.addParam("field", "nom");
                  url.addParam("limit", 30);
                  url.addParam("view_field", "name");
                  url.addParam("show_view", true);
                  url.addParam("input_field", "prestation_ponctuelle_view");
                  url.addParam("where[object_class]", "CPrestationPonctuelle");
                  url.autoComplete(form.prestation_ponctuelle_view, "prestation_autocomplete", {
                    minChars: 3,
                    method: "get",
                    select: "view",
                    dropdown: true,
                    afterUpdateElement: function(field,selected){
                      var item_prestation_id = selected.id.split("-").last();
                      var form_prestation = getForm("add_prestation_ponctuelle");
                      $V(form_prestation.item_prestation_id, item_prestation_id);
                      $V(form_prestation.date, $V(form.date));
                      form_prestation.onsubmit();
                    }
                  });
                  var dates = {
                    limit: {
                      start: "{{$sejour->entree}}",
                      stop:  "{{$sejour->sortie}}"
                    }
                  };
                  new Calendar.regField(form.date, dates);
                });
              </script>
          </th>
        {{/if}}
      </tr>
      {{foreach from=$dates item=_affectation_id key=_date name=foreach_date}}
        {{assign var=first_date value=$smarty.foreach.foreach_date.first}}
        {{assign var=day value=$_date|date_format:"%A"|upper|substr:0:1}}
        <tr class="{{if $_date == $relative_date}}border-bold{{/if}}">
          <td class="{{if $_date == $today}}current_hour{{/if}}"
            style="
            {{if $day == "S" || $day == "D"}}
              background-color: #ccc;
            {{elseif in_array($day, $bank_holidays)}}
              background-color: #fc0;
            {{/if}}">

            <span>
              {{if $editRights}}
                <button type="button" class="{{if array_key_exists($_date, $date_modified)}}edit{{else}}add{{/if}} notext" onclick="openModal('edit_{{$_date}}');"></button>
              {{/if}}
              <strong>{{$_date|date_format:"%d/%m"}} {{$day}}</strong>
            </span>
            
            <div id="edit_{{$_date}}" style="display: none;">
              <table class="form">
                <th class="title" colspan="2">
                  {{$_date|date_format:$conf.date}}
                </th>
                {{foreach from=$prestations_j item=_prestation key=prestation_id}}
                  {{if isset($liaisons_j.$_date.$prestation_id|smarty:nodefaults)}} 
                    {{assign var=liaison value=$liaisons_j.$_date.$prestation_id}}
                  {{else}}
                    {{assign var=liaison value=$empty_liaison}}
                  {{/if}}
                  <tr>
                    <th class="category" colspan="2">
                      {{mb_include module=system template=inc_object_history object=$liaison}}
                      <button type="button" class="cancel notext compact" onclick="emptyLiaison(this)" style="float: left;"></button>
                      {{$_prestation}}
                    </th>
                  </tr>
                  <tr>
                    <th>
                      Souhait
                    </th>
                    <td>
                      {{mb_include module=planningOp template=inc_vw_prestations_line}}
                    </td>
                  </tr>
                  <tr {{if $context != "all" || !$conf.dPhospi.show_realise}}style="display: none;"{{/if}}>
                    <th>
                      R�alis�
                    </th>
                    <td>
                      {{mb_include module=planningOp template=inc_vw_prestations_line type=realise}}
                    </td>
                  </tr>
                {{/foreach}}
                <tr>
                  <td class="button" colspan="2">
                    <button type="button" class="tick" onclick="this.form.onsubmit();Control.Modal.close();">{{tr}}Validate{{/tr}}</button>
                    <button type="button" class="cancel" onclick="closeModal('edit_{{$_date}}')">{{tr}}Close{{/tr}}</button>
                  </td>
                </tr>
              </table>
            </div>
          </td>
          
          {{foreach from=$prestations_j item=_prestation key=prestation_id name=foreach_presta}}
            {{if isset($liaisons_j.$_date.$prestation_id|smarty:nodefaults)}} 
              {{assign var=liaison value=$liaisons_j.$_date.$prestation_id}}
            {{else}}
              {{assign var=liaison value=$empty_liaison}}
            {{/if}}
            {{assign var=next_date value="CMbDT::date"|static_call:"+1 day":$_date}}
            {{if isset($liaisons_j.$next_date.$prestation_id|smarty:nodefaults)}}
              {{assign var=next_liaison value=$liaisons_j.$next_date.$prestation_id}}
            {{else}}
              {{assign var=next_liaison value=$empty_liaison}}
            {{/if}}

            {{assign var=item_presta value=$liaison->_ref_item}}
            {{assign var=item_presta_realise value=$liaison->_ref_item_realise}}

            {{assign var=next_item_presta value=$next_liaison->_ref_item}}
            {{assign var=next_item_presta_realise value=$next_liaison->_ref_item_realise}}

            {{assign var=sous_item value=$liaison->_ref_sous_item}}

            <td style="text-align: center;" class="
              {{if $item_presta->_id && $item_presta_realise->_id}}
                {{if $item_presta->rank < $item_presta_realise->rank}}
                  item_superior
                {{elseif $item_presta->rank == $item_presta_realise->rank}}
                  item_egal
                {{else}}
                  item_inferior
                {{/if}}
              {{/if}}">
              {{if $item_presta->_id}}
                {{if $item_presta_realise->_id && $item_presta->nom != $item_presta_realise->nom}}
                  <span {{if $item_presta_realise->color}}class="mediuser" style="border-left-color: #{{$item_presta_realise->color}}"{{/if}}>
                    {{$item_presta_realise->nom}}
                  </span> <br />
                  vs. <br />
                  <span {{if $item_presta->color}}class="mediuser" style="border-left-color: #{{$item_presta->color}}"{{/if}}>
                    {{if $sous_item->item_prestation_id == $item_presta->_id}}{{$sous_item->nom}}{{else}}{{$item_presta->nom}}{{/if}}
                  </span>
                {{else}}
                  <span {{if $item_presta->color}}class="mediuser" style="border-left-color: #{{$item_presta->color}}"{{/if}}>
                    {{if $sous_item->item_prestation_id == $item_presta->_id}}{{$sous_item->nom}}{{else}}{{$item_presta->nom}}{{/if}}
                  </span>
                {{/if}}
              {{elseif $item_presta_realise->_id}}
                <span {{if $item_presta_realise->color}}class="mediuser" style="border-left-color: #{{$item_presta_realise->color}}"{{/if}}>
                  {{if $sous_item->item_prestation_id == $item_presta_realise->_id}}{{$sous_item->nom}}{{else}}{{$item_presta_realise->nom}}{{/if}}
                </span>
              {{/if}}
            </td>
          {{/foreach}}
          
          {{if $context == "all"}}
            <td>
              {{if isset($liaisons_p.$_date|smarty:nodefaults)}}
                {{foreach from=$liaisons_p.$_date item=_liaisons_by_prestation key=prestation_id}}
                    {{assign var=prestation value=$prestations_p.$prestation_id}}
                    {{foreach from=$_liaisons_by_prestation item=_liaison}}
                      {{assign var=_item value=$_liaison->_ref_item}}
                      <div style="float: left; width: 8em;">
                        <span onmouseover="ObjectTooltip.createEx(this, '{{$_item->_guid}}');"
                              {{if $_item->color}}class="mediuser" style="border-left-color: #{{$_item->color}}"{{/if}}>
                          {{$_item}}
                        </span> :
                      </div>
                      <div style="float: left; width: 4em; padding-right: 2em;">
                        <input type="text" class="ponctuelle" name="liaisons_p[{{$_liaison->_id}}]" value="{{$_liaison->quantite}}" size="1" onchange="this.form.onsubmit()"/>
                        <script type="text/javascript">
                          Main.add(function() {
                             getForm('edit_prestations').elements['liaisons_p[{{$_liaison->_id}}]'].addSpinner(
                             {step: 1, min: 0});
                          });
                        </script>
                      </div>
                    {{/foreach}}
                {{/foreach}}
              {{/if}}
            </td>
          {{/if}}
        </tr>
      {{/foreach}}
    </table>
    <p style="text-align: center"><button type="button" class="cancel" onclick="Control.Modal.close();">{{tr}}Close{{/tr}}</button></p>
  </form>
</div>
