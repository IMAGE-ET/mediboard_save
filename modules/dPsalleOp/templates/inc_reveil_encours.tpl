<script type="text/javascript">  
  Main.add(function () { 
    Control.Tabs.setTabCount("encours", "{{$listOperations|@count}}");
       
    {{if $isImedsInstalled}}
      ImedsResultsWatcher.loadResults();
    {{/if}}
  });
</script>

<table class="tbl">
  <tr>
    <th>{{tr}}SSPI.Salle{{/tr}}</th>
    <th>{{tr}}SSPI.Praticien{{/tr}}</th>
    <th>{{tr}}SSPI.Patient{{/tr}}</th>
    <th class="narrow"></th>
    <th>{{mb_title class=COperation field=entree_salle}}</th>
		<th>{{mb_title class=COperation field=debut_op}}</th>
    <th class="narrow"></th>
  </tr>    
  {{foreach from=$listOperations item=_operation}}
    <tr>
      <td>{{$_operation->_ref_salle->_shortview}}</td>

      <td class="text">
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_operation->_ref_chir}}
      </td>

      <td class="text">
        <div style="float: right;">
          {{if $isImedsInstalled}}
            {{mb_include module=Imeds template=inc_sejour_labo link="#1" sejour=$_operation->_ref_sejour float="none"}}
          {{/if}}
        </div>

          <span class="{{if !$_operation->_ref_sejour->entree_reelle}}patient-not-arrived{{/if}} {{if $_operation->_ref_sejour->septique}}septique{{/if}}"
                onmouseover="ObjectTooltip.createEx(this, '{{$_operation->_ref_sejour->_ref_patient->_guid}}')">
            {{$_operation->_ref_patient->_view}}
          </span>
      </td>
      <td>
        <button class="button soins notext" onclick="showDossierSoins('{{$_operation->sejour_id}}','{{$_operation->_id}}');">
          Dossier de soin
        </button>
      </td>

      <td>{{mb_value object=$_operation field=entree_salle}}</td>

      <td>
        {{if $_operation->debut_op}}
          {{mb_value object=$_operation field=debut_op}}
        {{else}}
          -
        {{/if}}
      </td>
      <td>
        <button type="button" class="print notext"
          onclick="printDossier('{{$_operation->sejour_id}}', '{{$_operation->_id}}')"></button>
      </td>
    </tr>
  {{foreachelse}}
    <tr><td colspan="7" class="empty">{{tr}}COperation.none{{/tr}}</td></tr>
  {{/foreach}}
</table>