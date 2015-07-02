if (!window.Affectation) {
  Affectation = {
    from_tempo: false,
    delAffectation: function(form, lit_id, sejour_guid) {
      return onSubmitFormAjax(form, function() {
        if (window.refreshMouvements) {
          refreshMouvements(loadNonPlaces, lit_id);
        }
        if (sejour_guid) {
          $("view_affectations").select("." + sejour_guid).each(function(div) {
            var div_lit_id = div.get("lit_id");
            if (div_lit_id != lit_id) {
              refreshMouvements(loadNonPlaces, div_lit_id);
            }
          });
        }
      });
    },
    edit: function(affectation_id, lit_id, urgence, from_tempo) {
      var url = new Url("hospi", "ajax_edit_affectation");
      url.addParam("affectation_id", affectation_id);

      if (!Object.isUndefined(lit_id)) {
        url.addParam("lit_id", lit_id);
      }
      if (!Object.isUndefined(urgence)) {
        url.addParam("urgence", urgence);
      }

      url.addParam("from_tempo", Affectation.from_tempo ? "1" : "0");

      if (window.Placement) {
        Placement.stop();
      }
      url.requestModal("50%", "60%", {
        showReload: false,
        onClose: function() {
          if (window.Placement) {
            Placement.resume();
          }
        }
      });
    }
  }
}