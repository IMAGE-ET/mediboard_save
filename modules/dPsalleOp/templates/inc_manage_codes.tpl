<script type="text/javascript">

changeCodeToDel = function(subject_id, code_ccam, actes_ids){
  var oForm = getForm("manageCodes");
  $V(oForm._selCode, code_ccam);
  $V(oForm._actes, actes_ids);
  ActesCCAM.remove(subject_id);
}

</script>

<!-- Pas d'affichage de inc_manage_codes si la consultation est deja valid�e -->
  
  <table class="main layout">
    <tr>
      <td class="halfPane">
        <form name="manageCodes" action="?m={{$module}}" method="post">
        <input type="hidden" name="m" value="{{$subject->_ref_module->mod_name}}" />
        <input type="hidden" name="dosql" value="{{$do_subject_aed}}" />
        <input type="hidden" name="{{$subject->_spec->key}}" value="{{$subject->_id}}" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="codes_ccam" value="{{$subject->codes_ccam}}" />
        <input type="submit" disabled="disabled" style="display:none;"/>
        <input type="hidden" name="_chir" value="{{$subject->_praticien_id}}" />
        {{if ($subject->_class_name=="COperation")}}
        <input type="hidden" name="_anesth" value="{{$subject->_ref_plageop->anesth_id}}" />
        {{/if}}
        <input type="hidden" name="_class_name" value="{{$subject->_class_name}}" />
        <fieldset>
          <legend>Ajouter un code</legend>
          <input name="_actes" type="hidden" value="" />
          <input name="_selCode" type="hidden" value="" />
          <button class="search" type="button" onclick="CCAMSelector.init()">
            {{tr}}Search{{/tr}}
          </button>
       
          <input type="text" size="10" name="_codes_ccam" />
          
          <script type="text/javascript">   
            CCAMSelector.init = function(){
              this.sForm = "manageCodes";
              this.sClass = "_class_name";
              this.sChir = "_chir";
              {{if ($subject->_class_name=="COperation")}}
              this.sAnesth = "_anesth";
              {{/if}}
              this.sView = "_codes_ccam";
            this.pop();
            }

            var oForm = getForm("manageCodes");
            Main.add(function() {
              var url = new Url("dPccam", "httpreq_do_ccam_autocomplete");
              url.autoComplete(oForm._codes_ccam, '', {
                minChars: 1,
                dropdown: true,
                width: "250px",
                updateElement: function(selected) {
                  $V(oForm._codes_ccam, selected.down("strong").innerHTML);
                  ActesCCAM.add('{{$subject->_id}}','{{$subject->_praticien_id}}');
                }
              });
            });
          </script>
          
          <button class="add" name="addCode" type="button" onclick="ActesCCAM.add('{{$subject->_id}}','{{$subject->_praticien_id}}')">
            {{tr}}Add{{/tr}}
          </button>
        </fieldset>
        </form>
      </td>
      {{if !$subject instanceof CConsultation}}
      <td class="halfPane">
        <fieldset>
          <legend>Validation du codage</legend>
          {{if ($conf.dPsalleOp.CActeCCAM.envoi_actes_salle || $m == "dPpmsi") && ($subject instanceof COperation)}}
            <table class="main layout">
              <tr id="export_{{$subject->_class_name}}_{{$subject->_id}}">
                {{if $m == "dPpmsi" || $can->admin}}
                  {{mb_include template="inc_export_actes_pmsi" object=$subject confirmCloture=1}}
                {{else}}
                  <td>
                    <div class="small-warning">Export PMSI impossible</div>
                  </td>
                {{/if}}
              </tr>
            </table>
          {{/if}}
          {{if ($module == "dPsalleOp" || $module == "dPhospi") && $conf.dPsalleOp.CActeCCAM.signature}}
            <button class="tick" onclick="signerActes('{{$subject->_id}}', '{{$subject->_class_name}}')">
              Signer les actes
            </button>
          {{/if}}
        </fieldset>
      </td>
      {{/if}}
    </tr>
  </table>


{{if $ajax}}
<script type="text/javascript">

oCodesManagerForm = document.manageCodes;
prepareForm(oCodesManagerForm);

</script>
{{/if}}