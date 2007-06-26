<!-- $Id$ -->

{{mb_include_script module="dPpatients" script="autocomplete"}}


<script type="text/javascript">

function setClose(iId, sNom, sPrenom, sType) {
  window.opener.setMed(iId, sNom, sPrenom, sType);
  window.close();
}

function pageMain() {
  initInseeFields("editFrm", "cp", "ville","_tel1");
}
</script>

<table class="main">
  <tr>
    <td class="greedyPane">
    
      <form name="find" action="./index.php" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      {{if $dialog}}
      <input type="hidden" name="a" value="vw_medecins" />
      <input type="hidden" name="dialog" value="1" />
      {{else}}
      <input type="hidden" name="tab" value="{{$tab}}" />
      {{/if}}
      <input type="hidden" name="new" value="1" />
      
      <table class="form">
        <tr>
          <th class="category" colspan="2">Recherche d'un m�decin</th>
        </tr>
  
        <tr>
          <th><label for="medecin_nom" title="Nom complet ou partiel du m�decin recherch�">Nom</label></th>
          <td><input tabindex="1" type="text" name="medecin_nom" value="{{$nom|stripslashes}}" /></td>
        </tr>
        
        <tr>
          <th><label for="medecin_prenom" title="Pr�nom complet ou partiel du m�decin recherch�">Pr�nom</label></th>
          <td><input tabindex="2" type="text" name="medecin_prenom" value="{{$prenom|stripslashes}}" /></td>
        </tr>
        
        <tr>
          <th><label for="medecin_dept" title="D�partement du m�decin recherch�">D�partement (00 pour tous)</label></th>
          <td><input tabindex="3" type="text" name="medecin_dept" value="{{$departement|stripslashes}}" /></td>
        </tr>
        
        <tr>
          <td class="button" colspan="2"><button class="search" type="submit">Rechercher</button></td>
        </tr>
      </table>

      </form>

      {{if !$dialog}}
      <form name="fusion" action="index.php" method="get">
      <input type="hidden" name="m" value="dPpatients" />
      <input type="hidden" name="a" value="fusion_medecin" />
      {{/if}}
      
      <table class="tbl">
        <tr>
          {{if !$dialog}}
          <th><button type="submit" class="search">Fusion</button></th>
          {{/if}}
          <th>Nom - Pr�nom</th>
          <th>Adresse</th>
          <th>Ville</th>
          <th>CP</th>
          <th>Telephone</th>
          <th>Fax</th>
          {{if $dialog}}
          <th>S�lectionner</th>
          {{/if}}
        </tr>

        {{foreach from=$medecins item=curr_medecin}}
        {{assign var="medecin_id" value=$curr_medecin->medecin_id}}
        <tr>
          {{if $dialog}}
            {{assign var="href" value="#"}}
          {{else}}
            {{assign var="href" value="?m=$m&tab=$tab&medecin_id=$medecin_id"}}
            <td><input type="checkbox" name="fusion_{{$medecin_id}}" /></td>
          {{/if}}
          <td class="text">
            <a href="{{$href}}" {{if $dialog}}onclick="setClose({{$medecin_id}}, '{{$curr_medecin->nom|smarty:nodefaults|JSAttribute}}', '{{$curr_medecin->prenom|smarty:nodefaults|JSAttribute}}', '{{$type|smarty:nodefaults|JSAttribute}}')"{{/if}}>
              {{$curr_medecin->_view}}
            </a>
          </td>
          <td class="text">
            <a href="{{$href}}" {{if $dialog}}onclick="setClose({{$medecin_id}}, '{{$curr_medecin->nom|smarty:nodefaults|JSAttribute}}', '{{$curr_medecin->prenom|smarty:nodefaults|JSAttribute}}', '{{$type|smarty:nodefaults|JSAttribute}}')"{{/if}}>
              {{$curr_medecin->adresse}}
            </a>
          </td>
          <td class="text">
            <a href="{{$href}}" {{if $dialog}}onclick="setClose({{$medecin_id}}, '{{$curr_medecin->nom|smarty:nodefaults|JSAttribute}}', '{{$curr_medecin->prenom|smarty:nodefaults|JSAttribute}}', '{{$type|smarty:nodefaults|JSAttribute}}')"{{/if}}>
              {{$curr_medecin->ville}}
            </a>
          </td>
          <td>
            <a href="{{$href}}" {{if $dialog}}onclick="setClose({{$medecin_id}}, '{{$curr_medecin->nom|smarty:nodefaults|JSAttribute}}', '{{$curr_medecin->prenom|smarty:nodefaults|JSAttribute}}', '{{$type|smarty:nodefaults|JSAttribute}}')"{{/if}}>
              {{$curr_medecin->cp}}
            </a>
          </td>
          <td>
            <a href="{{$href}}" {{if $dialog}}onclick="setClose({{$medecin_id}}, '{{$curr_medecin->nom|smarty:nodefaults|JSAttribute}}', '{{$curr_medecin->prenom|smarty:nodefaults|JSAttribute}}', '{{$type|smarty:nodefaults|JSAttribute}}')"{{/if}}>
              {{$curr_medecin->tel}}
            </a>
          </td>
          <td>
            <a href="{{$href}}" {{if $dialog}}onclick="setClose({{$medecin_id}}, '{{$curr_medecin->nom|smarty:nodefaults|JSAttribute}}', '{{$curr_medecin->prenom|smarty:nodefaults|JSAttribute}}', '{{$type|smarty:nodefaults|JSAttribute}}')"{{/if}}>
              {{$curr_medecin->fax}}
            </a>
          </td>
          {{if $dialog}}
            <td>
              <button type="button" class="tick" onclick="setClose({{$medecin_id}}, '{{$curr_medecin->nom|smarty:nodefaults|JSAttribute}}', '{{$curr_medecin->prenom|smarty:nodefaults|JSAttribute}}', '{{$type|smarty:nodefaults|JSAttribute}}')">Selectionner</button>
            </td>
          {{/if}}
        </tr>
        {{/foreach}}
        
      </table>

      {{if !$dialog}}
      </form>
      {{/if}}

    </td>
    
    {{if !$dialog}}
    <td class="pane">
      <form name="editFrm" action="index.php?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_medecins_aed" />
      {{mb_field object=$medecin field="medecin_id" hidden=1 prop=""}}
      <input type="hidden" name="del" value="0" />
      <table class="form">
        {{if !$dialog && $medecin->medecin_id}}
        <tr>
          <td colspan="2"><a class="buttonnew" href="index.php?m={{$m}}&amp;tab={{$tab}}&amp;new=1">Cr�er un nouveau m�decin</a></td>
        </tr>
        {{/if}}
        <tr>
          <th class="category" colspan="2">
            {{if $medecin->medecin_id}}
	         <a style="float:right;" href="#" onclick="view_log('CMedecin',{{$medecin->medecin_id}})">
               <img src="images/icons/history.gif" alt="historique" />
              </a>
              Modification du Dr. {{$medecin->_view}}
            {{else}}
              Cr�ation d'une fiche
            {{/if}}
          </th>
        </tr>

        <tr>
          <th>{{mb_label object=$medecin field="nom"}}</th>
          <td>{{mb_field object=$medecin field="nom"}}</td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$medecin field="prenom"}}</th>
          <td>{{mb_field object=$medecin field="prenom"}}</td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$medecin field="adresse"}}</th>
          <td>{{mb_field object=$medecin field="adresse"}}</td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$medecin field="cp"}}</th>
          <td>
            {{mb_field object=$medecin field="cp" size="31" maxlength="5"}}
            <div style="display:none;" class="autocomplete" id="cp_auto_complete"></div>
          </td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$medecin field="ville"}}</th>
          <td>
            {{mb_field object=$medecin field="ville" size="31"}}
            <div style="display:none;" class="autocomplete" id="ville_auto_complete"></div>
          </td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$medecin field="tel" defaultFor="_tel1"}}</th>
          <td>
            {{mb_field object=$medecin field="_tel1" size="2" maxlength="2" prop="num length|2"}} -
            {{mb_field object=$medecin field="_tel2" size="2" maxlength="2" prop="num length|2"}} -
            {{mb_field object=$medecin field="_tel3" size="2" maxlength="2" prop="num length|2"}} -
            {{mb_field object=$medecin field="_tel4" size="2" maxlength="2" prop="num length|2"}} -
            {{mb_field object=$medecin field="_tel5" size="2" maxlength="2" prop="num length|2"}}
          </td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$medecin field="fax" defaultFor="_fax1"}}</th>
          <td>
            {{mb_field object=$medecin field="_fax1" size="2" maxlength="2" prop="num length|2"}} -
            {{mb_field object=$medecin field="_fax2" size="2" maxlength="2" prop="num length|2"}} -
            {{mb_field object=$medecin field="_fax3" size="2" maxlength="2" prop="num length|2"}} -
            {{mb_field object=$medecin field="_fax4" size="2" maxlength="2" prop="num length|2"}} -
            {{mb_field object=$medecin field="_fax5" size="2" maxlength="2" prop="num length|2"}}
          </td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$medecin field="email"}}</th>
          <td>{{mb_field object=$medecin field="email"}}</td>
        </tr>

        <tr>
          <th>{{mb_label object=$medecin field="disciplines"}}</th>
          <td>{{mb_field object=$medecin field="disciplines"}}</td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$medecin field="orientations"}}</th>
          <td>{{mb_field object=$medecin field="orientations"}}</td>
        </tr>

        <tr>
          <th>{{mb_label object=$medecin field="complementaires"}}</th>
          <td>{{mb_field object=$medecin field="complementaires"}}</td>
        </tr>

        <tr>
          <td class="button" colspan="4">
            {{if $medecin->medecin_id}}
            <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
            <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'le m�decin',objName:'{{$medecin->_view|smarty:nodefaults|JSAttribute}}'})">
              {{tr}}Delete{{/tr}}
            </button>
            {{else}}
            <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
            {{/if}}
          </td>
        </tr>
      </table>
      </form>
    </td>
    {{/if}}
  </tr>
</table>
      