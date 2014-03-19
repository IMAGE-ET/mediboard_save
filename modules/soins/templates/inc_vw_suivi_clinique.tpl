{{assign var=patient value=$sejour->_ref_patient}}
{{mb_script module=patients script=correspondant ajax=true}}
{{mb_script module=system script=alert ajax=true}}

<script>
  modalViewComplete = function(object_guid, title) {
    var url = new Url("system", "httpreq_vw_complete_object");
    url.addParam("object_guid", object_guid);
    url.requestModal(800, 500, { title: title });
  }

  popEtatSejour = function(sejour_id) {
    var url = new Url("hospi", "vw_parcours");
    url.addParam("sejour_id", '{{$sejour->_id}}');
    url.requestModal(700, 550);
  }

  toggleProgressBefore = function() {
    $$(".in_progress_before").invoke("toggleClassName", "show_important");
    $$(".class_progress_before").invoke("toggleClassName", "selected");
  }
  toggleProgressAfter = function() {
    $$(".in_progress_after").invoke("toggleClassName", "show_important");
    $$(".class_progress_after").invoke("toggleClassName", "selected");
  }
  
  afterEditCorrespondant = function() {
    if (window.loadSuiviClinique) {
      loadSuiviClinique(document.form_prescription.sejour_id.value)
    }
    else if (window.reloadSynthese) {
      window.reloadSynthese();
    }
  }
  
  {{if "forms"|module_active}}
    Main.add(function(){
      ExObject.loadExObjects("{{$sejour->_class}}", "{{$sejour->_id}}", "list-ex_objects", 0.5);
    });
  {{/if}}
</script>

