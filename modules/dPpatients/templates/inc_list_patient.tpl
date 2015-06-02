{{assign var=modFSE value="fse"|module_active}}

{{if $app->user_prefs.LogicielLectureVitale == 'vitaleVision'}}
  {{mb_include module="patients" template="inc_vitalevision" debug=false keepFiles=true}}
{{elseif $app->user_prefs.LogicielLectureVitale == 'none' && $modFSE && $modFSE->canRead()}}
   <script>
     var urlFSE = new Url;
     urlFSE.setModuleTab("patients", "vw_idx_patients");
     urlFSE.addParam("useVitale", 1);
   </script>
{{/if}}

<script>
  onMergeComplete = function() {
    location.reload();
  };

  window.checkedMerge = [];
  checkOnlyTwoSelected = function(checkbox) {
    checkedMerge = checkedMerge.without(checkbox);

    if (checkbox.checked)
      checkedMerge.push(checkbox);

    if (checkedMerge.length > 2)
      checkedMerge.shift().checked = false;
  };

  reloadPatient = function(patient_id, link, vw_cancelled){
      var url = new Url('patients', 'httpreq_vw_patient');
      url.addParam('patient_id', patient_id);
      url.addParam("vw_cancelled", vw_cancelled);
      url.requestUpdate('vwPatient', { onComplete: markAsSelected.curry(link) } );
  };

  toggleSearch = function() {
    $$(".field_advanced").invoke("toggle");
    $$(".field_basic").invoke("toggle");
  };

  emptyForm = function() {
    var form = getForm("find");
    $V(form.Date_Day, '');
    $V(form.Date_Month, '');
    $V(form.Date_Year, '');
    $V(form.prat_id, '');
    form.select("input[type=text]").each(function(elt) {
      $V(elt, '');
    });
    form.nom.focus();
  };

  checkEnoughTraits = function() {
    var form = getForm("find");

    return $V(form.nom).length >=2 ||
      $V(form.prenom).length >=2 ||
      $V(form.cp).length >=2 ||
      $V(form.ville).length >=2 ||
      $V(form.Date_Year) ||
      ($V(form.Date_Day) && $V(form.Date_Month) && $V(form.Date_Year));
  };

  togglePraticien = function(){
    var praticien = getForm("find").prat_id;
    var praticien_message = $("prat_id_message");
    var enough = checkEnoughTraits();

    praticien.setVisible(enough);
    praticien_message.setVisible(!enough);

    if (!enough) {
      $V(praticien, '');
    }
  };

  Main.add(function(){
    togglePraticien();

    var form = getForm("find");

    [
      form.nom,
      form.prenom,
      form.cp,
      form.ville,
      form.Date_Day,
      form.Date_Month,
      form.Date_Year
    ].each(function(select){
      select.observe("change", togglePraticien);
    });

    {{if $cp || $ville || ($conf.dPpatients.CPatient.tag_ipp && $patient_ipp) || $prat_id || $sexe || ($conf.dPplanningOp.CSejour.tag_dossier && $patient_nda) }}
      toggleSearch();
    {{/if}}
  });
</script>

<div id="modal-beneficiaire" style="display:none; text-align:center;">
  <p id="msg-multiple-benef">
    Cette carte vitale semble contenir plusieurs b�n�ficiaires, merci de s�lectionner la personne voulue :
  </p>
  <p id="msg-confirm-benef" style="display: none;"></p>
  <p id="benef-nom">
    <select id="modal-beneficiaire-select"></select>
    <span></span>
  </p>
  <div>
    <button type="button" class="tick" onclick="VitaleVision.search(getForm('find'), $V($('modal-beneficiaire-select'))); VitaleVision.modalWindow.close();">{{tr}}Choose{{/tr}}</button>
    <button type="button" class="cancel" onclick="VitaleVision.modalWindow.close();">{{tr}}Cancel{{/tr}}</button>
  </div>
</div>

<!-- formulaire de recherche -->
{{mb_include module=dPpatients template=inc_form_search_patient}}


<div id="search_result_patient">
</div>