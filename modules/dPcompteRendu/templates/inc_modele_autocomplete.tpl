{{assign var=pdf_thumbnails value=$conf.dPcompteRendu.CCompteRendu.pdf_thumbnails}}
{{assign var=pdf_and_thumbs value=$app->user_prefs.pdf_and_thumbs}}

<ul style="text-align: left;">
  {{foreach from=$modeles item=_modele}}
    {{if $_modele->_owner == "prat"}}
      {{if $_modele->user_id == $app->user_id}}
        {{assign var=owner_icon value="user-glow"}}
      {{else}}
        {{assign var=owner_icon value="user"}}
      {{/if}}
    {{elseif $_modele->_owner == "func"}}
      {{if $_modele->function_id == $app->_ref_user->function_id}}
        {{assign var=owner_icon value="user-function-glow"}}
      {{else}}
        {{assign var=owner_icon value="user-function"}}
      {{/if}}
    {{elseif $_modele->_owner == "etab"}}
      {{assign var=owner_icon value="group"}}
    {{/if}}
      
    <li>  
      <img style="float: right; clear: both; margin: -1px;" 
        src="images/icons/{{$owner_icon}}.png" />

        {{if $_modele->fast_edit_pdf && $pdf_thumbnails && $pdf_and_thumbs}}
          <img style="float: right;" src="modules/dPcompteRendu/fcke_plugins/mbprintPDF/images/mbprintPDF.png"/>
        {{elseif $_modele->fast_edit}}
          <img style="float: right;" src="modules/dPcompteRendu/fcke_plugins/mbprinting/images/mbprinting.png"/>
        {{/if}}
        {{if $_modele->fast_edit || ($_modele->fast_edit_pdf && $pdf_thumbnails && $pdf_and_thumbs)}}
          <img style="float: right;" src="images/icons/lightning.png"/>
        {{/if}}

      <div {{if $_modele->fast_edit || ($_modele->fast_edit_pdf && $pdf_thumbnails && $pdf_and_thumbs)}}class="fast_edit"{{/if}}>{{$_modele->nom|emphasize:$keywords}}</div>
      
      <!--{{if $_modele->file_category_id}}
        <small style="color: #666; margin-left: 1em;" class="text">
          {{mb_value object=$_modele field=file_category_id}}
        </small>
      {{/if}}-->
      
      <div style="display: none;" class="id">{{$_modele->_id}}</div>
    </li>
  {{/foreach}}
  <li>
    <div>
      {{tr}}CCompteRendu-blank_modele{{/tr}}
    </div>
    <div style="display: none;" class="id">0</div>
  </li>
</ul>