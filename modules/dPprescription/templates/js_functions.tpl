<script type="text/javascript">
// Affichage des div d'ajout de posologies
selDivPoso = function(type, line_id, type_elt){
  if(!type){
    type = "foisPar"+type_elt;
  }
  
  oDivFoisPar = $('foisPar'+type_elt+line_id);
  oDivTousLes = $('tousLes'+type_elt+line_id);
  
  oFormPrise = document.forms['addPrise'+type_elt+line_id].show();
  
  var selectMoments = (type_elt == "mode_grille") ? 
                        window.opener.document.moment_unitaire.moment_unitaire_id :
                        document.moment_unitaire.moment_unitaire_id;
  
                        
  oFormPrise.getElements().each (function (e) {
    if ((e.type != "hidden") && (e.name != "unite_prise") && (e.name != "quantite")) {
      e.hide().writeAttribute("disabled", "disabled").value = "";
    }
  });
  switch (type) {
    case  "tousLes"+type_elt: 
      oFormPrise.nb_tous_les.enable().show();
      oFormPrise.unite_tous_les.enable().show();
      oFormPrise.decalage_prise.enable().show();
      oDivFoisPar.hide();
      oDivTousLes.show();
    case  "moment"+type_elt: 
      if (oFormPrise.moment_unitaire_id.empty()) {
        $A(selectMoments.childNodes).each(function (optgroup) {
          oFormPrise.moment_unitaire_id.appendChild(optgroup.cloneNode(true));
        } );
      }
      oFormPrise.moment_unitaire_id.enable().show();
    break;
    case  "foisPar"+type_elt: 
      oFormPrise.nb_fois.enable().show();
      oFormPrise.unite_fois.enable().show();
      oDivFoisPar.show();
      oDivTousLes.hide();
    break;
  }
  
  if (type == "moment"+type_elt) {
    oDivFoisPar.hide();
    oDivTousLes.hide();
  }
}

reloadPrises = function(prescription_line_id, type){
  url = new Url;
  url.setModuleAction("dPprescription", "httpreq_vw_prises");
  url.addParam("prescription_line_id", prescription_line_id);
  url.addParam("type", type);
  url.requestUpdate('prises-'+type+prescription_line_id, { waitingText: null });
}

onSubmitPrise = function(oForm, type){
  if (!checkForm(oForm) || !oForm.object_id.value){
    return;
  }
  return onSubmitFormAjax(oForm, { onComplete:
    function(){
      reloadPrises(oForm.object_id.value, type);
      oForm.quantite.value = 1;
      oForm.moment_unitaire_id.value = "";
  } });
}



