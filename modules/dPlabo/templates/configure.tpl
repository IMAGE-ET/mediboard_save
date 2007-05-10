{{* $Id: $ *}}

<script type="text/javascript">

var Action = {
  module: "dPlabo",
  
  do: function (sName) {
    var url = new Url;
    url.setModuleAction(this.module, this.Requests[sName]);
    url.requestUpdate("action-" + sName);
  },
  
  Requests: {
    import: "httpreq_import_catalogue"
  }
}
</script>

<h2>Environnement d'execution</h2>

<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="dosql" value="do_configure" />

{{assign var="module" value="dPlabo"}}

<table class="form">  
  {{assign var="class" value="CCatalogueLabo"}}
    
  <tr>
    <th class="category" colspan="0">{{tr}}{{$class}}{{/tr}}</th>
  </tr>
  
  {{assign var="var" value="remote_name"}}
  <tr>
    <th>
      <label for="{{$module}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$module}}-{{$class}}-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$module}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input class="str maxLength|8" name="{{$module}}[{{$class}}][{{$var}}]" value="{{$dPconfig.$module.$class.$var}}" />
    </td>
  </tr>  
    
  {{assign var="var" value="remote_url"}}
  <tr>
    <th>
      <label for="{{$module}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$module}}-{{$class}}-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$module}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input class="str" name="{{$module}}[{{$class}}][{{$var}}]" value="{{$dPconfig.$module.$class.$var}}" />
    </td>
  </tr>  
    
  <tr>
    <td class="button" colspan="0">
      <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
    </td>
  </tr>
</table>

</form>

<table class="tbl">

<tr>
  <th class="title" colspan="0">Configuration</th>
</tr>

<tr>
  <td><button class="tick" onclick="Action.do('import')">Importer</button></td>
  <td id="action-import"></td>
</tr>

</table>

