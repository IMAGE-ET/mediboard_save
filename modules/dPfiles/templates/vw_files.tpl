{{include file="inc_files_functions.tpl"}}
{{mb_include_script module="system" script="object_selector"}}


<script type="text/javascript">

function pageMain() {
  initAccord(true);
}

</script>

<table class="main">
  <tr>
    <td>
      <form name="FrmClass" action="?m={{$m}}" method="get" onsubmit="return checkForm(this);">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="file_id" value="{{$file->file_id}}" />
      
      <table class="form">
        <tr>
          <td  class="readonly">
            <label for="selClass" title="Type de l'objet courant">Type</label>
            <input type="text" readonly="readonly" ondblclick="ObjectSelector.init()" name="selClass" value="{{$selClass|stripslashes}}" />
          </td>
          <td class="readonly">
            <label title="Nom de l'objet s�lectionn�">Nom</label>
            <input type="text" size="50" readonly="readonly" ondblclick="ObjectSelector.init()" name="selView" value="{{$selView|stripslashes}}" />
            <button type="button" onclick="ObjectSelector.init()" class="search">Rechercher</button>
            <input type="hidden" name="selKey" value="{{$selKey|stripslashes}}" />
            <input type="hidden" name="keywords" value="{{$keywords|stripslashes}}" />
            <script type="text/javascript">
              ObjectSelector.init = function(){
                var oForm = document.FrmClass;
                this.eId = oForm.file_id;
                this.eView = oForm.selView;
                this.eClass = oForm.selClass;
                this.onlyclass = "false"; 
                this.pop();
              }
            </script>
          </td>

          {{if $selKey}}
          <td>
            <select name="typeVue" onchange="if (this.form.onsubmit()) { this.form.submit() };">
              <option value="0" {{if $typeVue == 0}}selected="selected"{{/if}}>Miniatures et aper�us</option>
              <option value="1" {{if $typeVue == 1}}selected="selected"{{/if}}>Miniatures seuls</option>
              <option value="2" {{if $typeVue == 2}}selected="selected"{{/if}}>Aper�us seuls</option>
            </select>
          </td>
          {{/if}}
        </tr>
      </table>
      </form>
    </td>
  </tr>
  {{if $selClass && $selKey}}
  <tr>
    <td>
      <table class="form">
        <tr>
          <th class="category" colspan="2">{{$object->_view}}</th>
        </tr>
        <tr>
          {{if $typeVue}}
          <td colspan="2" id="listView">
            {{if $typeVue==1}}
            {{include file="inc_list_view_colonne.tpl"}}
            {{else $typeVue==2}}
            {{include file="inc_list_view_gd_thumb.tpl"}}            
            {{/if}}
          </td>      
          {{else}}
          <td style="width: 400px;" id="listView">
            {{include file="inc_list_view.tpl"}}
          </td>
          <td id="bigView" style="text-align: center;">
            {{include file="inc_preview_file.tpl"}}
          </td>
          {{/if}}
        </tr>
      </table>
    </td>
  </tr>
  {{/if}}
</table>