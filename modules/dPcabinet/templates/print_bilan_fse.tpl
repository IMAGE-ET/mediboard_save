<!-- $Id: print_compta.tpl 2031 2007-06-08 09:13:23Z lryo $ -->

{{include file="../../dPpatients/templates/inc_intermax.tpl"}}
<script type="text/javascript">

Consultation = {
  show: function(consult_id) {
    var url = new Url;
    url.setModuleTab("dPcabinet", "edit_consultation");
    url.addParam("selConsult", consult_id);
    url.redirectOpener();
  }
}

</script>

<table class="main">
  <tr>
    <td class="halfPane">
      <table>
        <tr>
          <th colspan="2">
            <a href="#" onclick="window.print()">
              Rapport du {{$filter->_date_min|date_format:"%d/%m/%Y"}}
              {{if $filter->_date_min != $filter->_date_max}}
              au {{$filter->_date_max|date_format:"%d/%m/%Y"}}
              {{/if}}
            </a>
          </th>
        </tr>
        {{if $prat->user_id}}
        <tr><th colspan="2">Dr. {{$prat->_view}}</th></tr>
        {{/if}}
        <tr>
          <td>{{mb_label object=$filter field=S_FSE_ETAT}}</td>
          <td>{{mb_value object=$filter field=S_FSE_ETAT}}</td>
        </tr>
      </table>
    </td>

    <td class="halfPane">
      <table class="form">
        <tr><th class="category" colspan="2">R�capitulatif</th></tr>
        <tr><th>Nombre FSE :</th><td>{{$total.count}}</td></tr>
        <tr><th>Total factur� :</th><td>{{$total.S_FSE_TOTAL_FACTURE|string_format:"%.2f"}}&euro;</td></tr>
      </table>
    </td>
  </tr>

  {{foreach from=$days key=_day item=_fses}}
  <tr>
    <td colspan="2"><strong>{{$_day|date_format:"%a %d %b %Y"}}</strong></td>
  </tr>
  <tr>
    <td colspan="2">
      <table class="tbl">
        <tr>
          <th width="15%">{{mb_label object=$filter field=S_FSE_NUMERO_FSE}}</th>
          <th width="15%">{{mb_label object=$filter field=_consult_id}}</th>
          <th width="15%">{{mb_label object=$filter field=S_FSE_ETAT}}</th>
          <th width="15%">{{mb_label object=$filter field=S_FSE_TOTAL_FACTURE}}</th>
        </tr>
        {{foreach from=$_fses item=_fse}}
        <tr>
          <td>
				  	<span class="tooltip-trigger" onmouseover="ObjectTooltip.create(this, { params: { object_class: 'CLmFSE', object_id: '{{$_fse->_id}}' } })">
				  	  {{$_fse->_id}}
				  	</span>
			      <button class="search" type="button" onclick="Intermax.Triggers['Consulter FSE']('{{$_fse->_id}}');">
			        {{tr}}View{{/tr}} 
			      </button>
			      <button class="print" type="button" onclick="Intermax.Triggers['Editer FSE']('{{$_fse->_id}}');">
			        {{tr}}Print{{/tr}}
			      </button>
			    </td>
			      
          <td>
            {{if $_fse->_consult_id}}
				  	<span class="tooltip-trigger" onmouseover="ObjectTooltip.create(this, { params: { object_class: 'CConsultation', object_id: '{{$_fse->_consult_id}}' } })">
				  	  <a href="#CConsultation-{{$_fse->_consult_id}}" onclick="Consultation.show('{{$_fse->_consult_id}}')">
				  	  	{{tr}}CConsultation{{/tr}} #{{$_fse->_consult_id}}
				  	  </a>
				  	</span>
            {{/if}}
          </td>
          {{if $_fse->_annulee}}
          <td colspan="2" class="cancelled">{{mb_value object=$_fse field=S_FSE_ETAT}}</td>
          {{else}}
          <td>{{mb_value object=$_fse field=S_FSE_ETAT}}</td>
          <td>{{mb_value object=$_fse field=S_FSE_TOTAL_FACTURE}}</td>
          {{/if}}
        </tr>
        {{/foreach}}
        <tr>
          <td colspan="3" style="font-weight:bold; text-align:right">Total pour {{$cumuls.$_day.count}} FSE</td>
          <td style="font-weight:bold;">{{$cumuls.$_day.S_FSE_TOTAL_FACTURE|string_format:"%.2f"}}&euro;</td>
        </tr>
      </table>
    </td>
  </tr>
  {{/foreach}}
</table>
      