{{* $Id: inc_main_courante.tpl 8462 2010-04-05 09:55:09Z MyttO $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 8462 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if !isset($print|smarty:nodefaults)}}
  <a href="{{$rpu_link}}">
{{else}}
  <div>
{{/if}}
  <strong onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}};')">
    <big class="CPatient-view">{{$patient}}</big> 
  </strong>
{{if !isset($print|smarty:nodefaults)}}
  </a>
{{else}}
  </div>
{{/if}}
{{mb_include module=patients template=inc_vw_ipp ipp=$patient->_IPP}}
({{$patient->sexe|upper}})
{{if $conf.dPurgences.age_patient_rpu_view}}{{$patient->_age}}{{/if}}