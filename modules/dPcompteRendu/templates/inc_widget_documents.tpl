{{* $id: $
  * @param $object CMbObject Target Object for documents
  * @param $modelesByOwner array|CCompteRendu sorted by owner
  * @param $packs array|CPack  List of packs
  * @param $praticien CMediuser Owner of modeles
  *}}
  
{{assign var=object_class value=$object->_class_name}}
{{assign var=object_id value=$object->_id}}

{{unique_id var=unique_id}}

<form name="DocumentAdd-{{$unique_id}}-{{$object->_guid}}" action="?m={{$m}}" method="post">

<table class="form">
  <tr>
    <td class="button">
    	{{if $praticien->_can->edit}}
      
      <input type="text" value="&mdash; Mod�le" name="keywords_modele" class="autocomplete str" autocomplete="off" onclick="this.value = ''; this.onclick=null;" style="width: 5em;" />
      <input type="text" value="&mdash; Pack" name="keywords_pack" class="autocomplete str" autocomplete="off" onclick="this.value = ''; this.onclick=null;" style="width: 4em;"/>
      
      <script tyle="text/javascript">
      
      Main.add(function() {
        var url = new Url("dPcompteRendu", "ajax_modele_autocomplete");
        url.addParam("user_id", "{{$praticien->_id}}");
        url.addParam("function_id", "{{$praticien->function_id}}");
        url.addParam("object_class", '{{$object->_class_name}}');
        url.addParam("object_id", '{{$object->_id}}');
        url.autoComplete('DocumentAdd-{{$unique_id}}-{{$object->_guid}}_keywords_modele', '', {
          minChars: 1,
          afterUpdateElement: createDoc,
          dropdown: true,
          width: "250px"
        });

        var url = new Url("dPcompteRendu", "ajax_pack_autocomplete");
        url.addParam("user_id", "{{$praticien->_id}}");
        url.addParam("function_id", "{{$praticien->function_id}}");
        url.addParam("object_class", '{{$object->_class_name}}');
        url.addParam("object_id", '{{$object->_id}}');
        url.autoComplete('DocumentAdd-{{$unique_id}}-{{$object->_guid}}_keywords_pack', '', {
          minChars: 1,
          afterUpdateElement: createPack,
          dropdown: true,
          width: "250px"
        });
        
        function createDoc(input, selected) {
          var id = selected.down(".id").innerHTML;
           
          if (selected.select(".fast_edit").length) {
            Document.fastMode('{{$object->_class_name}}', id, '{{$object_id}}', null, null, '{{$unique_id}}');
          } else {
            Document.create(id, '{{$object_id}}');
          }
          
          $V(input, '');
        }

        function createPack(input, selected) {
          Document.createPack(selected.down(".id").innerHTML, '{{$object_id}}');
          $V(input, '');
        }
      });
      </script>

      {{/if}}

			<!-- Cr�ation via ModeleSelector -->

	    <script type="text/javascript">
	      modeleSelector[{{$object_id}}] = new ModeleSelector("DocumentAdd-{{$unique_id}}-{{$object->_guid}}", null, "_modele_id", "_object_id");
	    </script>

      <button type="button" class="search notext" onclick="modeleSelector[{{$object_id}}].pop('{{$object_id}}','{{$object_class}}','{{$praticien->_id}}')">
	    	{{if $praticien->_can->edit}}
        Tous
	    	{{else}}
        Mod�les disponibles
	      {{/if}}
      </button>
      
	    <input type="hidden" name="_modele_id" value="" />
	    <input type="hidden" name="_object_id" value="" onchange="Document.create(this.form._modele_id.value, this.value,'{{$object_id}}','{{$object_class}}'); $V(this, ''); $V(this.form._modele_id, ''); "/>
    </td>
  </tr>
  
</table>

</form>

<div id="fast-{{$unique_id}}" style="display: none; width: auto; height: auto;"></div>

{{assign var=doc_count value=$object->_ref_documents|@count}}
{{if $mode != "hide"}}
<table class="tbl">
  
  {{if $doc_count && $mode == "collapse"}}
  <tr id="DocsEffect-{{$object->_guid}}-trigger">
    <th class="category" colspan="3">
    	{{tr}}{{$object->_class_name}}{{/tr}} :
    	{{$doc_count}} document(s)

		  <script type="text/javascript">
		    Main.add(function () {
		      new PairEffect("DocsEffect-{{$object->_guid}}", { 
		        bStoreInCookie: true
		      });
		    });
		  </script>
    </th>
  </tr>
  {{/if}}

  <tbody id="DocsEffect-{{$object->_guid}}" {{if $mode == "collapse" && $doc_count}}style="display: none;"{{/if}}>
  
  {{foreach from=$object->_ref_documents item=document}}
  <tr>
    <td class="text">
	    <a href="#{{$document->_guid}}" onclick="Document.edit({{$document->_id}}); return false;" style="display: inline;">
	      <span onmouseover="ObjectTooltip.createEx(this, '{{$document->_guid}}', 'objectView')">
	        {{$document}}
	      </span>
	    </a>
      {{if $document->private}}
        &mdash; <em>{{tr}}CCompteRendu-private{{/tr}}</em>
      {{/if}}
	  </td>
	  
	  <td class="button" style="width: 1px">
	    <form name="Edit-{{$document->_guid}}" action="?m={{$m}}" method="post">
  	    <input type="hidden" name="m" value="dPcompteRendu" />
  	    <input type="hidden" name="dosql" value="do_modele_aed" />
        <input type="hidden" name="del" value="0" />
  	    {{mb_key object=$document}}
        
  	    <input type="hidden" name="object_id" value="{{$object_id}}" />
  	    <input type="hidden" name="object_class" value="{{$object_class}}" />
  	    
  	    <button type="button" class="trash notext" onclick="Document.del(this.form, '{{$document->nom|smarty:nodefaults|JSAttribute}}')">
  	    	{{tr}}Delete{{/tr}}
  	    </button>
	    </form>
 	  </td> 

    {{if $dPconfig.dPfiles.system_sender}}
 	  <td class="button" style="width: 1px">
 	    <form name="Send-{{$document->_guid}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
  	    <input type="hidden" name="m" value="dPcompteRendu" />
  	    <input type="hidden" name="dosql" value="do_modele_aed" />
  	    <input type="hidden" name="del" value="0" />
        {{mb_key object=$document}}
      
        <!-- Send File -->
  		  {{mb_include module=dPfiles template=inc_file_send_button 
  	 	 		_doc_item=$document
  		 		notext=notext
  		 		onComplete="Document.refreshList('$object_class','$object_id')"
  	 	  }}
      </form>
 	  </td>
		{{/if}}
	</tr>
  {{foreachelse}}
  <tr>
    <td>
      <em>
      	{{tr}}{{$object->_class_name}}{{/tr}} :
      	Aucun document
      </em>
    </td>
  </tr>
  {{/foreach}}
</table>
{{/if}}