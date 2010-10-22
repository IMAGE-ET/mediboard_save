{{* $Id: configure.tpl 7993 2010-02-03 16:55:27Z MyttO $ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 7993 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="Config" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />

  <table class="form">
    <tr>
      <th class="title" colspan="2">Configuration des onglets
    </tr>

		{{assign var=class value=occupation_surveillance}}
    {{mb_include module=system template=inc_config_category}}
		{{mb_include module=system template=inc_config_str var=faible}}
    {{mb_include module=system template=inc_config_str var=eleve}}

    {{assign var=class value=occupation_technicien}}
    {{mb_include module=system template=inc_config_category}}
 	  {{mb_include module=system template=inc_config_str var=faible}}
    {{mb_include module=system template=inc_config_str var=eleve}}
    
    {{assign var=class value=repartition}}
    {{mb_include module=system template=inc_config_category}}
    {{mb_include module=system template=inc_config_bool var=show_tabs}}

    <tr>
      <td class="button" colspan="100">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>

{{mb_include module=system template=configure_dsn dsn=cdarr}}

<script type="text/javascript">

function startCdARR() {
  var CCAMUrl = new Url;
  CCAMUrl.setModuleAction("ssr", "httpreq_do_add_cdarr");
  CCAMUrl.requestUpdate("cdarr");
}

</script>

<h2>Import de la base de donn�es CdARR</h2>

<table class="tbl">
  <tr>
    <th>{{tr}}Action{{/tr}}</th>
    <th>{{tr}}Status{{/tr}}</th>
  </tr>
  
  <tr>
    <td><button class="tick" onclick="startCdARR()" >Importer la base de donn�es CdARR</button></td>
    <td id="cdarr"></td>
  </tr>
</table>

<h2>Mode offline</h2>

<table class="tbl">
  <tr>
    <td>
    	<a class="button search" href="?m={{$m}}&amp;a=offline_plannings_equipements&amp;dialog=1">
    		Plannings Equipements
			</a>
      <a class="button search" href="?m={{$m}}&amp;a=offline_plannings_techniciens&amp;dialog=1">
        Plannings R��ducateurs
      </a>
      <a class="button search" href="?m={{$m}}&amp;a=offline_repartition&amp;dialog=1">
        R�partition des patients
      </a>
      <a class="button search" href="?m={{$m}}&amp;a=offline_sejours&amp;dialog=1">
        S�jours
      </a>
		</td>
  </tr>
  
</table>