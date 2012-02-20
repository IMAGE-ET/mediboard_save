<script type="text/javascript">
  uncheckPrestation = function(elts) {
    elts.each(function(elt) {
      elt.checked = "";
    });
  }
  
  openModal = function(id_div) {
    var div = $(id_div);
    window.save_checked = div.select("input").pluck("checked");
    modal(id_div);
  }
  
  closeModal = function(id_div) {
    var div = $(id_div);
    div.select("input").each(function(elt, index) {
      elt.checked = window.save_checked[index];
    });
    Control.Modal.close();
  }
</script>
{{math equation=x+2 x=$dates|@count assign="colspan"}}

<form name="add_prestation_ponctuelle" method="post" action="?"
  onsubmit="return onSubmitFormAjax(this, {onComplete: function() {
    getForm('add_prestation_ponctuelle').up('div').up('div').select('button.change')[0].onclick(); }})">
  <input type="hidden" name="m" value="dPhospi" />
  <input type="hidden" name="dosql" value="do_add_item_ponctuelle_aed" />
  <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
  <input type="hidden" name="item_prestation_id" value="" />
  <input type="hidden" name="date" value="" />
</form>

<div style="height: 100%; overflow-y: auto;">
  <form name="edit_prestations" method="post" action="?" onsubmit="return onSubmitFormAjax(this, {onComplete: function() {
    getForm('add_prestation_ponctuelle').up('div').up('div').select('button.change')[0].onclick(); }})">
    <input type="hidden" name="m" value="dPhospi"/>
    <input type="hidden" name="dosql" value="do_items_liaisons_aed" />
    <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
    
    <table class="tbl">
      <tr>
        <th class="title narrow"></th>
        <th class="title" style="max-width: 45%" colspan="{{$prestations_j|@count}}">Journali�res</th>
        {{if $vue_prestation == "all"}}
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
          <th style="width: {{$width_prestation}}%">{{$_prestation->nom}}</th>
        {{/foreach}}
        {{if $vue_prestation == "all"}}
          <th>
            <div>
              <input type="hidden" name="date" class="date" value="{{$sejour->entree|date_format:"%Y-%m-%d"}}"/>
              <input type="text" class="autocomplete" name="prestation_ponctuelle_view"/>
              <div class="autocomplete" id="prestation_autocomplete" style="display: none; color: #000; text-align: left;"></div>
            </div>
            <script type="text/javascript">
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
                }
                new Calendar.regField(form.date, dates);
              });
            </script>
          </th>
        {{/if}}
      </tr>
      {{foreach from=$dates item=_affectation_id key=_date name=foreach_date}}
        {{assign var=first_date value=$smarty.foreach.foreach_date.first}}
        {{assign var=day value=$_date|date_format:"%A"|upper|substr:0:1}}
        <tr>
          <td {{if $_date == $today}}class="current_hour"{{/if}}
            style="
            {{if $day == "S" || $day == "D"}}
              background: #ccc;
            {{elseif in_array($day, $bank_holidays)}}
              background: #fc0; 
            {{/if}}">
            
            <span>
              <button type="button" class="edit notext" onclick="openModal('edit_{{$_date}}');"></button>
              <strong>{{$_date|date_format:"%d/%m"}} {{$day}}</strong>
            </span>
            
            
            
            <div class="modal" id="edit_{{$_date}}" style="display: none;">
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
                      {{$liaison->_id}}
                      {{$_prestation}}
                    </th>
                  </tr>
                  <tr>
                    <th>
                      Souhait
                    </th>
                    <td>
                      {{foreach from=$_prestation->_ref_items item=_item}}
                        <label>
                          <input type="radio"
                            name="liaisons_j[{{$prestation_id}}][{{$_date}}][souhait][{{$liaison->_id}}]"
                            onclick="var elt_hidden = this.next(); $V(elt_hidden, this.checked ? '{{$_item->_id}}' : 0);"
                            {{if $liaison->item_prestation_id == $_item->_id}}checked="checked"{{/if}} value="{{$_item->_id}}"/>{{$_item->nom}}
                        </label>
                      {{/foreach}}
                    </td>
                  </tr>
                  {{if $vue_prestation == "all"}}
                    <tr>
                      <th>
                        R�alis�
                      </th>
                      <td>
                        {{foreach from=$_prestation->_ref_items item=_item}}
                          <label>
                            <input type="radio"
                              name="liaisons_j[{{$prestation_id}}][{{$_date}}][realise][{{$liaison->_id}}]"
                              onclick="var elt_hidden = this.next(); $V(elt_hidden, this.checked ? '{{$_item->_id}}' : 0);"
                              {{if $liaison->item_prestation_realise_id == $_item->_id}}checked="checked"{{/if}} value="{{$_item->_id}}"/>{{$_item->nom}}
                          </label>
                        {{/foreach}}
                      </td>
                    </tr>
                  {{/if}}
                {{/foreach}}
                <tr>
                  <td class="button" colspan="2">
                    <button type="button" class="tick" onclick="Control.Modal.close(); this.form.onsubmit()">{{tr}}Validate{{/tr}}</button>
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
            {{assign var=item_presta value=$liaison->_ref_item}}
            {{assign var=item_presta_realise value=$liaison->_ref_item_realise}}
            
            <td class="button
              {{if $item_presta->_id && $item_presta_realise->_id}}
                {{if $item_presta->rank == $item_presta_realise->rank}}
                  item_egal
                {{elseif $item_presta->rank > $item_presta_realise->rank}}
                  item_inferior
                {{else}}
                  item_superior
                {{/if}}
              {{/if}}">
              
              {{if $item_presta->_id}}
                {{if $item_presta_realise->_id && $item_presta->nom != $item_presta_realise->nom}}
                  {{$item_presta_realise->nom}} <br />
                  vs. <br />
                  {{$item_presta->nom}}
                {{else}}
                  {{$item_presta->nom}}
                {{/if}}
              {{/if}}
            </td>
          {{/foreach}}
          
          {{if $vue_prestation == "all"}}
            <td>
              {{if isset($liaisons_p.$_date|smarty:nodefaults)}}
                <table class="tbl">
                  <tr>
                    {{foreach from=$liaisons_p.$_date item=_liaison key=prestation_id}}
                      {{assign var=prestation value=$prestations_p.$prestation_id}}
                      <th>
                        {{$prestation->nom}}
                      </th>
                    {{/foreach}}
                  </tr>
                  <tr>
                    {{foreach from=$liaisons_p.$_date item=_liaisons_by_prestation key=prestation_id}}
                      <td>
                        {{assign var=prestation value=$prestations_p.$prestation_id}}
                        {{foreach from=$_liaisons_by_prestation item=_liaison}}
                          {{assign var=_item value=$_liaison->_ref_item}}
                          {{$_item->nom}} :
                          <input type="text" name="liaisons_p[{{$_liaison->_id}}]" value="{{$_liaison->quantite}}" style="width: 3em;" onchange="this.form.onsubmit()"/>
                          <script type="text/javascript">
                            Main.add(function() {
                               getForm('edit_prestations').elements['liaisons_p[{{$_liaison->_id}}]'].addSpinner(
                               {step: 1, min: 0});
                            });
                          </script>
                        {{/foreach}}
                      </td>
                    {{/foreach}}
                  </tr>
                </table>
              {{/if}}
            </td>
          {{/if}}
        </tr>
      {{/foreach}}
      </table>
      <tr>
          <td class="button" colspan="{{$colspan}}">
            <button type="button" class="save" onclick="this.form.onsubmit();">{{tr}}Save{{/tr}}</button>
            <button type="button" class="cancel" onclick="Control.Modal.close();">{{tr}}Close{{/tr}}</button>
          </td>
        </tr>
  </form>
</div>
