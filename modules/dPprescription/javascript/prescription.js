/* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */
 
Prescription = {
	// Multiples occurences de la m�me widget
  suffixes: [],
  addEquivalent: function(code, line_id, mode_pharma, mode_protocole){
    var url = new Url("dPprescription", "httpreq_substitute_line");
    url.addParam("code_cip", code);
    url.addParam("line_id", line_id);
    url.addParam("mode_pharma", mode_pharma);
    url.addParam("mode_protocole", mode_protocole);
    url.requestUpdate("systemMsg");
  },
  addLine: function(code) {
    var oForm     = document.forms.addLine;
    var oFormDate = document.forms.selDateLine;
    
    if(oFormDate){
			if(oFormDate.debut && oFormDate.debut.value){
			  $V(oForm.debut, oFormDate.debut.value);  
			}
			if(oFormDate.time_debut && oFormDate.time_debut.value){
			  $V(oForm.time_debut, oFormDate.time_debut.value);
			}
			if(oFormDate.jour_decalage && oFormDate.jour_decalage.value){
        $V(oForm.jour_decalage, oFormDate.jour_decalage.value);
			}
      if(oFormDate.decalage_line && oFormDate.decalage_line.value){
			  $V(oForm.decalage_line, oFormDate.decalage_line.value);
			}
			if(oFormDate.unite_decalage && oFormDate.unite_decalage.value){
			  $V(oForm.unite_decalage, oFormDate.unite_decalage.value);
		  }
			if(oFormDate.operation_id && oFormDate.operation_id.value){
        $V(oForm.operation_id, oFormDate.operation_id.value);
      }
		}
    oForm.code_cip.value = code;
    var mode_pharma = oForm.mode_pharma.value;
    return onSubmitFormAjax(oForm);
  },
  addLineElement: function(element_id, chapitre){
    // Formulaire contenant la categorie courante
    var oForm     = document.forms.addLineElement;
    var oFormDate = document.forms.selDateLine;

		if(oFormDate){
      if(oFormDate.debut && oFormDate.debut.value){
        $V(oForm.debut, oFormDate.debut.value);  
      }
      if(oFormDate.time_debut && oFormDate.time_debut.value){
        $V(oForm.time_debut, oFormDate.time_debut.value);
      }
      if(oFormDate.jour_decalage && oFormDate.jour_decalage.value){
        $V(oForm.jour_decalage, oFormDate.jour_decalage.value);
      }
      if(oFormDate.decalage_line && oFormDate.decalage_line.value){
        $V(oForm.decalage_line, oFormDate.decalage_line.value);
      }
      if(oFormDate.unite_decalage && oFormDate.unite_decalage.value){
        $V(oForm.unite_decalage, oFormDate.unite_decalage.value);
      }
      if(oFormDate.operation_id && oFormDate.operation_id.value){
        $V(oForm.operation_id, oFormDate.operation_id.value);
      }
    }
    if(!chapitre || !Object.isString(chapitre)){
      var chapitre = oForm._chapitre.value;
    }
    oForm.element_prescription_id.value = element_id;
    
    return onSubmitFormAjax(oForm);
    oForm.debut.value = "";
    oForm.duree.value = "";
    oForm.unite_duree.value = "";
  },
  submitPriseElement: function(element_id){
    var oFormElement = document.forms.addLineElement;
    var oForm        = document.forms.addPriseElement;
    oForm.object_id.value = element_id;
    submitFormAjax(oForm, 'systemMsg', { 
      onComplete: function(){
        Prescription.reload(oFormElement.prescription_id.value, element_id, oForm.chapitre.value, null, null, null);
      } 
    });
  },
  submitPriseElementWithoutRefresh: function(element_id){
    var oFormElement = document.forms.addLineElement;
    var oForm        = document.forms.addPriseElement;
    oForm.object_id.value = element_id;
    submitFormAjax(oForm, 'systemMsg');
  },
  addLineElementWithoutRefresh: function(element_id, debut, duree, unite_duree, callback){
    // Formulaire contenant la categorie courante
    var oForm = document.forms.addLineElement;
    if(debut){
      oForm.debut.value = debut;
    }
    if(duree && unite_duree){
      oForm.duree.value = duree;
      oForm.unite_duree.value = unite_duree;
    }
    if(callback){
      oForm.callback.value = callback;
    }
    oForm.element_prescription_id.value = element_id;
    
    submitFormAjax(oForm, 'systemMsg');
    
    oForm.debut.value = "";
    oForm.duree.value = "";
    oForm.unite_duree.value = "";
  },
  delLineWithoutRefresh: function(line_id) {
    var oForm = document.forms.addLine;
    oForm.prescription_line_medicament_id.value = line_id;
    oForm.del.value = 1;
    submitFormAjax(oForm, 'systemMsg');
  },
  delLine: function(line_id) {
    var oForm = document.addLine;
    oForm.prescription_line_medicament_id.value = line_id;
    oForm.del.value = 1;
    var mode_pharma = oForm.mode_pharma.value;
    return onSubmitFormAjax(oForm, { 
      onComplete : function(){ 
        Prescription.reload(oForm.prescription_id.value, '', 'medicament','',mode_pharma);
       } 
    });
  },
  delLineElement: function(line_id, chapitre) {
    var oForm = document.addLineElement;
    oForm.prescription_line_element_id.value = line_id;
    oForm.del.value = 1;
    submitFormAjax(oForm, 'systemMsg', { 
      onComplete : function(){ 
        Prescription.reload(oForm.prescription_id.value, null, chapitre, null, null, null);
      } 
    });
  },
  stopTraitementPerso: function(oSelect, prescription_id, mode_pharma) {
    $V(oSelect, "");
    var url = new Url("dPprescription", "httpreq_prescription_modif_all_tp");
    url.addParam("prescription_id", prescription_id);
    url.addParam("actionType", "stop");
    if(document.selDateLine){
      url.addParam("date", $V(document.selDateLine.debut));
      url.addParam("time_debut", $V(document.selDateLine.time_debut));
    }
    url.addParam("mode_pharma", mode_pharma);
    url.requestUpdate("systemMsg");
  },
  goTraitementPerso: function(oSelect, prescription_id, mode_pharma) {
    $V(oSelect, "");
    var url = new Url("dPprescription", "httpreq_prescription_modif_all_tp");
    url.addParam("prescription_id", prescription_id);
    url.addParam("actionType", "go");
    if(document.selDateLine){
      url.addParam("date", $V(document.selDateLine.debut));
      url.addParam("time_debut", $V(document.selDateLine.time_debut));
    }
    if(document.selPraticienLine) {
      url.addParam("praticien_id", $V(document.selPraticienLine.praticien_id));
    }
    url.addParam("mode_pharma", mode_pharma);
    url.requestUpdate("systemMsg");
  },
  reload: function(prescription_id, element_id, chapitre, mode_protocole, mode_pharma, line_id, full_line_guid) {
      var oForm = document.addLine;    
      if(window.opener && window.opener.PrescriptionEditor){
        window.opener.PrescriptionEditor.refresh(oForm.object_id.value, oForm.object_class.value);
      }
      var urlPrescription = new Url("dPprescription", "httpreq_vw_prescription");
      urlPrescription.addParam("prescription_id", prescription_id);
      urlPrescription.addParam("element_id", element_id);
      urlPrescription.addParam("chapitre", chapitre);
      urlPrescription.addParam("mode_protocole", mode_protocole);
			urlPrescription.addParam("mode_pharma", mode_pharma);

      if(mode_pharma == "1"){
          urlPrescription.requestUpdate("div_medicament", { onComplete: function(){ Prescription.testPharma(line_id) } });      
      } else {
	      if(mode_protocole == "1"){
	        urlPrescription.requestUpdate("vw_protocole");
	      } else {
		 	    if(chapitre){
	          if (window[chapitre+'Loaded'] || chapitre == "medicament") {
	            urlPrescription.requestUpdate("div_"+chapitre, { onComplete: function(){
							  if(window.viewListPrescription){
						      viewListPrescription();
						    }
							}} );
	          } else {
	            urlPrescription.requestUpdate("div_"+chapitre);
	          }
	        } else {
	         // Dans le cas de la selection d'un protocole, rafraichissement de toute la prescription
	         urlPrescription.requestUpdate("produits_elements");
	        }
	      }
      }
  },
  testPharma: function(line_id){  
    if(line_id){
	    var oFormAccordPraticien = document.forms["editLineAccordPraticien-"+line_id];
	    if(oFormAccordPraticien.accord_praticien.value == 0){
	      if(confirm("Modifiez vous cette ligne en accord avec le praticien ?")){
	        oFormAccordPraticien.__accord_praticien.checked = true;
	        $V(oFormAccordPraticien.accord_praticien,"1");
	      }
	    }
    }
  },
  reloadPrescSejour: function(prescription_id, sejour_id, praticien_sortie_id, mode_anesth, operation_id, chir_id, anesth_id, full_line_guid, pratSel_id, mode_sejour, praticien_for_prot_id){
    if(!$('prescription_sejour')){
			return;
		}
    
    if(!mode_sejour){
      if(document.mode_affichage){
        mode_sejour = document.mode_affichage.mode_sejour.value;
      }
    }

    // Permet de garder le praticien selectionn� pour l'ajout de ligne et l'application de protocoles
    if(!praticien_for_prot_id){
      if(document.selPraticienLine){
        praticien_for_prot_id = document.selPraticienLine.praticien_id.value;
      }
    }
		
    var url = new Url("dPprescription", "httpreq_vw_prescription");
    url.addParam("prescription_id", prescription_id);
    url.addParam("sejour_id", sejour_id);
    
    if (window.DMI_operation_id) {
      url.addParam("operation_id", window.DMI_operation_id);
    }
		  
		url.addParam("chir_id", chir_id);
    url.addParam("anesth_id", anesth_id);
    url.addParam("full_mode", "1");
    url.addParam("praticien_sortie_id", praticien_sortie_id);
    url.addParam("mode_anesth", mode_anesth);
    url.addParam("mode_protocole", "0");
    url.addParam("mode_sejour", mode_sejour);
    url.addParam("pratSel_id", pratSel_id);
    url.addParam("praticien_for_prot_id", praticien_for_prot_id);
    url.requestUpdate("prescription_sejour", { onComplete: function(){
		  if(window.viewListPrescription){
				viewListPrescription();
			}
		} } );
  },
	reloadLine: function(line_guid, mode_protocole, mode_pharma, operation_id, mode_substitution){
		if (window.modalPrescription) {
			modalPrescription.close();
	  }
    $('modalPrescriptionLine').update('');
		modalPrescription = modal($('modalPrescriptionLine'));
		var url = new Url("dPprescription", "httpreq_vw_line");
		url.addParam("line_guid", line_guid);
		url.addParam("mode_protocole", mode_protocole);
		url.addParam("mode_pharma", mode_pharma);
		if (window.DMI_operation_id) {
	  	url.addParam("operation_id", window.DMI_operation_id);
	  }
		url.addParam("mode_substitution", mode_substitution);
		url.requestUpdate("modalPrescriptionLine", { onComplete: function(){ modalPrescription.position(); } });
	},
  reloadPrescPharma: function(prescription_id){
    var url = new Url("dPprescription", "httpreq_vw_prescription");
    url.addParam("prescription_id", prescription_id);
    url.addParam("mode_pharma", "1");
    url.addParam("refresh_pharma", "1");
    url.addParam("mode_protocole", "0");
    url.requestUpdate("prescription_pharma");
  },
  reloadPrescPerf: function(prescription_id, mode_protocole, mode_pharma){
	  Prescription.reload(prescription_id,'','medicament',mode_protocole,mode_pharma);
  },
  reloadAddProt: function(protocole_id) {
    Prescription.reload(protocole_id, '','', '1','0');
    Protocole.refreshList(protocole_id);
  },
  reloadDelProt: function(){
    Prescription.reload('','','','1','0');
  },
  reloadAlertes: function(prescription_id) {
    if(prescription_id){
      var urlAlertes = new Url("dPprescription", "httpreq_alertes_icons");
      urlAlertes.addParam("prescription_id", prescription_id);
      urlAlertes.requestUpdate("alertes");
    } else {
      alert('Pas de prescription en cours');
    }
  },
  printPrescription: function(prescription_id, print, object_id, no_pdf, dci) {
    // Select de choix du praticien
    var praticien_sortie_id = "";
    
    if(document.selPraticienLine){
      praticien_sortie_id = document.selPraticienLine.praticien_id.value;
    }
    
    if(prescription_id){
      var url = new Url("dPprescription", "print_prescription");
      url.addParam("prescription_id", prescription_id);
      if(!object_id && !no_pdf) {
	  	  url.addParam("suppressHeaders", 1);
	    }
      url.addParam("dci", dci);
      url.addParam("praticien_sortie_id", praticien_sortie_id);
      url.addParam("print", print);
			url.addParam("no_pdf", no_pdf);
      url.popup(800, 600, "print_prescription");
    }
  },
  viewFullAlertes: function(prescription_id) {
    var url = new Url("dPprescription", "vw_full_alertes");
    url.addParam("prescription_id", prescription_id);
    url.modale("Alertes");
  },
  onSubmitCommentaire: function(oForm, prescription_id, chapitre){
    return onSubmitFormAjax(oForm, { 
      onComplete: function() { 
        Prescription.reload(prescription_id, null, chapitre)
      } 
    } );
  },
  refreshTabHeader: function(tabName, lineCount, lineCountNonSignee){
    // On cible le bon a href
    var link = $('prescription_tab_group').select("a[href=#"+tabName+"]")[0];

    lineCountNonSignee > 0 ? link.addClassName("wrong") : link.removeClassName("wrong");
		lineCount == 0 ? link.addClassName("empty") : link.removeClassName("empty");
		
    link.select('span')[0].innerHTML = lineCount > 0 ? " ("+lineCount+")" : "";
  },
  viewAllergies: function(prescription_id){
    var url = new Url("dPprescription", "httpreq_vw_allergies_sejour");
    url.addParam("prescription_id", prescription_id);
    url.popup(500, 300, "Allergies");
  },
  viewProduit: function(code_cip, code_ucd, code_cis, fragment){
    var url = new Url("dPmedicament", "vw_produit");
    url.addParam("code_cip", code_cip);
    url.addParam("code_ucd", code_ucd);
    url.addParam("code_cis", code_cis);
    url.setFragment(fragment);
    url.popup(900, 640, "Descriptif produit");
  },
  viewHistorique: function(prescription_id, type){
	  var url = new Url("dPprescription", "view_historique");
	  url.addParam("prescription_id", prescription_id);
	  url.addParam("type", type);
	  url.popup(500, 400, type);
  },
  popup: function(prescription_id, type){
    switch (type) {
      case 'printPrescription':
        Prescription.printPrescription(prescription_id);
        break;
      case 'printOrdonnance':
        Prescription.printPrescription(prescription_id,'ordonnance');
        break;
      case 'viewAlertes':
        Prescription.viewFullAlertes(prescription_id)
        break;
      case 'viewHistorique':
        Prescription.viewHistorique(prescription_id, 'historique');
        break;
      case 'viewSubstitutions':
        Prescription.viewHistorique(prescription_id,'substitutions');
        break;
    }
  },
  viewSubstitutionLines: function(object_id, object_class, mode_pack){
    var url = new Url("dPprescription", "httpreq_add_substitution_line");
    url.addParam("object_id", object_id);
    url.addParam("object_class", object_class);
    url.addParam("mode_pack", mode_pack);
    url.popup(900,600, "Lignes de substitution");
  },
  valideAllLines: function(prescription_id, annulation, praticien_id){
    var url = new Url("dPprescription", "vw_signature_prescription");
    url.addParam("prescription_id", prescription_id);
    url.addParam("annulation", annulation);
    url.addParam("praticien_id", $V(document.selPraticienLine.praticien_id));
    url.popup(400,400,"Signatures des lignes de prescription");
  },
  viewStatPoso: function(code_cip, praticien_id){
    var url = new Url("dPprescription", "vw_stat_posologie");
    url.addParam("code_cip", code_cip);
    url.addParam("praticien_id", praticien_id);
    url.popup(800,400, "statistiques d'utilisation des posologies");
  },
	updatePerop: function(sejour_id){
		var url = new Url("dPprescription", "httpreq_vw_perop");
		url.addParam("sejour_id", sejour_id);
		url.requestUpdate("perop");
	},
	loadTraitement: function(sejour_id, date, nb_decalage, mode_dossier, object_id, object_class, unite_prise, chapitre, without_check_date) {
		var url = new Url("dPprescription", "httpreq_vw_dossier_soin");
	  url.addParam("sejour_id", sejour_id);
	  url.addParam("date", date);
	  url.addParam("line_type", "bloc");
	  url.addParam("mode_bloc", "0");
	  url.addParam("mode_dossier", mode_dossier);
	  if(nb_decalage){
	    url.addParam("nb_decalage", nb_decalage);
	  }
	  url.addParam("chapitre", chapitre);
	  url.addParam("object_id", object_id);
    url.addParam("object_class", object_class);
    url.addParam("unite_prise", unite_prise);
		url.addParam("without_check_date", without_check_date);
		
    if(object_id && object_class){
      if(object_class == 'CPrescriptionLineMix'){
        url.requestUpdate("line_"+object_class+"-"+object_id, { onComplete: function() { 
          $("line_"+object_class+"-"+object_id).hide();
          moveDossierSoin($("line_"+object_class+"-"+object_id));
        } } );
      }
      else {
        unite_prise = unite_prise.replace(/[^a-z0-9_-]/gi, '_');
   
				var first_td = $('first_'+object_id+"_"+object_class+"_"+unite_prise);
        var last_td = $('last_'+object_id+"_"+object_class+"_"+unite_prise);
        
        // Suppression des td entre les 2 td bornes
        var td = first_td;
        var colSpan = 0;
        
        while(td.next().id != last_td.id){
          if(td.next().visible()){
            colSpan++;
          }
          td.next().remove();
          first_td.show();
        }
				
				first_td.colSpan = colSpan;
                
        url.requestUpdate(first_td, {
          insertion: Insertion.After,
          onComplete: function(){
            moveDossierSoin($("line_"+object_class+"_"+object_id+"_"+unite_prise));
					  first_td.hide().colSpan = 1;
          }
        } );
      }
    } else {
      if(chapitre){
				if(chapitre == "med" || 
				   chapitre == "perfusion" || 
					 chapitre == "oxygene" || 
					 chapitre == "alimentation" ||
					 chapitre == "aerosol" ||
           chapitre == "inj" ||
					 chapitre == "inscription"){
          chapitre = "_"+chapitre;
        } else {
          chapitre = "_cat-"+chapitre;
        }
				if($(chapitre)){
          url.requestUpdate(chapitre, { onComplete: function() { moveDossierSoin($(chapitre)); } } );
        }
				
			} else {
        url.requestUpdate("dossier_traitement");
      }
    }
  },
	updateDebit: function(line_id) {
		var oForm = getForm("editPerf-"+line_id);
    var volume = $V(oForm.volume_debit);
    var duree = $V(oForm.duree_debit);

		if(volume == 0 || duree == 0){
			return;
		}
		
		var debit = (volume / duree).toFixed(2);
	  if (isNaN(debit)) {
      debit = "-";
    } 
    $("debitLineMix-"+line_id).update(debit);
  },
  confirmDelLine: function(view) {
    if (confirm("Voulez-vous vraiment supprimer la ligne : " + view + " ?"))
      return true;
  }  
};