{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPetablissement
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="main">
  <tr>
    <td class="halfPane">
      {{if $can->edit}}
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;group_id=0" class="button new">
        Cr�er un �tablissement
      </a>
      {{/if}}
      <table class="tbl">
        <tr>
          <th>Liste des �tablissements</th>
          <th>Fonctions associ�es</th>
        </tr>
        {{foreach from=$groups item=_group}}
        <tr {{if $_group->_id == $group->_id}} class="selected" {{/if}}>
          <td>
            {{if $can->edit}}
            <a href="?m={{$m}}&amp;tab={{$tab}}&amp;group_id={{$_group->_id}}">
              {{$_group->text}}
            </a>
            {{else}}
            {{$_group->text}}
            {{/if}}
          </td>
          <td>
            {{if $can->edit}}
            <a href="?m={{$m}}&amp;tab={{$tab}}&amp;group_id={{$_group->_id}}">
              {{$_group->_ref_functions|@count}}
            </a>
            {{else}}
              {{$_group->_ref_functions|@count}}
            </a>
            {{/if}}
          </td>
        </tr>
        {{/foreach}}
      </table>
    </td>
    {{if $can->edit}}
    <td class="halfPane">

			{{mb_script module="dPpatients" script="autocomplete"}}
			<script type="text/javascript">
			Main.add(function () {
			  InseeFields.initCPVille("group", "cp", "ville", "tel");
			});
			</script>
    	
      <form name="group" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_groups_aed" />
   	  <input type="hidden" name="group_id" value="{{$group->group_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {{if $group->group_id}}
          <th class="title text modify" colspan="2">
          {{assign var=object value=$group}}
          {{mb_include module=system template=inc_object_notes     }}
		      {{mb_include module=system template=inc_object_idsante400}}
		      {{mb_include module=system template=inc_object_history   }}
            Modification de l'�tablissement &lsquo;{{$group->text}}&rsquo;
          {{else}}
          <th class="title text" colspan="2">
            Cr�ation d'un �tablissement
          {{/if}}
          </th>
        </tr>
        <tr>
          <th>{{mb_label object=$group field="text"}}</th>
          <td>{{mb_field object=$group field="text"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$group field="raison_sociale"}}</th>
          <td>{{mb_field object=$group field="raison_sociale"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$group field="adresse"}}</th>
          <td>{{mb_field object=$group field="adresse"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$group field="cp"}}</th>
          <td>{{mb_field object=$group field="cp"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$group field="ville"}}</th>
          <td>{{mb_field object=$group field="ville"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$group field="tel"}}</th>
		      <td>{{mb_field object=$group field="tel"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$group field="fax"}}</th>
		      <td>{{mb_field object=$group field="fax"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$group field="tel_anesth"}}</th>
		      <td>{{mb_field object=$group field="tel_anesth"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$group field="mail"}}</th>
          <td>{{mb_field object=$group field="mail"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$group field="web"}}</th>
          <td>{{mb_field object=$group field="web"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$group field="directeur"}}</th>
          <td>{{mb_field object=$group field="directeur"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$group field="domiciliation"}}</th>
          <td>{{mb_field object=$group field="domiciliation"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$group field="siret"}}</th>
          <td>{{mb_field object=$group field="siret"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$group field="finess"}}</th>
          <td>{{mb_field object=$group field="finess"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$group field="ape"}}</th>
          <td>{{mb_field object=$group field="ape"}}</td>
     		</tr>

        {{if $group->_id}}
        <tr>
          <th>{{mb_label object=$group field="service_urgences_id"}}</th>
          <td>
            <select name="service_urgences_id">
              <option value="">&mdash; Choisir le service d'urgences</option>
              {{foreach from=$group->_ref_functions item=_function}}
              <option value="{{$_function->_id}}" class="mediuser" style="border-color: #{{$_function->color}}" {{if $_function->_id == $group->service_urgences_id}}selected="selected"{{/if}}>
                {{$_function}}
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$group field="pharmacie_id"}}</th>
          <td>
            <select name="pharmacie_id">
              <option value="">&mdash; Choisir la pharmacie</option>
              {{foreach from=$group->_ref_functions item=_function}}
              <option value="{{$_function->_id}}" class="mediuser" style="border-color: #{{$_function->color}}" {{if $_function->_id == $group->pharmacie_id}}selected="selected"{{/if}}>
                {{$_function}}
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        {{/if}}
        
        <tr>
          <th>{{mb_label object=$group field="chambre_particuliere"}}</th>
          <td>{{mb_field object=$group field="chambre_particuliere"}}</td>
        </tr>

        <tr>
          <td class="button" colspan="2">
          {{if $group->_id}}
            <button class="modify" type="submit" name="modify">
              {{tr}}Save{{/tr}}
            </button>
            <button class="trash" type="button" name="delete" onclick="confirmDeletion(this.form,{typeName:'l\'�tablissement',objName:'{{$group->text|smarty:nodefaults|JSAttribute}}'})">
              {{tr}}Delete{{/tr}}
            </button>
          {{else}}
            <button class="new" type="submit" name="create">
              {{tr}}Create{{/tr}}
            </button>
          {{/if}}
          </td>
        </tr>
      </table>
      </form>
    </td>
    {{/if}}
  </tr>
</table>