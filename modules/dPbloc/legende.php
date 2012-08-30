<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

?>

<table class="tbl">
  <tr>
    <td class="patient-not-arrived">M. PATIENT Patient</td>
    <td class="text">Patient pas encore dans l'�tablissement</td>
  </tr>
  <tr>
    <td class="septique">M. PATIENT Patient</td>
    <td class="text">Patient septique</td>
  </tr>   
  <tr>
    <td style="background-color:#ffa"></td>
    <td class="text">Patient entr� au bloc</td>
  </tr>       
  <tr>
    <td style="background-color:#cfc"></td>
    <td class="text">Intervention en cours</td>
  </tr>
  <tr>
    <td style="background-image:url(images/icons/ray.gif); background-repeat:repeat;"></td>
    <td class="text">Intervention termin�e</td>
  </tr> 
  <tr>
    <td style="background-color:#fcc"></td>
    <td class="text">Probl�me de timing</td>
  </tr>
  <tr>
    <td style="background-color:#ccf"></td>
    <td class="text">Intervention d�plac�e dans une autre salle</td>
  </tr>
  <tr>
    <td>
      <span class="mediuser" style="border-color: #F99">&nbsp;</span>
    </td>
    <td class="text">Aucun acte cod�</td>
  </tr>
</table>