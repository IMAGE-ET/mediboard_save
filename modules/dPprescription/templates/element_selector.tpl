<script type="text/javascript">

function setClose(element_id) {
  var oSelector = window.opener.ElementSelector;
  oSelector.set(element_id);
  window.close();
}

</script>

<table class="tbl">
  <tr>
    <th>Recherche d'�l�ments dans {{tr}}CCategoryPrescription.chapitre.{{$type}}{{/tr}}</th>
  </tr>
  <tr>
    <td style="text-align: center">
      <form name="searchElement" action="?">
        <input type="hidden" name="m" value="dPprescription" />
        <input type="hidden" name="a" value="element_selector" />
        <input type="hidden" name="dialog" value="1" />
        <input type="hidden" name="type" value="{{$type}}" />
        <input type="text" name="libelle" value="{{$libelle}}" />
        <button type="button" class="search" onclick="this.form.submit();">Rechercher</button>
      </form>
    </td>
  </tr>
  <tr>
    <th>
      {{$elements|@count}} r�sultat(s) dans la categorie {{tr}}CCategoryPrescription.chapitre.{{$type}}{{/tr}}
    </th>
  </tr>
  {{foreach from=$tabElements key=category_id item=catElement}}
  {{assign var=cat value=$categories.$category_id}}
  <tr>
    <th>{{$cat->_view}}</th>
  </tr>
  {{foreach from=$catElement item=element}}
  <tr>
    <td>
      <img src="./images/icons/plus.gif" onclick="setClose('{{$element->_id}}')" alt="Ajouter cet �l�ment � la prescription" title="Ajouter � la prescription" />
      {{$element->_view}}
    </td>
  </tr>
  {{/foreach}}
  {{/foreach}}
</table>