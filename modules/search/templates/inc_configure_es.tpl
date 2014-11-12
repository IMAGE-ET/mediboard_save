{{*
 * $Id$
 *  
 * @category ${Module}
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

{{mb_script module=search script=Search ajax=true}}
{{if $error == "mapping"}}
  <div class="small-error"> Un probl�me est survenu pour la cr�ation de l'index. V�rifiez l'�tat du service ElasticSearch</div>
{{/if}}
{{if $error == "index"}}
  <div class="small-error"> Un probl�me est survenu pour l'indexation des donn�es. V�rifiez l'�tat du service ElasticSearch</div>
{{/if}}
<form name="EditConfig-ES" action="?m={{$m}}&tab=configure" method="get" onsubmit="return onSubmitFormAjax(this)">
  <table class="main tbl" id="tab_config_es">
    <tr>
      <th class="title"> Indexation g�n�rique</th>
      <th class="title"> Indexation des recherches (logs)</th>
    </tr>
    <tbody id="first_indexing">
    <tr>
      <td class="halfPane">
        {{mb_include module=search template=inc_configure_es_generique}}
      </td>
      <td class="halfPane" style="vertical-align: top">
        {{mb_include module=search template=inc_configure_es_log}}
      </td>
    </tr>
    </tbody>
  </table>
</form>
