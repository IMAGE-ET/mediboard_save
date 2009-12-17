{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module=system script=mb_object}}
{{mb_include_script module=system script=object_selector}}

<script type="text/javascript">
function setField (field, value, form) {
  field = $(form.elements[field]);

  var dateView = $(form.elements[field.name+'_da']);
  if (dateView) {
    dateView.update(value);
    $V(field, (value ? Date.fromLocaleDate(value).toDATE() : ''));
    return;
  }
  
  $V(field, value);
  if (field.fire) {
    field.fire('mask:check');
  }
}

var objectsList = [];

ObjectSelector.init = function(key) {
  this.sForm     = "objects-selector";
  this.sView     = "objects_view["+key+"]";
  this.sId       = "objects_id["+key+"]";
  this.sClass    = "objects_class";
  this.onlyclass = "true";
  this.pop();
}

var emptyObjectField = '<div class="object readonly">';
emptyObjectField += '<input type="hidden" name="objects_id[%KEY]" value="%ID" />';
emptyObjectField += '<input type="text" size="40" name="objects_view[%KEY]" readonly="readonly" value="%VIEW" ondblclick="ObjectSelector.init(%KEY)" />';
emptyObjectField += '<button type="button" onclick="ObjectSelector.init(%KEY)" class="search notext">{{tr}}Search{{/tr}}</button>';
emptyObjectField += '<button type="button" onclick="this.up().remove();" class="remove notext">{{tr}}Remove{{/tr}}</button>';
emptyObjectField += '</div>';
var key = 0;

function addObjectField(id, view) {
  var field = emptyObjectField.gsub('%KEY', key++).gsub('%ID', id || '').gsub('%VIEW', view || '');
  $("objects-list").insert(field);
}

function toggleAltMode(check) {
  $(check.form).select("input[name='_base_object_id']").each(function(e){
    e.disabled = !e.disabled;
  });
}

function confirmMerge(button, fast) {
  $V(button.form.fast, fast);
  return confirm("Etes-vous s�r(e) de vouloir fusionner ces objets ?");
}

Main.add(function() {
  {{foreach from=$objects item=object}}
    addObjectField('{{$object->_id}}', '{{$object->_view}}');
    objectsList.push({{$object->_id}});
  {{foreachelse}}
    addObjectField();
    addObjectField();
  {{/foreach}}
}); 
</script>

<form name="objects-selector" method="get" action="">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="{{$actionType}}" value="{{$action}}" />
  <input type="hidden" name="readonly_class" value="{{$readonly_class}}" />
  <table class="main form">
    <tr>
      <th>Type d'objet</th>
      <td>
        {{if $readonly_class}}
          <input type="hidden" name="objects_class" value="{{$objects_class}}" />
          {{tr}}{{$objects_class}}{{/tr}}
        {{else}}
          <select name="objects_class">
            {{foreach from=$list_classes item=class}}
              <option value="{{$class}}" {{if $objects_class == $class}}selected="selected"{{/if}}>{{tr}}{{$class}}{{/tr}}</option>
            {{/foreach}}
          </select>
        {{/if}}
      </td>
      <th>Objets</th>
      <td id="objects-list"></td>
    </tr>
    <tr>
      <td />
      <td>
        <button type="submit" class="submit">{{tr}}Submit{{/tr}}</button>
        <label>
          <input type="checkbox" onclick="$$('tr.equal').invoke('setVisible', $V(this));" /> Afficher les champs vides ou identiques
        </label>
      </td>
      <th />
      <td><button type="button" class="add notext" onclick="addObjectField()">{{tr}}Add{{/tr}}</button></td>
    </tr>
  </table>
</form>

{{if $result}}

<h2>Fusion de {{tr}}{{$result->_class_name}}{{/tr}}</h2>

{{if $checkMerge}}
<div class="small-warning">
  <strong>La fusion de ces deux objets n'est pas possible</strong> � cause des probl�mes suivants :<br />
  - {{$checkMerge}}<br />
  Veuillez corriger ces probl�mes avant toute fusion.
</div>
{{/if}}

{{if $alternative_mode}}
  <div class="small-info">
    Vous �tes en mode SIP, vous ne pourrez fusionner que deux objets au maximum.
  </div>
{{/if}}

