{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />

  <table class="form">
    {{assign var="class" value="CProductOrder"}}
    {{assign var="var" value="order_number_format"}}
    <tr>
      <th>
        <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
          {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
        </label>
      </th>
      <td>
        <input type="text" name="{{$m}}[{{$class}}][{{$var}}]" value="{{$dPconfig.$m.$class.$var}}" size="40" class="code product_order" />
      </td>
    </tr>
    
    {{assign var="class" value="CProductStockGroup"}}
    {{assign var="var" value="unit_order"}}
    <tr>
      <th>{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}</th>
      <td>
        <label><input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="1" {{if  $dPconfig.$m.$class.$var}}checked="checked"{{/if}} /> {{tr}}bool.1{{/tr}}</label>
        <label><input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="0" {{if !$dPconfig.$m.$class.$var}}checked="checked"{{/if}} /> {{tr}}bool.0{{/tr}}</label>
      </td>
    </tr>
    {{assign var="var" value="infinite_quantity"}}
    <tr>
      <th>{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}</th>
      <td>
        <label><input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="1" {{if  $dPconfig.$m.$class.$var}}checked="checked"{{/if}} /> {{tr}}bool.1{{/tr}}</label>
        <label><input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="0" {{if !$dPconfig.$m.$class.$var}}checked="checked"{{/if}} /> {{tr}}bool.0{{/tr}}</label>
      </td>
    </tr>
    
    {{assign var="class" value="CProductStockService"}}
    {{assign var="var" value="infinite_quantity"}}
    <tr>
      <th>{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}</th>
      <td>
        <label><input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="1" {{if  $dPconfig.$m.$class.$var}}checked="checked"{{/if}} /> {{tr}}bool.1{{/tr}}</label>
        <label><input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="0" {{if !$dPconfig.$m.$class.$var}}checked="checked"{{/if}} /> {{tr}}bool.0{{/tr}}</label>
      </td>
    </tr>
    
    <tr>
      <td class="button" colspan="100">
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
