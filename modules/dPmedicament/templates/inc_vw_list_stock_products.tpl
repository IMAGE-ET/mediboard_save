{{mb_include module=system template=inc_pagination total=$total current=$start change_page='changePageProducts' step=20}}

<table class="main tbl">
  <tr>
    <th style="width: 0.1%;"></th>
    <th>{{mb_title class=CProduct field=name}}</th>
    <th>Dans le LT</th>
  </tr>
  {{foreach from=$list_products item=_product}}
    <tr>
      <td>
        <form action="?m=dPmedicament" method="post" name="addProduit-{{$_product->_id}}" onsubmit="return onSubmitFormAjax(this, {onComplete: filterProducts.curry(this)})">
          <input type="hidden" name="m" value="dPmedicament" />
          <input type="hidden" name="dosql" value="do_produit_livret_aed" />
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="group_id" value="{{$g}}"/>
          <input type="hidden" name="code_cip" value="{{$_product->code}}"/>
          
          {{if $_product->_is_valid}}
            {{if $_product->_in_livret}}
              <button type="button" class="cancel notext" 
                      onclick="confirmDeletion(this.form,{ajax:true,typeName:'le produit du livret th�rapeutique',objName:'{{$_product|smarty:nodefaults|JSAttribute}}'})">
                {{tr}}Delete{{/tr}}
              </button>
            {{else}}
              <button type="submit" class="add notext">{{tr}}Add{{/tr}}</button>
            {{/if}}
          {{/if}}
        </form>
      </td>
      <td>{{mb_value object=$_product field=name}}</td>
      <td>{{tr}}bool.{{$_product->_in_livret}}{{/tr}}</td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="10">{{tr}}CProduct.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>
