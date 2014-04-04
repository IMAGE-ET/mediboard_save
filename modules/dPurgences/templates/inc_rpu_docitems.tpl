{{* $Id: vw_aed_rpu.tpl 8113 2010-02-22 09:29:33Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 8113 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=patient value=$sejour->_ref_patient}}

<table class="form">
  <tr>
    <td class="halfPane">
      <fieldset>
        <legend>{{tr}}CFile{{/tr}} - {{tr}}CSejour{{/tr}}</legend>
        <div id="files-CSejour">
          <script type="text/javascript">
            File.register('{{$sejour->_id}}','{{$sejour->_class}}', 'files-CSejour');
          </script>
        </div>
      </fieldset>
    </td>
    <td class="halfPane">
      <fieldset>
        <legend>{{tr}}CCompteRendu{{/tr}} - {{tr}}CSejour{{/tr}}</legend>
        <div id="documents-CSejour">
          <script type="text/javascript">
            Document.register('{{$sejour->_id}}','{{$sejour->_class}}','{{$sejour->_praticien_id}}','documents-CSejour');
          </script>
        </div>
      </fieldset>
    </td>
  </tr>

  {{if $consult->_id}}
    <tr>
      <td class="halfPane">
        <fieldset>
          <legend>{{tr}}CFile{{/tr}} - {{tr}}CConsultation{{/tr}}</legend>
          <div id="files-CConsultation">
            <script type="text/javascript">
              File.register('{{$consult->_id}}','{{$consult->_class}}', 'files-CConsultation');
            </script>
          </div>
        </fieldset>
      </td>
      <td class="halfPane">
        <fieldset>
          <legend>{{tr}}CCompteRendu{{/tr}} - {{tr}}CConsultation{{/tr}}</legend>
          <div id="documents-CConsultation">
            <script type="text/javascript">
              Document.register('{{$consult->_id}}','{{$consult->_class}}','{{$consult->_praticien_id}}','documents-CConsultation');
            </script>
          </div>
        </fieldset>
      </td>
    </tr>
  {{else}}
    <tr>
      <td colspan="2">
        <div class="small-info">Consultation non r�alis�e</div>
      </td>
    </tr>
  {{/if}}
</table>