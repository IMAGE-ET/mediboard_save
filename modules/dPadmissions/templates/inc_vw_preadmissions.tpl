{{* $Id: inc_vw_admissions.tpl 6387 2009-06-03 07:44:06Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision: 6387 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=planningOp script=prestations ajax=1}}

<script type="text/javascript">
Prestations.callback = reloadPreAdmission;
Calendar.regField(getForm("changeDatePreAdmissions").date, null, {noView: true});
</script>

<table class="tbl">
  <tr>
    <th class="title" colspan="9">
      <a href="?m=dPadmissions&tab=vw_idx_preadmission&date={{$hier}}" style="display: inline"><<<</a>
      {{$date|date_format:$conf.longdate}}
      <form name="changeDatePreAdmissions" action="?" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="vw_idx_preadmission" />
        <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
      </form>
      <a href="?m=dPadmissions&tab=vw_idx_preadmission&date={{$demain}}" style="display: inline">>>></a>
      <br /> 
      <em>
      Toutes les pr�-admissions
      {{if $order_col_pre == "patient_id"}}tri�es par nom
      {{elseif $order_col_pre == "heure"}}tri�es par heure
      {{/if}}
      </em>
    </th>
  </tr>
  <tr>
    <th colspan="2">Consultation d'anesth�sie</th>
    <th colspan="6">Hospitalisation</th>
  </tr>
  <tr>
    <th>
      {{mb_colonne class="CConsultation" field="patient_id" order_col=$order_col_pre order_way=$order_way_pre order_suffixe="_pre" url="?m=$m&tab=vw_idx_preadmission"}}
    </th>
    <th>
      {{mb_colonne class="CConsultation" field="heure" order_col=$order_col_pre order_way=$order_way_pre order_suffixe="_pre" url="?m=$m&tab=vw_idx_preadmission"}}
    </th>
    <th>Praticien</th>
    <th>Admission</th>
    <th>Chambre</th>
    <th>Pr�par�</th>
    <th>CMU</th>
    <th>DH</th>
  </tr>
  {{foreach from=$listConsultations item=curr_consult}}
  <tr id="consultation{{$curr_consult->consultation_id}}">
    {{include file="inc_vw_preadmission_line.tpl"}}
  </tr>
  {{/foreach}}
</table>