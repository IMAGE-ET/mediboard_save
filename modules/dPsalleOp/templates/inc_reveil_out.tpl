      <table class="form">
        <tr>
          <th class="category">{$listOut|@count} patients sortis du bloc</th>
        </tr>
      </table>

      <table class="tbl">
        <tr>
          <th>Salle</th>
          <th>Praticien</th>
          <th>Patient</th>
          <th>Sortie Salle</th>
          <th>Entr�e reveil</th>
          <th>Sortie reveil</th>
        </tr> 
        {foreach from=$listOut key=key item=curr_op}
        <tr>
          <td>{$curr_op->_ref_plageop->_ref_salle->nom}</td>
          <td>Dr. {$curr_op->_ref_chir->_view}</td>
          <td class="text">{$curr_op->_ref_pat->_view}</td>
          <td>{$curr_op->sortie_bloc|date_format:"%Hh%M"}</td>
          <td>{$curr_op->entree_reveil|date_format:"%Hh%M"}</td>
          <td>
            <form name="editFrm{$curr_op->operation_id}" action="index.php" method="get">
              <input type="hidden" name="m" value="dPsalleOp" />
              <input type="hidden" name="a" value="do_set_hours" />
              <input type="hidden" name="operation_id" value="{$curr_op->operation_id}" />
              <input type="hidden" name="type" value="sortie_reveil" />
              <input type="hidden" name="del" value="0" />
              <select name="hour" onchange="this.form.submit()">
                {foreach from=$timing.$key.sortie_reveil item=curr_time}
                <option value="{$curr_time}" {if $curr_time == $curr_op->sortie_reveil}selected="selected"{/if}>
                  {$curr_time|date_format:"%Hh%M"}
                </option>
                {/foreach}
              </select>
              <button type="submit" onclick="this.form.del.value = 1">
                <img src="modules/{$m}/images/cross.png" alt="supprimer" />
              </button>
            </form>
          </td>
        </tr>
        {/foreach}
      </table>