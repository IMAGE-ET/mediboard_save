{{$count}} cat�gorie{{if $count>1}}s{{/if}} {{if $total}}(sur {{$total}}) {{/if}}trouv�e{{if $count>1}}s{{/if}}<br />
<select name="category_id" id="category_id" onchange="refreshProductsList(this.value); this.form.search_category.value=''; this.form.search_product.value='';" size="15" style="width: 150px;">
  <option value="0"> ----- Toutes ---- </option>
  {{foreach from=$list_categories item=curr_category}}
  <option value="{{$curr_category->_id}}" {{if $curr_category->_id==$selected_category}}selected="selected"{{/if}}>{{$curr_category->name}}</option>
  {{/foreach}}
</select>