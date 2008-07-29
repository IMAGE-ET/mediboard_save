<script type="text/javascript">
var oEvenementField = null;

function viewItems(iCategorie){
  var oForm = document.edit_type_ei;
  $('Items' + oForm._elemOpen.value).hide();
  $('Items' + iCategorie).show();
  oForm._elemOpen.value = iCategorie;
}
function checkCode(oElement){
  if(oElement.checked == true){
    putCode(oElement.name);
  }else{
    delCode(oElement.name);
  }
}
function delCode(iCode){
  var oForm = document.edit_type_ei;
  oEvenementField.remove(iCode);
  
  var oElement = eval("oForm._ItemsSel_cat_" + oForm._elemOpen.value);
  oItemSelField = new TokenField(oElement);
  oItemSelField.remove(iCode);
  
  refreshListChoix();
}

function putCode(iCode){
  var oForm = document.edit_type_ei;
  oEvenementField.add(iCode);
  
  var oElement = eval("oForm._ItemsSel_cat_" + oForm._elemOpen.value);
  oItemSelField = new TokenField(oElement);
  oItemSelField.add(iCode);
  
  refreshListChoix();
}
function refreshListChoix(){
  var oForm = document.edit_type_ei;
  var oCategorie = oForm._cat_evenement.options;
  var sListeChoix = "";
  for(i=0; i< oCategorie.length; i++){
    var oElement = eval("oForm._ItemsSel_cat_" + oCategorie[i].value);
    if(oElement.value){
      sListeChoix += "<strong>" + oCategorie[i].text + "</strong><ul>";
      var aItems = oElement.value.split("|");
      oItems = aItems.without("");
      iCode = 0;
      while (sCode = aItems[iCode++]) {
        sListeChoix += "<li>" + $('titleItem' + sCode).title + "</li>";
      }
      sListeChoix += "</ul>";
    }
  }
  $('listChoix').innerHTML = sListeChoix;
}

Main.add(function () {
  refreshListChoix();
  oEvenementField = new TokenField(document.edit_type_ei.evenements);
});

</script>
<table class="main">
<tr>
  <td class="halfPane">
    <table class="tbl">
      <tr>
        <th class="title" colspan="4">{{tr}}CTypeEi{{/tr}}</th>
      </tr>
      <tr>
        <th>{{mb_label object=$type_ei field="name"}}</th>
        <th>{{mb_label object=$type_ei field="concerne"}}</th>
        <th>{{mb_label object=$type_ei field="desc"}}</th>
      </tr>
      {{foreach from=$type_ei_list key=id item=type}}
      <tr>
      <td><a href="?m={{$m}}&amp;tab=vw_typeEi_manager&amp;type_ei_id={{$type->_id}}" title="Voir ou modifier le mod�le de fiche">
      {{mb_value object=$type field=name}}
      </a>
      </td>
      <td><a href="?m={{$m}}&amp;tab=vw_typeEi_manager&amp;type_ei_id={{$type->_id}}" title="Voir ou modifier le mod�le de fiche">
      {{mb_value object=$type field=concerne}}
      </a>
      </td>
      <td style="absolute"><a href="?m={{$m}}&amp;tab=vw_typeEi_manager&amp;type_ei_id={{$type->_id}}" title="Voir ou modifier le mod�le de fiche">
      {{mb_value object=$type field=desc}}
      </a>
      </td>
      </tr>
      {{/foreach}}
    </table>
  </td>
  <td class="halfPane">
    <a class="buttonnew" href="?m={{$m}}&amp;tab=vw_typeEi_manager&amp;type_ei_id=0">{{tr}}CTypeEi.create{{/tr}}</a>
    <form name="edit_type_ei" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_typeEi_aed" />
      <input type="hidden" name="type_ei_id" value="{{$type_ei->_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {{if $type_ei->_id}}
          <th class="title modify" colspan="2">{{tr}}CTypeEi.modify{{/tr}} {{$type_ei->_view}}</th>
          {{else}}
          <th class="title" colspan="2">{{tr}}CTypeEi.create{{/tr}}</th>
          {{/if}}
        </tr>   
        <tr>
          <th>{{mb_label object=$type_ei field="name"}}</th>
          <td>{{mb_field object=$type_ei size=30 field="name"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$type_ei field="concerne"}}</th>
          <td>{{mb_field object=$type_ei field="concerne"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$type_ei field="type_signalement"}}</th>
          <td>{{mb_field object=$type_ei field="type_signalement"}}</td>
        </tr>
        </tr>
        <tr>
          <th>{{mb_label object=$type_ei field="evenements"}}</th>
          <td>
            <input type="hidden" name="evenements" class="{{$type_ei->_props.evenements}}" value="{{$type_ei->evenements}}"/>
            <input type="hidden" name="_elemOpen" value="{{$firstdiv}}" />
            <select name="_cat_evenement" onchange="javascript:viewItems(this.value);">
            {{foreach from=$listCategories item=curr_evenement}}
            <option value="{{$curr_evenement->ei_categorie_id}}"{{if $curr_evenement->ei_categorie_id==$firstdiv}} selected="selected"{{/if}}>
              {{$curr_evenement->nom}}
            </option>
            {{/foreach}}
            </select>
            </td>
        <tr>
        <th></th>
         <td colspan="2">
           {{foreach from=$listCategories item=curr_evenement}}
           <input type="hidden" name="_ItemsSel_cat_{{$curr_evenement->ei_categorie_id}}" value="{{$curr_evenement->checked}}" />
           <table class="tbl" id="Items{{$curr_evenement->ei_categorie_id}}" {{if $curr_evenement->ei_categorie_id!=$firstdiv}}style="display:none;"{{/if}}>
             {{counter start=0 skip=1 assign=curr_data}}
             {{foreach name=itemEvenement from=$curr_evenement->_ref_items item=curr_item}}
             {{if $curr_data is div by 3 || $curr_data==0}}
             <tr>
             {{/if}}
               <td class="text">
                 <input type="checkbox" name="{{$curr_item->ei_item_id}}" onclick="javascript:checkCode(this);" {{if $curr_item->checked}}checked="checked"{{/if}}/><label for="{{$curr_item->ei_item_id}}" id="titleItem{{$curr_item->ei_item_id}}" title="{{$curr_item->nom}}">{{$curr_item->nom}}</label>
               </td>
             {{if (($curr_data+1) is div by 3 || $smarty.foreach.itemEvenement.last)}}
             </tr>
             {{/if}}
             {{counter}}
             {{foreachelse}}
             <tr>
               <td>
                 {{tr}}_CFicheEi-noitemscat{{/tr}}
               </td>
             </tr>
             {{/foreach}}
           </table>
           {{foreachelse}}
           {{tr}}CEiItem.none{{/tr}}
           {{/foreach}}
         </td>
       </tr>
       <tr>
       <th></th>
                 <td colspan="2" id="listChoix"></td>
       </tr>
        <tr>
          <th>{{mb_label object=$type_ei field="desc"}}</th>
          <td>{{mb_field object=$type_ei field="desc"}}</td>
        </tr>
        <tr>
          <td class="button" colspan="4">
            {{if $type_ei->_id}}
            <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
            <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'',objName:'{{$type_ei->_view|smarty:nodefaults|JSAttribute}}'})">
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
</table>