<form name="form-merge" action="?m={{$m}}" method="post" onsubmit="{{if $checkMerge}}false{{else}}checkForm(this){{/if}}">
  <input type="hidden" name="dosql" value="do_object_merge" />
  <input type="hidden" name="{{$actionType}}" value="{{$action}}" />
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="fast" value="0" />
  {{foreach from=$objects item=object name=object}}
  <input type="hidden" name="_merging" value="{{$object->_id}}" />
  <input type="hidden" name="_objects_id[{{$smarty.foreach.object.index}}]" value="{{$object->_id}}" />
  {{/foreach}}
  <input type="hidden" name="_objects_class" value="{{$result->_class_name}}" />
  
  {{math equation="100/(count+1)" count=$objects|@count assign=width}}
  <table class="form">
    <tr>
      <th class="category" style="width: 1%;">
        {{if !$alternative_mode}}
        <label style="font-weight: normal;">
          <input type="checkbox" name="_keep_object" value="1" onclick="toggleAltMode(this)" /> Mode de fusion alternatif
        </label>
        {{/if}}
      </th>
      {{foreach from=$objects item=object name=object}}
      <th class="category" style="width: {{$width}}%;">
        {{tr}}{{$object->_class_name}}{{/tr}} {{$smarty.foreach.object.iteration}}<br />
        <label style="font-weight: normal;">
          <input type="radio" name="_base_object_id" value="{{$object->_id}}" {{if $smarty.foreach.object.first}}checked="checked"{{/if}} {{if !$alternative_mode}}disabled="disabled"{{/if}} />
          Utiliser comme base [#{{$object->_id}}]
        </label>
      </th>
      {{/foreach}}
      <th class="category" style="width: {{$width}}%;">R�sultat</th>
    </tr>
    
    {{foreach from=$result->_specs item=spec name=spec}}
      {{if $spec->fieldName != $result->_spec->key && ($spec->fieldName|substr:0:1 != '_' || $spec->reported)}}
        {{if $spec instanceof CRefSpec}}
          {{include field=$spec->fieldName file="../../system/templates/inc_merge_field_ref.tpl"}}
        {{else}}
          {{include field=$spec->fieldName file="../../system/templates/inc_merge_field.tpl"}}
        {{/if}}
      {{/if}}
    {{/foreach}}

	  <tr>
	  	<td colspan="100" class="text">
       <button type="button" class="search" onclick="MbObject.viewBackRefs('{{$result->_class_name}}', objectsList);">
         {{tr}}CMbObject-merge-moreinfo{{/tr}}
       </button>
       
	  	 <div class="big-warning">
	  	   Vous �tes sur le point d'effectuer une fusion d'objets.
	  	   <br />
	  	   <strong>Cette op�ration est irr�versible, il est donc imp�ratif d'utiliser cette fonction avec une extr�me prudence !</strong>
	  	   <br />
         Vous avez la possibilit� de faire une fusion de deux mani�res diff�rentes :<br />
         <br />
	  	   La <strong>proc�dure normale</strong> de fusion se passe en trois phases :
	  	   <ol>
	  	     <li>Cr�ation d'un nouvel objet, avec les propri�t�s choisies ci-dessus</li>
	  	     <li>Transfert des relations depuis les anciens objets vers le nouveau</li>
	  	     <li>Suppression des anciens objets</li>
	  	   </ol>
         {{if $alternative_mode}}
           Ce mode n'est pas disponible � cause de la configuration de Mediboard (module SIP install� et handler actif)
         {{/if}}
         
         <br />
         L'<strong>autre proc�dure qui est n�cessaire dans certains cas</strong>, mais qui limite le nombre maximum d'objets fusionn�s � deux, se d�roule en trois phases :
         <ol>
           <li>Modification d'un des deux objets avec les propri�t�s choisies ci-dessus</li>
           <li>Transfert des relations depuis l'autre objet</li>
           <li>Suppression de l'autre objet</li>
         </ol>
         {{if !$alternative_mode}}
           Vous pouvez utiliser ce mode en cochant en haut de la colonne de gauche "Mode de fusion alternatif" et en choisissant quel objet est l'objet conserv�.<br />
         {{/if}}
	   	 </div>

    	 <div class="big-info">
    	   Il existe deux options possibles pour effectuer la fusion :
    	   <ul>
    	     <li><strong>La fusion standard qui automatise des sauvegardes normales</strong>. Ce processus
    	       <ul>
    	         <li>automatise des sauvegardes normales</li>
    	         <li>effectue des v�rifications d'int�grit�, au risque d'�chouer dans certaines circonstances</li>
    	         <li>journalise tous les transferts d'objet</li>
    	         <li>est tr�s lent</li>
    	       </ul>
    	     </li>
    	      
    	     <li><strong>La fusion massive, qui modifie la base de donn�es en direct</strong>. Ce processus
    	       <ul>
    	         <li>n'effectue aucune v�rification d'int�grit�</li>
    	         <li>ne journalise que la cr�ation du nouvel objet et l'op�ration de fusion</li>
    	         <li>est tr�s rapide</li>
    	       </ul>
    	     </li>
    	   </ul> 
    	 </div>

      </td>
    </tr>

    <tr>
	  	<td colspan="100" class="button">
		    <button type="submit" class="submit" onclick="return confirmMerge(this, '0')">
		      {{tr}}Merge{{/tr}}
		    </button>
		    <button type="submit" class="submit" onclick="return confirmMerge(this, '1');">
		      {{tr}}Merge{{/tr}} {{tr}}massively{{/tr}}
		    </button>
	    </td>
	  </tr>
  </table>
</form>

{{else}}
<div class="small-info">
  Veuillez choisir des objets existants � fusionner.
</div>
{{/if}}