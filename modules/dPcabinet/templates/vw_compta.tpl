<!-- $Id$ -->

{{mb_include_script module="dPplanningOp" script="ccam_selector"}}

<script type="text/javascript">
function checkRapport(){
  var form = document.printFrm;
  
  if(!(checkForm(form))){
    return false;
  }

  var url = new Url();
  url.setModuleAction("dPcabinet", form.a.value);
  url.addElement(form._date_min);
  url.addElement(form.a);
  url.addElement(form._date_min);
  url.addElement(form._date_max);
  url.addElement(form.chir);
  url.addElement(form._etat_paiement);
  url.addElement(form.type_tarif);
  url.addElement(form._type_affichage);
  url.popup(700, 550, "Rapport");
  
  return false;
}


var oCcamField = null;

function pageMain() {

  refreshListCCAM();

  // Creation du tokenField
  oCcamField = new TokenField(document.editFrm.codes_ccam, {   
    onChange : updateTokenCcam,
    sProps : "notNull code ccam"
  } );
  
  regFieldCalendar("printFrm", "_date_min");
  regFieldCalendar("printFrm", "_date_max");

  function refreshListCCAM() {
    oCcamNode = document.getElementById("listCodesCcam");
    
    var oForm = document.editFrm;
    var aCcam = oForm.codes_ccam.value.split("|");
    // Si la chaine est vide, il cr�e un tableau � un �l�ment vide donc :
    aCcam.removeByValue("");
  
    var aCodeNodes = new Array();
    var iCode = 0;
    while (sCode = aCcam[iCode++]) {
      var sCodeNode = sCode;
        sCodeNode += "<button class='cancel notext' type='button' onclick='oCcamField.remove(\"" + sCode + "\")'>";
        sCodeNode += "<\/button>";
      aCodeNodes.push(sCodeNode);
    }
    oCcamNode.innerHTML = aCodeNodes.join(" &mdash; ");
    //periodicalTimeUpdater.currentlyExecuting = false;
  }


  
  function updateTokenCcam(){
    refreshListCCAM();
    document.editFrm._codeCCAM.value="";
  }

  

}

</script>


