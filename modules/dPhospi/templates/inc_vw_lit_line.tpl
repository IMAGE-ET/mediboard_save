{{*
 * $Id$
 *  
 * @category Hospi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}
{{if $_lit && $_lit->_id}}
  <!--Enregistrement automatique du formulaire lors de la saisie -->
  <tr id="line_lit-{{$_lit->_guid}}">
    <td class="narrow" style="width: 5%">
      <form name="editLitRankFilter{{$_lit->_guid}}" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: function() {Infrastructure.reloadLitLine('{{$_lit->_id}}', '{{$chambre->_id}}')}})">
        {{mb_key object=$_lit}}
        {{mb_class object=$_lit}}
        <input type="hidden" name="chambre_id" value="{{$chambre->_id}}" />
        {{mb_field object=$_lit field=rank onchange="this.form.onsubmit()"}}
      </form>
    </td>
    <td class="text" style="width: 10%">
      <form name="editLitNom{{$_lit->_guid}}" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: function() {Infrastructure.reloadLitLine('{{$_lit->_id}}', '{{$chambre->_id}}')}})">
        {{mb_key object=$_lit}}
        {{mb_class object=$_lit}}
        <input type="hidden" name="chambre_id" value="{{$chambre->_id}}"/>
        {{mb_field object=$_lit field=nom  onchange="this.form.onsubmit()" size=10}}
      </form>
    </td>
    <td class="text" style="width: 10%">
      <form name="editLitNom_complet{{$_lit->_guid}}" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: function() {Infrastructure.reloadLitLine('{{$_lit->_id}}', '{{$chambre->_id}}')}})">
        {{mb_key object=$_lit}}
        {{mb_class object=$_lit}}
        <input type="hidden" name="chambre_id" value="{{$chambre->_id}}" />
        {{mb_field object=$_lit field=nom_complet  onchange="this.form.onsubmit()"}}
      </form>
    </td>
    <td>
      <form name="editLitAnnule{{$_lit->_guid}}" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: function() {Infrastructure.reloadLitLine('{{$_lit->_id}}', '{{$chambre->_id}}')}})">
        {{mb_key object=$_lit}}
        {{mb_class object=$_lit}}
        <input type="hidden" name="chambre_id" value="{{$chambre->_id}}" />
        {{mb_field object=$_lit field=annule  onchange="this.form.onsubmit()"}}
      </form>
    </td>

    <td class="text" id="edit_liaisons_items-{{$_lit->_id}}" style="width: 40%">
      {{if $_lit->_id}}
        <script type="text/javascript">
          Main.add(function() {
            Infrastructure.editLitLiaisonItem('{{$_lit->_id}}');
          });
        </script>
      {{/if}}
    </td>
    <td>
      {{mb_include module=system template=inc_object_notes      object=$_lit}}
      {{mb_include module=system template=inc_object_idsante400 object=$_lit}}
      {{mb_include module=system template=inc_object_history    object=$_lit}}
      {{mb_include module=system template=inc_object_uf         object=$_lit }}
      {{mb_include module=system template=inc_object_idex       object=$_lit tag=$tag_lit}}
    </td>

    <td class="button">
      <form name="editLit{{$_lit->_guid}}"  method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: function() {Infrastructure.reloadLitLine('{{$_lit->_id}}', '{{$chambre->_id}}')}})">
        {{mb_key object=$_lit}}
        {{mb_class object=$_lit}}
        <input type="hidden" name="chambre_id" value="{{$chambre->_id}}" />
        <input type="hidden" name="nom" value="{{$_lit->nom}}" />
        <input type="hidden" name="del" value />
        <button class="trash notext" type="button" onclick="Infrastructure.confirmDeletionLit(this.form)"></button>
      </form>
    </td>
  </tr>
{{else}}
  <!--Enregistrement manuel du formulaire -->
  <tr id="line_lit-{{$_lit->_guid}}">
    <td class="narrow" style="width: 5%">
      <label><input type="number" size="3" onchange="Infrastructure.setValueFormLit('rank', this.value)" style="width: 30px"/></label>
    </td>
    <td class="text" style="width: 10%">
      <label><input type="text" size="10" onchange="Infrastructure.setValueFormLit('nom', this.value)"/></label>
    </td>
    <td class="text" style="width: 10%">
      <label><input type="text" size="25" onchange="Infrastructure.setValueFormLit('nom_complet', this.value)"/></label>
    </td>
    <td>
      <label><input type="radio" name="__annule" value="1" onclick="Infrastructure.setValueFormLit('annule', this.value)"/> Oui </label>
      <label><input type="radio" name="__annule" value="0" onclick="Infrastructure.setValueFormLit('annule', this.value)" checked/> Non </label>
    </td>
    <td></td>
    <td>
      {{mb_include module=system template=inc_object_notes      object=$_lit}}
      {{mb_include module=system template=inc_object_idsante400 object=$_lit}}
      {{mb_include module=system template=inc_object_history    object=$_lit}}
      {{mb_include module=system template=inc_object_uf         object=$_lit }}
      {{mb_include module=system template=inc_object_idex       object=$_lit tag=$tag_lit}}
    </td>

    <td class="button">
      <form name="editLit"  method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: function() {Infrastructure.reloadLitLine('{{$_lit->_id}}', '{{$chambre->_id}}')}})">
        {{mb_key object=$_lit}}
        {{mb_class object=$_lit}}
        <input type="hidden" name="chambre_id" value="{{$chambre->_id}}" />
        <input type="hidden" id="rank" name="rank" value="{{$_lit->rank}}" />
        <input type="hidden" id="nom" name="nom" value="{{$_lit->nom}}" />
        <input type="hidden" id="nom_complet" name="nom_complet" value="{{$_lit->nom_complet}}" />
        <input type="hidden" id="annule" name="annule" value="{{$_lit->annule}}"/>

        <button class="save notext" type="submit"></button>
      </form>
    </td>
  </tr>
{{/if}}
