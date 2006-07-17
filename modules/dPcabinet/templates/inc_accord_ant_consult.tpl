<div style="margin-top:6px; border-top-width:1px; border-top-style:solid;" id="accordionConsult">

  <div id="Infos">
    <div id="InfosHeader" class="accordionTabTitleBar" style="background-color:#f00;">
      Informations sur le patient
    </div>
    <div id="InfosContent"  class="accordionTabContentBox">
      {{include file="inc_patient_infos_accord_consult.tpl"}}
    </div>
  </div>
  
  <div id="AntTrait">
    <div id="AntTraitHeader" class="accordionTabTitleBar">
      Ant�c�dents / Traitements
    </div>
    <div id="AntTraitContent"  class="accordionTabContentBox">
      {{include file="inc_ant_consult.tpl"}}
    </div>
  </div>

  <div id="Examens">
    <div id="ExamensHeader" class="accordionTabTitleBar">
      Examens
    </div>
    <div id="ExamensContent"  class="accordionTabContentBox">
      <div id="mainConsult">
      {{include file="inc_main_consultform.tpl"}}
      </div>
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

<script language="Javascript" type="text/javascript">
new Rico.Accordion( $('accordionConsult'), {panelHeight:310} );
</script>
