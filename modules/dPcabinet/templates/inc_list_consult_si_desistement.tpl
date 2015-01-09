{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage Cabinet
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<h3>
  Consultations � avancer si d�sistement {{if !$function_id}}pour le Dr {{$user}}{{/if}}
</h3>

<table class="tbl">
  <tr>
    <th class="narrow"></th>
    <th class="narrow">Date</th>
    <th class="narrow">Heure</th>
    <th>Nom</th>
    <th>Motif</th>
    <th>Remarques</th>
    <th class="narrow">RDV</th>
    <th>Etat</th>
    {{if $function_id}}
      <th>Prat</th>
    {{/if}}
  </tr>
  
  {{foreach from=$consultations item=_consult}}
  <tr>
    {{assign var="consult_id" value=$_consult->_id}}
    {{assign var=patient value=$_consult->_ref_patient}}
    {{assign var="href_planning" value="?m=$m&tab=edit_planning&consultation_id=$consult_id"}}

    {{if !$patient->_id}}
      {{assign var="style" value="style='background: #ffa;'"}}          
    {{elseif $_consult->premiere}} 
      {{assign var="style" value="style='background: #faa;'"}}          
    {{elseif $_consult->derniere}} 
      {{assign var="style" value="style='background: #faf;'"}}
    {{elseif $_consult->sejour_id}} 
      {{assign var="style" value="style='background: #CFFFAD;'"}}
    {{else}} 
      {{assign var="style" value=""}}
    {{/if}}
    
    <td>
      {{assign var=categorie value=$_consult->_ref_categorie}}
      {{if $categorie->_id}}
        {{mb_include module=cabinet template=inc_icone_categorie_consult 
          categorie=$categorie
        }}
      {{/if}}
    </td>
    
    <td {{$style|smarty:nodefaults}}>
      {{mb_value object=$_consult->_ref_plageconsult field=date}}
    </td>
    
    <td {{$style|smarty:nodefaults}}>
      <span onmouseover="ObjectTooltip.createEx(this, '{{$_consult->_guid}}')">
        {{mb_value object=$_consult field=heure}}
      </span>
    </td>

    <td class="text" {{$style|smarty:nodefaults}}>
      <span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">
        {{$patient}}
      </span>
    </td>
    <td class="text" {{$style|smarty:nodefaults}}>
      {{$_consult->motif|truncate:35:"...":false|nl2br}}
    </td>
    <td class="text" {{$style|smarty:nodefaults}}>
      {{$_consult->rques|truncate:35:"...":false|nl2br}}
    </td>
    <td {{$style|smarty:nodefaults}}>
      <a href="#1" onclick="opener.location='{{$href_planning}}'; window.close();">
        <img src="images/icons/planning.png" title="Modifier le rendez-vous" />
      </a>
    </td>
    <td {{$style|smarty:nodefaults}}>{{if $patient->_id}}{{$_consult->_etat}}{{/if}}</td>
    {{if $function_id}}
      <td>
        {{mb_ditto name=nom_prat value=$_consult->_ref_praticien}}
      </td>
    {{/if}}

  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="9" class="empty">{{tr}}CConsultation.none{{/tr}}</td>
  </tr>
  {{/foreach}}
</table>