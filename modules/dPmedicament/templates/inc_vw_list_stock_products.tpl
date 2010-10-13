{{mb_include module=system template=inc_pagination total=$total current=$start change_page='changePageProducts' step=25}}

<table class="main tbl">
  <tr>
    <th style="width: 0.1%;"></th>
    <th style="width: 16px;"></th>
    <th style="width: 0.1%;">{{mb_title class=CProduct field=code}}</th>
    <th>{{mb_title class=CProduct field=name}}</th>
  </tr>
  {{foreach from=$list_products item=_product}}
    <tr>
      <td>
        <form action="?m=dPmedicament" method="post" name="addProduit-{{$_product->_id}}" onsubmit="return onSubmitFormAjax(this, {onComplete: filterProducts.curry(this)})">
          <input type="hidden" name="m" value="dPmedicament" />
          <input type="hidden" name="dosql" value="do_produit_livret_aed" />
          <input type="hidden" name="group_id" value="{{$g}}"/>
          <input type="hidden" name="code_cip" value="{{$_product->code}}"/>
          <input type="hidden" name="del" value="0"/>
          
          {{if $_product->_is_valid}}
            {{if $_product->_in_livret}}
              <label title="Supprimer le produit du LT">
                <input type="checkbox" checked="checked" 
                       onclick="confirmDeletion(this.form,{ajax:true,typeName:'le produit du livret th�rapeutique',objName:'{{$_product->_view|smarty:nodefaults|JSAttribute}}'})" />
              </label>
            {{else}}
              <label title="{{tr}}Add{{/tr}}">
                <input type="checkbox" onclick="this.form.onsubmit()" />
              </label>
            {{/if}}
          {{else}}
            <img src="./images/icons/warning.png" title="Ce produit n'a pas un code CIP valide ({{$_product->code}})"/>
          {{/if}}
        </form>
      </td>
      <td>
        {{if $_product->cancelled}}
          <img src="./images/icons/archive.png" title="Ce produit est archiv�"/>
        {{/if}}
      </td>
      <td>{{mb_value object=$_product field=code}}</td>
      <td>{{mb_value object=$_product field=name}}</td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="10">{{tr}}CProduct.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>
