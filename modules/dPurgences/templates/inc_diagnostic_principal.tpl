{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<th>{{mb_label object=$sejour field="DP"}}</th>
<td>
{{mb_script module="dPplanningOp" script="cim10_selector"}}

<script type="text/javascript">
  function reloadDiagnostic(sejour_id, modeDAS) {
	  var url = new Url("dPurgences", "ajax_diagnostic_principal");
	  url.addParam("sejour_id", sejour_id);
	  url.requestUpdate("dp_"+sejour_id);

	  var url = new Url("dPsalleOp", "httpreq_diagnostic_principal");
	  url.addParam("sejour_id", sejour_id);
	  url.addParam("modeDAS", modeDAS);
	  url.requestUpdate("cim");
  }
  function deleteCodeCim10() {
	  var oForm = getForm("editSejour");
	  $V(oForm.keywords_code, '');
	  $V(oForm.DP, '');
	  submitSejour('{{$sejour->_id}}');
  }
  Main.add(function() {
    var url = new Url("dPcim10", "ajax_code_cim10_autocomplete");
    url.addParam("sejour_id", '{{$sejour->_id}}');
    url.autoComplete(getForm("editSejour").keywords_code, '', {
      minChars: 1,
      dropdown: true,
      select: "code",
      width: "250px",
      afterUpdateElement: function(oHidden) {
  	    $V(getForm('editSejour').DP, oHidden.value);
  	    submitSejour('{{$sejour->_id}}');
      }
    });
  });
</script>
 
  <input type="hidden" name="praticien_id" value="{{$sejour->praticien_id}}"/>

  <input type="text" name="keywords_code" id="editSejour_keywords_code" class="autocomplete str" value="{{$sejour->DP}}" size="10"/>
  <input type="hidden" name="DP"/>
  <script type="text/javascript">
    CIM10Selector.initDP = function() {
      this.sForm     = "editSejour";
      this.sView     = "DP";
      this.sChir     = "praticien_id";
      this.pop();
    }
  </script>
  <button type="button" class="search notext" onclick="CIM10Selector.initDP()">
    {{tr}}button-CCodeCIM10-choix{{/tr}}
  </button>
  <button type="button" class="cancel notext" onclick="deleteCodeCim10();">
  	{{tr}}Delete{{/tr}}
  </button>
</td>

