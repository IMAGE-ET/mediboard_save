{{mb_include_script module="dPplanningOp" script="ccam_selector"}}

<table class="main">
  <tr>
    <td>
      <form name="bloc" action="index.php" method="get">
      <input type="hidden" name="m" value="dPstats" />
      <input type="hidden" name="_chir" value="{{$user_id}}" />
      <input type="hidden" name="_class_name" value="" />
      <table class="form">
        <tr>
          <th colspan="2" class="category">
            <select name="typeVue" onChange="this.form.submit();">
              <option value="0">
                Moyenne des temps op�ratoires
              </option>
              <option value="1"{{if $typeVue == 1}} selected="selected"{{/if}}>
                Moyenne des temps de pr�paration
              </option>
              <option value="2"{{if $typeVue == 2}} selected="selected"{{/if}}>
                Moyenne des temps d'hospitalisation
              </option>
            </select>
          </th>
        </tr>

        {{if $typeVue == 0 || $typeVue == 2}}
        <tr>
          <th><label for="codeCCAM" title="Acte CCAM">Acte CCAM</label></th>
          <td>
            <input type="text" name="codeCCAM" value="{{$codeCCAM|stripslashes}}" />
            <button type="button" class="search" onclick="CCAMSelector.init()">S�lectionner un code</button>
            
            <script type="text/javascript">
              CCAMSelector.init = function(){
                var oForm = document.bloc;
                this.eView = oForm.codeCCAM;
                this.eChir = oForm._chir;
                this.eClass = oForm._class_name;
                this.pop();
              }
            </script>
            
          </td>
        </tr>
        <tr>
          <th><label for="prat_id" title="Praticien">Praticien</label></th>
          <td>
            <select name="prat_id">
              <option value="0">&mdash; Tous les praticiens</option>
              {{foreach from=$listPrats item=curr_prat}}
              <option value="{{$curr_prat->user_id}}" {{if $curr_prat->user_id == $prat_id}}selected="selected"{{/if}}>
                {{$curr_prat->_view}}
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        {{/if}}
        {{if $typeVue == 2}}
        <tr>
          <th><label for="type" title="Type d'hospitalisation">Type</label></th>
          <td>
            <select name="type">
              <option value="">
                &mdash; Tous les types
              </option>
              {{foreach from=$listHospis key=key_typeHospi item=curr_typeHospi}}
              <option value="{{$key_typeHospi}}" {{if $key_typeHospi==$type}}selected="selected"{{/if}}>
                {{tr}}CSejour.type.{{$key_typeHospi}}{{/tr}}
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        {{/if}}
        <tr>
          <td colspan="2" class="button"><button type="submit" class="search">Afficher</button></td>
        </tr>
      </table>
      </form>
      {{if $typeVue == 0}}
        {{include file="inc_vw_timeop_op.tpl"}}
      {{elseif $typeVue == 1}}
        {{include file="inc_vw_timeop_prepa.tpl"}}
      {{else}}
        {{include file="inc_vw_timehospi.tpl"}}
      {{/if}}
    </td>
  </tr>
</table>