      <table class="tbl">
        <tr>
          <th colspan="4"class="title">Alertes</th>
        </tr>
        <tr>
          <td>
            {{if $alertesInteractions}}
              <img src="images/icons/note_red.png" title="aucune" alt="aucune" />
            {{else}}
              <img src="images/icons/note_green.png" title="aucune" alt="aucune" />
            {{/if}}
          </td>
          <td>
            <strong>{{$alertesInteractions}}</strong>
            interactions
          </td>
          <td>
            {{if $alertesAllergies|@count}}
              <img src="images/icons/note_red.png" title="aucune" alt="aucune" />
            {{else}}
              <img src="images/icons/note_green.png" title="aucune" alt="aucune" />
            {{/if}}
          </td>
          <td>
            <strong>{{$alertesAllergies|@count}}</strong>
            hypersensibilit�(s)
          </td>
        </tr>
        <tr>
          <td>
            {{if $alertesProfil}}
              <img src="images/icons/note_red.png" title="aucune" alt="aucune" />
            {{else}}
              <img src="images/icons/note_green.png" title="aucune" alt="aucune" />
            {{/if}}
          </td>
          <td colspan="3">
            <strong>{{$alertesProfil}}</strong>
            contre-indication(s) / pr�caution(s) d'emploi
          </td>
        </tr>
        <tr>
          <td>
            {{if $alertesIPC}}
              <img src="images/icons/note_red.png" title="aucune" alt="aucune" />
            {{else}}
              <img src="images/icons/note_green.png" title="aucune" alt="aucune" />
            {{/if}}
          </td>
          <td colspan="3">
            <strong>{{$alertesIPC}}</strong>
            incompatibilit�(s) physico-chimique(s)
          </td>
        </tr>
      </table>