<table class="tbl">
	<tr>
	  <th>{{mb_title class=CCompteRendu field=nom}}</th>
	  <th>{{mb_title class=CCompteRendu field=object_class}}</th>
	  <th>{{mb_title class=CCompteRendu field=type}}</th>
	  <th>{{tr}}Action{{/tr}}</th>
	</tr>

	{{foreach from=$modeles item=_modele}}
	<tr>
	  <td>
      {{if $_modele->fast_edit}}
        <img style="float: right;" src="images/icons/lightning.png" title="{{tr}}CCompteRendu.document_fast_edit{{/tr}}"/>
      {{/if}}
	    <a href="?m={{$m}}&amp;tab=addedit_modeles&amp;compte_rendu_id={{$_modele->compte_rendu_id}}">
	   	{{mb_value object=$_modele field=nom}}
	    </a>
	  </td>
	
	  <td>{{mb_value object=$_modele field=object_class}}</td>
	
	  <td>{{mb_value object=$_modele field=type}}</td>
	  
	  <td>
	    <form name="delete-{{$_modele->_guid}}" action="?m={{$m}}" method="post">
  	    <input type="hidden" name="m" value="{{$m}}" />
  	    <input type="hidden" name="del" value="1" />
  	    <input type="hidden" name="dosql" value="do_modele_aed" />
  	    {{mb_key object=$_modele}}
  	    <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le mod�le',objName:'{{$_modele->nom|smarty:nodefaults|JSAttribute}}'})">
  	      {{tr}}Delete{{/tr}}
  	    </button>
	    </form>
	  </td>
	</tr>

	{{foreachelse}}
	<tr>
	  <td colspan="10">
	    <em>{{tr}}CCompteRendu.none{{/tr}}</em>
	  </td>
	</tr>
  {{/foreach}}
</table>


