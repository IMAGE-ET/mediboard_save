{{* $Id: configure_ftp.tpl 6239 2009-05-07 10:26:49Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage ftp
 * @version $Revision: 6239 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=system script=exchange_source ajax=true}}

<script type="text/javascript">
  Main.add(function(){
    ExchangeSource.showDirectory('{{$source_guid}}');
  })
</script>

<table class="tbl">
  <tr>
    <td style="vertical-align: top">
      <div id="listDirectory">
      </div>
    </td>
    <td style="vertical-align: top">
      <div id="listFiles">
      </div>
    </td>
  </tr>
</table>