<table class="main">
  <tr>
    <td class="halfPane">
      <form name="printFrm" action="./index.php" method="get" onSubmit="return checkRapport()">
      <input type="hidden" name="a" value="" />
      <input type="hidden" name="dialog" value="1" />
      <table class="form">
        <tr><th class="title" colspan="2">Edition de rapports</th></tr>
        <tr><th class="category" colspan="2">Choix de la periode</th></tr>
        <tr>
           <td>{{mb_label object=$filter field="_date_min"}}</td>
           <td class="date">{{mb_field object=$filter field="_date_min" form="printFrm" canNull="false"}} </td>
        </tr>
        <tr>
          <td>{{mb_label object=$filter field="_date_max"}}</td>
           <td class="date">{{mb_field object=$filter field="_date_max" form="printFrm" canNull="false"}} </td>
        </tr>
        <tr>
          <th class="category" colspan="2">Crit�res d'affichage</th>
        </tr>
        <tr>
          <td>{{mb_label object=$filter field="_prat_id"}}</td>
          <td>
            <select name="chir">
              <!-- <option value="">&mdash; Tous &mdash;</option> -->
              {{foreach from=$listPrat item=curr_prat}}
              <option class="mediuser" style="border-color: #{{$curr_prat->_ref_function->color}};" value="{{$curr_prat->user_id}}">{{$curr_prat->_view}}</option>
              {{/foreach}}
            </select>
          </td>
        <tr>
          <td>{{mb_label object=$filter field="_etat_paiement"}}</td>
          <td>{{mb_field object=$filter field="_etat_paiement" defaultOption="&mdash; Tous  &mdash;" canNull="true"}}</td>          
        </tr>
        <tr>
          <td>{{mb_label object=$filter field="type_tarif"}}</td>
          <td>{{mb_field object=$filter field="type_tarif" defaultOption="&mdash; Tout type &mdash;" canNull="true"}}</td>    
        </tr>
        <tr>
          <td>{{mb_label object=$filter field="_type_affichage"}}</td>
          <td>{{mb_field object=$filter field="_type_affichage" canNull="true"}}</td>     
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button class="search" type="submit" onclick="document.printFrm.a.value='print_rapport';">Validation paiements</button>
            <button class="print" type="submit" onclick="document.printFrm.a.value='print_compta';">Impression compta</button>
          </td>
        </tr>
      </table>
      </form>
    </td>
    <td class="halfPane">
      <table align="center">
      {{if $tarif->tarif_id}}
        <tr>
          <td colspan="3">
            <a class="buttonnew" href="index.php?m={{$m}}&amp;tarif_id=null">Cr�er un nouveau tarif</a>
          </td>
        </tr>
      {{/if}}
        <tr>
          <td>
            <table class="tbl">
              <tr>
                <th colspan="3">Tarifs du praticien</th>
              </tr>
              <tr>   
                {{if $is_praticien=="0"}}
                <td class="text">
                <div class="big-info">
                N'�tant pas praticien, vous n'avez pas acc�s � la liste de tarifs personnels.
                </div>
                </td>
                {{/if}}
              </tr>
              {{if $is_praticien=="1"}}
              <tr>
                <th>Nom</th>
                <th>Secteur 1</th>
                <th>Secteur 2</th>
              </tr>
              {{foreach from=$listeTarifsChir item=curr_tarif}}
              <tr>
                <td>
                  <a href="index.php?m={{$m}}&amp;tarif_id={{$curr_tarif->tarif_id}}">{{$curr_tarif->description}}</a>
                </td>
                <td>
                  <a href="index.php?m={{$m}}&amp;tarif_id={{$curr_tarif->tarif_id}}">{{$curr_tarif->secteur1}} �</a>
                </td>
                <td>
                  <a href="index.php?m={{$m}}&amp;tarif_id={{$curr_tarif->tarif_id}}">{{$curr_tarif->secteur2}} �</a>
                </td>
              </tr>
              {{/foreach}}
              {{/if}}
            </table>
          </td>
          
          <td>
            <table class="tbl">
              <tr><th colspan="3">Tarifs du cabinet</th></tr>
              <tr>
                <th>Nom</th>
                <th>Secteur 1</th>
                <th>Secteur 2</th>
              </tr>
              {{foreach from=$listeTarifsSpe item=curr_tarif}}
              <tr>
                <td>
                  <a href="index.php?m={{$m}}&amp;tarif_id={{$curr_tarif->tarif_id}}">{{$curr_tarif->description}}</a>
                </td>
                <td>
                  <a href="index.php?m={{$m}}&amp;tarif_id={{$curr_tarif->tarif_id}}">{{$curr_tarif->secteur1}} �</a>
                </td>
                <td>
                  <a href="index.php?m={{$m}}&amp;tarif_id={{$curr_tarif->tarif_id}}">{{$curr_tarif->secteur2}} �</a>
                </td>
              </tr>
              {{/foreach}}
            </table>
          </td>
          <td>
            <form name="editFrm" action="./index.php?m={{$m}}" method="post" onsubmit="return checkForm(this)">
            <input type="hidden" name="dosql" value="do_tarif_aed" />
            {{mb_field object=$tarif field="tarif_id" hidden=1 prop=""}}
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="chir_id" value="{{$mediuser->user_id}}" />
            {{mb_field object=$mediuser field="function_id" hidden=1 prop=""}}
            <table class="form">
              {{if $tarif->tarif_id}}
              <tr><th class="category" colspan="2">Editer ce tarif</th></tr>
              {{else}}
              <tr><th class="category" colspan="2">Cr�er un nouveau tarif</th></tr>
              {{/if}}
              <tr>
                <th>{{mb_label object=$tarif field="_type"}}</th>
                <td>
                  <select name="_type">
                    <option value="chir" {{if $tarif->chir_id}} selected="selected" {{/if}}>Tarif personnel</option>
                    <option value="function" {{if $tarif->function_id}} selected="selected" {{/if}}>Tarif de cabinet</option>
                  </select>
                </td>
              </tr>
              <tr>
                <th>{{mb_label object=$tarif field="description"}}</th>
                <td>
                  {{mb_field object=$tarif field="description"}}
                </td>
              </tr>
              <tr>
                <th>
                  {{mb_label object=$tarif field="codes_ccam" defaultFor="_codeCCAM"}}
                </th>
                <td>
                  <input type="text" name="_codeCCAM" size="10" value="" />
                  <button class="tick notext" type="button" onclick="oCcamField.add(this.form._codeCCAM.value,true)">{{tr}}Add{{/tr}}</button>
                  <input type="hidden" name="_codable_class" value="CConsultation" />
			      <button type="button" class="search notext" onclick="CCAMSelector.init()">{{tr}}button-CCodeCCAM-choix{{/tr}}</button>
			      <script type="text/javascript">
			        CCAMSelector.init = function(){
			        this.sForm  = "editFrm";
			        this.sView  = "_codeCCAM";
			        this.sChir  = "chir_id";
			        this.sClass = "_codable_class";
			        this.pop();
			      }
			      </script>             
                </td>
     
                <tr>
                  <th>
                    Liste des codes CCAM
                    {{mb_field object=$tarif field="codes_ccam" onchange="refreshListCCAM();" hidden=1 prop=""}}
                  </th>
                  <td colspan="2" class="text" id="listCodesCcam">
                  </td>
                </tr>
             
              <tr>
                <th>{{mb_label object=$tarif field="secteur1"}}</th>
                <td>{{mb_field object=$tarif field="secteur1" size="6"}}</td>
              </tr>
              <tr>
                <th>{{mb_label object=$tarif field="secteur2"}}</th>
                <td>{{mb_field object=$tarif field="secteur2" size="6"}}</td>
              </tr>
              <tr>
                <td class="button" colspan="2">
                  {{if $tarif->tarif_id}}
                  <button class="modify" type="submit">Modifier</button>
                  <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le tarif',objName:'{{$tarif->description|smarty:nodefaults|JSAttribute}}'})">
                    Supprimer
                  </button>
                  {{else}}
                  <button class="submit" type="submit" name="btnFuseAction">Cr�er</button>
                  {{/if}}
                </td>
              </tr>
            </table>
            </form>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>