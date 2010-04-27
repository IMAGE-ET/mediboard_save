{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module="mediusers" script="color_selector"}}

<script type="text/javascript">

toggleCancelled = function(){
  $$(".cancelled").invoke("toggle");
}

ColorSelector.init = function(form_name, color_view){
  this.sForm  = form_name;
  this.sColor = "color";
	this.sColorView = color_view;
  this.pop();
}


Main.add( function(){
  categories_tab = new Control.Tabs.create('categories_tab', true);

  if($('code_auto_complete')){
	  var url = new Url("ssr", "httpreq_do_activite_autocomplete");
	  url.autoComplete("editCdarr_code", "code_auto_complete", {
	    minChars: 2,
	    select: ".value"
	  } );
	}
	ViewPort.SetAvlHeight('topLeftDiv', 0.5);
	
});
	
</script>

<table class="main">
	<tr>
	  <td class="halfPane">
	  	
		 <div id="topLeftDiv">
				<a href="?m={{$m}}&amp;tab={{$tab}}&amp;category_prescription_id=0" class="button new">
				  Cr�er une cat�gorie
				</a>
				<ul class="control_tabs" id="categories_tab">
				{{foreach from=$categories key=chapitre item=_categories}}
				   <li>
				   	<a href="#div_{{$chapitre}}">
				   	  {{tr}}CCategoryPrescription.chapitre.{{$chapitre}}{{/tr}} 
							<small>({{$_categories|@count}} - {{$countElements.$chapitre}})</small>
						</a>
					</li>
				{{/foreach}}
				</ul>
				<hr class="control_tabs" />
	
				{{foreach from=$categories key=chapitre item=_categories}}
				  <table class="tbl" id="div_{{$chapitre}}" style="display: none;">
					  <tr>
					  	<th>{{mb_label class=CCategoryPrescription field=nom}}</th>
							<th>{{mb_label class=CCategoryPrescription field=group_id}}</th>
	            <th></th>
						</tr>
					  {{foreach from=$_categories item=_cat}}
						  <tr {{if $category->_id == $_cat->_id}}class="selected"{{/if}} >
					      <td>
				          <a href="?m={{$m}}&amp;tab={{$tab}}&amp;category_prescription_id={{$_cat->_id}}">
				            {{$_cat->nom}} ({{$_cat->_count_elements_prescription}})
				          </a>
				        </td>
				        <td>
				          {{if $_cat->group_id}}
				            {{$_cat->_ref_group->_view}}
				          {{else}}
				            Tous
				          {{/if}}
				        </td>
								<td style="width: 1em; {{if $_cat->color}}background-color: #{{$_cat->color}}{{/if}}">
									
								</td>
				      </tr>
				    {{/foreach}}
					</table>
				{{/foreach}}
		 </div>
    
		{{if $category->_id}}
		<table class="tbl">
      <tr>  
        <th colspan="3" class="title text">
            <button class="search" style="float: right" onclick="toggleCancelled();">
              Afficher les annul�s
            </button> 
            Elements de la cat�gorie '{{$category}}'
          </th>
        </tr>
        <tr>
          <th class="category">Libelle</th>
          <th class="category">Description</th>
          <th></th>
        </tr>
         {{foreach from=$category->_ref_elements_prescription item=_element}}
          <tr {{if $_element->_id == $element_prescription->_id}}class="selected"{{/if}}
              {{if $_element->cancelled}}class="cancelled" style="display: none; opacity: 0.5"{{/if}}>
            <td class="text">
              <a href="?m={{$m}}&amp;tab={{$tab}}&amp;element_prescription_id={{$_element->_id}}&amp;element_prescription_to_cdarr_id=0">
                {{$_element->libelle}}
              </a>
            </td>
            <td class="text">
              {{$_element->description}}
            </td>
             <td style="width: 1em; {{if $_element->color}}background-color: #{{$_element->color}}{{/if}}">
          </tr>
        {{foreachelse}}
         <tr>
          <td colspan="3">
            Aucun element dans cette cat�gorie
          </td>
         </tr>
        {{/foreach}}
      </table>
			{{/if}}
	  </td> 
    <td class="halfPane">
      <form name="editCategory" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_category_prescription_aed" />
      <input type="hidden" name="category_prescription_id" value="{{$category->_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {{if $category->_id}}
          <th class="title text modify" colspan="2">
            {{mb_include module=system template=inc_object_idsante400 object=$category}}
            {{mb_include module=system template=inc_object_history object=$category}}
            Modification de la cat�gorie &lsquo;{{$category}}&rsquo;
          {{else}}
          <th class="title text" colspan="2">
            Cr�ation d'une cat�gorie
          {{/if}}
          </th>
        </tr>
        <tr>
          <th>{{mb_label object=$category field="chapitre"}}</th>
          <td>{{mb_field object=$category field="chapitre" defaultOption="&mdash; S�lection d'un chapitre"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$category field="nom"}}</th>
          <td>{{mb_field object=$category field="nom"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$category field="description"}}</th>
          <td>{{mb_field object=$category field="description"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$category field="header"}}</th>
          <td>{{mb_field object=$category field="header"}}</td>
        </tr>
				<tr>
          <th>{{mb_label object=$category field="color"}}</th>
          <td>
            <a href="#1" id="select_color_cat" style="background: #{{$category->color}}; padding: 0 3px; border: 1px solid #aaa;" onclick="ColorSelector.init('editCategory','select_color_cat')">Cliquer pour changer</a>
            {{mb_field object=$category field="color" hidden=1}}
						<button type="button" class="cancel" onclick="$('select_color_cat').setStyle({ background: '' }); $V(this.form.color, '');">Vider</button>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$category field="group_id"}}</th>
          <td>
            <select name="group_id">
              <option value="">Tous</option>
            {{foreach from=$groups item=_group}}
              <option value="{{$_group->_id}}" {{if $category->group_id == $_group->_id}}selected="selected"{{/if}}>{{$_group->_view}}</option>
            {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <td class="button" colspan="2">
          {{if $category->_id}}
            <button class="modify" type="submit" name="modify">
              {{tr}}Save{{/tr}}
            </button>
            <button class="trash" type="button" name="delete" onclick="confirmDeletion(this.form,{typeName:'la cat�gorie',objName:'{{$category->nom|smarty:nodefaults|JSAttribute}}'})">
              {{tr}}Delete{{/tr}}
            </button>
          {{else}}
            <button class="new" type="submit" name="create">
              {{tr}}Create{{/tr}}
            </button>
          {{/if}}
          </td>
        </tr>
		  </table>
			</form>
			{{if $category->_id}}
		  <a href="?m={{$m}}&amp;tab={{$tab}}&amp;element_prescription_id=0" class="button new">
        Cr�er un �l�ment
      </a>
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;mode_duplication=1" class="button new">
        Dupliquer des elements
      </a>
			{{if $mode_duplication}}
       <table class="form">
         <tr>
           <th class="category">Duplication</th>
         </tr>
         <tr>
           <td>
             <form name="duplicationElts" action="?m={{$m}}" method="post">
               <input type="hidden" name="m" value="dPprescription" />
               <input type="hidden" name="dosql" value="do_duplicate_cat_elements_aed" />
               <input type="hidden" name="category_prescription_id" value="{{$category->_id}}" />
               Dupliquer les elements de <strong>{{$category->_view}}</strong> vers
               <select name="category_dest_id">
                 <option value="">&mdash; S�lection d'une cat�gorie</option>
                 {{foreach from=$categories key=chapitre item=_categories}}
                 {{if $categories}}
                 <optgroup label="{{tr}}CCategoryPrescription.chapitre.{{$chapitre}}{{/tr}}">
                   {{foreach from=$_categories item=_category}}
                   <option value="{{$_category->_id}}" {{if $_category->_id == $category->_id}}disabled="disabled"{{/if}}>{{$_category->nom}}</option>
                   {{/foreach}}
                 </optgroup>
                 {{/if}}
                 {{/foreach}}
               </select>
               <button type="button" class="submit" onclick="if(this.form.category_dest_id.value) { this.form.submit(); }">Valider</button>
							</form>
           </td>
         </tr>
       </table>
      {{else}}
			 <form name="editElement" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
         <input type="hidden" name="dosql" value="do_element_prescription_aed" />
         <input type="hidden" name="element_prescription_id" value="{{$element_prescription->_id}}" />
         <input type="hidden" name="category_prescription_id" value="{{$category->_id}}" />
         <input type="hidden" name="del" value="0" />
         <input type="hidden" name="cancelled" value="{{$element_prescription->cancelled}}" />
         <table class="form">
           <tr>
             {{if $element_prescription->_id}}
             <th class="title text modify" colspan="2">
               {{mb_include module=system template=inc_object_idsante400 object=$element_prescription}}
               {{mb_include module=system template=inc_object_history object=$element_prescription}}
               Modification de l'element &lsquo;{{$element_prescription->libelle}}&rsquo;
             {{else}}
             <th class="title text" colspan="2">
               Cr�ation d'un �l�ment
             {{/if}}
             </th>
           </tr>
           <tr>
             <th>{{mb_label object=$element_prescription field="libelle"}}</th>
             <td>{{mb_field object=$element_prescription field="libelle"}}</td>
           </tr>
           <tr>
             <th>{{mb_label object=$element_prescription field="description"}}</th>
             <td>{{mb_field object=$element_prescription field="description"}}</td>
           </tr>
					 <tr>
	           <th>{{mb_label object=$category field="color"}}</th>
	           <td class="text">
	             <a href="#1" id="select_color_elt" style="background: #{{$element_prescription->color}}; padding: 0 3px; border: 1px solid #aaa;" onclick="ColorSelector.init('editElement','select_color_elt');">Cliquer pour changer</a>
	             {{mb_field object=$element_prescription field="color" hidden=1}}
	             <button type="button" class="cancel" onclick="$('select_color_elt').setStyle({ background: '' }); $V(this.form.color, '');">Vider</button>
						 </td>
	         </tr>
					 <tr>
					 	 <td colspan="2">
               <div class="small-info">
                 Si aucune couleur n'est sp�cifi�e pour l'�l�ment, la couleur qui apparaitra sera celle de sa cat�gorie
               </div>
					   </td>
					 </tr>
           <tr>
             <td class="button" colspan="2">
             {{if $element_prescription->_id}}
               <button class="modify" type="submit" name="modify">
                 {{tr}}Save{{/tr}}
               </button>
               {{if $element_prescription->cancelled}}
                 <button class="tick" type="submit" name="restore" onclick="$V(this.form.cancelled, '0');">
                   {{tr}}Restore{{/tr}}
                 </button>
               {{else}}
                 <button class="cancel" type="submit" name="cancel" onclick="$V(this.form.cancelled, '1');">
                   {{tr}}Cancel{{/tr}}
                 </button>
               {{/if}}
               <button class="trash" type="button" name="delete" onclick="confirmDeletion(this.form,{typeName:'l\'element',objName:'{{$element_prescription->libelle|smarty:nodefaults|JSAttribute}}'})">
                 {{tr}}Delete{{/tr}}
               </button>
             {{else}}
               <button class="new" type="submit" name="create">
                 {{tr}}Create{{/tr}}
               </button>
             {{/if}}
             </td>
           </tr>
         </table>
       </form> 
				 
			 {{if $element_prescription->_id && @$modules.ssr->mod_active}}
			 <a href="?m={{$m}}&amp;tab={{$tab}}&amp;element_prescription_to_cdarr_id=0" class="button new">
        Ajouter un code Cdarr
       </a>
      
			 <form name="editCdarr" action="" method="post">
				 <input type="hidden" name="m" value="ssr" />
         <input type="hidden" name="dosql" value="do_element_prescription_to_cdarr_aed" />
				 <input type="hidden" name="del" value="0" />
				 <input type="hidden" name="element_prescription_id" value="{{$element_prescription->_id}}" />
         <input type="hidden" name="element_prescription_to_cdarr_id" value="{{$element_prescription_to_cdarr->_id}}" />
				 
				 <table class="form">
					 <tr>
				 	   {{if $element_prescription_to_cdarr->_id}}
						   <th class="title text modify" colspan="2">
						     Modification du code Cdarr pour '{{$element_prescription->_view}}'
               </th>
						 {{else}}
						   <th class="title text" colspan="2">
						     Ajout d'un code Cdarr pour '{{$element_prescription->_view}}'
               </th>
						 {{/if}}
						</tr>
					  <tr>
					 	  <th>{{mb_label object=$element_prescription_to_cdarr field="code"}}</th>
						  <td>
							  {{mb_field object=$element_prescription_to_cdarr field=code class="autocomplete"}}
	              <div style="display:none;" class="autocomplete" id="code_auto_complete"></div>
						  </td>
						</tr>
						<tr>
						 <th>{{mb_label object=$element_prescription_to_cdarr field="commentaire"}}</th>
						 <td>{{mb_field object=$element_prescription_to_cdarr field="commentaire"}}</td>
					 </tr>
					 
					 <tr>
					 	<td class="button" colspan="2">
					 		<button type="submit" class="submit">{{tr}}Save{{/tr}}</button>
              <button class="trash" type="button" name="delete" onclick="confirmDeletion(this.form,{typeName:'le code',objName:'{{$element_prescription_to_cdarr->code|smarty:nodefaults|JSAttribute}}'})">
                {{tr}}Delete{{/tr}}
              </button>
						</td>
					 </tr>
					</table>
				</form>
				
				<table class="tbl">
				  {{if $element_prescription->_back.cdarrs|@count}}
						<tr>
						  <th colspan="2">Liste des codes Cdarr</th>
						</tr>
						<tr>
						  <th>{{mb_label class=CElementPrescriptionToCdarr field=code}}</th>
							<th>{{mb_label class=CElementPrescriptionToCdarr field=commentaire}}</th>
						</tr>
						{{foreach from=$element_prescription->_back.cdarrs item=_element_to_cdarr}}
						  <tr>
		            <td>
		            	 <a href="?m={{$m}}&amp;tab={{$tab}}&amp;element_prescription_to_cdarr_id={{$_element_to_cdarr->_id}}">
		            	 	{{mb_value object=$_element_to_cdarr field=code}}
									 </a>
								</td>
								<td>{{mb_value object=$_element_to_cdarr field=commentaire}}</td>
		          </tr>
				    {{/foreach}}
				  {{/if}}
				</table>
			{{/if}}
		 {{/if}} 
		{{/if}}
    </td>
  </tr>
</table>