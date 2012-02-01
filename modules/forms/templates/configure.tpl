{{* $Id$ *}}

<script type="text/javascript">

var Action = {
  execute: function (type) {
    if (!confirm("Voulez-vous r�element �xecuter cette action ?")) return;
    
    var url = new Url("forms", "do_batch_action");
    url.addParam("action", type);
    url.requestUpdate("action-" + type);
  }
};

</script>

<form name="editConfigForms" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />
  <table class="form">
    
    {{assign var=class value=CExClassField}}
    <tr>
      <th colspan="2" class="title">{{tr}}{{$class}}{{/tr}}</th>
    </tr>
    {{mb_include module=system template=inc_config_bool var=force_concept}}
    
    {{assign var=class value=CExConcept}}
    <tr>
      <th colspan="2" class="title">{{tr}}{{$class}}{{/tr}}</th>
    </tr>
    {{mb_include module=system template=inc_config_bool var=force_list}}
    
    <tr>
      <td class="button" colspan="10">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>

{{*
<div class="small-error">
  Ces actions agissent sur l'ensemble des donn�es des formulaires, elles sont � manipuler avec pr�caution
</div>

<table class="tbl" style="table-layout: fixed;">
  <tr>
    <th class="title" colspan="2">Actions par lot</th>
  </tr>
  
  <tr>
    <td>
      <button class="tick" onclick="Action.execute('bool_defaul_reset')">Remettre les champs et concepts bool�ens en "Ind�fini"</button>
    </td>
    <td id="action-bool_defaul_reset"></td>
  </tr>
  
  <tr>
    <td>
      <button class="tick" onclick="Action.execute('str_to_text')">Passer les champs et concepts de type "Texte court" en "Texte long"</button>
    </td>
    <td id="action-str_to_text"></td>
  </tr>
</table>
*}}