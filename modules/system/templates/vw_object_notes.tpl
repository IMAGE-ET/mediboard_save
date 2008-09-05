<table class="note">
  {{foreach from=$notes item="curr_note"}}
  <tr>
    <th class="info {{$curr_note->degre}}">
      {{$curr_note->_ref_user->_view}}
      ({{$curr_note->_ref_user->_ref_function->_view}})
      <br />
      {{$curr_note->date|date_format:"%d/%m/%Y %Hh%M"}}
      <br /> 
    </th>
  </tr>
  <tr>
    <td class="text">
      {{if $user == $curr_note->user_id}}
        <form name="delNoteFrm{{$curr_note}}" action="" method="post">
        <input type="hidden" name="m" value="system" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="dosql" value="do_note_aed" />
        {{mb_field object=$curr_note field="note_id" hidden=1 prop=""}}
        
        <button style="float: right;" class="cancel notext" type="button" onclick="confirmDeletion(this.form, {typeName:'cette note',ajax:1,target:'systemMsg'},{onComplete:initNotes})">
        </button>
        </form>
      {{/if}}
      <strong>
        {{$curr_note->libelle}}
      </strong><br />
      {{$curr_note->text|nl2br}}
    </td>
  </tr>
  {{foreachelse}}
  <tr>
    <td>Pas de note visible</td>
  </tr>
  {{/foreach}}
</table>