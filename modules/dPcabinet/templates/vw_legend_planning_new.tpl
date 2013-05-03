{{*
  * Legend of the new planning
  *  
  * @category Cabinet
  * @package  Mediboard
  * @author   SARL OpenXtrem <dev@openxtrem.com>
  * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
  * @version  SVN: $Id:$ 
  * @link     http://www.mediboard.org
*}}

<style>
  #legendPlanningCabinetNew td.color {
    border:solid 1px black!important;
    width:10%;
  }
</style>

<table class="tbl" id="legendPlanningCabinetNew">
  <tbody>
    <tr>
      <th>{{tr}}Color{{/tr}}</th>
      <th>{{tr}}Legend{{/tr}}</th>
    </tr>
    <tr>
      <td style="background: #cfc;" class="color"></td>
      <td>Cr�neau Libre</td>
    </tr>
    <tr>
      <td style="background: #fee;" class="color"></td>
      <td>Cr�neau r�serv�e</td>
    </tr>
    <tr>
      <td style="background: #faa;" class="color"></td>
      <td>Premi�re consultation</td>
    </tr>
    <tr>
      <td style="background: #faf;" class="color"></td>
      <td>Derni�re consultation</td>
    </tr>
    <tr>
      <td style="background: #faa;" class="color"></td>
      <td>pause</td>
    </tr>
    <tr>
      <td style="background: #faa;" class="color"></td>
      <td>est remplac� par un autre praticien</td>
    </tr>
    <tr>
      <td style="background: #fda;" class="color"></td>
      <td>remplace un autre praticien</td>
    </tr>
  </tbody>
</table>