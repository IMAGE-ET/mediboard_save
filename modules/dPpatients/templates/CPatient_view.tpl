{{assign var="patient" value=$object}}

<table class="tbl tooltip">
  <tr>
    <th colspan="2">
      {{$patient->_view}}
    </th>
  </tr>
  <tr>
    <td>
      <table class="tbl">
        <tr>
          <th>Identit�</th>
        </tr>
        <tr>
          <td>
            <strong>{{mb_label object=$patient field="nom"}} :</strong>
            {{mb_value object=$patient field="nom"}}
          </td>
        </tr>
        <tr>
          <td>
            <strong>{{mb_label object=$patient field="prenom"}} :</strong>
            {{mb_value object=$patient field="prenom"}}
          </td>
        </tr>
        <tr>
          <td>
            <strong>{{mb_label object=$patient field="sexe"}} :</strong>
            {{mb_value object=$patient field="sexe"}}
          </td>
        </tr>
        <tr>
          <td>
            <strong>{{mb_label object=$patient field="naissance"}} :</strong>
            {{mb_value object=$patient field="naissance"}}
          </td>
        </tr>
        <tr>
          <td>
            <strong>{{mb_label object=$patient field="profession"}} :</strong>
            {{mb_value object=$patient field="profession"}}
          </td>
        </tr>
        <tr>
          <td>
            <strong>{{mb_label object=$patient field="matricule"}} :</strong>
            {{mb_value object=$patient field="matricule"}}
          </td>
        </tr>
      </table>
    </td>
    <td>
      <table class="tbl">
        <tr>
          <th>Coordonn�es</th>
        </tr>
        <tr>
          <td>
            <strong>{{mb_label object=$patient field="adresse"}} :</strong>
            {{mb_value object=$patient field="adresse"}}
          </td>
        </tr>
        <tr>
          <td>
            <strong>{{mb_label object=$patient field="cp"}} :</strong>
            {{mb_value object=$patient field="cp"}}
          </td>
        </tr>
        <tr>
          <td>
            <strong>{{mb_label object=$patient field="ville"}} :</strong>
            {{mb_value object=$patient field="ville"}}
          </td>
        </tr>
        <tr>
          <td>
            <strong>{{mb_label object=$patient field="tel"}} :</strong>
            {{mb_value object=$patient field="tel"}}
          </td>
        </tr>
        <tr>
          <td>
            <strong>{{mb_label object=$patient field="tel2"}} :</strong>
            {{mb_value object=$patient field="tel2"}}
          </td>
        </tr>
        <tr>
          <td>
            <strong>{{mb_label object=$patient field="rques"}} :</strong>
            {{mb_value object=$patient field="rques"}}
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>