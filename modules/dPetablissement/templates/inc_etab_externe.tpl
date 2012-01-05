
      {{mb_script module="dPpatients" script="autocomplete"}}
      <script type="text/javascript">
      Main.add(function () {
        InseeFields.initCPVille("etabExterne", "cp", "ville","tel");
      });
      </script>

      <form name="etabExterne" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_etabExterne_aed" />
      <input type="hidden" name="etab_id" value="{{$etabExterne->_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {{if $etabExterne->_id}}
          <th class="title text modify" colspan="2">
            {{mb_include module=system template=inc_object_idsante400 object=$etabExterne}}
            {{mb_include module=system template=inc_object_history object=$etabExterne}}
            Modification de l'�tablissement '{{$etabExterne->nom}}'
          {{else}}
          <th class="title" colspan="2">
            Cr�ation d'un �tablissement externe
          {{/if}}
          </th>
        </tr>
        <tr>
          <th>{{mb_label object=$etabExterne field="nom"}}</th>
          <td>{{mb_field object=$etabExterne field="nom" tabindex="1"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$etabExterne field="raison_sociale"}}</th>
          <td>{{mb_field object=$etabExterne field="raison_sociale" tabindex="2"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$etabExterne field="adresse"}}</th>
          <td>{{mb_field object=$etabExterne field="adresse" tabindex="3"}}</td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$etabExterne field="cp"}}</th>
          <td>{{mb_field object=$etabExterne field="cp" tabindex="4"}}</td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$etabExterne field="ville"}}</th>
          <td>{{mb_field object=$etabExterne field="ville" tabindex="5"}}</td>
        </tr>
        
        
        <tr>
          <th>{{mb_label object=$etabExterne field="tel"}}</th>
          <td>{{mb_field object=$etabExterne field="tel" tabindex="6"}}</td>
        </tr>
        <tr>
           <th>{{mb_label object=$etabExterne field="fax"}}</th>
           <td>{{mb_field object=$etabExterne field="fax" tabindex="7"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$etabExterne field="finess"}}</th>
          <td>{{mb_field object=$etabExterne field="finess" tabindex="8"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$etabExterne field="siret"}}</th>
          <td>{{mb_field object=$etabExterne field="siret"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$etabExterne field="ape"}}</th>
          <td>{{mb_field object=$etabExterne field="ape"}}</td>
        </tr>
        <tr>
          <td class="button" colspan="2">
          {{if $etabExterne->_id}}
            <button class="modify" type="submit" name="modify">
              {{tr}}Save{{/tr}}
            </button>
            <button class="trash" type="button" name="delete" onclick="confirmDeletion(this.form,{typeName:'l\'�tablissement',objName:'{{$etabExterne->nom|smarty:nodefaults|JSAttribute}}'})">
              {{tr}}Delete{{/tr}}
            </button>
          {{else}}
            <button class="new" type="submit" name="create">
              {{tr}}Create{{/tr}}
            </button>
          {{/if}}
          </td>
        </tr>
      </table>
      </form>