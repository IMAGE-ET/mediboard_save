<table class="tbl">
  <tr>
    <th>{{mb_colonne class=CCompteRendu field=nom              order_col=$order_col order_way=$order_way url="?m=compteRendu&tab=vw_modeles"}}</th>
    <th>{{mb_colonne class=CCompteRendu field=object_class     order_col=$order_col order_way=$order_way url="?m=compteRendu&tab=vw_modeles"}}</th>
    <th>{{mb_colonne class=CCompteRendu field=file_category_id order_col=$order_col order_way=$order_way url="?m=compteRendu&tab=vw_modeles"}}</th>
    <th>{{mb_colonne class=CCompteRendu field=type             order_col=$order_col order_way=$order_way url="?m=compteRendu&tab=vw_modeles"}}</th>
    <th class="narrow" colspan="2">{{tr}}Stats{{/tr}}</th>
    <th class="narrow"></th>
  </tr>

  {{foreach from=$modeles item=_modele}}
  <tr>
    <td>
      {{if $_modele->fast_edit_pdf}}
        <img style="float: right;" src="modules/dPcompteRendu/fcke_plugins/mbprintPDF/images/mbprintPDF.png" />
      {{elseif $_modele->fast_edit}}
        <img style="float: right;" src="modules/dPcompteRendu/fcke_plugins/mbprinting/images/mbprinting.png" />
      {{/if}}
      {{if $_modele->fast_edit || $_modele->fast_edit_pdf}}
        <img style="float: right;" src="images/icons/lightning.png" />
      {{/if}}
      <a href="?m={{$m}}&tab=addedit_modeles&compte_rendu_id={{$_modele->_id}}">
        {{assign var=object_class value=$_modele->object_class}}
        {{assign var=name         value=$_modele->nom}}
        {{if isset($special_names.$object_class.$name|smarty:nodefaults)}}
          <strong>Special:</strong>
          {{$_modele->nom|trim:"[]"}}
        {{else}}
          {{mb_value object=$_modele field=nom}}
        {{/if}}
      </a>
    </td>
  
    <td>{{mb_value object=$_modele field=object_class}}</td>

    <td>{{$_modele->_ref_category}}</td>
  
    <td>
      {{mb_value object=$_modele field=type}}
      <div class="compact">
        {{if $_modele->type == "body"}} 
          {{assign var=header value=$_modele->_ref_header}}
          {{if $header->_id}} 
            + 
            <span onmouseover="ObjectTooltip.createEx(this, '{{$header->_guid}}');">
              {{$header->nom}}
            </span>
          {{/if}}
          
          {{assign var=preface value=$_modele->_ref_preface}}
          {{if $preface->_id}} 
            + 
            <span onmouseover="ObjectTooltip.createEx(this, '{{$preface->_guid}}');">
              {{$preface->nom}}
            </span>
          {{/if}}
          
          {{assign var=ending value=$_modele->_ref_ending}}
          {{if $ending->_id}} 
            + 
            <span onmouseover="ObjectTooltip.createEx(this, '{{$ending->_guid}}');">
              {{$ending->nom}}
            </span>
          {{/if}}

          {{assign var=footer value=$_modele->_ref_footer}}
          {{if $footer->_id}} 
            + 
            <span onmouseover="ObjectTooltip.createEx(this, '{{$footer->_guid}}');">
              {{$footer->nom}}
            </span>
          {{/if}}
        {{elseif $_modele->type == "header"}}
          {{assign var=count value=$_modele->_count.modeles_headed}}
          {{if $count}}
            {{$_modele->_count.modeles_headed}} 
            {{tr}}CCompteRendu-back-modeles_headed{{/tr}}
          {{else}}
            <div class="empty">{{tr}}CCompteRendu-back-modeles_headed.empty{{/tr}}</div>
          {{/if}}
        {{elseif $_modele->type == "preface"}}
          {{assign var=count value=$_modele->_count.modeles_prefaced}}
          {{if $count}}
            {{$_modele->_count.modeles_prefaced}}
            {{tr}}CCompteRendu-back-modeles_prefaced{{/tr}}
          {{else}}
            <div class="empty">{{tr}}CCompteRendu-back-modeles_prefaced.empty{{/tr}}</div>
          {{/if}}
        {{elseif $_modele->type == "ending"}}
          {{assign var=count value=$_modele->_count.modeles_ended}}
          {{if $count}} 
            {{$_modele->_count.modeles_ended}} 
            {{tr}}CCompteRendu-back-modeles_ended{{/tr}}
          {{else}}
            <div class="empty">{{tr}}CCompteRendu-back-modeles_ended.empty{{/tr}}</div>
          {{/if}}
        {{elseif $_modele->type == "footer"}}
          {{assign var=count value=$_modele->_count.modeles_footed}}
          {{if $count}} 
            {{$_modele->_count.modeles_footed}} 
            {{tr}}CCompteRendu-back-modeles_footed{{/tr}}
          {{else}}
            <div class="empty">{{tr}}CCompteRendu-back-modeles_footed.empty{{/tr}}</div>
          {{/if}}
        {{/if}}
      </div>
    </td>

    <td style="text-align: center;">
      <strong>{{$_modele->_count.documents_generated|nozero}}</strong>
    </td>
    <td>
      <button class="notext stats" onclick="Modele.showUtilisation('{{$_modele->_id}}');">{{tr}}Stats{{/tr}}</button>
    </td>

    <td>
      {{if $_modele->_canEdit}}
        <form name="delete-{{$_modele->_guid}}" action="?m={{$m}}" method="post">
          <input type="hidden" name="m" value="{{$m}}" />
          <input type="hidden" name="del" value="1" />
          <input type="hidden" name="dosql" value="do_modele_aed" />
          <input type="hidden" name="_tab" value="_list" />
          {{mb_key object=$_modele}}
          <button class="trash notext" type="button"
              onclick="confirmDeletion(this.form, {
              typeName: 'le mod�le',
              objName:  '{{$_modele->nom|smarty:nodefaults|JSAttribute}}'
            })">{{tr}}Delete{{/tr}}</button>
        </form>
      {{/if}}
    </td>
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="10" class="empty">{{tr}}CCompteRendu.none{{/tr}}</td>
  </tr>
  {{/foreach}}
</table>


