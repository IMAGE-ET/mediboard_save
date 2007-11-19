{{assign var="chir_id" value=$consult->_ref_plageconsult->_ref_chir->_id}}
{{assign var="object" value=$consult}}
{{assign var="module" value="dPcabinet"}}
{{assign var="do_subject_aed" value="do_consultation_aed"}}
{{include file="../../dPsalleOp/templates/js_gestion_ccam.tpl"}}

<!-- div global de l'accordeon ==> accordionConsult -->
<div class="accordionMain" id="accordionConsult">
    
  <div id="AntTrait">
    <div id="AntTraitHeader" class="accordionTabTitleBar">
      Ant�c�dents / Traitements
    </div>
    <div id="AntTraitContent"  class="accordionTabContentBox">
      {{include file="inc_ant_consult.tpl"}}
    </div>
  </div>
  
  <div id="Exams">
    <div id="ExamsHeader" class="accordionTabTitleBar">
      Examens Clinique
    </div>
    <div id="ExamsContent"  class="accordionTabContentBox">
      {{include file="inc_consult_anesth/acc_examens_clinique.tpl"}}
    </div>
  </div>
  
  {{if $app->user_prefs.ccam == 1 }}
  <div id="Actes">
    <div id="ActesHeader" class="accordionTabTitleBar">
      Gestion des actes
    </div>
    <div id="ActesContent"  class="accordionTabContentBox">
      <table class="tbl"> 
        <tr>
        <td colspan="2">
            <ul id="main_tab_group" class="control_tabs">
              <li><a href="#one">Actes CCAM</a></li>
              <li><a href="#two">Actes NGAP</a></li>
            </ul>
          </td>
        </tr>
        
        <tr id="one">
          <th>Actes<br /><br />
            {{tr}}{{$consult->_class_name}}{{/tr}}
            {{if ($module=="dPplanningOp") || ($module=="dPsalleOp")}}
            <br />
            C�t� {{tr}}COperation.cote.{{$consult->cote}}{{/tr}}
            <br />
            ({{$consult->temp_operation|date_format:"%Hh%M"}})
            {{/if}}
          </th>
          <td>    
            <div id="ccam">
              {{assign var="module" value="dPcabinet"}}
              {{assign var="subject" value=$consult}}
              {{include file="../../dPsalleOp/templates/inc_gestion_ccam.tpl"}}
            </div>
          </td>
        </tr>
        
        <tr id="two">
          <th>Actes NGAP</th>
          <td>           
            {{include file="inc_acte_ngap.tpl"}}
          </td>
        </tr>
    	</table>
      <script type="text/javascript">new Control.Tabs('main_tab_group');</script>
    </div>
  </div>
  {{/if}}
   
  <div id="ExamsComp">
    <div id="ExamsCompHeader" class="accordionTabTitleBar">
      Examens Compl�mentaires
    </div>
    <div id="ExamsCompContent"  class="accordionTabContentBox">
      {{include file="inc_consult_anesth/acc_examens_complementaire.tpl"}}
    </div>
  </div>
 
  <div id="InfoAnesth">
    <div id="InfoAnesthHeader" class="accordionTabTitleBar">
      Informations Anesth�sie
    </div>
    <div id="InfoAnesthContent"  class="accordionTabContentBox">
      {{include file="inc_consult_anesth/acc_infos_anesth.tpl"}}      
    </div>
  </div>
  
  <div id="fdrConsult">
    <div id="fdrConsultHeader" class="accordionTabTitleBar">
      Documents et R�glements
    </div>
    <div id="fdrConsultContent"  class="accordionTabContentBox">
    {{include file="inc_fdr_consult.tpl"}}
    </div>
  </div>
  
</div>

