ContraintesRPU = {
  contraintesProvenance: [],
  contraintesDestination: [],
  contraintesOrientation: [],
  
	updateProvenance: function(mode_entree, clearField) {
		var oSelect = document.editRPU._provenance;
	
		// Le champ peut �tre cach�
		if (!oSelect) {
		  return;
		}
		
		// On remet la valeur � z�ro
		if (clearField) {
			oSelect.value = "";
		}
		
	  if (mode_entree == "") {
	    $A(oSelect).each( function(input) {
	      input.disabled = false;
	    });
	    return;
	  }
	  
	  var valeursPossibles = this.contraintesProvenance[mode_entree];

	  if (!valeursPossibles) {
	    $A(oSelect).each( function(input) {
	      input.disabled = true;
	    });
	    return;
	  }
	 
	  $A(oSelect).each( function(input) {
	    input.disabled = !valeursPossibles.include(input.value);
	  });
  },
  
	updateDestination: function(mode_sortie, clearField) {
		var oSelect = document.editRPUDest._destination;

		// Le champ peut �tre cach�
		if (!oSelect) {
		  return;
		}
		
		// On remet la valeur � z�ro
		if (clearField) {
			oSelect.value = "";
		}
		
	  if (mode_sortie == "") {
	    $A(oSelect).each( function(input) {
	      input.disabled = false;
	    });
	    return;
	  }
	  
	  var valeursPossibles = this.contraintesDestination[mode_sortie];

	  if(!valeursPossibles){
	    $A(oSelect).each( function(input) {
	      input.disabled = true;
	    });
	    return;
	  }
	 
	  $A(oSelect).each( function(input) {
	    input.disabled = !valeursPossibles.include(input.value);
	  });
	},
	
	updateOrientation: function(mode_sortie, clearField) {
		var oSelect = document.editRPUDest.orientation;

		// Le champ peut �tre cach�
		if (!oSelect) {
		  return;
		}
		
		// On remet la valeur � z�ro
		if (clearField) {
			oSelect.value = "";
		}
		
	  if (mode_sortie == "") {
	    $A(oSelect).each( function(input) {
	      input.disabled = false;
	    });
	    return;
	  }
	  
	  var valeursPossibles = this.contraintesOrientation[mode_sortie];

	  if(!valeursPossibles){
	    $A(oSelect).each( function(input) {
	      input.disabled = true;
	    });
	    return;
	  }
	 
	  $A(oSelect).each( function(input) {
	    input.disabled = !valeursPossibles.include(input.value);
	  });
	}
};
