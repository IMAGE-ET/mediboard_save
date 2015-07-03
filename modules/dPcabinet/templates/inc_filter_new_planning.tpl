{{*
 * $Id$
 *  
 * @category Cabinet
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<table class="tbl">
  <tr>
    <th>Afficher les plages libres</th>
    <td>
      <select name="show_free">
        <option value="1" selected>Oui</option>
        <option value="0">Non</option>
      </select>
    </td>
  </tr>

  <tr>
    <th>Afficher les consultations annul�es</th>
    <td>
      <select name="cancelled">
        <option value="1">Oui</option>
        <option value="0" selected>Non</option>
      </select>
    </td>
  </tr>

  <tr>
    <th>Statut des consultations factur�es</th>
    <td>
      <select name="facturated">
        <option value="">Tout voir</option>
        <option value="1">Seulement les factur�s</option>
        <option value="0">Seulement les non factur�s</option>
      </select>
    </td>
  </tr>

  <tr>
    <th>Statut des rdv</th>
    <td>
      <select name="finished">
        <option value="">&mdash; {{tr}}All{{/tr}}</option>
        <option value="16">Planifi�</option>
        <option value="32">Patient arriv�</option>
        <option value="48">En cours</option>
        <option value="64">Termin�</option>
      </select>
    </td>
  </tr>

  <tr>
    <th>Actes {{if $conf.ref_pays == 2}}tarmed{{/if}}</th>
    <td>
      <select name="actes">
        <option value="">&mdash; Tous</option>
        <option value="1">Seulement les cot�s</option>
        <option value="0">Seulement les non cot�s</option>
      </select>
    </td>
  </tr>

  <tr>
    <th>Affichage en fonction des cong�s</th>
    <td>
      <select name="hide_in_conge">
        <option value="0">Tout afficher</option>
        <option value="1">Cacher tout si cong�s</option>
      </select>
    </td>
  </tr>

  <tr>
    <td colspan="2" class="button">
      <button class="tick" type="button" onclick="Control.Modal.close();">{{tr}}OK{{/tr}}</button>
    </td>
  </tr>
</table>
