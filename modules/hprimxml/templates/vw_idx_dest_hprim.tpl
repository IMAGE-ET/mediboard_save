{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
  Main.add(function () {
	  {{if count($dest_hprim->_ref_exchanges_sources) > 0}}
      Control.Tabs.create('tabs-evenements-{{$dest_hprim->_guid}}', true);
		{{/if}}
  });
</script>

<table class="main">
  <tr>
    <td style="width:30%" rowspan="6">
      <a class="button new" href="?m=hprimxml&amp;tab=vw_idx_dest_hprim&amp;dest_hprim_id=0">
      	{{tr}}CDestinataireHprim-title-create{{/tr}}
      </a>
      <table class="tbl">
        <tr>
          <th class="title" colspan="6">{{tr}}CDestinataireHprim{{/tr}}</th>
        </tr>
        <tr>
          <th>{{mb_title object=$dest_hprim field="nom"}}</th>
          <th>{{mb_title object=$dest_hprim field="group_id"}}</th>
          <th>{{mb_title object=$dest_hprim field="type"}}</th>
					<th>{{mb_title object=$dest_hprim field="message"}}</th>
          <th>{{mb_title object=$dest_hprim field="actif"}}</th>
          <th>{{mb_title object=$dest_hprim field="register"}}</th>
        </tr>
        {{foreach from=$listDestHprim item=_dest_hprim}}
        <tr {{if $_dest_hprim->_id == $dest_hprim->_id}}class="selected"{{/if}}>
          <td>
            <a href="?m=hprimxml&amp;tab=vw_idx_dest_hprim&amp;dest_hprim_id={{$_dest_hprim->_id}}" title="Modifier le destinataire HPRIM">
              {{mb_value object=$_dest_hprim field="nom"}}
            </a>
          </td>
          <td>{{$_dest_hprim->_ref_group->_view}}</td>
          <td>{{mb_value object=$_dest_hprim field="type"}}</td>
					<td>{{mb_value object=$_dest_hprim field="message"}}</td>
          <td>{{mb_value object=$_dest_hprim field="actif"}}</td>
          <td>{{mb_value object=$_dest_hprim field="register"}}</td>
        </tr>
        {{/foreach}}
      </table>
    </td>
    <td class="halfPane">
      {{if $can->edit}}
      <form name="editcip" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_destinataire_aed" />
      <input type="hidden" name="dest_hprim_id" value="{{$dest_hprim->_id}}" />
      <input type="hidden" name="del" value="0" />      
      
      <table class="form">
        <tr>
          {{if $dest_hprim->_id}}
          <th class="title modify text" colspan="2">
            {{mb_include module=system template=inc_object_idsante400 object=$dest_hprim}}
            {{mb_include module=system template=inc_object_history object=$dest_hprim}}
            
            {{tr}}CDestinataireHprim-title-modify{{/tr}} '{{$dest_hprim->_view}}'
          </th>
          {{else}}
          <th class="title" colspan="2">
           {{tr}}CDestinataireHprim-title-create{{/tr}}
          </th>
          {{/if}}
        </tr>
        <tr>
          <th>{{mb_label object=$dest_hprim field="nom"}}</th>
          <td>{{mb_field object=$dest_hprim field="nom"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$dest_hprim field="libelle"}}</th>
          <td>{{mb_field object=$dest_hprim field="libelle"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$dest_hprim field="group_id"}}</th>
          <td>
            <select name="group_id" class="{{$dest_hprim->_props.group_id}}" style="width: 17em;">
              <option value="">&mdash; Associer � un �tablissement</option>
              {{foreach from=$listEtab item=_etab}}
                <option value="{{$_etab->_id}}" {{if $_etab->_id == $dest_hprim->group_id}} selected="selected" {{/if}}>
                  {{$_etab->_view}}
                </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>  
          <th>{{mb_label object=$dest_hprim field="type"}}</th>
          <td>
          	<input type="text" name="type" size="20" value="{{if $dPconfig.sip.server}}cip{{else}}sip{{/if}}" readonly="readonly" />
          </td>
        </tr>
				<tr>
          <th>{{mb_label object=$dest_hprim field="message"}}</th>
          <td>{{mb_field object=$dest_hprim field="message"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$dest_hprim field="actif"}}</th>
          <td>{{mb_field object=$dest_hprim field="actif"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$dest_hprim field="register"}}</th>
          <td>{{mb_field object=$dest_hprim field="register"}}</td>
        </tr>
        <tr>
          <td class="button" colspan="2">
          	{{if $dest_hprim->_id}}
              <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
              <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'',objName:'{{$dest_hprim->_view|smarty:nodefaults|JSAttribute}}'})">
                {{tr}}Delete{{/tr}}
              </button>
					  {{else}}
					     <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
            {{/if}}
          </td>
        </tr>     
      </table>
      </form>
    </td>
  </tr>
  {{if count($dest_hprim->_ref_exchanges_sources) > 0}}
  <tr>
    <td>
      <table class="form">  
        <tr>
          <th class="title" colspan="2">
            {{tr}}config-exchange-source{{/tr}} '{{mb_value object=$dest_hprim field="message"}}'
          </th>
        </tr>
        <tr>
          <td colspan="2"> 
					  <table class="form">  
              <tr>
              	<td>
              		{{assign var=message value="CDestinataireHprim"|static:"messagesHprim"}}
              		{{foreach from=$message key=_message item=_evenements}}
									  {{if $_message == $dest_hprim->message}}
										  <ul id="tabs-evenements-{{$dest_hprim->_guid}}" class="control_tabs">
										  	{{foreach from=$_evenements item=_evenement}}
									        <li><a href="#{{$_evenement}}">{{tr}}{{$_evenement}}{{/tr}}</a></li>
									      {{/foreach}}
									    </ul>
											
											<hr class="control_tabs" />
											
											{{foreach from=$_evenements item=_evenement}}
												<div id="{{$_evenement}}" style="display:none;">
	                       {{mb_include module=system template=inc_config_exchange_source source=$dest_hprim->_ref_exchanges_sources.$_evenement}}
												</div>
											{{/foreach}}
										{{/if}}
									{{/foreach}}
                </td>
              </tr>
						</table>
					</td>
        </tr>
      </table>
      {{/if}}
    </td>
  </tr>
  {{/if}}
</table>