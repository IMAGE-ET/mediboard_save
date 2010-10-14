if (!window.File.applet) {
  File.applet = {
    object_guid: null,
    debugConsole: null,
    directory: null,
    uploader: null,
    executer: null,
    url: null,
    current_list: [],
    current_list_status: [],
    extensions: null,
    modaleWindow: null,
    timer: null,
    isOpen: false,
/*    appletCode: DOM.applet({id: 'uploader', name: 'yopletuploader', width: 0, height: 0,
                            code: 'org.yoplet.Yoplet.class', archive: 'includes/applets/yoplet2.jar'},
      DOM.param({name: 'action', value: ''}),
      DOM.param({name: 'url', value: document.location.href.replace(/((index\.php)?\?.*)$/, "modules/dPfiles/ajax_yoplet_upload.php")}),
      DOM.param({name: 'content', value: 'a'})
    ),*/
    debug: function(text) {
      if (!this.debugConsole) return;
      
      this.debugConsole.insert(text+"<br />")
    },
    watchDirectory: function() {
      // Lister les nouveaux fichiers
      if (File.applet.isOpen) return;
      try {
        File.applet.uploader.listFiles(File.applet.directory, "false");
      } catch(e) {
        File.applet.debug(e);
      }
    },
    uploadFiles: function() {
      var files_to_upload = $$(".upload-file:checked");
      var files = files_to_upload.pluck("value");
      
      // Ajouter chaque fichier � uploader dans la liste current_list_status
      // Mettre la case Envoi en loading pour les fichiers
      files_to_upload.each(function(elem) {
        File.applet.current_list_status.push([elem.value, 0]);
        elem.up("tr").down(".upload").addClassName("loading");
      });
      
      var json = Object.toJSON(files);
      var rename = $V(getForm("addFastFile").file_rename) || "upload";
      
      this.uploader.performUpload(rename, json);
    },
    handleUploadKO: function(result) {
      alert('L\'envoi du fichier ' + result.path + ' sur le serveur a �chou�');
    },

    handleUploadOk: function(result) {
      var elem = this.modaleWindow.container.select("input[type=checkbox]:checked").detect(function(n) { return n.value == result.path });
      
      if (!elem) {
        this.debug("Checkbox for '"+result.path+"' not found (handleUploadOk)");
        return; // warning
      }
      
      // Cochage de la case envoi pour le fichier
      var elem_td = elem.up("tr").down(".upload");
      elem_td.className = "tick";
      
      // Apr�s l'upload du fichier, on peut cr�er le CFile
      var fast_file_form = getForm("addFastFile");
      $V(fast_file_form._checksum, result.checksum);
      $V(fast_file_form.object_class, this.object_guid.split('-')[0]);
      $V(fast_file_form.object_id, this.object_guid.split('-')[1]);
      $V(fast_file_form._file_path, result.path);
      fast_file_form.onsubmit();
    },
    handleListFiles: function(result) {
      $$(".yopletbutton").each(function(button) {
        button.disabled = "";
      });
      var list_files = $("file-list");
      list_files.update();
      File.applet.current_list = [];
      File.applet.current_list_status = [];
      var nb_files = 0;

      result.files.each(function(res, index) {
          var truncate = Preferences.directory_to_watch.length + 1;

          // Si le r�pertoire � surveiller n'a que 2 caract�res, il faut prendre en compte le slash
          // exemple : C:\
          if (Preferences.directory_to_watch.length == 3)
            truncate --; 

          var base_name = res.path.slice(truncate);
          // Ajout du fichier dans la liste et dans la modale
          list_files.insert(
            DOM.tr({},
              DOM.td({},
                DOM.input({className: "upload-file", type: "checkbox", value: res.path, checked: 'checked'})
              ),
              DOM.td({},
                DOM.span({}, base_name)
              ),
              DOM.td({className: "upload"}),
              DOM.td({className: "assoc"}),
              DOM.td({className: "delete"})));
          File.applet.current_list.push(res);
          
          nb_files ++;
      });
      
       if (nb_files > 0) {
         // On active tous les boutons upload disponibles
         $$(".yopletbutton").each(function(button) {
            button.disabled = "";
            button.style.border = "3px solid #080";
         });
       } else {
          $$(".yopletbutton").each(function(button) {
              button.style.border = "1px solid #888";
          });
       }
       File.applet.timer = setTimeout(File.applet.watchDirectory, 3000);
    },
    handleListFilesKO: function(result) {
      $$(".yopletbutton").each(function(button) {
        button.disabled = '';
        button.className = 'cancel';
        button.setStyle({border: "2px #f00 solid"});
        button.onclick = function() { alert('Le r�pertoire saisi dans vos pr�f�rences pr�sente un probl�me.');};
      });
      File.applet.timer = setTimeout(File.applet.watchDirectory, 3000);
    },
    handleDeletionOK: function(result) {
      var elem = $$("input:checked").detect(function(n) { return n.value == result.path });
      elem.up("tr").down(".delete").className = "tick";
    },
  
    handleDeletionKO: function(result) {
      alert('La suppression du fichier a �chou� : ' + result.path);
      var elem = this.modaleWindow.container.select("input:checked").detect(function(n) { return n.value == result.path });
      
      if (!elem) {
        this.debug("Checkbox for '"+result.path+"' not found (handleDeletionKO)");
        return; // warning
      }
      
      elem.up("tr").down(".delete").className = "warning";
    },
    emptyForm: function(){
      var oForm = getForm("addFastFile");
      $V(oForm.file_rename, '');
      oForm.delete_auto.checked = true;
      $V(oForm.keywords_category, String.fromCharCode(8212) + " Cat�gorie");
      $V(oForm.file_category_id, '');
	  },
    cancelModal: function() {
      // Ferme la modale en cliquant sur annuler,
      File.applet.isOpen = false;
      Control.Modal.close();
      this.emptyForm();
      File.applet.watchDirectory();
    },
    modalOpen: function(object_guid) {
      clearTimeout(File.applet.timer);
      File.applet.isOpen = true;
      this.modaleWindow = modal($("modal-yoplet"));
      $$(".uploadinmodal")[0].disabled = '';
      this.object_guid = object_guid;
      this.modaleWindow.position();
    },
    closeModal: function() {
      Control.Modal.close();
      File.applet.isOpen = false;
      this.emptyForm();
      // Clique sur Ok dans la modale,
      // alors on vide la liste des fichiers dans la modale
      // et on d�sactive les boutons upload
      File.applet.current_list = [];
      File.applet.current_list_status = [];
      $('file-list').update();
      $$('.yopletbutton').each(function(elem) {
          elem.disabled='disabled';
          elem.style.border = '1px solid #888';
      });
      File.refresh(this.object_guid.split("-")[1], this.object_guid.split("-")[0]);
      File.applet.watchDirectory();
    },
    addfile_callback: function(id, args) {
      var file_name = args["_old_file_path"].replace(/\\("|'|\\)/g, "$1");
      var elem = this.modaleWindow.container.select("input:checked").detect(function(n){
        return n.value.replace(/[^\x00-\xFF]/g, "?") == file_name.replace(/\\\\/g,"\\"); // vieux hack des sous bois
      });
      
      if (!elem) {
        this.debug("Checkbox for '"+file_name+"' not found (addfile_callback)");
        return; // warning
      }
      
      var td_el = elem.up("tr").down(".assoc");

      if (id > 0 ) {
        td_el.className = 'tick';
        var file = this.current_list.detect(function(n) { return n.path.replace(/[^\x00-\xFF]/g, "?") == file_name.replace(/\\\\/g,"\\")});
        file = file.path;
        // Ajouter le status associ� dans la liste des fichiers.
        var cur_status = this.current_list_status.detect(function(n) { return n[0].replace(/[^\x00-\xFF]/g, "?") == file_name.replace(/\\\\/g,"\\")});
        cur_status[1] = 1;

        // S'ils sont tous associ�s, alors on peut lancer la suppression        
        if (this.current_list_status.all(function(n){ return n[1] == 1;})) {
          
          // Si la suppression auto est coch�e
          if (getForm("addFastFile").delete_auto.checked) {
            File.applet.uploader.performDelete(Object.toJSON(this.current_list_status.pluck("0")));
          }
        }
      } else {
          td_el.className = 'warning';
      }
    }
  };
  
  function watching() {
    if (File.applet.uploader) {
      var active = false;
      try {
        active = File.applet.uploader.isActive();
      } catch(e) {
        //this.debug(e);
      }
      
      if (!active)
        setTimeout(watching, 50);
      else {
        File.applet.debugConsole = $("yoplet-debug-console");
        File.applet.debug("File extensions: "+File.applet.extensions);
        File.applet.uploader.setFileFilters(File.applet.extensions); // case sensitive !
        File.applet.watchDirectory();
      }
    }
    else {
      File.applet.uploader = document.applets.yopletuploader;
      File.applet.directory = File.appletDirectory;
      watching();
    }
  } 
  
  // La fonction appletCallBack ne peut pas �tre incluse dans l'objet File.applet
  function appletCallBack(args) {
    // Ajouter l'url du script comme param�tre
    if (args) {
      var operation = args.evalJSON();
      var opname = operation.name;
      switch (opname) {
        case 'init': 
                watching();
                break;
        case 'listfiles':
                File.applet.handleListFiles(operation.result);
                break;
        case 'listfilesKO':
                File.applet.handleListFilesKO(operation.result);
                break;
        case 'uploadok':
                File.applet.handleUploadOk(operation.result);
                break;
        case 'uploadko':
                File.applet.handleUploadKO(operation.result);
                break;
        case 'deleteok':
                File.applet.handleDeletionOK(operation.result);
                break;
        case 'deleteko':
                File.applet.handleDeletionKO(operation.result);
                break;
        default:
                break;
      }
    }
      else {
        alert('could not parse callback message');
    }
  }
}