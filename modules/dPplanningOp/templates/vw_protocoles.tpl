<!-- $Id$ -->

<script type="text/javascript">

function popupImport() {
  var url = new Url("dPplanningOp", "protocole_dhe_import_csv");
  url.popup(800, 600, "Import des Protocoles de DHE");
  return false;
}

var aProtocoles = {
  sejour: {},
  interv: {}
};
  
{{if $dialog}}
  Main.add(function(){
    var urlComponents = Url.parse();
    $(urlComponents.fragment || 'interv').show();
    getForm("selectFrm").action = "#"+urlComponents.fragment;
  });
{{/if}}

chooseProtocole = function(protocole_id) {
  {{if $dialog}}
  setClose(protocole_id);
  {{else}}
  var url =  new Url();
  url.setModuleTab("dPplanningOp", "vw_edit_protocole");
  url.addParam("protocole_id", protocole_id);
  url.redirect();
  {{/if}}
}

setClose = function(protocole_id) {
  window.parent.ProtocoleSelector.set(aProtocoles[protocole_id]);
  window._close();
}

refreshList = function(form, types, reset) {
  types = types || ["interv", "sejour"];
  
  if (reset) {
    types.each(function(type) {
      $V(form.elements["page["+type+"]"], 0, false);
    });
  }
  
  var url = new Url("dPplanningOp","httpreq_vw_list_protocoles");
  url.addParam("page[interv]", $V(form["page[interv]"]));
  url.addParam("page[sejour]", $V(form["page[sejour]"]));
  url.addParam("chir_id", $V(form.chir_id));
  url.addParam("dialog", $V(form.dialog));
  url.addParam("function_id", $V(form.function_id));
  url.addParam("sejour_type", "{{$sejour_type}}");
  
  types.each(function(type){
    url.addParam("type", type);
    url.requestUpdate(type);
  });
}

var changePage = {
  sejour: function (page) {
    $V(getForm("selectFrm").elements['page[sejour]'], page);
  },
  interv: function (page) {
    $V(getForm("selectFrm").elements['page[interv]'], page);
  }
};

reloadPage = function(form) {
  $V(form["page[interv]"], 0, false);
  $V(form["page[sejour]"], 0, false);
  form.submit();
}

Main.add(function(){
  var oForm = getForm("selectFrm");
  var urlComponents = Url.parse();
  
  refreshList(oForm);
  
  var url = new Url("dPplanningOp", "ajax_protocoles_autocomplete");
  url.addParam("field"            , "protocole_id");
  url.addParam("input_field"      , "search_protocole");
  url.addParam("chir_id"          , $V(oForm.chir_id));
  if(urlComponents.fragment == 'interv') {
    url.addParam("for_sejour", '0');
  } else if(urlComponents.fragment == 'sejour') {
    url.addParam("for_sejour", '1');
  }
  url.autoComplete(oForm.elements.search_protocole, null, {
    minChars: 3,
    method: "get",
    select: "view",
    dropdown: true,
    afterUpdateElement: function(field,selected){
        chooseProtocole(selected.id.split("-")[2]);
    }
  });
});
</script>
<table class="main">
  <tr>
    <td colspan="2">
      <a class="button new" href="?m={{$m}}&amp;dialog={{$dialog}}&amp;{{$actionType}}=vw_edit_protocole&amp;protocole_id=0">Cr�er un nouveau protocole</a>
      <form name="selectFrm" action="?" method="get" onsubmit="return false">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="dialog" value="{{$dialog}}" />
        <input type="hidden" {{if $dialog}} name="a" {{else}} name="tab" {{/if}} value="vw_protocoles" />
  			<input type="hidden" name="page[interv]" value="{{$page.interv}}" onchange="refreshList(this.form, ['interv'])" />
        <input type="hidden" name="page[sejour]" value="{{$page.sejour}}" onchange="refreshList(this.form, ['sejour'])" />
        
        <table class="form">
          <tr>
            <th><label for="chir_id" title="Filtrer les protocoles d'un praticien">Praticien</label></th>
            <td>
              <select name="chir_id" onchange="if (this.form.function_id) {this.form.function_id.selectedIndex=0;} reloadPage(this.form);">
                <option value="0">&mdash; Choisissez un praticien</option>
                {{foreach from=$listPrat item=curr_prat}}
                <option class="mediuser" style="border-color: #{{$curr_prat->_ref_function->color}}; {{if !$curr_prat->_ref_protocoles|@count}}color: #999;{{/if}}"
                        value="{{$curr_prat->user_id}}" {{if ($chir_id == $curr_prat->user_id) && !$function_id}} selected="selected" {{/if}}>
                  {{$curr_prat->_view}} ({{$curr_prat->_ref_protocoles|@count}})
                </option>
                {{/foreach}}
              </select>
            </td>
            <th><label for="prat_id" title="Filtrer les protocoles d'une fonction">Fonction</label></th>
            <td>
              {{if $can->admin}}
              <select name="function_id" onchange="if (this.form.chir_id) { this.form.selectedIndex=0; } reloadPage(this.form);">
                <option value="0">&mdash; Choisissez une fonction</option>
                {{foreach from=$listFunc item=curr_function}}
                <option class="mediuser" style="border-color: #{{$curr_function->color}}; {{if !$curr_function->_ref_protocoles|@count}}color: #999;{{/if}}"
                        value="{{$curr_function->_id}}" {{if $curr_function->_id == $function_id}}selected="selected"{{/if}}>
                  {{$curr_function->_view}} ({{$curr_function->_ref_protocoles|@count}})
                </option>
                {{/foreach}}
              </select>
              {{/if}}
            </td>
            <th>Recherche</th>
            <td>
              <input name="search_protocole" />
            </td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
	
  <tr>
    <td>
      {{if !$dialog}}
      <ul id="tabs-protocoles" class="control_tabs">
        <li><a href="#interv">Chirurgicaux <small>(0)</small></a></li>
        <li><a href="#sejour">M�dicaux <small>(0)</small></a></li>
        {{if !$dialog}}
        <li><button type="button" style="float:right;" onclick="return popupImport();" class="hslip">{{tr}}Import-CSV{{/tr}}</button></li>
        {{/if}}
      </ul>
      
      <script type="text/javascript">
      Main.add(function(){
        // Don't use .create() because the #fragment of the url 
        // is not taken into account, and this is important here
        new Control.Tabs('tabs-protocoles');
      });
      </script>
      
      <hr class="control_tabs" />
      {{/if}}
      
      <div style="display: none;" id="interv"></div>
      <div style="display: none;" id="sejour"></div>
    </td> 
  </tr>
</table>