{{*
 * $Id$
 *  
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<br/>
<div class="small-info">
  <div>Pour vous assurer du bon fonctionnement du module. Veuillez param�trer les diff�rents points ci-dessous :</div>
</div>
<br/>

<fieldset>
  <legend>{{tr}}Verification_parameter{{/tr}}</legend>
  <table class="tbl" style="width: auto">
    <tr>
      <th class="section">{{tr}}Data_command{{/tr}}</th>
      <th class="section">{{tr}}Test{{/tr}}</th>
    </tr>
    <tr>
      <td>
        {{tr}}Identification_praticien(ADELI/RPPS){{/tr}}
      </td>
      <td class="warning">
        {{tr}}Verification_impossible{{/tr}}
      </td>
    </tr>
    <tr>
      <td>
        {{tr}}Identification_group(FINESS/SIRET){{/tr}}
      </td>
      <td class="{{if $group->siret || $group->finess}}ok{{else}}error{{/if}}">
        {{if $group->siret || $group->finess}}
          {{tr}}Present{{/tr}}
        {{else}}
          {{tr}}Not_present{{/tr}}
        {{/if}}
      </td>
    </tr>
    <tr>
      <td>
        {{tr}}Mediboard_oid{{/tr}}
      </td>
      <td class="{{if $mb_oid}}ok{{else}}error{{/if}}">
        {{if $mb_oid}}
          {{tr}}Present{{/tr}}
        {{else}}
          {{tr}}Not_present{{/tr}}
        {{/if}}
      </td>
    </tr>
    <tr>
      <td>
        {{tr}}Install_Java{{/tr}}
      </td>
      <td class="{{if $java}}ok{{else}}error{{/if}}">
        {{if $java}}
          {{tr}}Install{{/tr}}
        {{else}}
          {{tr}}Not_install{{/tr}}
        {{/if}}
      </td>
    </tr>
    <tr>
      <td>
        {{tr}}Install_ghostscript{{/tr}}
      </td>
      <td class="{{if $ghostscript}}ok{{else}}error{{/if}}">
        {{if $ghostscript}}
          {{tr}}Install{{/tr}}
        {{else}}
          {{tr}}Not_install{{/tr}}
        {{/if}}
      </td>
    </tr>
  </table>
</fieldset>
<br/>
<fieldset>
  <legend>{{tr}}Do_association{{/tr}}</legend>
  <form name="form_type_code" method="POST">
    <input type="hidden" name="m" value="cda" />
    <input type="hidden" name="dosql" value="do_cda_association_aed" />
    <input type="hidden" name="group_id" value="{{$group->_id}}"/>
    <table class="form">
      <tr>
        <th class="title">
          {{tr}}Group{{/tr}}
        </th>
        <th class="title">
          {{tr}}Association{{/tr}}
        </th>
      </tr>
      <tr>
        <td>
          {{$group->text}}
        </td>
        <td>
          <select name="group_type">
            <option value="">&mdash; {{tr}}Association.none{{/tr}} &mdash;</option>
            {{foreach from=$type_group item=_type_group}}
              <option value="{{$_type_group.code}}"
                      {{if $group->_ref_last_id400->id400 === $_type_group.code}}selected{{/if}}>
                {{$_type_group.code}} - {{$_type_group.displayName}}
              </option>
            {{/foreach}}
          </select>
        </td>
      </tr><tr>
        <td colspan="2">
          <button type="submit" class="submit">{{tr}}Save{{/tr}}</button>
        </td>
      </tr>
    </table>
  </form>
</fieldset>