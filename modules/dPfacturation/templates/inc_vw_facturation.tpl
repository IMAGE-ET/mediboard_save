<!-- Facture -->
{{if $facture && $facture->_id}}
<fieldset>
  <legend>{{tr}}{{$facture->_class}}{{/tr}}: {{$facture}}</legend>
  <table class="main tbl">

    {{if $facture->cloture}}
    <tr>
      <td colspan="10">
        <div class="small-info">
          <strong>La facture est clotur�e.</strong>
          Pour pouvoir ajouter des �l�ments, veuillez la rouvrir.
        </div>
      </td>
    </tr>
    {{/if}}
    
    {{mb_include module=dPfacturation template=inc_vw_facturation_tarmed}}
    {{mb_include module=dPfacturation template=inc_vw_facturation_t2a   }}

  </table> 
</fieldset>
{{/if}}

<!-- Reglement -->
{{if $facture->_id && $facture->cloture}}
  <div id="reglements_facture">
    {{mb_include module=dPfacturation template="inc_vw_reglements"}}
  </div>
{{/if}}