<tr id="line_{{$_actor->_guid}}" {{if !$_actor->actif}} class="opacity-30" {{/if}}>
  <td class="text">
    <button title="Modifier {{$_actor->_view}}" class="edit notext" onclick="InteropActor.editActor('{{$_actor->_guid}}');"
            style="float: right">
      Modifier {{$_actor->_view}}
    </button>

    <a href="#" onclick="InteropActor.viewActor('{{$_actor->_guid}}', null, this);" title="Afficher l'acteur d'int�gration">
      {{$_actor->_view}}
    </a>
  </td>
  <td class="compact">{{$_actor->_ref_group->_view}}</td>
  <td>
    {{foreach from=$_actor->_ref_exchanges_sources item=_exchange_source}}
      {{if !$_actor instanceof CSenderSOAP && !$_actor instanceof CSenderMLLP && !$_actor instanceof CDicomSender}}
        {{mb_include module=system template=inc_img_status_source exchange_source=$_exchange_source
          actor_actif=$_actor->actif actor_parent_class=$_actor->_parent_class}}
      {{/if}}
    {{/foreach}}
  </td>
</tr>