<table class="main" style="text-align: left; width: 100%">
  <tr>
    <th class="title" colspan="2">
    {{mb_include module=system template=inc_object_notes object=$patient}}
      <a href="?m=dPpatients&tab=vw_full_patients&patient_id={{$patient->_id}}">
        <span style="float: left;">
          {{mb_include module="patients" template=inc_vw_photo_identite mode="read" size=32}}
        </span>
      </a>
      
      {{mb_include module=system template=inc_object_idsante400 object=$patient}}
      {{mb_include module=system template=inc_object_history object=$patient}}
      
      <a style="float:right;" href="#print-{{$patient->_guid}}" onclick="Patient.print('{{$patient->_id}}')">
        <img src="images/icons/print.png" alt="imprimer" title="Imprimer la fiche patient" />
      </a>
      
      {{if $can->edit}}
      <a style="float:right;" href="#edit-{{$patient->_guid}}" onclick="Patient.edit('{{$patient->_id}}')">
        <img src="images/icons/edit.png" alt="modifier" title="Modifier le patient" />
      </a>
      {{/if}}
      
      {{if $app->user_prefs.vCardExport}}
      <a style="float:right;" href="#export-{{$patient->_guid}}" onclick="Patient.exportVcard('{{$patient->_id}}')">
        <img src="images/icons/vcard.png" alt="export" title="Exporter le patient" />
      </a>
      {{/if}}

      {{if $patient->date_lecture_vitale}}
        <div style="float: right;">
          <img src="images/icons/carte_vitale.png" title="{{tr}}CPatient-date-lecture-vitale{{/tr}} : {{mb_value object=$patient field="date_lecture_vitale" format=relative}}" />
        </div>
      {{/if}}
      
      <form name="actionPat" action="?" method="get">
        <input type="hidden" name="m" value="patients" />
        <input type="hidden" name="tab" value="vw_idx_patients" />
        <input type="hidden" name="patient_id" value="{{$patient->_id}}" />
        <h2 style="color: #fff; font-weight: bold;">
          <span style="font-size: 0.7em;" onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">
            {{$patient->_view}}
          </span>
          -
          <span style="font-size: 0.7em;" onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}')">
            {{$sejour->_shortview|replace:"Du":"S�jour du"}} <br /> {{$sejour->_ref_curr_affectation->_ref_lit}}
          </span>
          {{assign var=dossier_medical value=$patient->_ref_dossier_medical}}
          {{assign var=antecedents value=$dossier_medical->_ref_antecedents_by_type}}
          {{assign var=sejour_id value=$sejour->_id}}
          {{include file="../../soins/templates/inc_vw_antecedent_allergie.tpl" nodebug=true}}

          {{if $dossier_medical->_id && $dossier_medical->_count_allergies}}
            <script type="text/javascript">
              ObjectTooltip.modes.allergies = {  
                module: "patients",
                action: "ajax_vw_allergies",
                sClass: "tooltip"
              };
            </script> 
            <img src="images/icons/warning.png" onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}', 'allergies');" />
          {{/if}}

          {{assign var=prescription value=$sejour->_ref_prescription_sejour}}
          {{if $prescription->_jour_op}}
            <br/>
            {{foreach from=$prescription->_jour_op item=_info_jour_op}}
              (<span onmouseover="ObjectTooltip.createEx(this, '{{$_info_jour_op.operation_guid}}');">J{{$_info_jour_op.jour_op}}</span>)
            {{/foreach}}
          {{/if}}
        </h2>
      </form>
    </th>
  </tr>
  <tr>
    <td colspan="2" style="text-align: center;">
      <table class="form">
        {{mb_include module=soins template=inc_infos_patients_soins add_class_poids=1 add_class_taille=1}}
      </table>
    </td>
  </tr>
  <tr>
    <!-- Informations sur le patient -->
    <td style="width: 50%; vertical-align: top">
       <table class="tbl" style="table-layout: fixed;">
        <tr>
          <th colspan="2" class="category">
            <span style="float: right">
              <button type="button" class="search" onclick="modalViewComplete('{{$patient->_guid}}', 'D�tail du patient')">Patient</button>
            </span>
            <span style="float: left;">
              <button class="lookup notext" style="margin: 0;" onclick="popEtatSejour();">Etat du s�jour</button>
            </span>
            Coordonn�es
          </th>
        </tr>
        <tr>
          <td colspan="2">
            {{if $patient->nom_jeune_fille}}
              <div class="cellule_patient">
                <strong>{{mb_label object=$patient field="nom_jeune_fille"}}</strong>
                {{mb_value object=$patient field="nom_jeune_fille"}}
              </div>
            {{/if}}
            {{if $patient->tel}}
              <div class="cellule_patient">
                <strong>{{mb_label object=$patient field="tel"}}</strong>
                {{mb_value object=$patient field="tel"}}
              </div>
            {{/if}}
            {{if $patient->tel2}}
              <div class="cellule_patient">
                <strong>{{mb_label object=$patient field="tel2"}}</strong>
                {{mb_value object=$patient field="tel2"}}
              </div>
            {{/if}}
            {{if $patient->tel_autre}}
              <div class="cellule_patient">
                <strong>{{mb_label object=$patient field="tel_autre"}}</strong>
                {{mb_value object=$patient field="tel_autre"}}
              </div>
            {{/if}}
            {{if $patient->email}}
              <div class="cellule_patient">
                <strong>{{mb_label object=$patient field="email"}}</strong>
                {{mb_value object=$patient field="email"}}
              </div>
            {{/if}}
            {{if $patient->profession}}
              <div class="cellule_patient">
                <strong>{{mb_label object=$patient field="profession"}}</strong>
                {{mb_value object=$patient field="profession"}}
              </div>
            {{/if}}
            {{if $patient->rques}}
              <div class="cellule_patient">
                <strong>{{mb_label object=$patient field="rques"}}</strong>
                {{mb_value object=$patient field="rques"}}
              </div>
            {{/if}}
          </td>
        </tr>
      </table>
      
      <!-- Correspondance -->
      <table class="tbl">
        <tr>
          <th style="width: 1%;">
            <button type="button" class="add notext" style="float: left;"
              onclick="Correspondant.edit(0, '{{$patient->_id}}', afterEditCorrespondant);"></button>
          </th>
          <th class="category">
            {{tr}}CCorrespondantPatient-nom{{/tr}} / {{tr}}CCorrespondantPatient-prenom{{/tr}}
          </th>
          <th class="category">
            {{tr}}CCorrespondantPatient-mob{{/tr}}
          </th>
          <th class="category">
            {{tr}}CCorrespondantPatient-tel{{/tr}}
          </th>
        </tr>
        {{foreach from=$patient->_ref_cp_by_relation item=_correspondants}}
          {{foreach from=$_correspondants item=_correspondant}}
            <tr>
              <td>
                <strong>{{mb_value object=$_correspondant field=relation}}</strong>
              </td>
              <td>
                <span onmouseover="ObjectTooltip.createEx(this, '{{$_correspondant->_guid}}')">
                  {{mb_value object=$_correspondant field="nom"}}
                  {{mb_value object=$_correspondant field="prenom"}}
                </span>
              </td>
              <td>
                {{mb_value object=$_correspondant field="mob"}}
              </td>
              <td>
                {{mb_value object=$_correspondant field="tel"}}
              </td>
            </tr>
          {{/foreach}}
        {{/foreach}}
        {{if !$patient->_ref_correspondants_patient|@count}}
          <tr>
            <td colspan="4" class="empty">
              {{tr}}CCorrespondantPatient.none{{/tr}}
            </td>
          </tr>
        {{/if}}
      </table>
      
      <!--  Informations sur le s�jour -->
      <form name="edit-sejour-frm" method="post" action="?"
        onsubmit="return onSubmitFormAjax(this, { onComplete: function() { loadSuiviClinique('{{$sejour->_id}}') } })">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="dosql" value="do_sejour_aed" />
        <input type="hidden" name="del" value="0" />
         {{mb_field object=$sejour field=entree_prevue hidden=true}}
        {{mb_key object=$sejour}}
        <table class="tbl">
          <tr>
            <th class="category" colspan="2">
              <span style="float: right">
                <button type="button" class="search" onclick="modalViewComplete('{{$sejour->_guid}}', 'D�tail du s�jour')">D�tail</button>
              </span>
              {{tr}}CSejour{{/tr}}
            </th>
          </tr>
          <tr>
            <td style="width: 50%;">
              <strong>{{mb_label object=$sejour field="libelle"}}</strong>
              {{mb_value object=$sejour field="libelle"}}
            </td>
            <td>
              <strong>{{mb_label object=$sejour field="praticien_id"}}</strong>
              {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$sejour->_ref_praticien}}
            </td>
          </tr>
          <tr>
            <td>
              <strong>{{mb_label object=$sejour field="entree"}}{{if $sejour->entree_reelle}} (effectu�e){{/if}}</strong>
              {{mb_value object=$sejour field="entree"}}
            </td>
            <td>
              <strong>{{mb_label object=$sejour field="sortie"}}{{if $sejour->sortie_reelle}} (effectu�e){{/if}}</strong>
              {{if $sejour->sortie_reelle || $sejour->confirme}}
                {{mb_value object=$sejour field="sortie"}}
              {{else}}
                {{mb_field object=$sejour field="sortie_prevue" register=true form="edit-sejour-frm" onchange="submitFormAjax(this.form);"}}
              {{/if}}
            </td>
          </tr>
          <tr>
            <td>
              <strong>{{mb_label object=$sejour field="type"}}</strong>
              {{mb_value object=$sejour field="type"}}
            </td>
            <td class="text {{if $sejour->confirme}}ok{{else}}warning{{/if}}">
              {{mb_field object=$sejour field=confirme typeEnum="checkbox" onchange="submitFormAjax(this.form);"}}
              {{mb_label object=$sejour field=confirme}}
              {{if $sejour->confirme}}
                par {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$user_confirm_sortie}}
              {{/if}}
            </td>
          </tr>
          <tr>
            <td colspan="2">
              <strong>{{mb_label object=$sejour field=rques text="CSejour-rques-court"}}</strong>
              <span id="CSejour-rques" class="text compact" style="white-space: normal;">{{$sejour->rques}}</span>
            </td>
          </tr>
        </table>
      </form>
      
      {{mb_include module=planningOp template=inc_infos_operation alert=1}}
      
      {{if $sejour->_ref_transmissions|@count}}
        <table class="tbl">
          <tr>
            <th class="title" colspan="3">Transmissions de synth�se</th>
          </tr>
          <tr>
            <th class="narrow">{{tr}}Date{{/tr}}</th>
            <th class="narrow">{{tr}}Hour{{/tr}}</th>
            <th>{{mb_title class=CTransmissionMedicale field=text}}</th>
          </tr>
          {{foreach from=$sejour->_ref_transmissions item=_transmissions key=_cat_name name=foreach_trans}}
            <tr>
              <th colspan="3" class="section">{{$_cat_name}}</th>
            </tr>
            {{foreach from=$_transmissions item=_trans}}
              <tr>
                <td style="text-align: center; height: 22px;">
                  {{mb_ditto name=date value=$_trans->date|date_format:$conf.date}}
                </td>
                <td style="text-align: center;">
                  {{$_trans->date|date_format:$conf.time}}
                </td>
                <td class="text {{if $_trans->type}}trans-{{$_trans->type}}{{/if}} libelle_trans">{{mb_value object=$_trans field=text}}</td>
              </tr>
            {{/foreach}}
          {{foreachelse}}
            <tr>
              <td colspan="3">{{tr}}CTransmissionMedicale.none{{/tr}}</td>
            </tr>
          {{/foreach}}
        </table>
      {{/if}}
      
      {{if $sejour->_ref_observations|@count}}
        <table class="tbl">
          <tr>
            <th colspan="7" class="title">Observations</th>
          </tr>
          <tr>
            <th class="narrow">{{tr}}Date{{/tr}}</th>
            <th class="narrow">{{tr}}Hour{{/tr}}</th>
            <th>{{mb_title class=CObservationMedicale field=text}}</th>
          </tr>
          {{foreach from=$sejour->_ref_observations item=_obs}}
            <tr>
              <td style="text-align: center; height: 22px;">
                {{mb_ditto name=date_obs value=$_obs->date|date_format:$conf.date}}
              </td>
              <td style="text-align: center;">
                {{$_obs->date|date_format:$conf.time}}
              </td>
              <td class="text libelle_trans">{{mb_value object=$_obs field=text}}</td>
            </tr>
          {{/foreach}}
        </table>
      {{/if}}
      
      {{if $sejour->_ref_tasks|@count}}
        {{mb_include module=soins template=inc_vw_tasks_sejour mode_realisation=0 readonly=1 header=0}}
      {{/if}}
      
    </td>
    
    
    <td style="vertical-align: top;" rowspan="2">
       
    {{if "dPprescription"|module_active}}
       {{assign var=prescription value=$sejour->_ref_prescription_sejour}}
        <table class="tbl">
          <tr>
            <th class="title">
              <button type="button" style="float: right;" class="search class_progress_after" onclick="toggleProgressAfter();" title="{{tr}}CPrescription.in_progress_after{{/tr}}">
                +{{$days_config}}J
              </button>
              <button type="button" style="float: right;" class="search class_progress_before" onclick="toggleProgressBefore();" title="{{tr}}CPrescription.in_progress_before{{/tr}}">
                -{{$days_config}}J
              </button>
              {{tr}}CPrescription.in_progress{{/tr}}
            </th>
            {{if $prescription->_ref_lines_med_comments.med|@count || $prescription->_ref_lines_med_comments.comment|@count ||
               $prescription->_ref_prescription_line_mixes|@count || $prescription->_ref_lines_elements_comments|@count}}
            {{if $prescription->_ref_lines_med_comments.med|@count}}
              <tr>
                <th>{{tr}}CPrescription._chapitres.med{{/tr}}</th>
              </tr>
              <!-- pass� -->
              <tr class="hatching in_progress_before opacity-60">
                <td class="text">
                  {{assign var=is_first value=1}}
                  {{assign var=total value=0}}
                  {{foreach from=$prescription->_ref_lines_med_comments.med item=_line}}
                    {{if $_line->_fin_reelle && $_line->_fin_reelle < $date && $_line->_fin_reelle >= $date_before}}
                      {{if !$is_first}}
                        &ndash;
                      {{/if}}
                      {{assign var=is_first value=0}}
                      {{math equation="x+1" x=$total assign=total}}
                      {{$_line->_ref_produit->libelle_abrege}}
                    {{/if}}
                  {{/foreach}}
                  {{if $total == 0}}
                    <div class="empty">Aucune ligne de m�dicament pass�e</div>
                  {{/if}}
                </td>
              </tr>
              <!-- Maintenant -->
              <tr>
                <td class="text">
                  {{assign var=is_first value=1}}
                  {{assign var=total value=0}}
                  {{foreach from=$prescription->_ref_lines_med_comments.med item=_line}}
                    {{if ($_line->_fin_reelle && $_line->_fin_reelle >= $date) &&
                       $_line->_debut_reel && $_line->_debut_reel <= $date}}
                      {{if !$is_first}}
                        &ndash;
                      {{/if}}
                      {{assign var=is_first value=0}}
                      {{math equation="x+1" x=$total assign=total}}
                      <span {{if $_line->_fin_reelle|iso_date <= $date_after|iso_date}}style="border-bottom: 2px solid orange"{{/if}}
                            onmouseover="ObjectTooltip.createEx(this, '{{$_line->_guid}}')">
                        {{$_line->_ref_produit->libelle_abrege}}
                        {{if $_line->_alerte_antibio}}
                          <label title="R��valuation antibioth�rapie" style="font-weight: bold; color: red;">(Reeval ATB)</label>
                        {{/if}}
                      </span>
                    {{/if}}
                  {{/foreach}}
                  {{if $total == 0}}
                    <div class="empty">Aucune ligne de m�dicament en cours</div>
                  {{/if}}
                </td>
              </tr>
              <!-- A venir -->
              <tr class="text in_progress_after opacity-60">
                <td>
                  {{assign var=is_first value=1}}
                  {{assign var=total value=0}}
                  {{foreach from=$prescription->_ref_lines_med_comments.med item=_line}}
                    {{if $_line->_debut_reel && $_line->_debut_reel > $date && $_line->_debut_reel <= $date_after}}
                      {{if !$is_first}}
                        &ndash;
                      {{/if}}
                      {{assign var=is_first value=0}}
                      {{math equation="x+1" x=$total assign=total}}
                      {{$_line->_ref_produit->libelle_abrege}}
                    {{/if}}
                  {{/foreach}}
                  {{if $total == 0}}
                    <div class="empty">Aucune ligne de m�dicament � venir</div>
                  {{/if}}
                </td>
              </tr>
            {{/if}}
            
            {{if $prescription->_ref_prescription_line_mixes_by_type|@count}}
              {{foreach from=$prescription->_ref_prescription_line_mixes_by_type item=_lines_by_type key=chap}}
                <tr>
                  <th>{{tr}}CPrescription._chapitres.{{$chap}}{{/tr}}</th>
                </tr>
                <tr class="hatching in_progress_before opacity-60">
                  <td class="text">
                    {{assign var=total value=0}}
                    {{assign var=is_first value=1}}
                    {{foreach from=$_lines_by_type item=_line}}
                      {{if $_line->_fin && $_line->_fin < $date && $_line->_fin >= $date_before}}
                        {{if !$is_first}}
                          &ndash;
                        {{/if}}
                        {{assign var=is_first value=0}}
                        {{math equation="x+1" x=$total assign=total}}
                        {{$_line->_libelle_voie}}
                        ({{$_line->_compact_view}})
                      {{/if}}
                    {{/foreach}}
                    {{if $total == 0}}
                      <div class="empty">Aucune ligne de {{tr}}CPrescription._chapitres.{{$chap}}{{/tr}} pass�e</div>
                    {{/if}}
                  </td>
                </tr>
                <tr>
                  <td class="text">
                    {{assign var=total value=0}}
                    {{assign var=is_first value=1}}
                    {{foreach from=$_lines_by_type item=_line}}
                      {{if ($_line->_fin && $_line->_fin >= $date) && ($_line->_debut && $_line->_debut <= $date)}}
                        {{if !$is_first}}
                          &ndash;
                        {{/if}}
                        {{assign var=is_first value=0}}
                        {{math equation="x+1" x=$total assign=total}}

                        <span {{if $_line->_fin|iso_date <= $date_after|iso_date}}style="border-bottom: 2px solid orange"{{/if}}
                              onmouseover="ObjectTooltip.createEx(this, '{{$_line->_guid}}')">
                          {{$_line->_libelle_voie}}
                          ({{$_line->_compact_view}})
                          {{if $_line->_alerte_antibio}}
                            <label title="R��valuation antibioth�rapie" style="font-weight: bold; color: red;">(Reeval ATB)</label>
                          {{/if}}
                        </span>
                      {{/if}}
                    {{/foreach}}
                    {{if $total == 0}}
                      <div class="empty">Aucune ligne de {{tr}}CPrescription._chapitres.{{$chap}}{{/tr}} en cours</div>
                    {{/if}}
                  </td>
                </tr>
                <tr class="in_progress_after opacity-60">
                  <td class="text">
                    {{assign var=total value=0}}
                    {{assign var=is_first value=1}}
                    {{foreach from=$_lines_by_type item=_line}}
                      {{if $_line->_debut && $_line->_debut > $date && $_line->_debut <= $date_after}}
                        {{if !$is_first}}
                          &ndash;
                        {{/if}}
                        {{assign var=is_first value=0}}
                        {{math equation="x+1" x=$total assign=total}}
                        {{$_line->_libelle_voie}}
                        ({{$_line->_compact_view}})
                      {{/if}}
                    {{/foreach}}
                    {{if $total == 0}}
                      <div class="empty">Aucune ligne de {{tr}}CPrescription._chapitres.{{$chap}}{{/tr}} � venir</div>
                    {{/if}}
                  </td>
                </tr>
              {{/foreach}}
            {{/if}}
            
            {{assign var=display_cat_for_elt value=$conf.dPprescription.CPrescription.display_cat_for_elt}}
            
            {{foreach from=$prescription->_ref_lines_elements_comments item=chap_element key=_chap_name}}
              <tr>
                <th>{{tr}}CPrescription._chapitres.{{$_chap_name}}{{/tr}}</th>
              </tr>
              
              <tr class="hatching in_progress_before opacity-60">
                <td class="text">
                  {{assign var=total value=0}}
                  {{assign var=is_first_chap value=1}}
                  {{assign var=count_elts value=0}}
                  {{foreach from=$chap_element item=cat_element}}
                    {{if !$is_first_chap && $count_elts && $cat_element.element|@count > 0}}
                      {{assign var=count_elts value=0}}
                      &ndash;
                    {{/if}}
                    {{assign var=is_first_chap value=0}}
                    {{assign var=is_first_cat value=1}}
                    {{foreach from=$cat_element.element item=element}}
                      {{if $element->_fin_reelle && $element->_fin_reelle < $date && $element->_fin_reelle >= $date_before}}
                        {{if !$is_first_cat}}
                          &ndash;
                        {{elseif $display_cat_for_elt}}
                          <strong>{{$element->_ref_element_prescription->_ref_category_prescription->nom}} : </strong>
                        {{/if}}
                        {{assign var=is_first_cat value=0}}
                        {{math equation="x+1" x=$count_elts assign=count_elts}}
                        {{math equation="x+1" x=$total assign=total}}
                        {{$element->_view}}
                      {{/if}}
                    {{/foreach}}
                  {{/foreach}}
                  {{if $total == 0}}
                    <div class="empty">Aucune ligne de {{tr}}CPrescription._chapitres.{{$_chap_name}}{{/tr}} pass�e</div>
                  {{/if}}
                </td>
              </tr>
             
              <tr>
                <td class="text">
                  {{assign var=total value=0}}
                  {{assign var=is_first_chap value=1}}
                  {{assign var=count_elts value=0}}
                  {{foreach from=$chap_element item=cat_element}}
                    {{if !$is_first_chap && $count_elts && $cat_element.element|@count > 0}}
                       {{assign var=count_elts value=0}}
                       &ndash;
                    {{/if}}
                    {{assign var=is_first_chap value=0}}
                    {{assign var=is_first_cat value=1}}
                    {{foreach from=$cat_element.element item=element}}
                      {{if ($element->_fin_reelle && $element->_fin_reelle >= $date) && ($element->_debut_reel && $element->_debut_reel <= $date)}}
                        {{if !$is_first_cat}}
                          &ndash;
                        {{elseif $display_cat_for_elt}}
                          <strong>{{$element->_ref_element_prescription->_ref_category_prescription->nom}} : </strong>
                        {{/if}}
                        {{assign var=is_first_cat value=0}}
                        {{math equation="x+1" x=$total assign=total}}
                        {{math equation="x+1" x=$count_elts assign=count_elts}}

                        <span {{if $element->_fin_reelle|iso_date <= $date_after|iso_date}}style="border-bottom: 2px solid orange"{{/if}}
                              onmouseover="ObjectTooltip.createEx(this, '{{$element->_guid}}')">
                          {{$element->_view}}
                        </span>
                      {{/if}}
                    {{/foreach}}
                  {{/foreach}}
                  {{if $total == 0}}
                    <div class="empty">Aucune ligne de {{tr}}CPrescription._chapitres.{{$_chap_name}}{{/tr}} en cours</div>
                  {{/if}}
                </td>
              </tr>
             
              <tr class="in_progress_after opacity-60">
                <td class="text">
                  {{assign var=total value=0}}
                  {{assign var=is_first_chap value=1}}
                  {{assign var=count_elts value=0}}
                  {{foreach from=$chap_element item=cat_element}}
                    {{if !$is_first_chap && $count_elts && $cat_element.element|@count > 0}}
                       {{assign var=count_elts value=0}}
                       &ndash;
                    {{/if}}
                    {{assign var=is_first_chap value=0}}
                    {{assign var=is_first_cat value=1}}
                    {{foreach from=$cat_element.element item=element}}
                      {{if $element->_debut_reel && $element->_debut_reel > $date && $element->_debut_reel <= $date_after}}
                        {{if !$is_first_cat}}
                          &ndash;
                        {{elseif $display_cat_for_elt}}
                          <strong>{{$element->_ref_element_prescription->_ref_category_prescription->nom}} : </strong>
                        {{/if}}
                        {{assign var=is_first_cat value=0}}
                        {{math equation="x+1" x=$total assign=total}}
                        {{math equation="x+1" x=$count_elts assign=count_elts}}
                        {{$element->_view}}
                      {{/if}}
                    {{/foreach}}
                  {{/foreach}}
                  {{if $total == 0}}
                    <div class="empty">Aucune ligne de {{tr}}CPrescription._chapitres.{{$_chap_name}}{{/tr}} � venir</div>
                  {{/if}}
                </td>
              </tr>
            {{/foreach}}
          {{else}}
          <tr>
            <td>
              Aucune ligne en cours
            </td>
          </tr>
          {{/if}}
        </table>
      {{/if}}
      {{if "forms"|module_active}}
        <table class="main tbl">
          <tr>
            <th>Formulaires</th>
          </tr>
        </table>
        <div id="list-ex_objects"></div>
      {{/if}}
    </td>
  </tr>
</table>