// Calcul de la date de debut lors de la modification de la fin
syncDate = function(oForm, curr_line_id, fieldName, type, object_class, cat_id) {
 
  // D�claration des div des dates
  oDivDebut = $('editDates-'+type+'-'+curr_line_id+'_debut_da');
  oDivFin = $('editDates-'+type+'-'+curr_line_id+'__fin_da');

  // Recuperation de la date actuelle
  var todayDate = new Date();
  var dToday = todayDate.toDATE();
  
  // Recuperation des dates des formulaires
  var sDebut = oForm.debut.value;
  var sFin = oForm._fin.value;
  var nDuree = parseInt(oForm.duree.value, 10);
  var sType = oForm.unite_duree.value;
  
  // Transformation des dates
  if(sDebut){
    var dDebut = Date.fromDATE(sDebut);  
  }
  if(sFin){
    var dFin = Date.fromDATE(sFin);  
  }
  
  // Modification de la fin en fonction du debut
  if(fieldName != "_fin" && sDebut && sType && nDuree) {
    dFin = dDebut;
    switch (sType) {
      case "jour":      dFin.addDays(nDuree-1); break;
      case "semaine":   dFin.addDays(nDuree*7-1); break;
      case "quinzaine": dFin.addDays(nDuree*14-1); break;
      case "mois":      dFin.addDays(nDuree*30-1); break;
      case "trimestre": dFin.addDays(nDuree*90-1); break;
      case "semestre":  dFin.addDays(nDuree*180-1); break;
      case "an":        dFin.addDays(nDuree*365-1); break;
    }

    oForm._fin.value = dFin.toDATE();
    oDivFin.innerHTML = dFin.toLocaleDate();
    if(curr_line_id){
      testPharma(curr_line_id);
    }
  }
  
  //-- Lors de la modification de la fin --
  // Si debut, on modifie la duree
  if(sDebut && sFin && fieldName == "_fin"){
    var nDuree = parseInt((dFin - dDebut)/86400000,10);
    oForm.duree.value = nDuree+1;
    oForm.unite_duree.value = "jour";
    if(curr_line_id){
      testPharma(curr_line_id);
    }
  }
  
  // Si !debut et duree, on modifie le debut
  if(!sDebut && nDuree && sType && fieldName == "_fin"){
    dDebut = dFin;
    
    switch (sType) {
      case "jour":      dDebut.addDays(-nDuree); break;
      case "semaine":   dDebut.addDays(-nDuree*7); break;
      case "quinzaine": dDebut.addDays(-nDuree*14); break;
      case "mois":      dDebut.addDays(-nDuree*30); break;
      case "trimestre": dDebut.addDays(-nDuree*90); break;
      case "semestre":  dDebut.addDays(-nDuree*180); break;
      case "an":        dDebut.addDays(-nDuree*365); break;
    }

    oForm.debut.value = dDebut.toDATE();
    oDivDebut.innerHTML = dDebut.toLocaleDate();
    
    if(curr_line_id){
      testPharma(curr_line_id);
    } 
  }
  
  // Si !debut et !duree, on met le debut a aujourd'hui, et on modifie la duree
  if(!sDebut && !nDuree && fieldName == "_fin"){
    dDebut = todayDate;
    oForm.debut.value = todayDate.toDATE();
    oDivDebut.innerHTML = todayDate.toLocaleDate();
    var nDuree = parseInt((dFin - dDebut)/86400000,10);
    oForm.duree.value = nDuree;
    oForm.unite_duree.value = "jour";
    if(curr_line_id){
      testPharma(curr_line_id);
    }
  }
  
  if ($('line_medicament_'+curr_line_id) || $('line_element_'+curr_line_id)) {
    if(object_class == 'CPrescriptionLineMedicament'){
      var oTbody = $('line_medicament_'+curr_line_id);
    } else {
      var oTbody = $('line_element_'+curr_line_id);
    }
    
    // Classes du tbody avant la modification
    var classes_before = oTbody.className;
    
    // Ligne finie
    var oDiv = $('th_line_'+object_class+'_'+curr_line_id);
    if(oForm._fin.value != "" && oForm._fin.value < '{{$today}}'){
      oDiv.addClassName("arretee");
      oTbody.addClassName("line_stopped");
    } else {
      oDiv.removeClassName("arretee");
      oTbody.removeClassName("line_stopped");
    }
  
    
    // Classes du tbody apres la modification
    var classes_after = oTbody.className;
    
    // Si modif, deplacement du tbody
    if(classes_after != classes_before){
      if(object_class == 'CPrescriptionLineMedicament'){
        moveTbody(oTbody);
      } else {
        moveTbodyElt(oTbody, cat_id);
      }
    }
  }
}

addLineContigue = function(oForm){
  if(document.selPraticienLine){
    oForm.praticien_id.value = document.selPraticienLine.praticien_id.value;
  }
  submitFormAjax(oForm, 'systemMsg'); 
}

// Fonction lanc�e lors de la modfication de la posologie
submitPoso = function(oForm, curr_line_id){
  // Suppression des prises de la ligne de prescription
  oForm._delete_prises.value = "1";
  submitFormAjax(oForm, 'systemMsg', { onComplete: 
    function(){
      // Preparation des prises pour la nouvelle posologie selectionn�e
      var url = new Url;
      url.setModuleAction("dPprescription", "httpreq_prescription_prepare");
      url.addParam("prescription_line_id", curr_line_id);
      url.addParam("no_poso", oForm.no_poso.value);
      url.addParam("code_cip", oForm._code_cip.value);
      url.requestUpdate('prises-Med'+curr_line_id, { waitingText: null });
    } 
   }
  );
}

