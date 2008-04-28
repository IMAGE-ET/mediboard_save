<script type="text/javascript">

function submitPersonnel(oForm){
  submitFormAjax(oForm, 'systemMsg', { onComplete : function() {reloadPersonnel(document.forms["affectationPers-aideop"].object_id.value)} });
}

</script>
       
<table class="form">
<tr>
  <th class="category" colspan="3">
    Personnel en salle
  </th>
</tr>
<tr>
  <th class="category">Personnel pr�vu</th>
  <th class="category">Personnel ajout�<br />
    <form name="affectationPers-aideop" action="?m={{$m}}" method="post">
      <input type="hidden" name="m" value="dPpersonnel" />
      <input type="hidden" name="dosql" value="do_affectation_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="affect_id" value="" />
    
      <input type="hidden" name="object_class" value="COperation" />
      <input type="hidden" name="object_id" value="{{$selOp->_id}}" />
      <input type="hidden" name="realise" value="0" />
    
      <select name="personnel_id" onchange="submitPersonnel(this.form)">
        <option value="">&mdash; Aide op�ratoire</option>
        {{foreach from=$listPersAideOp item="pers"}}
        <option value="{{$pers->_id}}">{{$pers->_ref_user->_view}}</option>
        {{/foreach}}
      </select>
    </form>
    <br />
    <form name="affectationPers-penseuse" action="?m={{$m}}" method="post">
      <input type="hidden" name="m" value="dPpersonnel" />
      <input type="hidden" name="dosql" value="do_affectation_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="affect_id" value="" />
    
      <input type="hidden" name="object_class" value="COperation" />
      <input type="hidden" name="object_id" value="{{$selOp->_id}}" />
      <input type="hidden" name="realise" value="0" />
    
      <select name="personnel_id" onchange="submitPersonnel(this.form)">
        <option value="">&mdash; Panseuse</option>
        {{foreach from=$listPersPanseuse item="pers"}}
        <option value="{{$pers->_id}}">{{$pers->_ref_user->_view}}</option>
        {{/foreach}}
      </select>
    </form>
    
  </th>
</tr>

{{assign var=submit value=submitPersonnel}}

<tr>
  <!-- Personnel pr�vu dans la plage op -->
  <td>
    {{foreach from=$tabPersonnel.plage item=affectation}}
    <form name="affectationPersonnel-{{$affectation->_id}}" action="?m={{$m}}" method="post">
    <input type="hidden" name="m" value="dPpersonnel" />
    <input type="hidden" name="dosql" value="do_affectation_aed" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="affect_id" value="{{$affectation->_id}}" />
    <input type="hidden" name="personnel_id" value="{{$affectation->_ref_personnel->_id}}" />
    <input type="hidden" name="object_class" value="COperation" />
    <input type="hidden" name="object_id" value="{{$selOp->_id}}" />
    <input type="hidden" name="realise" value="0" />
    
    {{assign var="affectation_id" value=$affectation->_id}}
    {{assign var="timing" value=$timingAffect.$affectation_id}}
    
    {{$affectation->_ref_personnel->_ref_user->_view}} /
    {{tr}}CPersonnel.emplacement.{{$affectation->_ref_personnel->emplacement}}{{/tr}}

    <table class="form">
      <tr>
		    {{include file=inc_field_timing.tpl object=$affectation field=_debut}}
		    {{include file=inc_field_timing.tpl object=$affectation field=_fin  }}
      </tr>
    </table>

   </form>

  <hr />
  {{/foreach}}
  </td>
  
  <!-- Personnel ajout� pour l'intervention -->
  <td>
    {{foreach from=$tabPersonnel.operation item=affectation}}

    {{if $can->edit || $modif_operation}}
    <div style="float: right">
    <form name="cancelAffectation-{{$affectation->_id}}" action="?m={{$m}}" method="post">
      <input type="hidden" name="affect_id" value="{{$affectation->_id}}" />
      <input type="hidden" name="m" value="dPpersonnel" />
      <input type="hidden" name="dosql" value="do_affectation_aed" />
      <input type="hidden" name="del" value="1" />
      <button type="button" class="cancel notext" onclick="submitPersonnel(this.form)">{{tr}}Cancel{{/tr}}</button>
    </form>
    </div>
    {{/if}}

    <form name="affectationPersonnel-{{$affectation->_id}}" action="?m={{$m}}" method="post">
    <input type="hidden" name="m" value="dPpersonnel" />
    <input type="hidden" name="dosql" value="do_affectation_aed" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="affect_id" value="{{$affectation->_id}}" />
    <input type="hidden" name="personnel_id" value="{{$affectation->_ref_personnel->_id}}" />
    <input type="hidden" name="object_class" value="COperation" />
    <input type="hidden" name="object_id" value="{{$selOp->_id}}" />
    <input type="hidden" name="realise" value="0" />
    
    {{assign var="affectation_id" value=$affectation->_id}}
    {{assign var="timing" value=$timingAffect.$affectation_id}}

    {{$affectation->_ref_personnel->_ref_user->_view}} / 
    {{tr}}CPersonnel.emplacement.{{$affectation->_ref_personnel->emplacement}}{{/tr}}
    <table class="form">
      <tr>
		    {{include file=inc_field_timing.tpl object=$affectation field=_debut}}
		    {{include file=inc_field_timing.tpl object=$affectation field=_fin  }}
      </tr>
    </table>
   </form>

 <hr />   
{{/foreach}}
</td>
</tr>


</table>

