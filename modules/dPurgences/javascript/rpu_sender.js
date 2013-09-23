RPU_Sender = {
  popupImport: function() {
    new Url("dPurgences", "ajax_import_key")
    .pop(500, 400, "Import de la cl� publique");

    return false;
  },

  updateKey: function(fingerprint) {
    var message = fingerprint ? '<div class="info">Cl� publique ajout�e au trousseau : '+fingerprint+'<\/div>' :
      '<div class="error">Impossible d\'importer la cl� publique.<\/div>';

    $("import_key").update(message);
  }
};