// Permet de mettre la ligne en traitement
transfertTraitement = function(line_id){
  if(!line_id){
    return;
  }
  var oForm = document.transfertToTraitement;
  oForm.prescription_line_id.value = line_id;
  submitFormAjax(oForm, "systemMsg");
}


moveTbody = function(oTbody){
  var oTableMed = $('med');
  var oTableTrt = $('traitement');
  var oTableMedArt = $('med_art');
  var oTableTrtArt = $('traitement_art');
  
  if(oTbody.hasClassName('med')){
    if(oTbody.hasClassName('line_stopped')){
      oTableMedArt.insert(oTbody);      
    } else {
      oTableMed.insert(oTbody);     
    } 
  }
  if (oTbody.hasClassName('traitement')){
    if(oTbody.hasClassName('line_stopped')){
      oTableTrtArt.insert(oTbody);      
    } else {
      oTableTrt.insert(oTbody);     
    }       
  }
}

moveTbodyElt = function(oTbody, cat_id){
  var oTableElt = $('elt_'+cat_id);
  var oTableEltArt = $('elt_art_'+cat_id);
  
  if(oTbody.hasClassName('elt')){
    if(oTbody.hasClassName('line_stopped')){
      oTableEltArt.insert(oTbody);      
    } else {
      oTableElt.insert(oTbody);     
    } 
  }
}

changePraticienMed = function(praticien_id){
  var oFormAddLine = document.addLine;
  var oFormAddLineCommentMed = document.addLineCommentMed;
  
  oFormAddLine.praticien_id.value = praticien_id;
  oFormAddLineCommentMed.praticien_id.value = praticien_id;
}

// Test permettant de pr�-selectionner la case � cocher 
testPharma = function(line_id){
  // si on est pas en mode pharmacie, on sort de la fonction
  {{if !$mode_pharma}}return;{{/if}} 
  var oFormAccordPraticien = document.forms["editLineAccordPraticien-"+line_id];
  if(oFormAccordPraticien.accord_praticien.value == 0){
    if(confirm("Modifiez vous cette ligne en accord avec le praticien ?")){
      oFormAccordPraticien.__accord_praticien.checked = true;
      $V(oFormAccordPraticien.accord_praticien,"1");
    }
  }
}

// Preselection des executants
preselectExecutant = function(executant_id, category_id){
 $$('select.executant-'+category_id).each( function(select) {
   select.value = executant_id;
   select.onchange();
 })
}

changePraticienElt = function(praticien_id, element){
  var oFormAddLineElement = document.addLineElement;
  var oFormAddLineCommentElement = document.forms['addLineComment'+element];
  
  oFormAddLineElement.praticien_id.value = praticien_id;
  if(oFormAddLineCommentElement){
    oFormAddLineCommentElement.praticien_id.value = praticien_id;
  }
}

// UpdateFields de l'autocomplete des elements
updateFieldsElement = function(selected, formElement, element) {
  Element.cleanWhitespace(selected);
  dn = selected.childNodes;
  Prescription.addLineElement(dn[0].firstChild.nodeValue, dn[1].firstChild.nodeValue);
  $(formElement+'_'+element).value = "";
}

// UpdateFields de l'autocomplete de medicaments
updateFieldsMedicament = function(selected) {
  Element.cleanWhitespace(selected);
  dn = selected.childNodes;
  Prescription.addLine(dn[0].firstChild.nodeValue);
  $('searchProd_produit').value = "";
}

modifUniteDecal = function(oFieldJour, oFieldUnite){
  if(oFieldJour.value != "I"){
    $V(oFieldUnite,"jour");
    oFieldUnite.disabled = "disabled";
  } else {
    oFieldUnite.disabled = "";
  }
}
</script>