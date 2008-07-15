{{mb_include_script module=dPstock script=filter}}

<script type="text/javascript">
Main.add(function () {
  filterFields = ["category_id", "societe_id", "keywords"];
  productsFilter = new Filter("filter-products", "{{$m}}", "httpreq_vw_products_list", "list-products", filterFields);
  productsFilter.submit();
});
</script>

<table class="main">
  <tr>
    <td class="halfPane" rowspan="3">
      <form name="filter-products" action="?" method="post" onsubmit="return productsFilter.submit('keywords');">
        <input type="hidden" name="m" value="{{$m}}" />
        
        <select name="category_id" onchange="productsFilter.submit();">
          <option value="0" >&mdash; {{tr}}CProductCategory.all{{/tr}} &mdash;</option>
        {{foreach from=$list_categories item=curr_category}}
          <option value="{{$curr_category->category_id}}" {{if $category_id==$curr_category->_id}}selected="selected"{{/if}}>{{$curr_category->name}}</option>
        {{/foreach}}
        </select>
        
        <select name="societe_id" onchange="productsFilter.submit();">
          <option value="0" >&mdash; {{tr}}CSociete.all{{/tr}} &mdash;</option>
        {{foreach from=$list_societes item=curr_societe}} 
          <option value="{{$curr_societe->societe_id}}" {{if $societe_id==$curr_societe->_id}}selected="selected"{{/if}}>{{$curr_societe->name}}</option>
        {{/foreach}}
        </select>
        
        <input type="text" name="keywords" value="" />
        
        <button type="button" class="search" onclick="productsFilter.submit('keywords');">{{tr}}Filter{{/tr}}</button>
        <button type="button" class="cancel notext" onclick="productsFilter.empty();"></button>
      </form>

      <div id="list-products"></div>
    </td>
    <td class="halfPane">
      <a class="buttonnew" href="?m={{$m}}&amp;tab=vw_idx_product&amp;product_id=0">{{tr}}CProduct.create{{/tr}}</a>
      <form name="edit_product" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_product_aed" />
	  <input type="hidden" name="product_id" value="{{$product->_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {{if $product->_id}}
          <th class="title modify" colspan="2">{{tr}}CProduct.modify{{/tr}} {{$product->_view}}</th>
          {{else}}
          <th class="title" colspan="2">{{tr}}CProduct.create{{/tr}}</th>
          {{/if}}
        </tr>   
        <tr>
          <th>{{mb_label object=$product field="name"}}</th>
          <td>{{mb_field object=$product field="name"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$product field="category_id"}}</th>
          <td><select name="category_id" class="{{$product->_props.category_id}}">
            <option value="">&mdash; {{tr}}CProductCategory.select{{/tr}}</option>
            {{foreach from=$list_categories item=curr_category}}
              <option value="{{$curr_category->_id}}" {{if $product->category_id == $curr_category->_id || $list_categories|@count==1}} selected="selected" {{/if}} >
              {{$curr_category->_view}}
              </option>
            {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$product field="societe_id"}}</th>
          <td><select name="societe_id" class="{{$product->_props.societe_id}}">
            <option value="">&mdash; {{tr}}CSociete.select{{/tr}}</option>
            {{foreach from=$list_societes item=curr_societe}}
              <option value="{{$curr_societe->_id}}" {{if $product->societe_id == $curr_societe->_id || $list_societes|@count==1}} selected="selected" {{/if}} >
              {{$curr_societe->_view}}
              </option>
            {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$product field="code"}}</th>
          <td>{{mb_field object=$product field="code"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$product field="description"}}</th>
          <td>{{mb_field object=$product field="description"}}</td>
        </tr>
        <tr>
          <td class="button" colspan="4">
            {{if $product->_id}}
            <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
            <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'',objName:'{{$product->_view|smarty:nodefaults|JSAttribute}}'})">
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
  {{if $product->_id}}
  <tr>
    <td class="halfPane">
      <table class="tbl">
        <tr>
          <th class="title" colspan="4">{{tr}}CProduct-back-stocks{{/tr}}</th>
        </tr>
        <tr>
          <th>{{tr}}CGroups{{/tr}}</th>
          <th>{{tr}}CProductStock-quantity{{/tr}}</th>
          <th>{{tr}}CProductStock-bargraph{{/tr}}</th>
        </tr>
        {{foreach from=$product->_ref_stocks item=curr_stock}}
        <tr>
          <td><a href="?m={{$m}}&amp;tab=vw_idx_stock&amp;stock_id={{$curr_stock->_id}}" title="Voir ou modifier le stock">{{$curr_stock->_ref_group->_view}}</a></td>
          <td>{{$curr_stock->quantity}}</td>
          <td>{{include file="inc_bargraph.tpl" stock=$curr_stock}}</td>
        </tr>
        {{foreachelse}}
        <tr>
          <td colspan="3">{{tr}}CProductProduct.none{{/tr}}</td>
        </tr>
        {{/foreach}}
        {{if $product->_id}}
          <tr>
            <td colspan="3">
              <button class="new" type="button" onclick="window.location='?m={{$m}}&amp;tab=vw_idx_stock&amp;stock_id=0&amp;product_id={{$product->_id}}'">
                {{tr}}CProductStock.create{{/tr}}
              </button>
            </td>
          </tr>
        {{/if}}
      </table>
    </td>
  </tr>
  <tr>
    <td class="halfPane">
      <table class="tbl">
        <tr>
          <th class="title" colspan="4">{{tr}}CProduct-back-references{{/tr}}</th>
        </tr>
        <tr>
           <th>Fournisseur</th>
           <th>Quantit�</th>
           <th>Prix</th>
           <th>Prix Unitaire</th>
         </tr>
         {{foreach from=$product->_ref_references item=curr_reference}}
         <tr>
           <td>{{$curr_reference->_ref_societe->_view}}</td>
           <td>{{mb_value object=$curr_reference field=quantity}}</td>
           <td>{{mb_value object=$curr_reference field=price}}</td>
           <td>{{mb_value object=$curr_reference field=_unit_price}}</td>
         </tr>
         {{foreachelse}}
         <tr>
           <td colspan="4">{{tr}}CProductReference.none{{/tr}}</td>
         </tr>
         {{/foreach}}
         {{if $product->_id}}
          <tr>
            <td colspan="4">
              <button class="new" type="button" onclick="window.location='?m={{$m}}&amp;tab=vw_idx_reference&amp;reference_id=0&amp;product_id={{$product->_id}}'">
                Nouvelle r�f�rence pour ce produit
              </button>
            </td>
          </tr>
        {{/if}}
       </table>
    
    </td>
  </tr>
  {{/if}}
</table>