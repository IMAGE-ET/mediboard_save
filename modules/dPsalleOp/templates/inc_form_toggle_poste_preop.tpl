{{mb_default var=type value="preop"}}

<form name="editPoste{{$type}}{{$_operation->_id}}" method="post">
  <input type="hidden" name="m" value="dPplanningOp" />
  <input type="hidden" name="dosql" value="do_planning_aed" />
  {{mb_key object=$_operation}}
  <input type="hidden" name="poste_preop_id" value="{{$_operation->poste_preop_id}}"
         onchange="onSubmitFormAjax(this.form, refreshTabReveil.curry('{{$type}}'))"/>
  <input type="text" name="_poste_preop_id_autocomplete" value="{{$_operation->_ref_poste_preop}}"/>
  <script>
    Main.add(function() {
      var form = getForm("editPoste{{$type}}{{$_operation->_id}}");
      var url = new Url("system", "ajax_seek_autocomplete");
      url.addParam("object_class", "CPosteSSPI");
      url.addParam('show_view', true);
      url.addParam("input_field", "_poste_preop_id_autocomplete");
      url.addParam("where[group_id]", "{{$g}}");
      url.addParam("where[type]", "preop");
      url.autoComplete(form.elements._poste_preop_id_autocomplete, null, {
        minChars: 2,
        method: "get",
        select: "view",
        dropdown: true,
        afterUpdateElement: function(field,selected) {
          var guid = selected.getAttribute('id');
          if (guid) {
            $V(field.form['poste_preop_id'], guid.split('-')[2]);
          }
        }
      });
    });
  </script>
</form>