{{$count}} 
{{if $count==0}}
  {{tr}}CProduct.one{{/tr}}
{{else}}
  {{tr}}CProduct.more{{/tr}}
{{/if}}
{{if $total}}(sur {{$total}}) {{/if}} trouv�{{if $count>1}}s{{/if}}<br />
<select name="product" id="product" size="15" style="width: 200px;" onchange="refreshProductInfo(this.value);">
  {{foreach from=$list_products item=curr_product}}
  <option value="{{$curr_product->_id}}" {{if $curr_product->_id==$selected_product}}selected="selected"{{/if}}>{{$curr_product->_view}}</option>
  {{/foreach}}
</select>
