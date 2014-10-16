{{*
 * $Id$
 *  
 * @category Search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @link     http://www.mediboard.org*}}

{{mb_script module="search" script="Search" ajax=true}}
<script>
  Main.add(function () {
    var form = getForm("esSearch");
    window.calendar_planning_fin = Calendar.regField(form._min_date);
    window.calendar_planning_fin = Calendar.regField(form._max_date);
    window.calendar_planning_fin = Calendar.regField(form._date);

    var element = form.elements.user_id,
      tokenField = new TokenField(element, {onChange: function(){}.bind(element)});

    var element_input = form.elements.user_view;
    var url = new Url("system", "ajax_seek_autocomplete");
    url.addParam("object_class", "CMediusers");
    url.addParam("input_field", element_input.name);

    url.autoComplete(element_input, null, {
      minChars: 2,
      method: "get",
      dropdown: true,
      updateElement: function(selected) {
        var guid = selected.get("id");
        var _name  = selected.down().down().getText();

        var to_insert = !tokenField.contains(guid);
        tokenField.add(guid);

        if (to_insert) {
          insertTag(guid, _name);
        }

        var element_input = form.elements.user_view;
        $V(element_input, "");
      }
    });

    window.user_tag_token = tokenField;
  });

  changePage = function(start) {
    var form = getForm("esSearch");
    $V(form.elements.start, start);
    form.onsubmit();
  };

  insertTag = function (guid, name) {
    var tag = $("CTag-" + guid);

    if (!tag) {
      var btn = DOM.button({
        "type": "button",
        "className": "delete",
        "style": "display: inline-block !important",
        "onclick": "window.user_tag_token.remove($(this).up('li').get('tag_item_id')); this.up().remove();"
      });

      var li = DOM.li({
        "data-tag_item_id": guid,
        "id": "CTag-"+guid,
        "className": "tag"
      }, name, btn);

      $("user_tags").insert(li);
    }
  }
</script>

<form method="get" name="esSearch" action="?m=search" class="watched prepared" onsubmit="return Search.displayResults(this);" onchange="onchange=$V(this.form, '0')">
  <input type="hidden" name="start" value="0">
  <input type="hidden" name="sejour_id" value="{{$sejour->_id}}"/>
  <table class="main layout">
    <tbody>
    <tr>
      <td id="td_container_search">
        <input type="search" id="words" name="words" value="" placeholder="Saisissez les termes de votre recherche ici..." style="width:100%; height:1.5em; font-size:medium;" onchange="$V(this.form.start, '0')" autofocus>
        <input type="checkbox" name="aggregate" id="aggregate" value="1">
        <label for="aggregate"> Agr�gation des r�sultats</label>
        {{mb_include module=search template=inc_tooltip_help}}
      </td>
    </tr>
    </tbody>
  </table>
  <table class="main layout">
    <tbody>
    <tr>
      <!-- Fieldset de tri par date -->
      <td class="narrow">
        <fieldset>
          <legend>Intervalle de date </legend>
          {{*{{mb_include module=search template=inc_tooltip_date}}*}}
          <table>
            <tr>
              <td>
                <input type="hidden" class="datetime" id="_min_date" name="_min_date" onchange="$V(this.form.start, '0')" >
                <b>&raquo;</b>
                <input type="hidden" class="datetime" id="_max_date" name="_max_date" onchange="$V(this.form.start, '0')" >
                <br/>
                <strong>{{tr}}or{{/tr}}</strong>
                Jour seul : <input type="hidden" class="datetime" id="_date" name="_date" onchange="$V(this.form.start, '0')" >
              </td>
            </tr>
          </table>
        </fieldset>
      </td>
      <!-- Fieldset de tri par Intervenants -->

      <td class="narrow">
        <fieldset>
          <legend> Intervenants</legend>
          <table class="layout">
            <tr>
              <td>
                <input type="text" name="user_view" class="autocomplete" value="" placeholder="&mdash; Choisir un intervenant"/>
                <input type="hidden" name="user_id" value="" />

                <button type="button" class="user notext"
                        onclick="window.user_tag_token.add('{{$app->user_id}}'); insertTag('{{$app->_ref_user->_guid}}', '{{$app->_ref_user}}')">
                </button>
                <button type="button" class="erase notext" onclick="$V(this.form.elements.user_id, '');
                          $V(this.form.elements.user_view, ''); $$('li.tag').each(function(elt) { elt.remove(); });">
                </button>
                <ul id="user_tags" class="tags" style="display: inline-block;">
                </ul>
              </td>
            </tr>
          </table>
        </fieldset>
      </td>

      <!-- Fieldset de tri par Types -->
      <td class="narrow">
        <fieldset>
          <legend>
            <input type="checkbox" name="searchAll" id="SearchAll" value="SearchAll" onclick="Search.checkAllCheckboxes(this, 'names_types[]')">
            <label for="SearchAll">Types</label>
          </legend>
          <table class="layout" id="first_indexing">
            <tr>
              <td>
                <input type="checkbox" name="names_types[]" id="CCompteRendu" value="CCompteRendu"/>
                <label for="CCompteRendu">Compte rendu</label>
              </td>
            </tr>
            <tr>
              <td>
                <input type="checkbox" name="names_types[]" id="CTransmissionMedicale" value="CTransmissionMedicale">
                <label for="CTransmissionMedicale"> Transmission M�dicale</label>
              </td>
            </tr>
            <tr>
              <td>
                <input type="checkbox" name="names_types[]" id="CObservationMedicale" value="CObservationMedicale">
                <label for="CObservationMedicale"> Observation M�dicale</label>
              </td>
            </tr>
            <tr>
              <td>
                <input type="checkbox" name="names_types[]" id="CConsultation" value="CConsultation">
                <label for="CConsultation"> Consultation de s�jour</label>
              </td>
            </tr>
            <tr>
              <td>
                <input type="checkbox" name="names_types[]" id="CConsultAnesth" value="CConsultAnesth">
                <label for="CConsultAnesth"> Consultation anesth�sique de s�jour</label>
              </td>
            </tr>
          </table>
        </fieldset>
      </td>
    <tr>
      <td type="button">
        <button type="submit" id="button_search" class="button lookup">D�marrer la recherche</button>
      </td>
    </tr>
    </tbody>
  </table>
  <div id="list_result">
    <!-- R�sultats de la Recherche -->
  </div>
</form>