<script type="text/javascript">
  addCIM10Favori = function(code) {
    var oForm = getForm('addFavoris');
    $V(oForm.favoris_code, code);
    oForm.onsubmit();
  }
</script>
<div class="info">
  Nouvelle interface de recherche de codes CIM10 par mots-cl�s. <br />
  Vous pouvez retrouver l'ancienne recherche dans les pr�f�rences utilisateur (volet CIM10).
</div>


<form name="addFavoris" action="?" method="post" onsubmit="return onSubmitFormAjax(this);">
  <input type="hidden" name="m" value="dPcim10" />
  <input type="hidden" name="dosql" value="do_favoris_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="favoris_id" value="" />
  <input type="hidden" name="favoris_code" value="" />
  <input type="hidden" name="favoris_user" value="{{$app->user_id}}" />
  <input type="hidden" name="ajax" value="1" />
</form>

<form name="filterCode" method="get" action="?" onsubmit="return onSubmitFormAjax(this, null, 'code_area');">
  <input type="hidden" name="m" value="dPcim10" />
  <input type="hidden" name="a" value="code_selector_cim10" />
  <input type="hidden" name="only_list" value="1" />
  <input type="hidden" name="_all_codes" value="0" />
  <input type="hidden" name="chir" value="{{$chir}}" />
  <input type="hidden" name="anesth" value="{{$anesth}}" />
  <input type="hidden" name="object_class" value="{{$object_class}}" />
  <table class="tbl">
    <tr>
      <th>
        Filtre de recherche
      </th>
    </tr>
      <td>
        Mot-cl� : <input type="text" name="_keywords_code"/> <button type="submit" class="search notext"></button>
        <br />
        <label>
          <input type="checkbox" id="_all_codes_view" onchange="$V(this.form._all_codes, this.checked ? 1 : 0)"/> Chercher dans toute la base CIM10
        </label>
      </td>
    </tr>
  </table>
</form>
<div id="code_area" style="height: 60%; text-align: left;">
  {{mb_include module=dPcim10 template=inc_code_selector_cim10}}
</div>