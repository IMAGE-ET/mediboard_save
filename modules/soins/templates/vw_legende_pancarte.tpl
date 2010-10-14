{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage soins
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=specs_chapitre value=$prescription->_specs._chapitres}}
{{assign var=images value="CPrescription"|static:"images"}}

<table class="tbl">
  <tr>
    <th colspan="2">L�gende</th>
  </tr>
  {{foreach from=$specs_chapitre->_list item=_chapitre}}
	{{if array_key_exists($_chapitre, $images)}}
  <tr>
    <td class="narrow"><img src="{{$images.$_chapitre}}" /></td>
    <td>{{tr}}CPrescription._chapitres.{{$_chapitre}}{{/tr}}</td>
  </tr>
	{{/if}}
  {{/foreach}}
  <tr>
    <td class="narrow"><img src="images/icons/ampoule.png" /></td>
    <td>Ligne modifi�e r�cemment</td>
  </tr>
  <tr>
    <td class="narrow"><img src="images/icons/ampoule_urgence.png" /></td>
    <td>Ligne urgente</td>
  </tr>
  <tr>
    <th colspan="2">
      Couleurs possibles
    </th>
  </tr>
  <tr>
    <td  style="background-color: #B2FF9B; height: 100%;">
    
    </td>
    <td>
      Enti�rement effectu�
    </td>
  </tr>
  <tr>
    <td style="background-color: #FB4; height: 100%;">
      
    </td>
    <td>
      Partiellement ou pas effectu�
    </td>
  </tr>
</table>