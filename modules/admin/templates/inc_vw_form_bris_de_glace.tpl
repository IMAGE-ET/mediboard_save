{{*
 * $Id$
 *  
 * @category Admin
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}


<form method="post" name="bris_de_glace_{{$sejour->_guid}}" onsubmit="return onSubmitFormAjax(this, {onComplete : Control.Modal.close});">
  <input type="hidden" name="m" value="admin"/>
  <input type="hidden" name="dosql" value="do_bris_de_glace" />
  <input type="hidden" name="object_class" value="{{$sejour->_class}}" />
  <input type="hidden" name="object_id" value="{{$sejour->_id}}"/>

  <table class="form">
    <tr>
      <td colspan="2">
        <div class="small-info">Pour acc�der � ce {{tr}}{{$sejour->_class}}{{/tr}}, vous devez le notifier et en donner la raison. (<a href="#" onclick="$(this).up().next().toggle();">En savoir plus</a>)</div>
        <fieldset style="display: none;">
          <legend>Explication</legend>
          <p>Pour acc�der � ce s�jour, vous devez "briser la glace".<br/>
            Cela signifie que vous notifiez votre passage dans le dossier au praticien responsable de ce s�jour ainsi qu'au patient du s�jour.<br/>
            Cette notification n'est pas r�versible et elle est disponible pour un temps d�termin�.<br/>
            Vous devez en outre justifier de votre acc�s au dossier dans la zone de texte ci-dessous</p>
        </fieldset>
      </td>
    </tr>
    <tr>
      <th>{{tr}}{{$sejour->_class}}{{/tr}}</th>
      <td>{{$sejour}}</td>
    </tr>
    <tr>
      <th>Demande d'acc�s de</th>
      <td>{{$app->_ref_user}}</td>
    </tr>
    <tr>
      <th>
        {{mb_label object=$bris field=comment}}
      </th>
      <td>
        {{mb_field object=$bris field=comment aidesaisie="validateOnBlur: 0" form="bris_de_glace_`$bris->_guid`"}}
      </td>
    </tr>
    <tr>
      <td class="button" colspan="2">
        <button class="tick" type="button" onclick="this.form.onsubmit();">Notifier mon passage dans ce dossier</button>
      </td>
    </tr>
  </table>
</form>
