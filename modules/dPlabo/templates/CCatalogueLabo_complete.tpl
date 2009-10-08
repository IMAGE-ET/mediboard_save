<script type="text/javascript">
var oDragOptions = { 
  revert: true,
  ghosting: true,
  starteffect : function(element) { 
    $(element).addClassName("dragged");
    new Effect.Opacity(element, { duration:0.2, from:1.0, to:0.7 }); 
  },
  reverteffect: function(element, top_offset, left_offset) {
    var dur = Math.sqrt(Math.abs(top_offset^2)+Math.abs(left_offset^2))*0.02;
    element._revert = new Effect.Move(element, { 
      x: -left_offset, 
      y: -top_offset, 
      duration: dur,
      afterFinish : function (effect) { 
        $(effect.element).removeClassName("dragged");
      }
    } );
  },
  endeffect: function(element) { 
    new Effect.Opacity(element, { duration:0.2, from:0.7, to:1.0 } ); 
  }       
}

</script>
  

<table class="tbl">
  <tr>
    <th class="title" colspan="6">
      {{mb_include module=system template=inc_object_idsante400 object=$object}}
      {{mb_include module=system template=inc_object_history object=$object}}
      {{$object->_view}}
    </th>
  </tr>
  <tr>
    <th class="category">Analyse</th>
    <th class="category">Type</th>
    <th class="category">Unit�</th>
    <th class="category">Min</th>
    <th class="category">Max</th>
  </tr>
  {{foreach from=$object->_ref_examens_labo item="curr_examen"}}
  <tr>
    <td>
      <div class="draggable" id="examen-{{$curr_examen->_id}}">
        <script type="text/javascript">
        new Draggable('examen-{{$curr_examen->_id}}', oDragOptions);
        </script>
        {{$curr_examen->_view}}
      </div>
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