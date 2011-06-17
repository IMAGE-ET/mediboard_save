<script type="text/javascript">
Main.add(function () {
  if (document.selCabinet && "{{$offline}}" == 0){
    Calendar.regField(getForm("selCabinet").date, null, {noView: true});
  }
  
  // Mise � jour du compteur de patients arriv�s
	if($('tab_main_courante')){
		var link = $('tab_main_courante').select("a[href=#consultations]")[0];
	  link.update('Reconvocations <small>({{$nb_attente}} / {{$nb_a_venir}})</small>');
	  {{if $nb_attente == '0'}}
	    link.addClassName('empty');
	  {{else}}
	    link.removeClassName('empty');
	  {{/if}}
  }
});

printPlage = function(plage_id) {
  var url = new Url;
  url.setModuleAction("dPcabinet", "print_plages");
  url.addParam("plage_id", plage_id);
  url.popup(700, 550, "Planning");
}

Reconvocation = {
  checkPraticien: function() {
    var form = getForm('Create-Reconvocation');
		
    if ($V(form.prat_id) == '') {
      alert('Veuillez s�lectionner un praticien');
      return false;
    }
    return true;
  },
  
  choosePatient: function() {
    Consultations.stop();

    {{if !$mode_urgence}}
      if (!Reconvocation.checkPraticien()) {
        return false;
      }
    {{/if}}
    
		{{if $mode_urgence}}
      this.createConsult();
		{{/if}}
		
    return false;
  },
  createConsult: function() {
    var oForm = getForm("Create-Reconvocation");
    var url = new Url("dPcabinet", "ajax_create_reconvoc");
    url.requestModal(500);
  },
  submit: function() {
    var form = getForm('Create-Reconvocation');
    return onSubmitFormAjax(form, { onComplete: Consultations.start.curry(80) });  
  } 
}
</script>

<table class="main">
  {{if $mode_urgence}}
	<tr>
		<td>
		  <script type="text/javascript">
				PatSelector.init = function() {
				  this.sForm = 'Create-Reconvocation';
				  this.sId   = 'patient_id';
				  this.sView = '_patient_view';
				  this.pop();
				}
		  </script>
			
      <form name="Create-Reconvocation" method="post" action="?" onsubmit="return Reconvocation.choosePatient();">
        <input type="hidden" name="dosql" value="do_consult_now" />
        <input type="hidden" name="m" value="dPcabinet" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="_datetime" value="now" class="dateTime" />

        <input type="hidden" name="patient_id" class="ref notNull" onchange="Reconvocation.submit();"/>   
        <input type="hidden" name="_patient_view" />   
        <input type="hidden" name="prat_id" value="" />
        <input type="hidden" name="motif" value="" />   
        <button type="submit" class="new">Reconvocation imm�diate</button>
      </form>
			
		</td>
	</tr>
	{{else}}
  <tr>
    <td>
      <form name="selCabinet" action="?" method="get">
	    <input type="hidden" name="m" value="{{$m}}" />
	    <input type="hidden" name="tab" value="{{$tab}}" />
      <table class="form">
        <tr>
          <th class="title" colspan="100">
          	Journ�e de consultation du
            {{$date|date_format:$conf.longdate}}
            <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
          </th>
        </tr>
        
        {{if !$offline}}
          <tr>
            <th>
              <label for="cabinet_id" title="S�lectionner un cabinet">Cabinet</label>
            </th>
            <td>
              <select name="cabinet_id" onchange="this.form.submit()">
                <option value="">&mdash; Choisir un cabinet</option>
                {{foreach from=$cabinets item=curr_cabinet}}
                  <option value="{{$curr_cabinet->_id}}" class="mediuser" style="border-color: #{{$curr_cabinet->color}}" {{if $curr_cabinet->_id == $cabinet_id}} selected="selected" {{/if}}>
                    {{$curr_cabinet->_view}}
                  </option>
                {{/foreach}}
              </select>
            </td>
            
  		      <th>
  		      	<label for="closed" title="Type de vue du planning">Type de vue</label>
            </th>
  		      <td colspan="5">
  		        <select name="closed" onchange="this.form.submit()">
  		          <option value="1"{{if $closed == "1"}}selected="selected"{{/if}}>Tout afficher</option>
  		          <option value="0"{{if $closed == "0"}}selected="selected"{{/if}}>Masquer les termin�es</option>
  		        </select>
  		      </td>
          </tr>
        {{else}}
          <tr>
            <th class="title" colspan="100">
              {{$cabinet}}
            </th>
          </tr>
        {{/if}}
        
      </table> 
      </form>
    </td>
  </tr>
 {{/if}}
  <tr>
    <td>
      <table class="form">
        <tr>
        {{foreach from=$praticiens item=_praticien}}
          <th class="title">
            {{$_praticien}}
          </th>
        {{/foreach}}
        </tr>
   
         <!-- Affichage de la liste des consultations -->    
         <tr>
         {{foreach from=$listPlages item=curr_day}}
           <td style="width: 200px; vertical-align: top;">
             {{assign var="listPlage" value=$curr_day.plages}}
             {{assign var="tab" value=""}}
             {{assign var="vue" value="0"}}
             {{assign var="userSel" value=$curr_day.prat}}
             
             {{mb_ternary var=current_m test=$mode_urgence value=dPurgences other=dPcabinet}}
             
             {{mb_include module=dPcabinet template=inc_list_consult}}
           </td>
         {{foreachelse}}
           <td class="empty">{{tr}}CPlageconsult.none{{/tr}}</td>
         {{/foreach}}
       </tr>
     </table>
   </td>
 </tr>
</table>