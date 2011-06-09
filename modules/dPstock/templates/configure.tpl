{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
function fixUnits(){
  var url = new Url("dPstock", "do_fix_quantities_units");
	url.requestUpdate("fix-units-logs");
}
</script>

<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />

  <table class="form" style="table-layout: fixed;">
    
    {{assign var="var" value="host_group_id"}}
    <tr>
      <th>{{tr}}config-{{$m}}-{{$var}}{{/tr}}</th>
      <td>
        {{assign var=name value="$m[$var]"}}
        
        <select name="{{$name}}">
          <option value=""> &ndash; {{tr}}None{{/tr}}</option>
          {{foreach from=$groups_list item=_group}}
            <option value="{{$_group->_id}}" {{if $conf.$m.$var == $_group->_id}}selected="selected"{{/if}}>{{$_group}}</option>
          {{/foreach}}
        </select>
      </td>
    </tr>
    
    {{assign var="class" value="CProductOrder"}}
    <tr><th class="category" colspan="2">{{tr}}{{$class}}{{/tr}}</th></tr>
    {{mb_include module=system template=inc_config_str var=order_number_format cssClass="code product_order"}}
    {{mb_include module=system template=inc_config_bool var=order_number_contextual}}
    
    {{assign var="class" value="CProductReference"}}
    <tr><th class="category" colspan="2">{{tr}}{{$class}}{{/tr}}</th></tr>
    {{mb_include module=system template=inc_config_bool var=use_mdq}}
    {{mb_include module=system template=inc_config_bool var=show_cond_price}}
    
    {{assign var="class" value="CProductStock"}}
    <tr><th class="category" colspan="2">{{tr}}{{$class}}{{/tr}}</th></tr>
    {{mb_include module=system template=inc_config_bool var=advanced_bargraph}}
    {{mb_include module=system template=inc_config_bool var=hide_bargraph}}
    
    {{assign var="class" value="CProductStockGroup"}}
    <tr><th class="category" colspan="2">{{tr}}{{$class}}{{/tr}}</th></tr>
    {{mb_include module=system template=inc_config_bool var=infinite_quantity}}
    {{mb_include module=system template=inc_config_bool var=negative_allowed}}

    {{assign var="class" value="CProductStockService"}}
    <tr><th class="category" colspan="2">{{tr}}{{$class}}{{/tr}}</th></tr>
    {{mb_include module=system template=inc_config_bool var=infinite_quantity}}

    {{assign var="class" value="CProductDelivery"}}
    <tr><th class="category" colspan="2">{{tr}}{{$class}}{{/tr}}</th></tr>
    {{mb_include module=system template=inc_config_bool var=auto_dispensation}}
    
    {{assign var="class" value="CProduct"}}
    <tr><th class="category" colspan="2">{{tr}}{{$class}}{{/tr}}</th></tr>
    {{mb_include module=system template=inc_config_bool var=use_renewable}}
    
    <tr>
      <th colspan="2" class="category">Paginations</th>
    </tr>
    
    {{mb_include module=system template=inc_config_pagination_size class=CProduct}}
    {{mb_include module=system template=inc_config_pagination_size class=CProductReference}}
    {{mb_include module=system template=inc_config_pagination_size class=CProductStockGroup}}
    {{mb_include module=system template=inc_config_pagination_size class=CProductStockService}}
    
    <tr>
      <td class="button" colspan="2">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>

<div class="big-info">
Les caract�res suivants sont utilis�s pour sp�cifier le format du num�ro de commande :
<ul>
  <li>%id - identifiant unique de la commande en base de donn�es</li>
  <li>%a - nom abr�g� du jour de la semaine (local)</li>
  <li>%A - nom complet du jour de la semaine (local)</li>
  <li>%b - nom abr�g� du mois (local)</li>
  <li>%B - nom complet du mois (local)</li>
  <li>%c - repr�sentation pr�f�r�e pour les dates et heures, en local</li>
  <li>%C - num�ro de si�cle (l&#039;ann�e, divis�e par 100 et arrondie entre 00 et 99)</li>
  <li>%d - jour du mois en num�rique (intervalle 01 � 31)</li>
  <li>%D - identique � %m/%d/%y</li>
  <li>%e - num�ro du jour du mois. Les chiffres sont pr�c�d�s d&#039;un espace (de &#039; 1&#039; � &#039;31&#039;)</li>
  <li>%g - identique � %G, sur 2 chiffres</li>
  <li>%G - L&#039;ann�e sur 4 chiffres correspondant au num�ro de semaine (voir %V). M�me format et valeur que %Y, except� que si le num�ro de la semaine appartient � l&#039;ann�e pr�c�dente ou suivante, l&#039;ann�e courante sera utilis� � la place</li>
  <li>%h - identique � %b</li>
  <li>%H - heure de la journ�e en num�rique, et sur 24-heures (intervalle de 00 � 23)</li>
  <li>%I - heure de la journ�e en num�rique, et sur 12- heures (intervalle 01 � 12)</li>
  <li>%j - jour de l&#039;ann�e, en num�rique (intervalle 001 � 366)</li>
  <li>%m - mois en num�rique (intervalle 1 � 12)</li>
  <li>%M - minute en num�rique</li>
  <li>%n - caract�re de nouvelle ligne</li>
  <li>%p - soit `am&#039; ou `pm&#039; en fonction de l&#039;heure absolue, ou en fonction des valeurs enregistr�es en local</li>
  <li>%r - l&#039;heure au format a.m. et p.m</li>
  <li>%R - l&#039;heure au format 24h</li>
  <li>%S - secondes en num�rique</li>
  <li>%t - tabulation</li>
  <li>%T - l&#039;heure actuelle (�gal � %H:%M:%S)</li>
  <li>%u - le num�ro de jour dans la semaine, de 1 � 7. (1 repr�sente Lundi)</li>
  <li>%U - num�ro de semaine dans l&#039;ann�e, en consid�rant le premier dimanche de l&#039;ann�e comme le premier jour de la premi�re semaine</li>
  <li>%V - le num�ro de semaine comme d�fini dans l&#039;ISO 8601:1988, sous forme d�cimale, de 01 � 53. La semaine 1 est la premi�re semaine qui a plus de 4 jours dans l&#039;ann�e courante, et dont Lundi est le premier jour. (Utilisez %G ou %g pour les �l�ments de l&#039;ann�e qui correspondent au num�ro de la semaine pour le timestamp donn�.)</li>
  <li>%W - num�ro de semaine dans l&#039;ann�e, en consid�rant le premier lundi de l&#039;ann�e comme le premier jour de la premi�re semaine</li>
  <li>%w - jour de la semaine, num�rique, avec Dimanche = 0</li>
  <li>%x - format pr�f�r� de repr�sentation de la date sans l&#039;heure</li>
  <li>%X - format pr�f�r� de repr�sentation de l&#039;heure sans la date</li>
  <li>%y - l&#039;ann�e, num�rique, sur deux chiffres (de 00 � 99)</li>
  <li>%Y - l&#039;ann�e, num�rique, sur quatre chiffres</li>
  <li>%Z ou %z - fuseau horaire, ou nom ou abr�viation</li>
  <li>%% - un caract�re `%&#039; litt�ral</li>
</ul>
</div>

<table class="main tbl" style="table-layout: fixed;">
	<tr>
    <td>
    	<button type="button" class="change oneclick2" onclick="fixUnits()">Corriger les unit�s des stocks</button>
    </td>
    <td id="fix-units-logs"></td>
	</tr>
</table>
