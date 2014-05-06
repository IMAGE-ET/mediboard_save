{{*
 * $Id$
 *  
 * @category ${Module}
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}
{{if $error == "mapping"}}
  <div class="small-error"> Un probl�me est survenu pour la cr�ation de l'index. V�rifiez l'�tat du service ElasticSearch</div>
{{/if}}
{{if $error == "index"}}
  <div class="small-error"> Un probl�me est survenu pour l'indexation des donn�es. V�rifiez l'�tat du service ElasticSearch</div>
{{/if}}
<table class="main" id="table_main">
  <tr>
    <td>
      <form name="EditConfig-Search" action="?m={{$m}}&tab=configure" method="post" onsubmit="return onSubmitFormAjax(this)">
        <input type="hidden" name="m" value="system" />
        <input type="hidden" name="dosql" value="do_configure" />
        <table class="form">
          <tr>
            <td id="optionnal" colspan="2">
              <input type="checkbox" onclick="Search.toggleElement($('first_indexing'))"/>
            </td>
          </tr>
          <tr>
            <td id="first_indexing" style="display:none;" colspan="2">
              <button class="new singleclick" onclick="Search.firstIndexing(true, null);">Remplir table temporaire</button>
              <button class="new singleclick" onclick="Search.firstIndexing(null, true)">Cr�er le sch�ma Nosql</button>
              <button class="new singleclick" onclick="Search.routineIndexing()">Indexer les donn�es</button>
            </td>
          </tr>
          {{mb_include module=system template=inc_config_str var=client_host}}
          {{mb_include module=system template=inc_config_str var=client_port}}
          {{mb_include module=system template=inc_config_str var=index_name}}
          {{mb_include module=system template=inc_config_str var=interval_indexing}}
          <tr>
            <td class="button" colspan="2">
              <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
            </td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
</table>