{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
printOffline = function(element) {
  var elements = [element];
	
	$$('.modal-view').each(function(modal){
	  var id = modal.id;
		var tab = window["tab-"+id];
    var sejour_id = id.match(/(\d+)/)[1];
		var sejour_guid = 'CSejour-'+sejour_id;
    
    modal.show();
    $("planning-"+sejour_id).show();
    
    $(sejour_guid).down('.week-container').setStyle({height: '800px' });
    window['planning-'+sejour_guid].updateEventsDimensions();
    
    elements.push(
      modal.down(".modal-title"), 
      tab
    );
    
    modal.hide();
    $("planning-"+sejour_id).hide();
  });
  
  Element.print(elements);
}
</script>

<table id="sejours-ssr" class="tbl" style="page-break-after: always;">
  <tr>
    <th class="title" colspan="10">
      {{if @$offline}}
        <button class="print not-printable" style="float: right;" onclick="printOffline($(this).up('table'))">{{tr}}Print{{/tr}}</button>
      {{/if}}
      
      <span style="text-align: left">
        ({{$sejours|@count}}) 
      </span>
      S�jours SSR du {{$date|date_format:$conf.longdate}}
      
      {{if !$dialog && !@$offline}}
      <form name="selDate" action="?" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <script type="text/javascript">
        Main.add(function () {
          Calendar.regField(getForm("selDate").date, null, { noView: true } );
        });
        </script>
        
        <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
      </form>
      {{/if}}
      
    </th>
  </tr>
  <tr>
    {{assign var=url value="?m=$m&$actionType=$action&dialog=$dialog"}}
    <th>{{mb_colonne class="CAffectation" field="lit_id" order_col=$order_col order_way=$order_way url=$url}}</th>
    <th style="width: 20em;">{{mb_colonne class="CSejour" field="patient_id" order_col=$order_col order_way=$order_way url=$url}}</th>
    <th class="narrow">
      <input type="text" size="6" class="not-printable" onkeyup="SejoursSSR.filter(this)" id="filter-patient-name" />
    </th>
    <th style="width:  5em;">{{mb_colonne class="CSejour" field="entree"     order_col=$order_col order_way=$order_way url=$url}}</th>
    <th style="width:  5em;">{{mb_colonne class="CSejour" field="sortie"     order_col=$order_col order_way=$order_way url=$url}}</th>

    <th style="width:  5em;">
      {{mb_colonne class="CSejour" field="service_id" order_col=$order_col order_way=$order_way url=$url}}
      
      {{if !$dialog && !@$offline && $order_col != "service_id"}}
        <br />
        <select name="service_id" onchange="$V(getForm('Filter').service_id, $V(this), true);">
          <option value="">&mdash; {{tr}}All{{/tr}}</option>
          {{foreach from=$services item=_service}}
          <option value="{{$_service->_id}}" {{if $_service->_id == $filter->service_id}}selected="selected"{{/if}}>
            {{$_service}}
          </option>
          {{/foreach}}
        </select>
      {{/if}}
    </th>

    <th style="width: 20em;">
      {{mb_colonne class="CSejour" field="libelle" order_col=$order_col order_way=$order_way url=$url}}
    </th>

    <th style="width: 12em;">
      {{mb_colonne class="CSejour" field="praticien_id" order_col=$order_col order_way=$order_way url=$url}}
      
      {{if $order_col != "praticien_id"}}
        {{mb_title class=CSejour field=praticien_id}} /
        {{mb_title class=CBilanSSR field=_prat_demandeur_id}}
      
        {{if !$dialog && !@$offline}}
        <br />
        <select name="praticien_id" onchange="$V(getForm('Filter').praticien_id, $V(this), true);">
          <option value="">&mdash; {{tr}}All{{/tr}}</option>
          {{mb_include module=mediusers template=inc_options_mediuser list=$praticiens selected=$filter->praticien_id}}
        </select>
        {{/if}}
      {{/if}}
    </th>

    <th style="width: 16em;">
      {{mb_title class=CBilanSSR field=_kine_referent_id}} /
      {{mb_title class=CBilanSSR field=_kine_journee_id}}

      {{if !$dialog && !@$offline && $order_col != "_kine_referent_id" && $order_col != "kine_journee_id"}}
        <br />
        <select name="referent_id" onchange="$V(getForm('Filter').referent_id, $V(this), true);">
          <option value="">&mdash; {{tr}}All{{/tr}}</option>
          {{mb_include module=mediusers template=inc_options_mediuser list=$kines selected=$filter->referent_id}}
        </select>
      {{/if}}

    </th>
    
    <th colspan="2" class="narrow"><label title="Ev�nements planifi�s pour ce patient (ce jour - pendant tout le s�jour)">Evt.</label></th>
  </tr>
  
  {{foreach from=$sejours key=sejour_id item=_sejour}}
  {{assign var=ssr_class value=""}}
  {{if !$_sejour->entree_reelle}}
  {{assign var=ssr_class value=ssr-prevu}}
  {{elseif $_sejour->sortie_reelle}}
  {{assign var=ssr_class value=ssr-termine}}
  {{/if}}

  <tr class="{{$ssr_class}}">
    <td>
      {{if @$offline}}
        <button class="search notext not-printable" onclick="modalwindow = modal($('modal-view-{{$_sejour->_id}}'));" style="float: left;"></button>
      {{/if}}
      {{assign var=affectation value=$_sejour->_ref_curr_affectation}}
      {{if $affectation->_id}}
        {{$affectation->_ref_lit}}
      {{/if}}
    </td>
    <td colspan="2" class="text">      
      {{mb_include template=inc_view_patient patient=$_sejour->_ref_patient
        link="?m=$m&tab=vw_aed_sejour_ssr&sejour_id=$sejour_id"
      }}
    </td>

    {{assign var=distance_class value=ssr-far}}
    {{if $_sejour->_entree_relative == "-1"}}
    {{assign var=distance_class value=ssr-close}}
    {{elseif $_sejour->_entree_relative == "0"}}
    {{assign var=distance_class value=ssr-today}}
    {{/if}}
    <td class="{{$distance_class}}">
      {{mb_value object=$_sejour field=entree format=$conf.date}}
      <div style="text-align: left; opacity: 0.6;">{{$_sejour->_entree_relative}}j</div>
    </td>

    {{assign var=distance_class value=ssr-far}}
    {{if $_sejour->_sortie_relative == "1"}}
    {{assign var=distance_class value=ssr-close}}
    {{elseif $_sejour->_sortie_relative == "0"}}
    {{assign var=distance_class value=ssr-today}}
    {{/if}}
    <td class="{{$distance_class}}">
      {{mb_value object=$_sejour field=sortie format=$conf.date}}
      <div style="text-align: right; opacity: 0.6;">{{$_sejour->_sortie_relative}}j</div>
    </td>
    
    <td style="text-align: center;">
      <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}');">
       {{mb_include module=dPplanningOp template=inc_vw_numdos nda=$_sejour->_NDA}}
      </span>

      {{assign var=bilan value=$_sejour->_ref_bilan_ssr}}
      <div class="opacity-60">
        {{if $bilan->hospit_de_jour}} 
          <img style="float: right;" title="{{mb_value object=$bilan field=_demi_journees}}" src="modules/ssr/images/dj-{{$bilan->_demi_journees}}.png" />
        {{/if}}
        {{mb_value object=$_sejour field=service_id}}
      </div>
    </td>
    
    <td class="text">
      {{mb_include module=system template=inc_get_notes_image object=$_sejour mode=view float=right}}
      {{mb_value object=$_sejour field=libelle}}
      {{assign var=libelle value=$_sejour->libelle|upper}}
      {{assign var=color value=$colors.$libelle}}
      {{if $color->color}}
        <div class="motif-color" style="background-color: #{{$color->color}};" />
      {{/if}}
    </td>
    
    {{if $_sejour->annule}}
    <td colspan="4" class="cancelled">
      {{tr}}CSejour-{{$_sejour->recuse|ternary:"recuse":"annule"}}{{/tr}}
    </td>

    {{else}}
      <td class="text">
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_sejour->_ref_praticien}}
        {{assign var=prat_demandeur value=$bilan->_ref_prat_demandeur}}
        {{if $prat_demandeur->_id}} 
        <br />{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$prat_demandeur}}
        {{/if}}
      </td>
          
      <td class="text">
        {{assign var=kine_referent value=$bilan->_ref_kine_referent}}
        {{if $kine_referent->_id}}
          {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$kine_referent}}
          {{assign var=kine_journee value=$bilan->_ref_kine_journee}}
          {{if $kine_journee->_id != $kine_referent->_id}}
          <br/>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$kine_journee}}
          {{/if}}
        {{/if}}
      </td>
      
      <td colspan="2" style="text-align: center;">
        {{assign var=prescription value=$_sejour->_ref_prescription_sejour}}
        {{if $prescription->_id}}
          {{if $prescription->_count_fast_recent_modif}}
            <img src="images/icons/ampoule.png" onmouseover="ObjectTooltip.createEx(this, '{{$prescription->_guid}}')"/>
          {{else}}
            <img src="images/icons/ampoule_grey.png" onmouseover="ObjectTooltip.createEx(this, '{{$prescription->_guid}}')"/>
          {{/if}}
        {{/if}}
      </td>
       {{/if}}

  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="10" class="empty">
      {{tr}}CSejour.none{{/tr}}
    </td>
  </tr>
  {{/foreach}}
</table>