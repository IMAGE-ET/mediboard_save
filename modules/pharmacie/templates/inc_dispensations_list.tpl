{{if $mode_nominatif && !$prescription->_id}}
  <div class="big-info">
    Veuillez s�lectionner un patient pour r�aliser une dispensation nominative.
  </div>
{{else}}
<table class="tbl">
  {{if $mode_nominatif}}
  <tr>
    <th colspan="10" class="title">
      Dispensation pour {{$prescription->_ref_object->_ref_patient->_view}}
    </th>
  </tr>
  {{/if}}
  <tr>
    <th>Quantit� de la prise</th>
    <th>Quantit� calcul�e (unit� de r�f�rence)</th>
    <th>Calcul</th>
    <th>Quantit�</th>
    <th>D�j� effectu�es</th>
    <th>Dispensation</th>
    <th>Stock du service</th>
  </tr>
  {{foreach from=$dispensations key=code_cip item=unites}}
    {{assign var=medicament value=$medicaments.$code_cip}}
    {{assign var=_patients value=$patients.$code_cip}}
    <tr>
      <th colspan="10">{{$medicament->libelle}}</th>
    </tr>
    <tbody class="hoverable">
    {{foreach from=$unites key=unite_prise item=quantite name="dispensation"}}
      <tr>
        <td>
          {{$quantite}} {{$unite_prise}} 
          {{if array_key_exists($code_cip, $warning) && array_key_exists($unite_prise, $warning.$code_cip)}}
            <img src="images/icons/warning.png" alt="Poids non renseign�" title="Poids non renseign�" />
          {{/if}}   
        </td>
        <td>
        {{if array_key_exists($code_cip,$quantites_reference) && array_key_exists($unite_prise, $quantites_reference.$code_cip)}}
          {{$quantites_reference.$code_cip.$unite_prise}} {{$medicament->libelle_unite_presentation}}
        {{/if}}
        </td>
        
        {{if $smarty.foreach.dispensation.first}}
        <td rowspan="{{$unites|@count}}" style="text-align: center" />{{$quantites_reference.$code_cip.total}} / {{$medicament->nb_unite_presentation}} ({{$medicament->libelle_unite_presentation}})</td>
        <td rowspan="{{$unites|@count}}" style="text-align: center">
          <div onmouseover="ObjectTooltip.create(this, {mode: 'dom',  params: {element: 'tooltip-content-{{$code_cip}}'} })"
               class="tooltip-trigger">
            <a href="#">{{$quantites.$code_cip}} {{$medicament->libelle_conditionnement}}</a>
          </div>  
          <div id="tooltip-content-{{$code_cip}}" style="display: none; text-align: left;">
            <ul>
              {{foreach from=$_patients item=_patient}}
	              <li>
	                {{$_patient->_view}}
	              </li>
	            {{/foreach}}
            </ul>
          </div>
        </td>
        <td rowspan="{{$unites|@count}}" style="text-align: left">       
          {{foreach from=$done.$code_cip item=curr_done name="done"}}
            {{if !$smarty.foreach.done.first}}
              {{$curr_done->quantity}} {{$medicament->libelle_conditionnement}} le {{$curr_done->date_dispensation|@date_format:"%d/%m/%Y"}}
              {{if $curr_done->date_delivery}}
                (d�livr� le {{$curr_done->date_delivery|@date_format:"%d/%m/%Y"}})
              {{else}}
          <form name="form-dispensation-del-{{$curr_done->_id}}" action="?" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: refreshDeliveriesList})">
            <input type="hidden" name="m" value="dPstock" />
            <input type="hidden" name="dosql" value="do_delivery_aed" />
            <input type="hidden" name="del" value="1" />
            <input type="hidden" name="delivery_id" value="{{$curr_done->_id}}" />
            <button type="submit" class="cancel">Annuler</button>
          </form>
              {{/if}}
              <br />
            {{/if}}
          {{foreachelse}}
            Aucune
          {{/foreach}}
          </div>
        </td>
        <td rowspan="{{$unites|@count}}" style="text-align: center">
        {{if array_key_exists($code_cip,$delivrances)}}
          {{assign var=delivrance value=$delivrances.$code_cip}}
         
          <script type="text/javascript">prepareForm('form-dispensation-{{$code_cip}}');</script>
          <form name="form-dispensation-{{$code_cip}}" action="?" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: refreshDeliveriesList})">
            <input type="hidden" name="m" value="dPstock" />
            <input type="hidden" name="tab" value="{{$tab}}" />
            <input type="hidden" name="dosql" value="do_delivery_aed" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="date_dispensation" value="now" />
            <input type="hidden" name="stock_id" value="{{$delivrance->stock_id}}" />
            <input type="hidden" name="service_id" value="{{$delivrance->service_id}}" />
            {{if $mode_nominatif}}
            <input type="hidden" name="patient_id" value="{{$prescription->_ref_object->_ref_patient->_id}}" />
            {{/if}}
            {{if $delivrance->quantity==0}}<div style="color: red;">Dispensation impossible ou d�j� effectu�e</div>{{/if}}
            {{mb_field object=$delivrance field=quantity form="form-dispensation-$code_cip" increment=1 size=3}}
            <button type="submit" class="tick">
              Dispenser
            </button>
          </form>
        {{else}}
        Pas de stock � la pharmacie
        {{/if}}
        </td>
        <td rowspan="{{$unites|@count}}" style="text-align: center">
        {{if $stocks_service.$code_cip}}
          {{assign var=stock_service value=$stocks_service.$code_cip}}
          {{if $stock_service->quantity>0}}
          {{$stock_service->quantity}} d�j� en stock
          {{/if}}
        {{/if}}
        </td>
        {{/if}}
      </tr>
    {{/foreach}}
    </tbody>
  {{/foreach}}
</table>
{{/if}}