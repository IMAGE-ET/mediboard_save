{{*
 * Configure EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

<script type="text/javascript">
  Main.add(Control.Tabs.create.curry('tabs-configure', true));
</script>

<ul id="tabs-configure" class="control_tabs">
  <li><a href="#object-servers">{{tr}}config-object-servers{{/tr}}</a></li>
  <li><a href="#config-eai">{{tr}}config-eai{{/tr}}</a></li>
</ul>

<hr class="control_tabs" />

<div id="object-servers" style="display: none;">
  {{mb_include template=inc_config_object_servers}}
</div>

<div id="config-eai" style="display: none;">
  {{mb_include template=inc_config_eai}}
</div>