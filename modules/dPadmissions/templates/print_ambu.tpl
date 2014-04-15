{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl">
  <tr>
    <th colspan="9">
      <span style="float: right">
      Service
      <form name="selService" action="" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="dialog" value="1" />
        <input type="hidden" name="a" value="{{$a}}" />
        <select name="service_id" onchange="this.form.submit();">
          <option value="">&mdash; Tous les services</option>
          {{foreach from=$services item=_service}}
          <option value="{{$_service->_id}}" {{if $service_id == $_service->_id}}selected="selected"{{/if}}>{{$_service->_view}}</option>
          {{/foreach}}
        </select>
      </form>
      </span>
      <a href="#" onclick="window.print()">Patients en s�jour de type {{tr}}CSejour.type.{{$type}}{{/tr}} du {{$date|date_format:$conf.date}}</a>
    </th>
  </tr>
  <tr>
    <th>Patient</th>
    <th>Praticien</th>
    <th>Service</th>
    <th>Chambre</th>
    <th>Entr�e<br />�tablissement</th>
    <!-- <th>Entr�e<br />au bloc</th>         -->
    <th>D�part<br />bloc</th>
    <!-- <th>Entr�e<br />salle de r�veil</th> -->
    <!-- <th>Sortie<br />de bloc</th>         -->
    <th>Retour<br />de bloc</th>
    <th>Sortie<br />�tablissement</th>
    <th>Temps �coul�</th>
  </tr>
  {{foreach from=$sejours item=_sejour}}
    {{assign var=last_op value=$_sejour->_ref_last_operation}}
    <tr>
      <td class="text">{{$_sejour->_ref_patient->_view}}</td>
      <td class="text">{{$_sejour->_ref_praticien->_view}}</td>
      <td class="text">
        {{foreach from=$_sejour->_ref_affectations item="affectation"}}
            {{$affectation->_ref_lit->_ref_chambre->_ref_service->_view}}<br />
        {{/foreach}}
        {{if !$_sejour->_ref_affectations|@count}}
          -
        {{/if}}
      </td>
      <td class="text">
        {{foreach from=$_sejour->_ref_affectations item="affectation"}}
            {{$affectation->_ref_lit->_view}}<br />
        {{/foreach}}
        {{if !$_sejour->_ref_affectations|@count}}
          -
        {{/if}}
      </td>
      <td style="text-align: center;">{{$_sejour->entree_reelle|date_format:$conf.time}}</td>
      <td style="text-align: center;">{{$_sejour->_ref_last_operation->entree_salle|date_format:$conf.time}}</td>
      <!-- <td style="text-align: center;">{{$last_op->entree_reveil|date_format:$conf.time}}</td> -->
      <td style="text-align: center;">
        {{$last_op->sortie_reveil_possible|date_format:$conf.time}}
        {{if $last_op->sortie_locker_id}}
          <br />
          {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$last_op->_ref_sortie_locker}}
        {{/if}}
      </td>
      <td style="text-align: center;">{{$_sejour->sortie_reelle|date_format:$conf.time}}</td>
      <td style="text-align: center;">{{$_sejour->_duree|date_format:$conf.time}}</td></td>
    </tr>
  {{/foreach}}
</table>