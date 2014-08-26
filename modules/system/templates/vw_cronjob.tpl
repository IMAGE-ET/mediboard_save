{{*
 * $Id$
 *  
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{mb_script module="system" script="cronjob" ajax=true}}

<script>
  Main.add(function () {
    Control.Tabs.create('tabs-cronjob', false);
    if (Control.Tabs.activeLink = "tab_list_cronjobs") {
      CronJob.refresh_list_cronjobs();
    }
  });
</script>

<ul id="tabs-cronjob" class="control_tabs">
  <li onmousedown="CronJob.refresh_list_cronjobs()"><a href="#tab_list_cronjobs">{{tr}}CCronJob.list{{/tr}}</a></li>
  <li><a href="#tab_log_cronjobs">{{tr}}CCronJobLog{{/tr}}</a></li>
  <li><a href="#tab_purge_cronjobs_log">{{tr}}CCronJobLog-purge{{/tr}}</a></li>
</ul>

<div id="tab_list_cronjobs" style="display: none;">
  <button class="new" type="button" onclick="CronJob.edit(0)">{{tr}}CCronJob.new{{/tr}}</button>
  <table class="tbl">
    <tr>
      <th class="title" colspan="6">{{tr}}CCronJob{{/tr}}</th>
      <th class="title" colspan="5" style="width: 50%">Execution</th>
    </tr>
    <tr>
      <th>{{mb_title class="CCronJob" field="active"}}</th>
      <th>{{mb_title class="CCronJob" field="name"}}</th>
      <th>{{mb_title class="CCronJob" field="description"}}</th>
      <th>{{mb_title class="CCronJob" field="params"}}</th>
      <th>{{mb_title class="CCronJob" field="execution"}}</th>
      <th>{{mb_title class="CCronJob" field="servers_address"}}</th>
      <th>n</th>
      <th>n+1</th>
      <th>n+2</th>
      <th>n+3</th>
      <th>n+4</th>
    </tr>

    <tbody id="list_cronjobs">
    </tbody>
  </table>
</div>

<div id="tab_log_cronjobs">
  <form name="search_cronjob" method="post" onsubmit="return onSubmitFormAjax(this, CronJob.refresh_logs(this))">
    <input type="hidden" name="page">
    <table class="form">
      <tr>
        <th>{{mb_title object=$log_cron field="status"}}</th>
        <td colspan="3">{{mb_field object=$log_cron field="status" canNull=true emptyLabel="Choose"}}</td>
      </tr>
      <tr>
        <th>{{mb_title object=$log_cron field="cronjob_id"}}</th>
        <td colspan="3">
          {{mb_field object=$log_cron field="cronjob_id" canNull=true form="search_cronjob" autocomplete="true,1,50,true,true"}}
        </td>
      </tr>
      <tr>
        <th style="width: 50%">Du</th>
        <td>{{mb_field object=$log_cron field="_date_min" form="search_cronjob" register=true}}</td>
        <th>jusqu'au</th>
        <td style="width: 50%">{{mb_field object=$log_cron field="_date_max" form="search_cronjob" register=true}}</td>
      </tr>
      <tr>
        <td colspan="4" class="button"><button type="submit" class="search">{{tr}}Search{{/tr}}</button></td>
      </tr>
    </table>
  </form>
  <div id="search_log_cronjob"></div>
</div>

<div id="tab_purge_cronjobs_log">
  <form name="purge_cronjob_log" action="?" method="get" onsubmit="return CronJob.purge(true)">
    <table class="form">
      <tr>
        <th style="width: 50%">{{mb_label class="CCronJobLog" field="_date_max" prop="dateTime notNull"}}</th>
        <td>{{mb_field class="CCronJobLog" field="_date_max" prop="dateTime notNull" register=true form="purge_cronjob_log"}}</td>
      </tr>
      <tr>
        <th><label for="delete">Supprimer les journaux d'ex�cution ant�rieurs � 6 mois : </label></th>
        <td><input type="checkbox" name="delete" value="1" /></td>
      </tr>
      <tr>
        <td class="button" colspan="2">
          <button type="submit" class="change">{{tr}}CCronJobLog-purge-search{{/tr}}</button>
          <label><input type="checkbox" name="do_purge" />{{tr}}Purge{{/tr}}</label>
          <label><input type="checkbox" name="auto" />{{tr}}Auto{{/tr}}</label>
        </td>
      </tr>
    </table>
  </form>
  <div id="purge-log"></div>
</div>