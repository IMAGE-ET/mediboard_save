<table class="tbl">
  <tr>
    <th colspan="2">L�gende</th>
  </tr>
  <tr>
    <td style="width: 60px">
      <div class="wrapper_line">
        <div class="affect_legend clit" style="width: 33px">&nbsp;</div>
      </div>
    </td>
    <td>
      Affectation dont le d�but ou la fin ne sont pas visibles 
    </td>
  </tr>
  <tr>
    <td>
      <div class="wrapper_line">
        <div class="affect_legend clit debut_sejour" style="width: 30px">&nbsp;</div>
      </div>
    </td>
    <td>
      Affectation dont le d�but correspond au d�but du s�jour
    </td>
  </tr>
  <tr>
    <td>
      <div class="wrapper_line">
        <div class="affect_legend clit fin_sejour" style="width: 30px">&nbsp;</div>
      </div>
    </td>
    <td>
      Affectation dont la fin correspond � la fin du s�jour
    </td>
  </tr>
  <tr>
    <td>
      <div class="wrapper_line">
        <div class="affect_legend clit affect_left" style="width: 30px">&nbsp;</div>
      </div>
    </td>
    <td>
      Affectation faisant suite � une pr�c�dente affectation
    </td>
  </tr>
  <tr>
    <td>
      <div class="wrapper_line">
        <div class="affect_legend clit affect_right" style="width: 30px">&nbsp;</div>
      </div>
    </td>
    <td>
      Affectation qui fera suite � une autre affectation
    </td>
  </tr>
  <tr>
    <td>
      <div class="wrapper_line">
        <div class="affect_legend clit" style="width: 30px">
          <div class="wrapper_op">
            <div class="operation_in_mouv opacity-40" style="width: 15px; left: 7px; top: 0"></div>
          </div>
        </div>
      </div>
    </td>
    <td>
      Intervention
    </td>
  </tr>
  <tr>
    <td>
      <div class="wrapper_line">
        <div class="affect_legend clit" style="width: 30px">
          <div class="wrapper_op">
            <div class="soins_uscpo opacity-40" style="width: 15px; left: 7px; top: 0"></div>
          </div>
        </div>
      </div>
    </td>
    <td>
      Soins USCPO
    </td>
  </tr>
  <tr>
    <td>
      <div class="wrapper_line">
        <div class="affect_legend clit sejour_sortie_autorisee" style="width: 33px">&nbsp;</div>
      </div>
    </td>
    <td>
      Sortie autoris�e
    </td>
  </tr>
  <tr>
    <td>
      <div class="wrapper_line">
        <div class="affect_legend clit_bloque" style="width: 33px"><span style="font-size: xx-small;">BLOQUE</span></div>
      </div>
    </td>
    <td>
      Lit bloqu�
    </td>
  </tr>
  <tr>
    <td>
      M. X y
    </td>
    <td>
      Patient pr�sent
    </td>
  </tr>
  <tr>
    <td class="septique">
      M. X y
    </td>
    <td>
      Patient septique
    </td>
  </tr>
  <tr>
    <td style="font-style: italic">
      M. X y
    </td>
    <td>
      S�jour de type ambulatoire
    </td>
  </tr>
  <tr>
    <td>
      <span style="color: #A33">M. X y</span>
    </td>
    <td>
      Patient non pr�sent (apr�s d�placement ou premi�re affectation)
    </td>
  </tr>
  <tr>
    <td>
      <span style="text-decoration: line-through">M. X y</span>
    </td>
    <td>
      Patient sorti
    </td>
  </tr>
  <tr>
    <td class="button" colspan="2">
      <button type="button" class="cancel" onclick="Control.Modal.close();">{{tr}}Close{{/tr}}</button>
    </td>
  </tr>
</table>