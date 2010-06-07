editPlageVac = function(plage_id, user_id){
  var url = new Url("dPpersonnel", "ajax_edit_plage_vac");
  url.addParam("plage_id", plage_id);
  url.addParam("user_id", user_id);
  url.requestUpdate("edit_plage", {
    method: "post",
    getParameters:
        {m: "dPpersonnel", a: "ajax_edit_plage_vac"}
  });
  if(plage_id != '') {
    if($("p"+plage_id) != null) {
      var plage = $("p"+plage_id); 
      var siblings = plage.siblings();
      siblings.each(function(item) {
      item.className = '';
      });
      plage.className = "selected";
    }
  }
}

loadUser=function(user_id, plage_id){
  var url = new Url("dPpersonnel", "ajax_plage_vac");
  url.addParam("plage_id", plage_id);
  url.addParam("user_id", user_id);
  url.requestUpdate("vw_user");
  if($("u"+user_id) != null) {
    var user = $("u"+user_id); 
    var siblings = user.siblings();
    siblings.each(function(item) {
    item.className = '';
    });
    user.className = "selected";
  }
  
}

editPlageVacCallback = function(id) {
  editPlageVac(id);
}
