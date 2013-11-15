{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $coreModules|@count}}
  <div class="big-warning">
    Un ou plusieurs des modules de base ne sont pas � jour.<br />
    Des erreurs risquent de s'afficher et le syst�me ne fonctionnera pas correctement.<br />
    Veuillez les mettre � jour afin de supprimer ces erreurs r�sultantes et avoir acc�s aux autres modules
  </div>
  {{mb_include template="inc_modules" object=$coreModules installed=true}}
{{else}}
  <script type="text/javascript">
  Main.add(function () {
    Control.Tabs.create('tabs-modules', true);
  });
  </script>

  <ul id="tabs-modules" class="control_tabs">
    <li><a {{if $upgradable}}class="wrong"{{/if}} {{if !$mbmodules.installed|@count}}class="empty"{{/if}} href="#installed">{{tr}}CModule-modules-installed{{/tr}} ({{$mbmodules.installed|@count}})</a></li>
    <li><a {{if !$mbmodules.notInstalled|@count}}class="empty"{{/if}} href="#notInstalled">{{tr}}CModule-modules-notInstalled{{/tr}} ({{$mbmodules.notInstalled|@count}})</a></li>
    {{if $can->edit}}
    <li><a href="#cache">{{tr}}module-system-cache{{/tr}}</a></li>
    {{/if}}
    <li><a {{if $obsoleteLibs|@count}}class="wrong"{{/if}} href="#libs">{{tr}}module-system-libs{{/tr}} {{if $obsoleteLibs|@count}}({{$obsoleteLibs|@count}}){{/if}}</a></li>
    <li><a  href="#assistant">{{tr}}module-system-assistant{{/tr}}</a></li>
  </ul>
  
  <hr class="control_tabs" />
  
  <div id="installed" style="display: none;">
    {{mb_include template="inc_modules" object=$mbmodules.installed installed=true}}
  </div>
  
  <div id="notInstalled" style="display: none;">
    {{mb_include template="inc_modules" object=$mbmodules.notInstalled installed=false}}
  </div>
  
  {{if $can->edit}}
  <div id="cache" style="display: none;">
    <script type="text/javascript">
      function updateControlTabs() {
        var tab = $$('a[href="#cache"]').first();
        if ($(this.id).select('.error, .warning').length)
          tab.addClassName('wrong');
        else 
          tab.removeClassName('wrong');
      }
      
      var CacheManager = {
        id : "CacheManagerLog",
        
        empty: function(ie) {
          var url = new Url("system", "httpreq_do_empty_shared_memory");
          url.requestUpdate(this.id, updateControlTabs.bind(this));
        },
        
        check: function(ie) {
          var url = new Url("system", "httpreq_check_shared_memory");
          url.requestUpdate(this.id, updateControlTabs.bind(this));
        },

        allCheck: function() {
          new Url("system", "httpreq_check_shared_memory_all_servers")
            .requestUpdate("CacheManagerAllLog");
        },

        allEmpty : function() {
          new Url("system", "httpreq_do_empty_shared_memory_all_servers")
            .requestUpdate("CacheManagerAllLog");
        }
      }
      
      Main.add(function () {
        CacheManager.check("CacheManagerLog");
      });
      
      </script>
      
      <table class="tbl" style="table-layout: fixed;">
        <tr>
          <th>{{tr}}Action{{/tr}}</th>
          <th>{{tr}}Status{{/tr}}</th>
        </tr>
        <tr>
          <td style="text-align: right;">
            <button class="cancel singleclick" onclick="CacheManager.empty(); ">
              Vider les caches
            </button>
          </td>
          <td id="CacheManagerLog"></td>
        </tr>
        {{if $servers_ip}}
          <tr>
            <td style="text-align: right;">
              <button class="lookup singleclick" onclick="CacheManager.allCheck();">
                V�rifier le cache de tous les serveurs
              </button><br/>
              <button class="cancel singleclick" onclick="CacheManager.allEmpty();">
                Vider le cache de tous les serveurs
              </button>
              {{foreach from=$servers_ip item=_server}}
                <br/>{{$_server}}
              {{/foreach}}
            </td>
            <td id="CacheManagerAllLog"></td>
          </tr>
        {{/if}}
      </table>
  </div>
  {{/if}}
  
  <div id="libs" style="display: none;">
    <table class="tbl" style="table-layout: fixed;">
      <tr>
        <th>{{tr}}Action{{/tr}}</th>
        <th>{{tr}}Status{{/tr}}</th>
      </tr>
      <tr>
        <td>
          <a class="button change" href="install/03_install.php">
            Mettre � jour les biblioth�ques externes
          </a>
        </td>
        <td>
          {{if $obsoleteLibs|@count}}
          <div class='error'>
            {{$obsoleteLibs|@count}} biblioth�ques � mettre � jour <br />
            {{foreach from=$obsoleteLibs item=_lib}}
              - {{$_lib}} <br />
            {{/foreach}}
          </div>
         {{else}} 
           <div class="info">Biblioth�ques externes � jour</div>
         {{/if}}
        </td>
      </tr>
    </table>
  </div>
  
  <div id="assistant" style="display: none;">
    {{mb_include template="view_install"}} 
  </div>
{{/if}}