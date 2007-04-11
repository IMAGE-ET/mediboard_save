<script type="text/javascript">

var Pack = {
  dropExamen: function(examen_id, pack_id) {
    oForm = $('newPackItem');
    oForm.examen_labo_id.value       = examen_id.substring(7);
    oForm.pack_examens_labo_id.value = pack_id;
    submitFormAjax(oForm, 'systemMsg', { onComplete: reloadPacks });
    return true;
  },
  delExamen: function(oForm) {
    oFormBase = $('newPackItem');
    oFormBase.pack_examens_labo_id.value = {{$pack->_id}};
    submitFormAjax(oForm, 'systemMsg', { onComplete: reloadPacks });
    return true;
  }
}
  
</script>

<form name="editPackItem" id="newPackItem" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="m" value="dPlabo" />
  <input type="hidden" name="dosql" value="do_pack_item_aed" />
  <input type="hidden" name="pack_item_examen_labo_id" value="" />
  <input type="hidden" name="examen_labo_id" value="" />
  <input type="hidden" name="pack_examens_labo_id" value="" />
  <input type="hidden" name="del" value="0" />
</form>

{{foreach from=$listPacks item="curr_pack"}}
<div class="tree-header {{if $curr_pack->_id == $pack->_id}}selected{{/if}}" id="drop-pack-{{$curr_pack->_id}}">
  <script>
  Droppables.add('drop-pack-{{$curr_pack->_id}}', {
    onDrop: function(element) {
      Pack.dropExamen(element.id, {{$curr_pack->_id}})
    }, 
    hoverclass:'selected'
  } );
  </script>
  <div style="float:right;">
    {{$curr_pack->_ref_items_examen_labo|@count}} Examens
  </div>
  <a href="#nothing" onclick="reloadPacks({{$curr_pack->_id}})">
    {{$curr_pack->_view}}
  </a>
</div>
{{/foreach}}

<table class="tbl">
  <tr>
    <th class="title" colspan="6">
      <a style="float:right;" href="#nothing" onclick="view_log('{{$object->_class_name}}', {{$object->_id}})">
        <img src="images/icons/history.gif" alt="historique" title="Voir l'historique" />
      </a>
      {{$pack->_view}}
    </th>
  </tr>
  <tr>
    <th class="category">Examen</th>
    <th class="category">Type</th>
    <th class="category">Unit�</th>
    <th class="category">Min</th>
    <th class="category">Max</th>
  </tr>
  {{foreach from=$pack->_ref_items_examen_labo item="curr_item"}}
  {{assign var="curr_examen" value=$curr_item->_ref_examen_labo}}
  <tr>
    <td>
      <form name="delPackItem-{{$curr_item->_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
        <input type="hidden" name="m" value="dPlabo" />
        <input type="hidden" name="dosql" value="do_pack_item_aed" />
        <input type="hidden" name="pack_item_examen_labo_id" value="{{$curr_item->_id}}" />
        <input type="hidden" name="del" value="1" />
        <button type="button" class="trash notext" style="float: right;" onclick="Pack.delExamen(this.form)"/>
      </form>
      {{$curr_examen->_view}}
    </td>
    <td>
      <a href="?m={{$m}}&amp;tab=vw_edit_examens&amp;examen_labo_id={{$curr_examen->_id}}">
        {{$curr_examen->type}}
      </a>
    </td>
    <td>
      <a href="?m={{$m}}&amp;tab=vw_edit_examens&amp;examen_labo_id={{$curr_examen->_id}}">
        {{$curr_examen->unite}}
      </a>
    </td>
    <td>
      <a href="?m={{$m}}&amp;tab=vw_edit_examens&amp;examen_labo_id={{$curr_examen->_id}}">
        {{$curr_examen->min}} {{$curr_examen->unite}}
      </a>
    </td>
    <td>
      <a href="?m={{$m}}&amp;tab=vw_edit_examens&amp;examen_labo_id={{$curr_examen->_id}}">
        {{$curr_examen->max}} {{$curr_examen->unite}}
      </a>
    </td>
  </tr>
  {{/foreach}}
</table>