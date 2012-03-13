{{* $Id: configure.tpl 8820 2010-05-03 13:18:20Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage dPpmsi
 * @version $Revision: 8820 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module="dPprescription" script=prescription}}
{{mb_script module="dPcompteRendu" script="document"}}
{{mb_script module="dPcompteRendu" script="modele_selector"}}
{{mb_script module="dPcabinet" script="file"}}

{{if @$sejour->_id}}
  <script type="text/javascript">
    loadDocuments = function() {
      var url = new Url("dPhospi", "httpreq_documents_sejour");
      url.addParam("sejour_id" , '{{$sejour->_id}}');
      url.requestUpdate("Docs");
    }
    Main.add(function() {
      Control.Tabs.create('tabs-pmsi', true)
      loadDocuments();
    });
  </script>
  
  {{assign var=patient value=$sejour->_ref_patient}}
  <table class="form">
    <tr>
      <th class="title text">
        <span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">
          {{$patient->_view}}
        </span>
        &mdash;
        <span onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}')">
          {{$sejour->_shortview}}
        </span>
      </th>
    </tr>
    
    {{if ($conf.dPpmsi.systeme_facturation == "siemens") && $patient->_ref_IPP}}
    <tr>
      <th id="IPP"> {{mb_include module=pmsi template=inc_ipp_form}} </th>
    </tr>
    {{/if}}
  
    {{if ($conf.dPpmsi.systeme_facturation == "siemens")}}
    <tr>
      <td id="Numdos{{$sejour->sejour_id}}" class="text">
        {{mb_include module=pmsi template=inc_numdos_form}}
      </td>
    </tr>
    {{/if}}    
  </table>
  
  <ul id="tabs-pmsi" class="control_tabs">
    <li><a href="#tab-PMSI">{{tr}}PMSI{{/tr}}</a></li>
    <li><a href="#ServeurActes" {{if !$sejour->_ref_operations}}class="empty"{{/if}}>Actes</a></li>
    <li onmousedown="loadDocuments()"><a href="#Docs">Documents</a></li>
    <li style="float: right">
      <button type="button" class="print" onclick="printDossierComplet('{{$sejour->_id}}');">
        Dossier complet
      </button>
    </li>
    {{if $sejour->_ref_prescription_sejour && $sejour->_ref_prescription_sejour->_id}}
    <li style="float: right">
      <button type="button" class="print" onclick="Prescription.printOrdonnance('{{$sejour->_ref_prescription_sejour->_id}}');">
        Prescription
      </button>
    </li>
    {{/if}}
  </ul>
  
  <hr class="control_tabs" />
  
  <div id="tab-PMSI" style="display: none;">
    {{mb_include template=inc_vw_pmsi}}
  </div>
  
  <div id="ServeurActes" style="display: none;">
    {{mb_include template=inc_vw_serveur_actes}}
  </div>
  
  <div id="Docs" style="display: none;"></div>

{{else}}
  <div class="small-info">Veuillez s�lectionner un s�jour dans la liste des s�jours sur la gauche.</div>
{{/if}}